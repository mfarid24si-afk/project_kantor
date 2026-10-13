<?php

namespace App\Services;

use Exception;
use ZipArchive;

class SimpleSpreadsheetReader
{
    /**
     * Membaca file CSV atau XLSX menjadi array baris (array of associative array atau array of array).
     *
     * @return array<int, array<string, string>>
     */
    public function readFile(string $filePath, string $extension): array
    {
        $ext = strtolower($extension);

        if ($ext === 'csv' || $ext === 'txt') {
            return $this->readCsv($filePath);
        }

        if ($ext === 'xlsx') {
            return $this->readXlsx($filePath);
        }

        throw new Exception("Format file .{$extension} tidak didukung. Gunakan file .xlsx atau .csv.");
    }

    /**
     * Membaca file CSV dengan deteksi otomatis delimiter (koma atau titik koma).
     *
     * @return array<int, array<string, string>>
     */
    public function readCsv(string $filePath): array
    {
        $handle = fopen($filePath, 'r');
        if (! $handle) {
            return [];
        }

        // Cek BOM UTF-8
        $bom = fread($handle, 3);
        if ($bom !== "\xEF\xBB\xBF") {
            rewind($handle);
        }

        // Deteksi delimiter dari baris pertama
        $firstLine = fgets($handle);
        rewind($handle);
        if ($bom === "\xEF\xBB\xBF") {
            fread($handle, 3);
        }

        $delimiter = ',';
        if ($firstLine !== false && substr_count($firstLine, ';') > substr_count($firstLine, ',')) {
            $delimiter = ';';
        }

        $rows = [];
        $headers = [];

        while (($row = fgetcsv($handle, 0, $delimiter)) !== false) {
            if (empty($row) || (count($row) === 1 && trim((string) $row[0]) === '')) {
                continue;
            }

            if (empty($headers)) {
                $headers = array_map(fn ($h) => strtolower(trim((string) $h)), $row);

                continue;
            }

            $rowData = [];
            foreach ($headers as $index => $header) {
                $rowData[$header] = isset($row[$index]) ? trim((string) $row[$index]) : '';
            }

            $rows[] = $rowData;
        }

        fclose($handle);

        return $rows;
    }

    /**
     * Membaca file XLSX (OpenXML format) tanpa dependensi eksternal.
     * Menggunakan ZipArchive bawaan PHP dan SimpleXML.
     *
     * @return array<int, array<string, string>>
     */
    public function readXlsx(string $filePath): array
    {
        $zip = new ZipArchive;
        if ($zip->open($filePath) !== true) {
            throw new Exception('Gagal membuka file Excel (.xlsx). Pastikan file tidak rusak.');
        }

        // 1. Ambil shared strings jika ada
        $sharedStrings = [];
        $sharedStringsXml = $zip->getFromName('xl/sharedStrings.xml');
        if ($sharedStringsXml !== false) {
            $xml = simplexml_load_string($sharedStringsXml);
            if ($xml !== false) {
                foreach ($xml->si as $si) {
                    if (isset($si->t)) {
                        $sharedStrings[] = (string) $si->t;
                    } elseif (isset($si->r)) {
                        $text = '';
                        foreach ($si->r as $r) {
                            $text .= (string) $r->t;
                        }
                        $sharedStrings[] = $text;
                    } else {
                        $sharedStrings[] = '';
                    }
                }
            }
        }

        // 2. Ambil worksheet pertama (xl/worksheets/sheet1.xml)
        $sheetXml = $zip->getFromName('xl/worksheets/sheet1.xml');
        $zip->close();

        if ($sheetXml === false) {
            throw new Exception('Sheet kerja di file Excel tidak ditemukan.');
        }

        $xml = simplexml_load_string($sheetXml);
        if ($xml === false || ! isset($xml->sheetData)) {
            return [];
        }

        $sheetRows = [];
        foreach ($xml->sheetData->row as $row) {
            $cells = [];
            foreach ($row->c as $c) {
                $ref = (string) $c['r']; // misal "A1", "B2"
                $colLetters = preg_replace('/[0-9]/', '', $ref);
                $colIndex = $this->letterToColumnIndex($colLetters);

                $val = '';
                $type = (string) $c['t'];

                if ($type === 's') { // shared string
                    $idx = (int) $c->v;
                    $val = $sharedStrings[$idx] ?? '';
                } elseif ($type === 'inlineStr' && isset($c->is->t)) {
                    $val = (string) $c->is->t;
                } elseif (isset($c->v)) {
                    $val = (string) $c->v;
                }

                $cells[$colIndex] = trim($val);
            }

            // Normalisasi array kolom terurut
            if (! empty($cells)) {
                $maxCol = max(array_keys($cells));
                $orderedRow = [];
                for ($i = 0; $i <= $maxCol; $i++) {
                    $orderedRow[$i] = $cells[$i] ?? '';
                }
                $sheetRows[] = $orderedRow;
            }
        }

        if (empty($sheetRows)) {
            return [];
        }

        $headers = array_map(fn ($h) => strtolower(trim((string) $h)), array_shift($sheetRows));
        $result = [];

        foreach ($sheetRows as $row) {
            // Abaikan jika semua kolom kosong
            $allEmpty = true;
            foreach ($row as $val) {
                if ($val !== '') {
                    $allEmpty = false;
                    break;
                }
            }
            if ($allEmpty) {
                continue;
            }

            $rowData = [];
            foreach ($headers as $index => $header) {
                $rowData[$header] = isset($row[$index]) ? trim((string) $row[$index]) : '';
            }
            $result[] = $rowData;
        }

        return $result;
    }

    /**
     * Konversi kode kolom Excel ('A' -> 0, 'B' -> 1, 'AA' -> 26, dst).
     */
    private function letterToColumnIndex(string $letters): int
    {
        $letters = strtoupper($letters);
        $index = 0;
        $len = strlen($letters);
        for ($i = 0; $i < $len; $i++) {
            $index = $index * 26 + (ord($letters[$i]) - 64);
        }

        return $index - 1;
    }
}

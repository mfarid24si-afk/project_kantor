<?php

namespace App\Http\Controllers;

use App\Models\Guru;
use App\Models\Komunitas;
use App\Models\Siswa;
use App\Models\Umum;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ApiController extends Controller
{
    /**
     * Map nama tabel ke Model Eloquent yang sesuai.
     */
    protected function getModel(string $table)
    {
        return match ($table) {
            'guru' => Guru::class,
            'siswa' => Siswa::class,
            'komunitas' => Komunitas::class,
            'umum' => Umum::class,
            default => null,
        };
    }

    /**
     * Hitung jumlah guru per kabupaten untuk peta & perbandingan.
     */
    public function jumlahGuru(): JsonResponse
    {
        $counts = Guru::select('kabupaten', DB::raw('count(*) as total'))
            ->groupBy('kabupaten')
            ->pluck('total', 'kabupaten')
            ->toArray();

        return response()->json($counts);
    }

    /**
     * Hitung jumlah siswa per kabupaten.
     */
    public function jumlahSiswa(): JsonResponse
    {
        return response()->json([]);
    }

    /**
     * Ambil data tabel (mendukung filter kabupaten dan limit/order).
     */
    public function getTableData(Request $request, string $table): JsonResponse
    {
        $modelClass = $this->getModel($table);
        if (! $modelClass) {
            return response()->json(['error' => 'Tabel tidak ditemukan'], 404);
        }

        $query = $modelClass::query()->orderBy('created_at', 'desc');

        if ($request->has('kabupaten') && $table === 'guru') {
            $query->where('kabupaten', $request->query('kabupaten'));
        }

        return response()->json($query->get());
    }

    /**
     * Insert satu baris data ke tabel.
     */
    public function insertRow(Request $request, string $table): JsonResponse
    {
        $modelClass = $this->getModel($table);
        if (! $modelClass) {
            return response()->json(['error' => 'Tabel tidak ditemukan'], 404);
        }

        $data = $request->all();
        $record = $modelClass::create($data);

        return response()->json($record, 201);
    }

    /**
     * Insert banyak baris (bulk insert CSV).
     */
    public function insertBatch(Request $request, string $table): JsonResponse
    {
        $modelClass = $this->getModel($table);
        if (! $modelClass) {
            return response()->json(['error' => 'Tabel tidak ditemukan'], 404);
        }

        $rows = $request->json()->all();
        if (! is_array($rows)) {
            return response()->json(['error' => 'Payload harus berupa array'], 400);
        }

        $now = now();
        $records = [];
        foreach ($rows as $row) {
            if (is_array($row) && ! empty($row)) {
                $row['created_at'] = $now;
                $row['updated_at'] = $now;
                $records[] = $row;
            }
        }

        if (! empty($records)) {
            DB::table($table)->insert($records);
        }

        return response()->json(['success' => true, 'inserted' => count($records)], 201);
    }
}

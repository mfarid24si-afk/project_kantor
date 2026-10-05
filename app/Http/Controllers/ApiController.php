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

        $rows = json_decode($request->getContent(), true);
        if (! is_array($rows) || empty($rows) || ! array_is_list($rows)) {
            return response()->json(['error' => 'Payload harus berupa array dan tidak boleh kosong'], 400);
        }

        $now = now();
        $records = [];
        $fillable = (new $modelClass)->getFillable();
        $allowedKeys = array_flip($fillable);

        foreach ($rows as $row) {
            if (is_array($row) && ! empty($row)) {
                $filtered = array_intersect_key($row, $allowedKeys);
                if (! empty($filtered)) {
                    $filtered['created_at'] = $now;
                    $filtered['updated_at'] = $now;
                    $records[] = $filtered;
                }
            }
        }

        if (! empty($records)) {
            DB::table($table)->insert($records);
        }

        return response()->json(['success' => true, 'inserted' => count($records)], 201);
    }
}

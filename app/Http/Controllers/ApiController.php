<?php

namespace App\Http\Controllers;

use App\Models\Guru;
use App\Models\Kegiatan;
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
     * Dapatkan daftar tahun kegiatan yang tersedia.
     */
    public function getTahunList(): JsonResponse
    {
        $yearsFromKegiatan = Kegiatan::whereNotNull('tahun')
            ->distinct()
            ->orderBy('tahun', 'desc')
            ->pluck('tahun')
            ->map(fn ($y) => (int) $y)
            ->toArray();

        if (empty($yearsFromKegiatan)) {
            $yearsFromKegiatan = [(int) date('Y')];
        }

        return response()->json($yearsFromKegiatan);
    }

    /**
     * Hitung jumlah guru per kabupaten untuk peta & perbandingan.
     */
    public function jumlahGuru(Request $request): JsonResponse
    {
        $query = Guru::select('kabupaten', DB::raw('count(*) as total'))
            ->whereNotNull('kabupaten');

        if ($request->filled('tahun')) {
            $tahun = (int) $request->query('tahun');
            $query->where(function ($w) use ($tahun) {
                $w->whereHas('kegiatan', function ($k) use ($tahun) {
                    $k->where('tahun', $tahun);
                })->orWhere(function ($fallback) use ($tahun) {
                    $fallback->whereNull('kegiatan_id')
                        ->whereYear('created_at', $tahun);
                });
            });
        }

        $counts = $query->groupBy('kabupaten')
            ->pluck('total', 'kabupaten')
            ->toArray();

        return response()->json($counts);
    }

    /**
     * Hitung jumlah siswa per kabupaten.
     */
    public function jumlahSiswa(Request $request): JsonResponse
    {
        $query = Siswa::select('kabupaten', DB::raw('count(*) as total'))
            ->whereNotNull('kabupaten');

        if ($request->filled('tahun')) {
            $tahun = (int) $request->query('tahun');
            $query->where(function ($w) use ($tahun) {
                $w->whereHas('kegiatan', function ($k) use ($tahun) {
                    $k->where('tahun', $tahun);
                })->orWhere(function ($fallback) use ($tahun) {
                    $fallback->whereNull('kegiatan_id')
                        ->whereYear('created_at', $tahun);
                });
            });
        }

        $counts = $query->groupBy('kabupaten')
            ->pluck('total', 'kabupaten')
            ->toArray();

        return response()->json($counts);
    }

    /**
     * Hitung jumlah komunitas per kabupaten.
     */
    public function jumlahKomunitas(): JsonResponse
    {
        $counts = Komunitas::select('kabupaten', DB::raw('count(*) as total'))
            ->whereNotNull('kabupaten')
            ->groupBy('kabupaten')
            ->pluck('total', 'kabupaten')
            ->toArray();

        return response()->json($counts);
    }

    /**
     * Hitung jumlah umum per kabupaten.
     */
    public function jumlahUmum(): JsonResponse
    {
        $counts = Umum::select('kabupaten', DB::raw('count(*) as total'))
            ->whereNotNull('kabupaten')
            ->groupBy('kabupaten')
            ->pluck('total', 'kabupaten')
            ->toArray();

        return response()->json($counts);
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

        if ($request->has('kabupaten') && in_array($table, ['guru', 'siswa', 'komunitas', 'umum'])) {
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

        $fillable = (new $modelClass)->getFillable();
        $allowedKeys = array_flip($fillable);
        $data = array_intersect_key($request->all(), $allowedKeys);
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

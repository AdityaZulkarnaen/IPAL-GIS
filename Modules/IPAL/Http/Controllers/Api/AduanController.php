<?php

namespace Modules\IPAL\Http\Controllers\Api;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Modules\IPAL\Http\Controllers\Controller;
use Modules\IPAL\Models\Aduan;
use Modules\IPAL\Models\AduanDokumentasi;
use Modules\IPAL\Models\AduanHistory;
use Modules\IPAL\Models\IpalJaringanPipa;
use Modules\IPAL\Models\IpalManhole;
use Modules\IPAL\Services\ImageCompressionService;

class AduanController extends Controller
{
    public function __construct(private ImageCompressionService $imageService) {}

    /**
     * Generate a math captcha question and return an encrypted token containing the answer.
     * Token expires in 10 minutes.
     */
    public function captcha(): JsonResponse
    {
        $a     = rand(1, 10);
        $b     = rand(1, 10);
        $token = Crypt::encryptString(json_encode([
            'answer' => $a + $b,
            'exp'    => now()->addMinutes(10)->timestamp,
        ]));

        return response()->json([
            'success' => true,
            'data'    => [
                'question' => "{$a} + {$b}",
                'token'    => $token,
            ],
        ]);
    }

    /**
     * Submit a new complaint (public, no auth required).
     */
    public function store(Request $request): JsonResponse
    {
        if (config('ipal.aduan_captcha_enabled')) {
            $captchaValidator = Validator::make($request->all(), [
                'captcha_token'  => 'required|string',
                'captcha_answer' => 'required|integer',
            ], [
                'captcha_token.required'  => 'Token captcha tidak ditemukan. Muat ulang halaman dan coba lagi.',
                'captcha_answer.required' => 'Jawaban captcha wajib diisi.',
                'captcha_answer.integer'  => 'Jawaban captcha harus berupa angka.',
            ]);

            if ($captchaValidator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validasi gagal.',
                    'data'    => $captchaValidator->errors(),
                ], 422);
            }

            try {
                $decoded = json_decode(Crypt::decryptString($request->captcha_token), true);

                if (
                    empty($decoded['exp']) ||
                    empty($decoded['answer']) ||
                    $decoded['exp'] < now()->timestamp ||
                    (int) $decoded['answer'] !== (int) $request->captcha_answer
                ) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Validasi gagal.',
                        'data'    => ['captcha_answer' => ['Jawaban captcha salah atau telah kedaluwarsa.']],
                    ], 422);
                }
            } catch (\Throwable) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validasi gagal.',
                    'data'    => ['captcha_answer' => ['Token captcha tidak valid. Muat ulang halaman dan coba lagi.']],
                ], 422);
            }
        }

        $validator = Validator::make($request->all(), [
            'pipa_id'     => 'required_without:manhole_id|nullable|integer|exists:ipal_jaringan_pipa,id',
            'manhole_id'  => 'required_without:pipa_id|nullable|integer|exists:ipal_manholes,id',
            'deskripsi'   => 'required|string|max:5000',
            'foto'        => 'nullable|array|max:' . config('ipal.aduan_max_foto'),
            'foto.*'      => 'file|mimes:jpg,jpeg,png,webp',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal.',
                'data'    => $validator->errors(),
            ], 422);
        }

        if (!$request->filled('pipa_id') && !$request->filled('manhole_id')) {
            return response()->json([
                'success' => false,
                'message' => 'Salah satu dari pipa_id atau manhole_id wajib diisi.',
                'data'    => null,
            ], 422);
        }

        try {
            DB::beginTransaction();

            $aduan = Aduan::create([
                'nomor_tiket'    => Aduan::generateNomorTiket(),
                'id_pelapor'     => $request->user()?->id,
                'pipa_id'        => $request->filled('pipa_id') ? $request->pipa_id : null,
                'manhole_id'     => $request->filled('manhole_id') ? $request->manhole_id : null,
                'deskripsi'      => $request->deskripsi,
                'titik_koordinat' => $request->titik_koordinat,
                'status_aduan'   => 'masuk',
            ]);

            if ($request->hasFile('foto')) {
                foreach ($request->file('foto') as $file) {
                    $path = $this->imageService->compressToMaxKb($file, config('ipal.aduan_foto_max_kb_user'), $aduan->id);

                    AduanDokumentasi::create([
                        'aduan_id'        => $aduan->id,
                        'file_name'       => $file->getClientOriginalName(),
                        'file_path'       => $path,
                        'tipe_pengunggah' => 'pelapor',
                        'uploaded_at'     => now(),
                    ]);
                }
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Aduan berhasil dikirim.',
                'data'    => [
                    'id'          => $aduan->id,
                    'nomor_tiket' => $aduan->nomor_tiket,
                ],
            ], 201);
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('Aduan store failed', [
                'message' => $e->getMessage(),
                'trace'   => $e->getTraceAsString(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Gagal menyimpan aduan.',
                'data'    => null,
            ], 500);
        }
    }

    /**
     * List all complaints with optional filters (admin only).
     */
    public function index(Request $request): JsonResponse
    {
        $query = Aduan::with(['pipa:id,kode_pipa,wilayah', 'manhole:id,kode_manhole,wilayah'])
            ->withCount('dokumentasi');

        if ($request->filled('status_aduan')) {
            $query->where('status_aduan', $request->status_aduan);
        }

        if ($request->filled('pipa_id')) {
            $query->where('pipa_id', $request->pipa_id);
        }

        if ($request->filled('manhole_id')) {
            $query->where('manhole_id', $request->manhole_id);
        }

        if ($request->filled('search')) {
            $keyword = '%' . $request->search . '%';
            $query->where(function ($q) use ($keyword) {
                $q->where('nomor_tiket', 'like', $keyword)
                  ->orWhere('deskripsi', 'like', $keyword);
            });
        }

        $perPage = min((int) $request->get('per_page', 15), 100);
        $data    = $query->orderByDesc('created_at')->paginate($perPage);

        return response()->json([
            'success' => true,
            'message' => 'Daftar aduan berhasil dimuat.',
            'data'    => $data,
        ]);
    }

    /**
     * Get full detail of a single complaint (admin only).
     */
    public function show(int $id): JsonResponse
    {
        $aduan = Aduan::with([
            'pipa',
            'manhole',
            'dokumentasi',
            'history.admin:id,name',
        ])->find($id);

        if (!$aduan) {
            return response()->json([
                'success' => false,
                'message' => 'Aduan tidak ditemukan.',
                'data'    => null,
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'Detail aduan berhasil dimuat.',
            'data'    => $aduan,
        ]);
    }

    /**
     * Update complaint status and optionally update asset status (admin only).
     */
    public function updateStatus(Request $request, int $id): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'status_aduan'          => 'required|in:masuk,verifikasi,proses,selesai',
            'status_aset'           => 'nullable|in:baik,perbaikan,rusak',
            'catatan_tindak_lanjut' => 'nullable|string|max:5000',
            'foto'                  => 'nullable|file|mimes:jpg,jpeg,png,webp',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal.',
                'data'    => $validator->errors(),
            ], 422);
        }

        $aduan = Aduan::find($id);

        if (!$aduan) {
            return response()->json([
                'success' => false,
                'message' => 'Aduan tidak ditemukan.',
                'data'    => null,
            ], 404);
        }

        try {
            DB::beginTransaction();

            $statusSebelumnya = $aduan->status_aduan;

            AduanHistory::create([
                'aduan_id'              => $aduan->id,
                'admin_id'              => $request->user()->id,
                'status_sebelumnya'     => $statusSebelumnya,
                'status_sesudah'        => $request->status_aduan,
                'catatan_tindak_lanjut' => $request->catatan_tindak_lanjut,
                'created_at'            => now(),
            ]);

            $aduan->update(['status_aduan' => $request->status_aduan]);

            if ($request->filled('status_aset')) {
                if ($aduan->pipa_id) {
                    IpalJaringanPipa::where('id', $aduan->pipa_id)
                        ->update(['status' => $request->status_aset]);
                }

                if ($aduan->manhole_id) {
                    IpalManhole::where('id', $aduan->manhole_id)
                        ->update(['status' => $request->status_aset]);
                }
            }

            if ($request->hasFile('foto')) {
                $file = $request->file('foto');
                $path = $this->imageService->compressToMaxKb($file, config('ipal.aduan_foto_max_kb_admin'), $aduan->id);

                AduanDokumentasi::create([
                    'aduan_id'        => $aduan->id,
                    'file_name'       => $file->getClientOriginalName(),
                    'file_path'       => $path,
                    'tipe_pengunggah' => 'admin',
                    'uploaded_at'     => now(),
                ]);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Status aduan berhasil diperbarui.',
                'data'    => $aduan->fresh(['dokumentasi', 'history.admin:id,name']),
            ]);
        } catch (\Throwable $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Gagal memperbarui status aduan.',
                'data'    => null,
            ], 500);
        }
    }
}

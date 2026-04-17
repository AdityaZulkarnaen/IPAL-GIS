<?php

namespace Modules\IPAL\Http\Controllers\Api;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Modules\IPAL\Http\Controllers\Controller;
use Modules\IPAL\Models\Aduan;
use Modules\IPAL\Models\AduanDokumentasi;
use Modules\IPAL\Models\AduanHistory;
use Modules\IPAL\Models\IpalAssetStatus;
use Modules\IPAL\Models\IpalJaringanPipa;
use Modules\IPAL\Models\IpalManhole;
use Modules\IPAL\Services\ImageCompressionService;

class AduanController extends Controller
{
    private const WORKFLOW_ACTIONS = ['terima', 'tolak', 'mulai_perbaikan', 'tandai_selesai'];
    private const VERIFICATION_STATUSES = ['masuk', 'verifikasi', 'ditolak'];

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
        $filteredQuery = $this->buildFilteredAduanQuery($request);
        $perPage = min((int) $request->get('per_page', 15), 100);

        $groupPaginator = (clone $filteredQuery)
            ->selectRaw("CASE WHEN pipa_id IS NOT NULL THEN 'pipa' ELSE 'manhole' END AS asset_type")
            ->selectRaw('COALESCE(pipa_id, manhole_id) AS asset_id')
            ->selectRaw('MAX(created_at) AS latest_created_at')
            ->selectRaw('COUNT(*) AS laporan_count')
            ->groupBy('asset_type', 'asset_id')
            ->orderByDesc('latest_created_at')
            ->paginate($perPage);

        $groupRows = collect($groupPaginator->items());

        if ($groupRows->isNotEmpty()) {
            $groupedItems = Aduan::with(['pipa:id,kode_pipa,wilayah', 'manhole:id,kode_manhole,wilayah'])
                ->withCount('dokumentasi')
                ->where(function (Builder $query) use ($groupRows) {
                    foreach ($groupRows as $row) {
                        $query->orWhere(function (Builder $nestedQuery) use ($row) {
                            if ($row->asset_type === 'pipa') {
                                $nestedQuery->where('pipa_id', (int) $row->asset_id);
                                return;
                            }

                            $nestedQuery->where('manhole_id', (int) $row->asset_id);
                        });
                    }
                })
                ->orderByDesc('created_at')
                ->get();

            $groupedByKey = $groupedItems->groupBy(
                static fn (Aduan $item): ?string => Aduan::buildAssetGroupKey($item->pipa_id, $item->manhole_id)
            );

            $groupCollection = $groupRows
                ->map(function ($row) use ($groupedByKey) {
                    $groupKey = $row->asset_type . ':' . $row->asset_id;
                    $relatedAduan = $groupedByKey->get($groupKey, collect())->values();
                    $representative = $relatedAduan->first();

                    if (!$representative) {
                        return null;
                    }

                    return [
                        'group_key' => $groupKey,
                        'asset_type' => $row->asset_type,
                        'asset_id' => (int) $row->asset_id,
                        'laporan_count' => (int) $row->laporan_count,
                        'status_aduan' => $representative->status_aduan,
                        'representative_id' => $representative->id,
                        'latest_created_at' => $representative->created_at,
                        'asset' => [
                            'kode_aset' => $representative->pipa?->kode_pipa ?? $representative->manhole?->kode_manhole,
                            'wilayah' => $representative->pipa?->wilayah ?? $representative->manhole?->wilayah,
                        ],
                        'related_aduan' => $relatedAduan->map(static function (Aduan $item) {
                            return [
                                'id' => $item->id,
                                'nomor_tiket' => $item->nomor_tiket,
                                'status_aduan' => $item->status_aduan,
                                'created_at' => $item->created_at,
                            ];
                        })->values(),
                    ];
                })
                ->filter()
                ->values();

            $groupPaginator->setCollection($groupCollection);
        }

        return response()->json([
            'success' => true,
            'message' => 'Daftar aduan berhasil dimuat.',
            'data'    => $groupPaginator,
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

        $relatedAduan = $this->relatedAduanQuery($aduan)
            ->orderByDesc('created_at')
            ->get(['id', 'nomor_tiket', 'status_aduan', 'created_at']);

        $payload = $aduan->toArray();
        $payload['laporan_count'] = $relatedAduan->count();
        $payload['related_aduan'] = $relatedAduan;

        return response()->json([
            'success' => true,
            'message' => 'Detail aduan berhasil dimuat.',
            'data'    => $payload,
        ]);
    }

    /**
     * Update complaint status and optionally update asset status (admin only).
     */
    public function updateStatus(Request $request, int $id): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'workflow_action'       => 'nullable|in:' . implode(',', self::WORKFLOW_ACTIONS),
            'status_aduan'          => 'nullable|in:masuk,verifikasi,proses,ditolak,selesai',
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

        if (!$request->filled('workflow_action') && !$request->filled('status_aduan')) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal.',
                'data'    => ['workflow_action' => ['Pilih aksi workflow atau kirim status_aduan manual.']],
            ], 422);
        }

        try {
            $transition = $request->filled('workflow_action')
                ? $this->resolveWorkflowTransition($aduan, (string) $request->workflow_action)
                : [
                    'status_aduan' => (string) $request->status_aduan,
                    'status_aset' => $request->filled('status_aset') ? (string) $request->status_aset : null,
                    'success_message' => 'Status aduan berhasil diperbarui.',
                ];

            $updatedCount = 0;

            DB::transaction(function () use ($aduan, $request, $transition, &$updatedCount) {
                $relatedAduan = $this->relatedAduanQuery($aduan)
                    ->lockForUpdate()
                    ->get();

                $updatedCount = $relatedAduan->count();

                foreach ($relatedAduan as $relatedItem) {
                    $statusSebelumnya = $relatedItem->status_aduan;

                    $relatedItem->update(['status_aduan' => $transition['status_aduan']]);

                    AduanHistory::create([
                        'aduan_id'              => $relatedItem->id,
                        'admin_id'              => $request->user()->id,
                        'status_sebelumnya'     => $statusSebelumnya,
                        'status_sesudah'        => $transition['status_aduan'],
                        'catatan_tindak_lanjut' => $request->catatan_tindak_lanjut,
                        'created_at'            => now(),
                    ]);

                    if ($request->hasFile('foto')) {
                        $file = $request->file('foto');
                        $path = $this->imageService->compressToMaxKb(
                            $file,
                            config('ipal.aduan_foto_max_kb_admin'),
                            (string) $relatedItem->id
                        );

                        AduanDokumentasi::create([
                            'aduan_id'        => $relatedItem->id,
                            'file_name'       => $file->getClientOriginalName(),
                            'file_path'       => $path,
                            'tipe_pengunggah' => 'admin',
                            'uploaded_at'     => now(),
                        ]);
                    }
                }

                if ($transition['status_aset'] !== null) {
                    $this->updateRelatedAssetStatus($aduan, $transition['status_aset']);
                }
            });

            $successMessage = $transition['success_message'];
            if ($updatedCount > 1) {
                $successMessage .= ' ' . $updatedCount . ' aduan terkait turut diperbarui.';
            }

            $relatedAduan = $this->relatedAduanQuery($aduan)
                ->orderByDesc('created_at')
                ->get(['id', 'nomor_tiket', 'status_aduan', 'created_at']);

            $payload = $aduan->fresh(['dokumentasi', 'history.admin:id,name'])->toArray();
            $payload['laporan_count'] = $relatedAduan->count();
            $payload['related_aduan'] = $relatedAduan;

            return response()->json([
                'success' => true,
                'message' => $successMessage,
                'data'    => $payload,
            ]);
        } catch (\DomainException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
                'data'    => null,
            ], 422);
        } catch (\Throwable $e) {
            Log::error('Aduan update status failed', [
                'message' => $e->getMessage(),
                'trace'   => $e->getTraceAsString(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Gagal memperbarui status aduan.',
                'data'    => null,
            ], 500);
        }
    }

    private function resolveWorkflowTransition(Aduan $aduan, string $action): array
    {
        return match ($action) {
            'terima' => $this->transitionTerima($aduan),
            'tolak' => $this->transitionTolak($aduan),
            'mulai_perbaikan' => $this->transitionMulaiPerbaikan($aduan),
            'tandai_selesai' => $this->transitionTandaiSelesai($aduan),
            default => throw new \DomainException('Aksi workflow tidak dikenal.'),
        };
    }

    private function transitionTerima(Aduan $aduan): array
    {
        if (!in_array($aduan->status_aduan, self::VERIFICATION_STATUSES, true)) {
            throw new \DomainException('Aksi Terima hanya bisa dilakukan pada tahap verifikasi.');
        }

        return [
            'status_aduan' => 'proses',
            'status_aset' => 'rusak',
            'success_message' => 'Laporan diterima. Status aduan menjadi Diproses dan status aset menjadi Rusak.',
        ];
    }

    private function transitionTolak(Aduan $aduan): array
    {
        if (!in_array($aduan->status_aduan, self::VERIFICATION_STATUSES, true)) {
            throw new \DomainException('Aksi Tolak hanya bisa dilakukan pada tahap verifikasi.');
        }

        return [
            'status_aduan' => 'ditolak',
            'status_aset' => null,
            'success_message' => 'Laporan ditolak. Status aduan menjadi Ditolak.',
        ];
    }

    private function transitionMulaiPerbaikan(Aduan $aduan): array
    {
        if ($aduan->status_aduan !== 'proses') {
            throw new \DomainException('Aksi Mulai Perbaikan hanya tersedia untuk aduan berstatus Diproses.');
        }

        return [
            'status_aduan' => 'proses',
            'status_aset' => 'perbaikan',
            'success_message' => 'Perbaikan dimulai. Status aset diperbarui menjadi Perbaikan.',
        ];
    }

    private function transitionTandaiSelesai(Aduan $aduan): array
    {
        if ($aduan->status_aduan !== 'proses') {
            throw new \DomainException('Aksi Tandai Selesai hanya tersedia untuk aduan berstatus Diproses.');
        }

        return [
            'status_aduan' => 'selesai',
            'status_aset' => 'baik',
            'success_message' => 'Aduan selesai. Status aset dikembalikan menjadi Baik.',
        ];
    }

    private function buildFilteredAduanQuery(Request $request): Builder
    {
        $query = Aduan::query();

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
            $keyword = '%' . trim((string) $request->search) . '%';
            $query->where(function (Builder $searchQuery) use ($keyword) {
                $searchQuery->where('nomor_tiket', 'like', $keyword)
                    ->orWhere('deskripsi', 'like', $keyword)
                    ->orWhereHas('pipa', function (Builder $pipeQuery) use ($keyword) {
                        $pipeQuery->where('kode_pipa', 'like', $keyword)
                            ->orWhere('wilayah', 'like', $keyword);
                    })
                    ->orWhereHas('manhole', function (Builder $manholeQuery) use ($keyword) {
                        $manholeQuery->where('kode_manhole', 'like', $keyword)
                            ->orWhere('wilayah', 'like', $keyword);
                    });
            });
        }

        return $query;
    }

    private function relatedAduanQuery(Aduan $aduan): Builder
    {
        return Aduan::query()->sameAssetAs($aduan);
    }

    private function updateRelatedAssetStatus(Aduan $aduan, string $statusAset): void
    {
        $normalizedStatus = IpalAssetStatus::normalizeStatus($statusAset);

        if ($aduan->pipa_id) {
            $pipe = IpalJaringanPipa::query()
                ->select(['id', 'kode_pipa'])
                ->find($aduan->pipa_id);

            if ($pipe !== null && trim((string) $pipe->kode_pipa) !== '') {
                IpalAssetStatus::updateOrCreate(
                    [
                        'asset_type' => IpalAssetStatus::ASSET_TYPE_PIPE,
                        'asset_code' => $pipe->kode_pipa,
                    ],
                    [
                        'asset_id' => $pipe->id,
                        'status' => $normalizedStatus,
                    ]
                );
            }

            IpalJaringanPipa::where('id', $aduan->pipa_id)->update(['status' => $normalizedStatus]);
        }

        if ($aduan->manhole_id) {
            $manhole = IpalManhole::query()
                ->select(['id', 'kode_manhole'])
                ->find($aduan->manhole_id);

            if ($manhole !== null && trim((string) $manhole->kode_manhole) !== '') {
                IpalAssetStatus::updateOrCreate(
                    [
                        'asset_type' => IpalAssetStatus::ASSET_TYPE_MANHOLE,
                        'asset_code' => $manhole->kode_manhole,
                    ],
                    [
                        'asset_id' => $manhole->id,
                        'status' => $normalizedStatus,
                    ]
                );
            }

            IpalManhole::where('id', $aduan->manhole_id)->update(['status' => $normalizedStatus]);
        }
    }
}

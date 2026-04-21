<?php

namespace Modules\IPAL\Http\Controllers;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use App\Models\KonfigurasiModel;
use Modules\IPAL\Models\Aduan;
use Modules\IPAL\Models\AduanDokumentasi;
use Modules\IPAL\Models\AduanHistory;
use Modules\IPAL\Models\IpalAssetStatus;
use Modules\IPAL\Models\IpalJaringanPipa;
use Modules\IPAL\Models\IpalManhole;
use Modules\IPAL\Services\ImageCompressionService;

class AduanController extends Controller
{
    private const WORKFLOW_ACTIONS = ['terima', 'tolak', 'mulai_perbaikan', 'tandai_selesai', 'simpan_catatan'];
    private const VERIFICATION_STATUSES = ['masuk', 'verifikasi', 'ditolak'];

    public function __construct(private ImageCompressionService $imageService) {}

    public function index(Request $request)
    {
        $toptitle = 'IPAL';
        $title    = 'Manajemen Aduan';
        $subtitle = 'Daftar Aduan Masuk';

        $allowedPerPage = [5, 10, 15, 25, 50];
        $perPage = (int) $request->input('per_page', 5);
        if (!in_array($perPage, $allowedPerPage, true)) {
            $perPage = 5;
        }

        $filteredQuery = $this->buildFilteredAduanQuery($request);

        $groupPaginator = (clone $filteredQuery)
            ->selectRaw("CASE WHEN pipa_id IS NOT NULL THEN 'pipa' ELSE 'manhole' END AS asset_type")
            ->selectRaw('COALESCE(pipa_id, manhole_id) AS asset_id')
            ->selectRaw('MAX(created_at) AS latest_created_at')
            ->selectRaw('COUNT(*) AS laporan_count')
            ->groupBy('asset_type', 'asset_id')
            ->orderByDesc('latest_created_at')
            ->paginate($perPage)
            ->withQueryString();

        $groupRows = collect($groupPaginator->items());
        $groupedAduan = $groupRows
            ->map(function ($row) {
                $representativeQuery = Aduan::with(['pipa:id,kode_pipa,wilayah', 'manhole:id,kode_manhole,wilayah'])
                    ->withCount('dokumentasi');

                if ($row->asset_type === 'pipa') {
                    $representativeQuery->where('pipa_id', (int) $row->asset_id);
                } else {
                    $representativeQuery->where('manhole_id', (int) $row->asset_id);
                }

                $representative = $representativeQuery
                    ->orderByDesc('created_at')
                    ->orderByDesc('id')
                    ->first();

                if (!$representative) {
                    return null;
                }

                $representative->laporan_count = (int) $row->laporan_count;

                return $representative;
            })
            ->filter()
            ->values();

        $groupPaginator->setCollection($groupedAduan);
        $aduan = $groupPaginator;

        $data_konfig = KonfigurasiModel::first();
        $service     = ['data_konfig' => $data_konfig];

        return view('ipal::aduan.index', compact(
            'toptitle', 'title', 'subtitle', 'service', 'aduan'
        ));
    }

    public function show(int $id)
    {
        $toptitle = 'IPAL';
        $title    = 'Manajemen Aduan';
        $subtitle = 'Detail Aduan';

        $aduan = Aduan::with([
            'pipa',
            'pipa.canonicalStatus',
            'manhole',
            'manhole.canonicalStatus',
            'dokumentasi',
            'history.admin:id,name',
        ])->findOrFail($id);
        $relatedAduanCount = (clone $this->relatedAduanQuery($aduan))->count();

        $data_konfig = KonfigurasiModel::first();
        $service     = ['data_konfig' => $data_konfig];

        return view('ipal::aduan.show', compact(
            'toptitle',
            'title',
            'subtitle',
            'service',
            'aduan',
            'relatedAduanCount'
        ));
    }

    public function relatedAduanIndex(Request $request, int $id): JsonResponse
    {
        $aduan = Aduan::with(['pipa:id,kode_pipa,wilayah', 'manhole:id,kode_manhole,wilayah'])->findOrFail($id);

        $allowedPerPage = [5, 10, 20, 50];
        $perPage = (int) $request->input('per_page', 10);
        if (!in_array($perPage, $allowedPerPage, true)) {
            $perPage = 10;
        }

        $query = $this->relatedAduanQuery($aduan)
            ->withCount([
                'dokumentasi as pelapor_foto_count' => static fn (Builder $q) => $q->where('tipe_pengunggah', 'pelapor'),
                'dokumentasi as admin_foto_count' => static fn (Builder $q) => $q->where('tipe_pengunggah', 'admin'),
            ]);

        if ($request->filled('search')) {
            $keyword = '%' . trim((string) $request->input('search')) . '%';
            $query->where(function (Builder $searchQuery) use ($keyword) {
                $searchQuery->where('nomor_tiket', 'like', $keyword)
                    ->orWhere('deskripsi', 'like', $keyword)
                    ->orWhere('status_aduan', 'like', $keyword);
            });
        }

        $paginator = $query
            ->orderByDesc('created_at')
            ->orderByDesc('id')
            ->paginate($perPage)
            ->withQueryString();

        $items = collect($paginator->items())->map(function (Aduan $item) use ($aduan) {
            $statusMeta = $this->statusMeta($item->status_aduan);

            return [
                'id' => $item->id,
                'nomor_tiket' => $item->nomor_tiket,
                'status_aduan' => $item->status_aduan,
                'status_label' => $statusMeta['label'],
                'status_class' => $statusMeta['class'],
                'created_at' => optional($item->created_at)->toIso8601String(),
                'created_at_label' => optional($item->created_at)?->format('d F Y, H.i'),
                'deskripsi_preview' => Str::limit(trim((string) $item->deskripsi), 160),
                'pelapor_foto_count' => (int) $item->pelapor_foto_count,
                'admin_foto_count' => (int) $item->admin_foto_count,
                'detail_url' => route('ipal.aduan.related.show', ['id' => $aduan->id, 'relatedId' => $item->id]),
            ];
        })->values();

        $paginator->setCollection($items);

        return response()->json([
            'success' => true,
            'message' => 'Daftar aduan terkait berhasil dimuat.',
            'data' => [
                'summary' => [
                    'laporan_count' => $paginator->total(),
                    'asset_type' => $aduan->pipa_id ? 'pipa' : 'manhole',
                    'asset_code' => $aduan->pipa?->kode_pipa ?? $aduan->manhole?->kode_manhole,
                    'asset_location' => $aduan->pipa?->wilayah ?? $aduan->manhole?->wilayah,
                ],
                'list' => $paginator,
            ],
        ]);
    }

    public function relatedAduanShow(int $id, int $relatedId): JsonResponse
    {
        $aduan = Aduan::with(['pipa:id,kode_pipa,wilayah', 'manhole:id,kode_manhole,wilayah'])->findOrFail($id);

        $relatedAduan = $this->relatedAduanQuery($aduan)
            ->with(['dokumentasi' => static fn ($query) => $query->orderByDesc('uploaded_at')])
            ->where('id', $relatedId)
            ->first();

        if (!$relatedAduan) {
            return response()->json([
                'success' => false,
                'message' => 'Aduan terkait tidak ditemukan.',
                'data' => null,
            ], 404);
        }

        $statusMeta = $this->statusMeta($relatedAduan->status_aduan);

        $pelaporPhotos = $relatedAduan->dokumentasi
            ->where('tipe_pengunggah', 'pelapor')
            ->values()
            ->map(static fn (AduanDokumentasi $foto): array => [
                'id' => $foto->id,
                'file_name' => $foto->file_name,
                'url' => Storage::url($foto->file_path),
            ]);

        $adminPhotos = $relatedAduan->dokumentasi
            ->where('tipe_pengunggah', 'admin')
            ->values()
            ->map(static fn (AduanDokumentasi $foto): array => [
                'id' => $foto->id,
                'file_name' => $foto->file_name,
                'url' => Storage::url($foto->file_path),
            ]);

        return response()->json([
            'success' => true,
            'message' => 'Detail aduan terkait berhasil dimuat.',
            'data' => [
                'id' => $relatedAduan->id,
                'nomor_tiket' => $relatedAduan->nomor_tiket,
                'status_aduan' => $relatedAduan->status_aduan,
                'status_label' => $statusMeta['label'],
                'status_class' => $statusMeta['class'],
                'created_at' => optional($relatedAduan->created_at)->toIso8601String(),
                'created_at_label' => optional($relatedAduan->created_at)?->format('d F Y, H.i'),
                'deskripsi' => (string) $relatedAduan->deskripsi,
                'pelapor_photos' => $pelaporPhotos->values(),
                'admin_photos' => $adminPhotos->values(),
            ],
        ]);
    }

    public function updateStatus(Request $request, int $id)
    {
        $aduan = Aduan::findOrFail($id);
        $expectsJson = $request->expectsJson() || $request->ajax();

        $validator = Validator::make($request->all(), [
            'workflow_action'       => 'required|in:' . implode(',', self::WORKFLOW_ACTIONS),
            'catatan_tindak_lanjut' => 'nullable|string|max:5000',
            'foto'                  => 'nullable|file|mimes:jpg,jpeg,png,webp',
        ]);

        if ($validator->fails()) {
            if ($expectsJson) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validasi gagal.',
                    'data' => $validator->errors(),
                ], 422);
            }

            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            $transition = $this->resolveWorkflowTransition($aduan, (string) $request->workflow_action);
            $noteLines = $this->extractProgressNoteLines((string) $request->catatan_tindak_lanjut);

            if (($transition['requires_note'] ?? false) && $noteLines->isEmpty()) {
                throw new \DomainException('Tambahkan minimal satu catatan progress sebelum menyimpan.');
            }

            $updatedCount = 0;

            DB::transaction(function () use ($aduan, $request, $transition, $noteLines, &$updatedCount) {
                $relatedAduan = $this->relatedAduanQuery($aduan)
                    ->lockForUpdate()
                    ->get();

                $updatedCount = $relatedAduan->count();

                foreach ($relatedAduan as $relatedItem) {
                    $statusSebelumnya = $relatedItem->status_aduan;

                    if (($transition['update_status'] ?? true) === true) {
                        $relatedItem->update(['status_aduan' => $transition['status_aduan']]);
                    }

                    if (($transition['record_status_history'] ?? true) === true) {
                        AduanHistory::create([
                            'aduan_id'              => $relatedItem->id,
                            'admin_id'              => $request->user()->id,
                            'status_sebelumnya'     => $statusSebelumnya,
                            'status_sesudah'        => $transition['status_aduan'],
                            'catatan_tindak_lanjut' => null,
                            'created_at'            => now(),
                        ]);
                    }

                    if ($noteLines->isNotEmpty()) {
                        $statusCatatan = $statusSebelumnya === 'proses' ? 'proses' : $relatedItem->status_aduan;
                        $this->persistProgressNotes(
                            $relatedItem,
                            (int) $request->user()->id,
                            $statusCatatan,
                            $noteLines
                        );
                    }

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

            if ($expectsJson) {
                return response()->json([
                    'success' => true,
                    'message' => $successMessage,
                    'data' => [
                        'redirect_url' => route('ipal.aduan.show', $aduan->id),
                    ],
                ]);
            }

            return redirect()->route('ipal.aduan.show', $aduan->id)
                ->with('success', $successMessage);
        } catch (\DomainException $e) {
            if ($expectsJson) {
                return response()->json([
                    'success' => false,
                    'message' => $e->getMessage(),
                    'data' => null,
                ], 422);
            }

            return redirect()->back()
                ->with('error', $e->getMessage())
                ->withInput();
        } catch (\Throwable $e) {
            if ($expectsJson) {
                return response()->json([
                    'success' => false,
                    'message' => 'Gagal memperbarui status aduan.',
                    'data' => null,
                ], 500);
            }

            return redirect()->back()
                ->with('error', 'Gagal memperbarui status aduan.')
                ->withInput();
        }
    }

    private function resolveWorkflowTransition(Aduan $aduan, string $action): array
    {
        return match ($action) {
            'terima' => $this->transitionTerima($aduan),
            'tolak' => $this->transitionTolak($aduan),
            'mulai_perbaikan' => $this->transitionMulaiPerbaikan($aduan),
            'tandai_selesai' => $this->transitionTandaiSelesai($aduan),
            'simpan_catatan' => $this->transitionSimpanCatatan($aduan),
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
            'update_status' => true,
            'record_status_history' => true,
            'requires_note' => false,
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
            'update_status' => true,
            'record_status_history' => true,
            'requires_note' => false,
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
            'update_status' => true,
            'record_status_history' => true,
            'requires_note' => false,
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
            'update_status' => true,
            'record_status_history' => true,
            'requires_note' => false,
            'success_message' => 'Aduan selesai. Status aset dikembalikan menjadi Baik.',
        ];
    }

    private function transitionSimpanCatatan(Aduan $aduan): array
    {
        if ($aduan->status_aduan !== 'proses') {
            throw new \DomainException('Simpan Catatan hanya tersedia untuk aduan berstatus Diproses.');
        }

        return [
            'status_aduan' => $aduan->status_aduan,
            'status_aset' => null,
            'update_status' => false,
            'record_status_history' => false,
            'requires_note' => true,
            'success_message' => 'Catatan progress berhasil disimpan.',
        ];
    }

    private function extractProgressNoteLines(string $rawNotes)
    {
        return collect(preg_split('/\r\n|\r|\n/', $rawNotes))
            ->map(static fn (string $line): string => trim($line))
            ->filter(static fn (string $line): bool => $line !== '')
            ->values();
    }

    private function persistProgressNotes(Aduan $aduan, int $adminId, string $status, $noteLines): void
    {
        $existingNotes = AduanHistory::query()
            ->where('aduan_id', $aduan->id)
            ->whereNotNull('catatan_tindak_lanjut')
            ->pluck('catatan_tindak_lanjut')
            ->map(static fn (?string $note): string => trim((string) $note))
            ->filter(static fn (string $note): bool => $note !== '')
            ->all();

        $newNotes = $noteLines
            ->reject(static fn (string $line): bool => in_array($line, $existingNotes, true))
            ->values();

        foreach ($newNotes as $index => $line) {
            AduanHistory::create([
                'aduan_id'              => $aduan->id,
                'admin_id'              => $adminId,
                'status_sebelumnya'     => $status,
                'status_sesudah'        => $status,
                'catatan_tindak_lanjut' => $line,
                'created_at'            => now()->addSeconds($index + 1),
            ]);
        }
    }

    private function buildFilteredAduanQuery(Request $request): Builder
    {
        $query = Aduan::query();

        if ($request->filled('status_aduan')) {
            $query->where('status_aduan', $request->status_aduan);
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

    private function statusMeta(string $status): array
    {
        return match ($status) {
            'masuk', 'verifikasi' => ['label' => 'MENUNGGU', 'class' => 'aduan-status aduan-status-menunggu'],
            'proses' => ['label' => 'DIPROSES', 'class' => 'aduan-status aduan-status-diproses'],
            'ditolak' => ['label' => 'DITOLAK', 'class' => 'aduan-status aduan-status-ditolak'],
            'selesai' => ['label' => 'SELESAI', 'class' => 'aduan-status aduan-status-selesai'],
            default => ['label' => strtoupper($status), 'class' => 'aduan-status'],
        };
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

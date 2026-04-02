<?php

namespace Modules\IPAL\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use App\Models\KonfigurasiModel;
use Modules\IPAL\Models\Aduan;
use Modules\IPAL\Models\AduanDokumentasi;
use Modules\IPAL\Models\AduanHistory;
use Modules\IPAL\Models\IpalJaringanPipa;
use Modules\IPAL\Models\IpalManhole;
use Modules\IPAL\Services\ImageCompressionService;

class AduanController extends Controller
{
    private const WORKFLOW_ACTIONS = ['terima', 'tolak', 'mulai_perbaikan', 'tandai_selesai'];
    private const VERIFICATION_STATUSES = ['masuk', 'verifikasi', 'ditolak'];

    public function __construct(private ImageCompressionService $imageService) {}

    public function index(Request $request)
    {
        $toptitle = 'IPAL';
        $title    = 'Manajemen Aduan';
        $subtitle = 'Daftar Aduan Masuk';

        $query = Aduan::with(['pipa:id,kode_pipa,wilayah', 'manhole:id,kode_manhole,wilayah'])
            ->withCount('dokumentasi');

        if ($request->filled('status_aduan')) {
            $query->where('status_aduan', $request->status_aduan);
        }

        if ($request->filled('search')) {
            $keyword = '%' . $request->search . '%';
            $query->where(function ($q) use ($keyword) {
                $q->where('nomor_tiket', 'like', $keyword)
                  ->orWhere('deskripsi', 'like', $keyword);
            });
        }

        $aduan = $query->orderByDesc('created_at')->paginate(15)->withQueryString();

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
            'manhole',
            'dokumentasi',
            'history.admin:id,name',
        ])->findOrFail($id);

        $data_konfig = KonfigurasiModel::first();
        $service     = ['data_konfig' => $data_konfig];

        return view('ipal::aduan.show', compact(
            'toptitle', 'title', 'subtitle', 'service', 'aduan'
        ));
    }

    public function updateStatus(Request $request, int $id)
    {
        $aduan = Aduan::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'workflow_action'       => 'required|in:' . implode(',', self::WORKFLOW_ACTIONS),
            'catatan_tindak_lanjut' => 'nullable|string|max:5000',
            'foto'                  => 'nullable|file|mimes:jpg,jpeg,png,webp',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            $transition = $this->resolveWorkflowTransition($aduan, (string) $request->workflow_action);

            DB::transaction(function () use ($aduan, $request, $transition) {
                $statusSebelumnya = $aduan->status_aduan;

                $aduan->update(['status_aduan' => $transition['status_aduan']]);

                if ($transition['status_aset'] !== null) {
                    $this->updateRelatedAssetStatus($aduan, $transition['status_aset']);
                }

                AduanHistory::create([
                    'aduan_id'              => $aduan->id,
                    'admin_id'              => $request->user()->id,
                    'status_sebelumnya'     => $statusSebelumnya,
                    'status_sesudah'        => $transition['status_aduan'],
                    'catatan_tindak_lanjut' => $request->catatan_tindak_lanjut,
                    'created_at'            => now(),
                ]);

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
            });

            return redirect()->route('ipal.aduan.show', $aduan->id)
                ->with('success', $transition['success_message']);
        } catch (\DomainException $e) {
            return redirect()->back()
                ->with('error', $e->getMessage())
                ->withInput();
        } catch (\Throwable $e) {
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

    private function updateRelatedAssetStatus(Aduan $aduan, string $statusAset): void
    {
        if ($aduan->pipa_id) {
            IpalJaringanPipa::where('id', $aduan->pipa_id)->update(['status' => $statusAset]);
        }

        if ($aduan->manhole_id) {
            IpalManhole::where('id', $aduan->manhole_id)->update(['status' => $statusAset]);
        }
    }
}

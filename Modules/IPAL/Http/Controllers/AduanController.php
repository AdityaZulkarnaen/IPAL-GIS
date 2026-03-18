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
            'status_aduan'          => 'required|in:masuk,verifikasi,proses,selesai',
            'status_aset'           => 'nullable|in:baik,perbaikan,rusak',
            'catatan_tindak_lanjut' => 'nullable|string|max:5000',
            'foto'                  => 'nullable|file|mimes:jpg,jpeg,png,webp',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            DB::beginTransaction();

            AduanHistory::create([
                'aduan_id'              => $aduan->id,
                'admin_id'              => $request->user()->id,
                'status_sebelumnya'     => $aduan->status_aduan,
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

            return redirect()->route('ipal.aduan.show', $aduan->id)
                ->with('success', 'Status aduan berhasil diperbarui.');
        } catch (\Throwable $e) {
            DB::rollBack();

            return redirect()->back()
                ->with('error', 'Gagal memperbarui status aduan.')
                ->withInput();
        }
    }
}

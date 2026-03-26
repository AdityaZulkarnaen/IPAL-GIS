<div class="bg-white border border-slate-200 rounded-xl shadow-panel h-full">
    <div class="px-6 pt-6 pb-2">
        <h3 class="text-lg font-bold text-slate-800">Riwayat Upload</h3>
    </div>
    <div class="px-6 pb-6 pt-0">
        <ul class="list-none m-0 p-0">
            @forelse($uploads->getCollection()->take(6) as $upload)
            <li class="py-3 border-b border-slate-100 last:border-b-0">
                <div class="flex items-start justify-between gap-3">
                    <div>
                        <div class="font-semibold text-slate-800 break-all">{{ $upload->nama_file_asli }}</div>
                        <div class="text-xs text-slate-500">
                            {{ strtoupper($upload->tipe) }}
                            @if(!is_null($upload->total_fitur)) • {{ $upload->total_fitur }} fitur @endif
                            • {{ $upload->created_at->diffForHumans() }}
                        </div>
                    </div>
                    <div>
                        @if($upload->status === 'completed')
                        <span class="inline-flex items-center rounded-full px-2 py-1 text-xs font-semibold bg-emerald-100 text-emerald-700">Completed</span>
                        @elseif($upload->status === 'failed')
                        <span class="inline-flex items-center rounded-full px-2 py-1 text-xs font-semibold bg-rose-100 text-rose-700" title="{{ $upload->pesan_error }}">Failed</span>
                        @elseif($upload->status === 'processing')
                        <span class="inline-flex items-center rounded-full px-2 py-1 text-xs font-semibold bg-amber-100 text-amber-700">Processing</span>
                        @else
                        <span class="inline-flex items-center rounded-full px-2 py-1 text-xs font-semibold bg-slate-100 text-slate-700">Pending</span>
                        @endif
                    </div>
                </div>
            </li>
            @empty
            <li class="text-sm text-slate-500">Belum ada riwayat upload.</li>
            @endforelse
        </ul>

        @if($uploads->total() > 6)
        <div class="text-right mt-5">
            <span class="text-xs text-slate-500">Menampilkan 6 upload terbaru.</span>
        </div>
        @endif
    </div>
</div>

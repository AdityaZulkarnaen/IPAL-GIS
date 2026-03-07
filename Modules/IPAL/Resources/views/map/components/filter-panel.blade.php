<div class="fixed top-[72px] left-4 z-[500] bg-white rounded-2xl shadow-lg p-4 w-[264px]">

    {{-- Header row --}}
    <div class="flex items-center justify-between mb-3.5">
        <div class="flex items-center gap-2">
            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="#475569" stroke-width="2.5"
                 stroke-linecap="round" stroke-linejoin="round">
                <polygon points="22 3 2 3 10 12.46 10 19 14 21 14 12.46 22 3"/>
            </svg>
            <span class="text-[13px] font-bold text-slate-800">Filter Jaringan</span>
        </div>
        <span class="inline-flex items-center gap-1 text-[11px] font-bold text-green-700 bg-green-100 rounded-full px-2 py-0.5">
            <span class="live-dot w-[7px] h-[7px] rounded-full bg-green-400 shrink-0"></span>LIVE
        </span>
    </div>

    {{-- Status Jaringan --}}
    <div class="mb-3.5">
        <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-2">Status Jaringan</p>
        <div class="flex gap-1.5 flex-wrap">
            <button class="status-btn btn-aman inline-flex items-center gap-1.5 text-[11px] font-semibold rounded-full px-2.5 py-1 border-2 bg-white cursor-pointer transition-opacity"
                    data-status="aman"><span class="dot w-2 h-2 rounded-full shrink-0"></span>Aman</button>
            <button class="status-btn btn-perbaikan inline-flex items-center gap-1.5 text-[11px] font-semibold rounded-full px-2.5 py-1 border-2 bg-white cursor-pointer transition-opacity"
                    data-status="perbaikan"><span class="dot w-2 h-2 rounded-full shrink-0"></span>Perbaikan</button>
            <button class="status-btn btn-masalah inline-flex items-center gap-1.5 text-[11px] font-semibold rounded-full px-2.5 py-1 border-2 bg-white cursor-pointer transition-opacity"
                    data-status="masalah"><span class="dot w-2 h-2 rounded-full shrink-0"></span>Masalah</button>
        </div>
    </div>

    {{-- Jenis Pipa --}}
    <div class="mb-3.5">
        <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-2">Jenis Pipa</p>
        <select id="filter-jenis" class="filter-select w-full text-[13px] text-gray-700 border border-slate-200 rounded-lg px-2.5 py-[7px] bg-white cursor-pointer">
            <option value="">Semua Jenis Pipa</option>
            <option value='PVC 12"'>PVC 12"</option>
            <option value='PVC 8"'>PVC 8"</option>
            <option value='Beton 15"'>Beton 15"</option>
            <option value='Besi 10"'>Besi 10"</option>
        </select>
    </div>

    {{-- Wilayah --}}
    <div>
        <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-2">Wilayah</p>
        <div class="relative">
            <svg class="wilayah-icon" width="14" height="14" viewBox="0 0 24 24" fill="none"
                 stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                <circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/>
            </svg>
            <input id="filter-wilayah" type="text" placeholder="Cari wilayah..."
                   class="filter-input w-full text-[13px] text-gray-700 border border-slate-200 rounded-lg py-[7px] pl-8 pr-2.5 bg-white placeholder:text-slate-400">
        </div>
    </div>
</div>

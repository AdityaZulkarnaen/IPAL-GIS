<div id="filter-panel" class="bg-white rounded-2xl shadow-lg p-4">

    {{-- Header row (clickable to toggle) --}}
    <div class="panel-header flex items-center justify-between" id="filter-toggle">
        <div class="flex items-center gap-2">
            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="#475569" stroke-width="2.5"
                 stroke-linecap="round" stroke-linejoin="round">
                <polygon points="22 3 2 3 10 12.46 10 19 14 21 14 12.46 22 3"/>
            </svg>
            <span class="text-[13px] font-bold text-slate-800">Filter Jaringan</span>
        </div>
        <div class="flex items-center gap-2">
            <svg id="filter-chevron" class="panel-chevron" width="14" height="14" viewBox="0 0 24 24" fill="none"
                 stroke="#94a3b8" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                <polyline points="6 9 12 15 18 9"/>
            </svg>
        </div>
    </div>

    {{-- Collapsible body --}}
    <div id="filter-body" class="collapsible-body">
        <div class="pt-3.5">

            {{-- Status Jaringan --}}
            <div class="mb-3.5">
                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-2">Status Jaringan</p>
                <div class="flex gap-1 flex-wrap">
                    <button class="status-btn btn-aman inline-flex items-center gap-1.5 text-[11px] font-semibold rounded-lg px-2.5 py-1 border-2 cursor-pointer transition-opacity"
                            data-status="aman"><span class="dot w-2 h-2 rounded-full shrink-0"></span>Aman</button>
                    <button class="status-btn btn-perbaikan inline-flex items-center gap-1.5 text-[11px] font-semibold rounded-lg px-2.5 py-1 border-2 cursor-pointer transition-opacity"
                            data-status="perbaikan"><span class="dot w-2 h-2 rounded-full shrink-0"></span>Perbaikan</button>
                    <button class="status-btn btn-masalah inline-flex items-center gap-1.5 text-[11px] font-semibold rounded-lg px-2.5 py-1 border-2 cursor-pointer transition-opacity"
                            data-status="masalah"><span class="dot w-2 h-2 rounded-full shrink-0"></span>Bermasalah</button>
                </div>
            </div>

            {{-- Jenis Pipa --}}
            <div class="mb-0">
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
            {{-- <div>
                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-2">Wilayah</p>
                <div class="relative">
                    <svg class="wilayah-icon" width="14" height="14" viewBox="0 0 24 24" fill="none"
                         stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/>
                    </svg>
                    <input id="filter-wilayah" type="text" placeholder="Cari wilayah..."
                           class="filter-input w-full text-[13px] text-gray-700 border border-slate-200 rounded-lg py-[7px] pl-8 pr-2.5 bg-white placeholder:text-slate-400">
                </div>
            </div> --}}

        </div>
    </div>
</div>

<script>
(function () {
    var body    = document.getElementById('filter-body');
    var chevron = document.getElementById('filter-chevron');
    var toggle  = document.getElementById('filter-toggle');
    var isOpen  = window.innerWidth >= 768;

    function setState(open) {
        isOpen = open;
        body.classList.toggle('is-open',   open);
        body.classList.toggle('is-closed', !open);
        chevron.classList.toggle('is-open',   open);
        chevron.classList.toggle('is-closed', !open);
    }

    setState(isOpen);

    toggle.addEventListener('click', function () { setState(!isOpen); });
})();
</script>

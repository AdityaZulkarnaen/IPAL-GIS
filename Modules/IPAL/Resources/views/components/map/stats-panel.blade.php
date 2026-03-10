<div id="stats-panel" class="bg-white rounded-2xl shadow-lg p-4">

    {{-- Header row (clickable to toggle) --}}
    <div class="panel-header flex items-center justify-between" id="stats-toggle">
        <div class="flex items-center gap-2">
            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="#475569" stroke-width="2.5"
                 stroke-linecap="round" stroke-linejoin="round">
                <line x1="18" y1="20" x2="18" y2="10"/>
                <line x1="12" y1="20" x2="12" y2="4"/>
                <line x1="6"  y1="20" x2="6"  y2="14"/>
            </svg>
            <span class="text-[13px] font-bold text-slate-800">Statistik Jaringan</span>
        </div>
        <svg id="stats-chevron" class="panel-chevron" width="14" height="14" viewBox="0 0 24 24" fill="none"
             stroke="#94a3b8" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
            <polyline points="6 9 12 15 18 9"/>
        </svg>
    </div>

    {{-- Collapsible body --}}
    <div id="stats-body" class="collapsible-body">
        <div class="pt-3.5 flex flex-col gap-3">

            {{-- Total Panjang Jaringan --}}
            <div>
                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1">Total Panjang Jaringan</p>
                <p class="text-[18px] font-bold text-slate-800 leading-none">1,234.5 <span class="text-[14px] font-bold text-slate-500">km</span></p>
            </div>

            {{-- Jumlah Manhole + Jumlah Pipa --}}
            <div class="flex items-between justify-between gap-0 border-t border-slate-200 pt-3">
                <div class="flex-1 pr-3">
                    <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1">Jumlah Manhole</p>
                    <p class="text-[18px] font-bold text-slate-800 leading-none">1,234</p>
                </div>
                <div class="flex-row">
                    <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1">Jumlah Pipa</p>
                    <p class="text-[18px] font-bold text-slate-800 leading-none">567</p>
                </div>
            </div>

            {{-- Status Manhole --}}
            <div>
                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-2 border-t border-slate-200 pt-3">Status Manhole</p>
                <div class="grid grid-cols-3 gap-1.5">
                    <div class="bg-green-50 border border-green-300 rounded-xl p-2 text-center">
                        <p class="text-[16px] font-semibold text-green-500 leading-none mb-0.5">308</p>
                        <p class="text-[9px] font-regular text-green-500 uppercase tracking-wider">Baik</p>
                    </div>
                    <div class="bg-yellow-50 border border-yellow-300 rounded-xl p-2 text-center">
                        <p class="text-[16px] font-semibold text-yellow-500 leading-none mb-0.5">308</p>
                        <p class="text-[9px] font-regular text-yellow-500 uppercase tracking-wider">Perbaikan</p>
                    </div>
                    <div class="bg-red-50 border border-red-300 rounded-xl p-2 text-center">
                        <p class="text-[16px] font-semibold text-red-500 leading-none mb-0.5">308</p>
                        <p class="text-[9px] font-regular text-red-500 uppercase tracking-wider">Bermasalah</p>
                    </div>
                </div>
            </div>

            {{-- Informasi Pipa --}}
            <div>
                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-2 border-t border-slate-200 pt-3">Informasi Pipa</p>
                <div class="grid grid-cols-3 gap-1.5">
                    <div class="bg-blue-50 border border-blue-300 rounded-xl p-2 text-center">
                        <p class="text-[16px] font-semibold text-blue-500 leading-none mb-0.5">189</p>
                        <p class="text-[9px] font-regular text-blue-500 uppercase tracking-wider">Glontor</p>
                    </div>
                    <div class="bg-blue-50 border border-blue-300 rounded-xl p-2 text-center">
                        <p class="text-[16px] font-semibold text-blue-500 leading-none mb-0.5">189</p>
                        <p class="text-[9px] font-regular text-blue-500 uppercase tracking-wider">Induk</p>
                    </div>
                    <div class="bg-blue-50 border border-blue-300 rounded-xl p-2 text-center">
                        <p class="text-[16px] font-semibold text-blue-500 leading-none mb-0.5">189</p>
                        <p class="text-[9px] font-regular text-blue-500 uppercase tracking-wider">Lateral</p>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>

<script>
(function () {
    var body    = document.getElementById('stats-body');
    var chevron = document.getElementById('stats-chevron');
    var toggle  = document.getElementById('stats-toggle');
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

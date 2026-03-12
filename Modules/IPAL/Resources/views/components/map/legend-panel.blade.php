<div id="legend-panel" class="fixed top-[72px] right-4 z-[500] bg-white rounded-2xl shadow-lg p-4 w-40 max-w-[calc(100vw-2rem)]">

    {{-- Header row (clickable to toggle) --}}
    <div class="panel-header flex items-center justify-between gap-2" id="legend-toggle">
        <p class="text-[11px] font-bold text-slate-500 uppercase tracking-wider">Legenda Peta</p>
        <svg id="legend-chevron" class="panel-chevron" width="14" height="14" viewBox="0 0 24 24" fill="none"
             stroke="#94a3b8" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
            <polyline points="6 9 12 15 18 9"/>
        </svg>
    </div>

    {{-- Collapsible body --}}
    <div id="legend-body" class="collapsible-body">
        <div class="pt-2.5">
            <div class="flex items-center gap-2 text-[12px] text-slate-500 mb-2">
                <span class="w-7 h-1 rounded-sm shrink-0 bg-green-400"></span>Beroperasi Baik
            </div>
            <div class="flex items-center gap-2 text-[12px] text-slate-500 mb-2">
                <span class="w-7 h-1 rounded-sm shrink-0 bg-yellow-400"></span>Pemeliharaan
            </div>
            <div class="flex items-center gap-2 text-[12px] text-slate-500 mb-2">
                <span class="w-7 h-1 rounded-sm shrink-0 bg-red-400"></span>Bermasalah
            </div>
            <div class="flex items-center gap-2 text-[12px] text-slate-500">
                <span class="w-3 h-3 rounded-full bg-cyan-400 border-2 border-white shadow-sm shrink-0 ml-2 mr-2"></span>Manhole
            </div>
        </div>
    </div>
</div>

<script>
(function () {
    var body    = document.getElementById('legend-body');
    var chevron = document.getElementById('legend-chevron');
    var toggle  = document.getElementById('legend-toggle');
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

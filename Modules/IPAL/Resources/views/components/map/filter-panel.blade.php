<div id="filter-panel" class="bg-white rounded-2xl shadow-lg p-4">

    {{-- Header row (clickable to toggle) --}}
    <div class="panel-header flex items-center justify-between" id="filter-toggle" title="Filter Jaringan">
        <div class="flex items-center gap-2">
            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="#475569" stroke-width="2.5"
                 stroke-linecap="round" stroke-linejoin="round">
                <polygon points="22 3 2 3 10 12.46 10 19 14 21 14 12.46 22 3"/>
            </svg>
            <span class="panel-label text-[13px] font-bold text-slate-800">Filter Jaringan</span>
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

            {{-- Fungsi Pipa --}}
            <div class="mb-3.5">
                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-2">Fungsi Pipa</p>
                <select id="filter-jenis" class="filter-select w-full text-[13px] text-gray-700 border border-slate-200 rounded-lg px-2.5 py-[7px] bg-white cursor-pointer">
                    <option value="">Semua Fungsi</option>
                </select>
            </div>


        </div>
    </div>
</div>

<script>
(function () {
    var body    = document.getElementById('filter-body');
    var chevron = document.getElementById('filter-chevron');
    var toggle  = document.getElementById('filter-toggle');
    var sidebar = document.getElementById('left-sidebar');
    var isMobile = window.innerWidth < 768;
    var isOpen  = isMobile ? false : (sidebar && sidebar.classList.contains('is-compact') ? false : true);

    function syncSidebar() {
        if (!sidebar || !sidebar.classList.contains('is-compact')) return;
        var anyOpen = !!document.querySelector('#left-sidebar .collapsible-body.is-open');
        sidebar.classList.toggle('has-open-panel', anyOpen);
    }

    function setState(open) {
        isOpen = open;
        body.classList.toggle('is-open',   open);
        body.classList.toggle('is-closed', !open);
        chevron.classList.toggle('is-open',   open);
        chevron.classList.toggle('is-closed', !open);
        syncSidebar();
    }

    setState(isOpen);

    // Close this panel when another panel opens on mobile
    document.addEventListener('map:panelOpen', function (e) {
        if (window.innerWidth < 768 && e.detail.id !== 'filter' && isOpen) {
            setState(false);
        }
    });

    toggle.addEventListener('click', function () {
        var opening = !isOpen;
        setState(opening);
        if (opening && window.innerWidth < 768) {
            document.dispatchEvent(new CustomEvent('map:panelOpen', { detail: { id: 'filter' } }));
        }
    });
})();
</script>

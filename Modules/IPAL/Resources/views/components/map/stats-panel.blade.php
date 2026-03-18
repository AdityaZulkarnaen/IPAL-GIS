<div id="stats-panel" class="bg-white rounded-2xl shadow-lg p-4 z-999">

    {{-- Header row (clickable to toggle) --}}
    <div class="panel-header flex items-center justify-between" id="stats-toggle" title="Statistik Jaringan">
        <div class="flex items-center gap-2">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path opacity="0.3" d="M16.3622 1.28003H7.63784C4.10993 1.28003 1.25 4.13996 1.25 7.66787V16.3279C1.25 19.8558 4.10993 22.7157 7.63784 22.7157H16.3622C19.8901 22.7157 22.75 19.8558 22.75 16.3279V7.66787C22.75 4.13996 19.8901 1.28003 16.3622 1.28003Z" fill="#53EAFD"/>
                <path d="M9.42725 11.7734C9.64041 11.7734 9.84486 11.8581 9.99561 12.0088C10.1464 12.1595 10.231 12.364 10.231 12.5771V16.4678C10.2309 16.5732 10.2108 16.6779 10.1704 16.7754C10.13 16.8728 10.0702 16.9615 9.99561 17.0361C9.92103 17.1106 9.83226 17.1696 9.73486 17.21C9.6374 17.2503 9.53273 17.2715 9.42725 17.2715C9.21418 17.2714 9.00955 17.1868 8.85889 17.0361C8.70823 16.8855 8.62361 16.6808 8.62354 16.4678V12.5771C8.62354 12.3641 8.70832 12.1595 8.85889 12.0088C9.00955 11.8581 9.21418 11.7735 9.42725 11.7734ZM15.4517 6.08203C15.6647 6.08213 15.8694 6.16672 16.02 6.31738C16.1707 6.46805 16.2553 6.67268 16.2554 6.88574V16.4678C16.2568 16.5737 16.2367 16.6792 16.1968 16.7773C16.1569 16.8753 16.0977 16.9642 16.0229 17.0391C15.948 17.114 15.8584 17.174 15.7603 17.2139C15.6623 17.2536 15.5573 17.2729 15.4517 17.2715C15.2385 17.2715 15.034 17.1868 14.8833 17.0361C14.7326 16.8854 14.6479 16.681 14.6479 16.4678V6.88574C14.648 6.67267 14.7326 6.46805 14.8833 6.31738C15.034 6.16685 15.2386 6.08203 15.4517 6.08203Z" fill="#00D3F3"/>
            </svg>
            <span class="panel-label text-[13px] font-bold text-slate-800">Statistik Jaringan</span>
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
                <p class="text-[18px] font-bold text-slate-800 leading-none"><span id="stat-total-panjang">—</span> <span class="text-[14px] font-bold text-slate-500">km</span></p>
            </div>

            {{-- Jumlah Manhole + Jumlah Pipa --}}
            <div class="flex items-between justify-between gap-0 border-t border-slate-200 pt-3">
                <div class="flex-1 pr-3">
                    <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1">Jumlah Manhole</p>
                    <p id="stat-total-manhole" class="text-[18px] font-bold text-slate-800 leading-none">—</p>
                </div>
                <div class="flex-row">
                    <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1">Jumlah Pipa</p>
                    <p id="stat-total-pipa" class="text-[18px] font-bold text-slate-800 leading-none">—</p>
                </div>
            </div>

            {{-- Status Manhole --}}
            <div>
                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-2 border-t border-slate-200 pt-3">Status Manhole</p>
                <div class="grid grid-cols-3 gap-1.5">
                    <div class="bg-green-50 border border-green-300 rounded-xl p-2 text-center">
                        <p id="stat-status-baik" class="text-[16px] font-semibold text-green-500 leading-none mb-0.5">—</p>
                        <p class="text-[9px] font-regular text-green-500 uppercase tracking-wider">Baik</p>
                    </div>
                    <div class="bg-yellow-50 border border-yellow-300 rounded-xl p-2 text-center">
                        <p id="stat-status-perbaikan" class="text-[16px] font-semibold text-yellow-500 leading-none mb-0.5">—</p>
                        <p class="text-[9px] font-regular text-yellow-500 uppercase tracking-wider">Perbaikan</p>
                    </div>
                    <div class="bg-red-50 border border-red-300 rounded-xl p-2 text-center">
                        <p id="stat-status-rusak" class="text-[16px] font-semibold text-red-500 leading-none mb-0.5">—</p>
                        <p class="text-[9px] font-regular text-red-500 uppercase tracking-wider">Rusak</p>
                    </div>
                </div>
            </div>

            {{-- Informasi Pipa --}}
            <div>
                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-2 border-t border-slate-200 pt-3">Informasi Pipa</p>
                <div class="grid grid-cols-3 gap-1.5">
                    <div class="bg-blue-50 border border-blue-300 rounded-xl p-2 text-center">
                        <p id="stat-fungsi-glontor" class="text-[16px] font-semibold text-blue-500 leading-none mb-0.5">—</p>
                        <p class="text-[9px] font-regular text-blue-500 uppercase tracking-wider">Glontor</p>
                    </div>
                    <div class="bg-blue-50 border border-blue-300 rounded-xl p-2 text-center">
                        <p id="stat-fungsi-induk" class="text-[16px] font-semibold text-blue-500 leading-none mb-0.5">—</p>
                        <p class="text-[9px] font-regular text-blue-500 uppercase tracking-wider">Induk</p>
                    </div>
                    <div class="bg-blue-50 border border-blue-300 rounded-xl p-2 text-center">
                        <p id="stat-fungsi-lateral" class="text-[16px] font-semibold text-blue-500 leading-none mb-0.5">—</p>
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
        if (window.innerWidth < 768 && e.detail.id !== 'stats' && isOpen) {
            setState(false);
        }
    });

    toggle.addEventListener('click', function () {
        var opening = !isOpen;
        setState(opening);
        if (opening && window.innerWidth < 768) {
            document.dispatchEvent(new CustomEvent('map:panelOpen', { detail: { id: 'stats' } }));
        }
    });
})();
</script>

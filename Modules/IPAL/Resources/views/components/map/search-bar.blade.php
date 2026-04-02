<div id="search-bar-container" class="fixed bottom-14 left-1/2 -translate-x-1/2 z-[500] w-[520px] max-w-[calc(100vw-2rem)]">
    {{-- Suggestions dropdown (rendered above the bar) --}}
    <div id="search-suggestions"
         style="display:none;position:absolute;bottom:calc(100% + 8px);left:0;right:0;background:#fff;border-radius:14px;box-shadow:0 8px 28px rgba(0,0,0,.13);overflow-y:auto;max-height:280px;font-family:'Montserrat',sans-serif;">
    </div>

    {{-- Search bar card --}}
    <div class="bg-white rounded-2xl shadow-lg px-4 py-1 flex items-center gap-2.5">
        <svg class="text-slate-400 shrink-0" width="16" height="16" viewBox="0 0 24 24" fill="none"
             stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
            <circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/>
        </svg>
         <input id="search-input" type="text" placeholder="Cari ID Pipa, Jalan, atau Koordinat..."
             class="flex-1 w-2/3 text-[13px] text-gray-700 border-none outline-none ring-0 focus:outline-none focus:ring-0 focus-visible:outline-none focus-visible:ring-0 bg-transparent placeholder:text-slate-400"
               autocomplete="off">
        <button id="search-btn"
            class="hidden md:block text-[13px] font-semibold text-white bg-[#13C8EC] border-none rounded-lg px-4 py-1 cursor-pointer transition-colors shrink-0">
            Cari Data
        </button>
    </div>
</div>

<style>
    #search-input:focus,
    #search-input:focus-visible {
        outline: none !important;
        box-shadow: none !important;
    }
</style>

<nav class="fixed top-0 left-0 right-0 z-[1000] bg-white shadow-sm">

    {{-- Main bar --}}
    <div class="h-14 flex items-center px-4 gap-3">

        {{-- Logo + title --}}
        <div class="flex items-center gap-2.5 flex-1 min-w-0">
            <img src="{{ asset('logo/216801987556425cc63bbea0.png') }}" alt="Logo BPAL PJK DIY" class="h-9 w-9 object-contain shrink-0">
            <span class="text-[15px] font-bold text-[#0F172A] leading-tight truncate hidden sm:block">
                Balai Pengelolaan Air Limbah dan Pengembangan Jasa Konstruksi
            </span>
            <span class="text-[14px] font-bold text-[#0F172A] leading-tight sm:hidden">
                BPAL PJK DIY
            </span>
        </div>

        {{-- Desktop nav links --}}
        <div class="hidden md:flex items-center gap-6 shrink-0">
            <a href="{{ route('ipal.map.index') }}"
               class="text-sm font-semibold pb-0.5 no-underline transition-colors
                      {{ request()->routeIs('ipal.map.*') ? 'text-red-600 border-b-2 border-red-600' : 'text-slate-500 hover:text-slate-800' }}">
                Beranda
            </a>
            <a href="{{ route('ipal.lapor-masalah.index') }}"
               class="text-sm font-medium no-underline transition-colors
                      {{ request()->routeIs('ipal.lapor-masalah.*') ? 'text-red-600 font-semibold border-b-2 border-red-600 pb-0.5' : 'text-slate-500 hover:text-slate-800' }}">
                Lapor Masalah
            </a>
            <a href="{{ route('login') }}" class="bg-blue-600 text-white font-semibold text-[13px] px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors no-underline">Admin Login</a>
        </div>

        {{-- Hamburger button (mobile only) --}}
        <button id="hamburger-btn"
                class="flex md:hidden items-center justify-center w-9 h-9 rounded-lg text-slate-600 hover:bg-slate-100 transition-colors shrink-0"
                aria-label="Buka menu" aria-expanded="false">
            <svg id="icon-menu" xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24"
                 fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                <line x1="3" y1="6" x2="21" y2="6"/>
                <line x1="3" y1="12" x2="21" y2="12"/>
                <line x1="3" y1="18" x2="21" y2="18"/>
            </svg>
            <svg id="icon-close" class="hidden" xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24"
                 fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                <line x1="18" y1="6" x2="6" y2="18"/>
                <line x1="6" y1="6" x2="18" y2="18"/>
            </svg>
        </button>
    </div>

    {{-- Mobile dropdown menu (hidden by default) --}}
    <div id="mobile-menu" class="hidden flex-col border-t border-slate-100 px-5 py-3 gap-3">
        <a href="{{ route('ipal.map.index') }}"
           class="text-sm no-underline py-1 {{ request()->routeIs('ipal.map.*') ? 'text-red-600 font-semibold' : 'text-slate-600 font-medium' }}">
            Beranda
        </a>
        <a href="{{ route('ipal.lapor-masalah.index') }}"
           class="text-sm no-underline py-1 {{ request()->routeIs('ipal.lapor-masalah.*') ? 'text-red-600 font-semibold' : 'text-slate-600 font-medium' }}">
            Lapor Masalah
        </a>
        <a href="{{ route('login') }}"
           class="block text-center bg-blue-600 text-white font-semibold text-[13px] px-4 py-2.5 rounded-lg hover:bg-blue-700 transition-colors no-underline mt-1">Admin Login</a>
    </div>
</nav>

<script>
(function () {
    var btn    = document.getElementById('hamburger-btn');
    var menu   = document.getElementById('mobile-menu');
    var iMenu  = document.getElementById('icon-menu');
    var iClose = document.getElementById('icon-close');

    btn.addEventListener('click', function () {
        var opening = menu.classList.contains('hidden');
        menu.classList.toggle('hidden', !opening);
        menu.classList.toggle('flex',    opening);
        iMenu.classList.toggle('hidden',  opening);
        iClose.classList.toggle('hidden', !opening);
        btn.setAttribute('aria-expanded', opening ? 'true' : 'false');
    });
})();
</script>

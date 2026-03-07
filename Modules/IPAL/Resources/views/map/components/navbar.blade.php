<nav class="fixed top-0 left-0 right-0 z-[1000] h-14 bg-white shadow-sm flex items-center px-5 gap-4">
    <div class="flex items-center gap-2.5 flex-1 min-w-0">
        <img src="{{ asset('images/PJK.svg') }}" alt="Logo BPAL PJK DIY" class="h-9 w-9 object-contain shrink-0">
        <span class="text-[16px] font-bold text-[#0F172A] leading-tight">
            Balai Pengelolaan Air Limbah dan Pengembangan Jasa Konstruksi
        </span>
    </div>
    <div class="flex items-center gap-6 shrink-0">
        <a href="/" class="text-red-600 font-semibold text-sm border-b-2 border-red-600 pb-0.5 no-underline">Beranda</a>
        <a href="#" class="text-slate-500 font-medium text-sm hover:text-slate-800 transition-colors no-underline">Lapor Masalah</a>
        <a href="{{ route('login') }}" class="bg-blue-600 text-white font-semibold text-[13px] px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors no-underline">Admin Login</a>
    </div>
</nav>

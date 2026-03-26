<div class="bg-white border border-slate-200 rounded-xl shadow-panel mb-8" id="pipe-table-card">
    <div class="px-6 pt-6 pb-2 flex flex-row items-start justify-between">
        <h3 class="text-lg font-bold text-slate-800">Data Jalur Pipa</h3>
        <div class="flex flex-wrap items-center gap-2.5 mb-5">
            <input id="pipe-search" type="text" class="h-10 w-full md:w-[250px] rounded-lg border border-slate-300 bg-white px-3 text-sm text-slate-700 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-blue-200" placeholder="Cari ID pipa, jalur, fungsi">
            <select id="pipe-status" class="h-10 w-full md:w-[175px] rounded-lg border border-slate-300 bg-white px-3 text-sm text-slate-700 focus:outline-none focus:ring-2 focus:ring-blue-200">
                <option value="">Status Semua</option>
            </select>
            <select id="pipe-fungsi" class="h-10 w-full md:w-[225px] rounded-lg border border-slate-300 bg-white px-3 text-sm text-slate-700 focus:outline-none focus:ring-2 focus:ring-blue-200">
                <option value="">Fungsi Semua</option>
            </select>
            <button id="pipe-reset" type="button" class="h-10 rounded-lg border border-blue-200 bg-blue-50 px-4 text-sm font-semibold text-blue-700 hover:bg-blue-100">Reset Filter</button>
        </div>
    </div>
    <div class="px-6 pb-6 pt-0">
        

        <div class="overflow-x-auto rounded-lg border border-slate-200">
            <table class="min-w-full border-collapse bg-white">
                <thead class="bg-slate-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">ID Jalur Pipa</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">Status</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">Fungsi</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">Diameter</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">Panjang</th>
                    </tr>
                </thead>
                <tbody id="pipe-tbody">
                    <tr>
                        <td colspan="5" class="py-8 text-center text-sm text-slate-500">Memuat data pipa...</td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div class="mt-4 flex flex-wrap items-center justify-between gap-3">
            <div class="flex flex-row items-center gap-2">
                <div>Menampilkan</div>
                <select id="pipe-per-page" class="h-10 w-full md:w-[125px] rounded-lg border border-slate-300 bg-white px-3 text-sm text-slate-700 md:ml-auto focus:outline-none focus:ring-2 focus:ring-blue-200">
                    <option value="5">5</option>
                    <option value="10" selected>10</option>
                    <option value="15">15</option>
                    <option value="25">25</option>
                </select>
                <div>per Halaman</div>
            </div>
            <div id="pipe-pagination-info" class="text-xs text-slate-500"></div>
            <div id="pipe-pagination" class="flex items-center gap-2"></div>
        </div>
    </div>
</div>

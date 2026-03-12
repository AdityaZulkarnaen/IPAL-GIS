<x-layouts.map title="IPAL GIS – Balai Pengelolaan Air Limbah">

    <x-map.navbar />

    <x-map.canvas />

    {{-- Left sidebar: filter + stats stacked --}}
    <div id="left-sidebar" class="fixed top-[72px] left-4 z-[500] flex flex-col gap-3 w-auto md:w-72 max-w-[calc(100vw-2rem)] max-h-[calc(100vh-88px)] overflow-y-auto scrollbar-hide is-compact">
        <x-map.filter-panel />
        <x-map.stats-panel />
    </div>

    <x-map.legend-panel />

    <x-map.search-bar />

    <x-map.footer />

    <x-map.script />

</x-layouts.map>

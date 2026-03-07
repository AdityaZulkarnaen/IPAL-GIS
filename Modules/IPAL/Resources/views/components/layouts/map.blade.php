@props(['title' => 'IPAL GIS'])
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title }}</title>

    {{-- Leaflet CSS --}}
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />

    {{-- Tailwind CDN (map page is standalone, no build step needed) --}}
    <script src="https://cdn.tailwindcss.com"></script>

    <style>
        html, body { margin: 0; padding: 0; height: 100%; overflow: hidden; }

        /* Filter button active/inactive states */
        .btn-aman    { border-color: #22c55e; color: #15803d; }
        .btn-perbaikan { border-color: #eab308; color: #a16207; }
        .btn-masalah { border-color: #ef4444; color: #dc2626; }
        .btn-aman .dot    { background: #22c55e; }
        .btn-perbaikan .dot { background: #eab308; }
        .btn-masalah .dot { background: #ef4444; }
        .status-btn.inactive { opacity: 0.35; }

        /* Filter/select focus styles */
        .filter-select:focus,
        .filter-input:focus { outline: 2px solid #3b82f6; outline-offset: -1px; }

        /* Wilayah search icon positioning */
        .wilayah-icon { position: absolute; left: 9px; top: 50%; transform: translateY(-50%); color: #94a3b8; pointer-events: none; }

        /* Live dot pulse */
        .live-dot { animation: pulse 2s infinite; }
        @keyframes pulse {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.4; }
        }

        /* Leaflet popup overrides */
        .leaflet-popup-content-wrapper { padding: 0; border-radius: 12px; overflow: hidden; }
        .leaflet-popup-content { margin: 0; }
    </style>
</head>
<body>
    {{ $slot }}
</body>
</html>

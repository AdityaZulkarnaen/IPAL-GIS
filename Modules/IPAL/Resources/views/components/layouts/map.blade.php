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

    {{-- Tailwind + custom map styles (compiled via Vite) --}}
    @vite(['Modules/IPAL/Resources/assets/css/map.css'])
</head>
<body>
    {{ $slot }}
</body>
</html>

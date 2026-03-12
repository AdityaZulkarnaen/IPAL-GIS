{{-- ══════════════════════════════════════════════════════════════════
     LEFT: Mini-map  (desktop ~55 %, mobile full-width 40vh)
══════════════════════════════════════════════════════════════════════ --}}
<div class="lapor-map-col">

    {{-- Panduan lokasi overlay --}}
    <div id="panduan-overlay"
         style="position:absolute;top:12px;left:12px;z-index:900;
                background:rgba(255,255,255,.95);border-radius:10px;
                padding:10px 13px;font-family:'Montserrat',sans-serif;
                font-size:12px;max-width:220px;box-shadow:0 2px 10px rgba(0,0,0,.15);">
        <div style="font-weight:700;color:#0F172A;margin-bottom:4px;font-size:13px;"
             id="overlay-title">Memuat…</div>
        <div style="color:#64748b;line-height:1.4;" id="overlay-subtitle"></div>
    </div>

    <div id="lapor-map" style="width:100%;height:100%;"></div>
</div>

<x-layouts.map title="Lapor Masalah – IPAL GIS">

{{-- Override map.css "html, body { overflow: hidden }" so the page can scroll on mobile --}}
<style>
@media (max-width: 767px) {
    html, body { height: auto !important; overflow: visible !important; overflow-x: hidden !important; }
}
</style>

    <x-map.navbar />

    {{-- ─── Page wrapper (full viewport minus navbar) ──────────────────────── --}}
    <div class="lapor-page-wrap">

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

        {{-- ══════════════════════════════════════════════════════════════════
             RIGHT: Form panel  (desktop ~45 %, mobile full)
        ══════════════════════════════════════════════════════════════════════ --}}
        <div class="lapor-form-col">

            {{-- Header --}}
            <div style="text-align:center;margin-bottom:24px;">
                <h1 style="font-size:22px;font-weight:800;color:#0F172A;margin:0 0 6px;">
                    Lapor Masalah Jaringan IPAL
                </h1>
                <p style="font-size:13px;color:#64748b;margin:0;line-height:1.5;">
                    Laporkan masalah operasional atau kerusakan infrastruktur air limbah (IPAL) di wilayah Anda.<br>
                    Kami akan segera menindaklanjuti laporan Anda.
                </p>
            </div>

            {{-- ── Success banner (hidden by default) ── --}}
            <div id="success-banner"
                 style="display:none;background:#f0fdf4;border:1px solid #bbf7d0;border-radius:12px;
                        padding:14px 16px;margin-bottom:18px;display:none;align-items:center;gap:10px;">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24"
                     fill="none" stroke="#22c55e" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M20 6 9 17l-5-5"/>
                </svg>
                <div>
                    <div style="font-weight:700;color:#166534;font-size:13px;">Laporan berhasil terkirim</div>
                    <div style="font-size:12px;color:#15803d;">Nomor tiket: <span id="nomor-tiket"
                         style="font-family:monospace;font-weight:700;"></span></div>
                </div>
            </div>

            {{-- ── Form card ── --}}
            <form id="lapor-form" autocomplete="off">

                {{-- Row 1: ID IPAL + Titik Lokasi --}}
                <div style="display:flex;flex-wrap:wrap;gap:14px;margin-bottom:14px;">
                    <div style="flex:1 1 160px;min-width:0;">
                        <label class="lapor-label">ID IPAL</label>
                        <div class="lapor-input-wrap lapor-readonly-wrap">
                            <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24"
                                 fill="none" stroke="#94a3b8" stroke-width="2" stroke-linecap="round"
                                 stroke-linejoin="round" style="flex-shrink:0;">
                                <circle cx="12" cy="12" r="3"/><circle cx="12" cy="12" r="10"/>
                            </svg>
                            <input id="field-id-ipal" type="text" readonly
                                   value="—"
                                   class="lapor-input lapor-readonly" />
                        </div>
                    </div>
                    <div style="flex:1 1 160px;min-width:0;">
                        <label class="lapor-label">Titik Lokasi</label>
                        <div class="lapor-input-wrap lapor-readonly-wrap">
                            <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24"
                                 fill="none" stroke="#94a3b8" stroke-width="2" stroke-linecap="round"
                                 stroke-linejoin="round" style="flex-shrink:0;">
                                <path d="M20 10c0 6-8 13-8 13s-8-7-8-13a8 8 0 0 1 16 0Z"/>
                                <circle cx="12" cy="10" r="3"/>
                            </svg>
                            <input id="field-koordinat" type="text" readonly
                                   value="—"
                                   class="lapor-input lapor-readonly" />
                        </div>
                        <p style="font-size:10.5px;color:#94a3b8;margin:4px 0 0;">
                            Titik akan terisi otomatis setelah Anda memilih lokasi
                        </p>
                    </div>
                </div>

                {{-- Deskripsi Laporan --}}
                <div style="margin-bottom:14px;">
                    <label class="lapor-label" for="field-deskripsi">Deskripsi Laporan</label>
                    <textarea id="field-deskripsi" rows="5" maxlength="5000"
                              placeholder="Jelaskan detail masalah yang Anda temukan…"
                              class="lapor-textarea"></textarea>
                    <div id="deskripsi-error" style="display:none;font-size:12px;color:#dc2626;margin-top:4px;">
                        <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" viewBox="0 0 24 24"
                             fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"
                             stroke-linejoin="round" style="vertical-align:middle;margin-right:3px;">
                            <circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/>
                            <line x1="12" y1="16" x2="12.01" y2="16"/>
                        </svg>
                        Please fill in this field
                    </div>
                </div>

                {{-- Upload Foto --}}
                <div style="margin-bottom:18px;">
                    <label class="lapor-label">Upload Foto Dokumentasi</label>
                    <div id="foto-dropzone" class="lapor-dropzone"
                         onclick="document.getElementById('foto-input').click()">
                        <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" viewBox="0 0 24 24"
                             fill="none" stroke="#94a3b8" stroke-width="1.5" stroke-linecap="round"
                             stroke-linejoin="round" style="margin-bottom:6px;">
                            <rect x="3" y="3" width="18" height="18" rx="2"/>
                            <circle cx="9" cy="9" r="2"/><path d="m21 15-5-5L5 21"/>
                        </svg>
                        <p style="margin:0;font-size:13px;color:#94a3b8;">Klik atau drag untuk unggah foto</p>
                    </div>
                    <input id="foto-input" type="file" accept="image/jpeg,image/jpg,image/png,image/webp"
                           multiple style="display:none;" onchange="handleFotoChange(this)" />
                    <div id="foto-preview" style="display:flex;flex-wrap:wrap;gap:8px;margin-top:10px;"></div>
                </div>

                {{-- Global error --}}
                <div id="global-error"
                     style="display:none;background:#fef2f2;border:1px solid #fecaca;border-radius:8px;
                            padding:10px 14px;font-size:13px;color:#dc2626;margin-bottom:14px;"></div>

                {{-- Submit --}}
                <button type="submit" id="submit-btn" class="lapor-submit-btn">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24"
                         fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"
                         stroke-linejoin="round" id="submit-icon">
                        <path d="M22 2 11 13"/><path d="M22 2 15 22 11 13 2 9l20-7z"/>
                    </svg>
                    <svg id="submit-spinner" xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                         viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"
                         stroke-linecap="round" stroke-linejoin="round"
                         style="display:none;animation:spin .8s linear infinite;">
                        <path d="M21 12a9 9 0 1 1-6.219-8.56"/>
                    </svg>
                    <span id="submit-label">Kirim Laporan</span>
                </button>

            </form>
        </div>
    </div>

{{-- ─── Styles ─────────────────────────────────────────────────────────── --}}
<style>
@import url('https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700;800&display=swap');

*, *::before, *::after { box-sizing: border-box; }

body { font-family: 'Montserrat', sans-serif; background: #f8fafc; margin: 0; }

/* ── Page layout ── */
.lapor-page-wrap {
    display: flex;
    height: calc(100vh - 56px);
    margin-top: 56px;
    overflow: hidden;
}

/* ── Map column ── */
.lapor-map-col {
    position: relative;
    flex: 0 0 55%;
    height: 100%;
    overflow: hidden;
}

/* ── Form column ── */
.lapor-form-col {
    flex: 0 0 45%;
    height: 100%;
    min-height: 0;          /* ← critical: lets overflow-y:auto work inside a flex child */
    overflow-y: auto;
    -webkit-overflow-scrolling: touch;
    padding: 32px 36px;
    background: #fff;
    border-left: 1px solid #e2e8f0;
}

/* ── Form typography helpers ── */
.lapor-label {
    display: block;
    font-size: 13px;
    font-weight: 600;
    color: #374151;
    margin-bottom: 6px;
}

/* ── Read-only input row ── */
.lapor-input-wrap {
    display: flex;
    align-items: center;
    gap: 8px;
    border: 1px solid #e2e8f0;
    border-radius: 8px;
    padding: 9px 12px;
    background: #f8fafc;
}
.lapor-readonly-wrap { cursor: default; }
.lapor-input {
    flex: 1;
    border: none;
    background: transparent;
    font-size: 13px;
    color: #475569;
    font-family: inherit;
    outline: none;
    min-width: 0;
}
.lapor-readonly { color: #475569; }

/* ── Textarea ── */
.lapor-textarea {
    width: 100%;
    border: 1px solid #e2e8f0;
    border-radius: 8px;
    padding: 10px 13px;
    font-size: 13px;
    font-family: inherit;
    color: #1e293b;
    resize: none;
    outline: none;
    transition: border-color .15s;
    line-height: 1.55;
}
.lapor-textarea:focus { border-color: #2563eb; box-shadow: 0 0 0 3px rgba(37,99,235,.12); }
.lapor-textarea.error { border-color: #dc2626; }

/* ── Drop zone ── */
.lapor-dropzone {
    border: 2px dashed #cbd5e1;
    border-radius: 10px;
    padding: 22px 16px;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    transition: border-color .15s, background .15s;
}
.lapor-dropzone:hover { border-color: #3b82f6; background: #f0f9ff; }

/* ── Submit button ── */
.lapor-submit-btn {
    width: 100%;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    background: #2563eb;
    color: #fff;
    border: none;
    border-radius: 10px;
    padding: 13px 20px;
    font-size: 14px;
    font-weight: 700;
    font-family: inherit;
    cursor: pointer;
    transition: background .15s, transform .1s;
}
.lapor-submit-btn:hover:not(:disabled) { background: #1d4ed8; }
.lapor-submit-btn:active:not(:disabled) { transform: scale(.99); }
.lapor-submit-btn:disabled { opacity: .55; cursor: not-allowed; }

@keyframes spin { to { transform: rotate(360deg); } }

/* ── Tablet layout (768 – 1023 px): still side-by-side but tighter padding ── */
@media (min-width: 768px) and (max-width: 1023px) {
    .lapor-map-col  { flex: 0 0 50%; }
    .lapor-form-col { flex: 0 0 50%; padding: 24px 22px; }
}

/* ── Mobile layout ── */
@media (max-width: 767px) {
    .lapor-page-wrap { flex-direction: column; height: auto; overflow: visible; }
    .lapor-map-col   { flex: none; height: 42vh; min-height: 260px; }
    .lapor-form-col  { flex: none; width: 100%; height: auto; min-height: 0; border-left: none; border-top: 1px solid #e2e8f0; padding: 24px 18px 40px; overflow-y: visible; }
}
</style>

{{-- ─── Leaflet CSS ─────────────────────────────────────────────────────── --}}
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css">

{{-- ─── Scripts ────────────────────────────────────────────────────────── --}}
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
(function () {
    'use strict';

    /* ── Constants ─────────────────────────────────────────── */
    const BASE_URL   = '{{ url('/') }}';
    const API_BASE   = BASE_URL + '/api/ipal';
    const MAX_FOTO   = {{ config('ipal.aduan_max_foto', 5) }};

    const COLOR      = { aman:'#22c55e', perbaikan:'#eab308', masalah:'#ef4444' };
    const BADGE_BG   = { aman:'#22C55E1A', perbaikan:'#fef3c7', masalah:'#fee2e2' };
    const BADGE_TEXT = { aman:'#22c55e',   perbaikan:'#a16207', masalah:'#dc2626' };
    const LABEL      = { aman:'AMAN',      perbaikan:'PERBAIKAN', masalah:'MASALAH' };

    /* ── Parse URL params ───────────────────────────────────── */
    const params  = new URLSearchParams(window.location.search);
    const aType   = params.get('type');   // 'pipa' | 'manhole'
    const aId     = params.get('id');     // numeric DB id
    const aKode   = params.get('kode');   // human-readable code (pre-fill while loading)
    const aCoord  = params.get('coord');  // "lat,lng" string (pre-fill while loading)
    const aWilayah = params.get('wilayah') || '';

    /* ── Pre-fill fields immediately from URL params ────────── */
    if (aKode)  document.getElementById('field-id-ipal').value   = aKode;
    if (aCoord) document.getElementById('field-koordinat').value = aCoord;

    /* ── State ──────────────────────────────────────────────── */
    let pipaId    = null;
    let manholeId = null;
    let assetData = null;
    let selectedFiles = [];

    /* ── Init mini-map ──────────────────────────────────────── */
    const miniMap = L.map('lapor-map', {
        zoomControl: false,
        attributionControl: true,
    });

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a>',
        maxZoom: 19,
    }).addTo(miniMap);

    L.control.zoom({ position: 'bottomright' }).addTo(miniMap);

    /* ── Geometry helpers (same as main map script) ─────────── */
    function buildCoordSets(geometry) {
        if (!geometry) return [];
        if (geometry.type === 'LineString') {
            return [geometry.coordinates.map(c => [c[1], c[0]])];
        }
        if (geometry.type === 'MultiLineString') {
            return geometry.coordinates.map(ring => ring.map(c => [c[1], c[0]]));
        }
        return [];
    }

    function statusColor(s) {
        return COLOR[s] || '#94a3b8';
    }

    /* ── Popup HTML for the mini-map (mirror of main map) ───── */
    function buildPipePopupHtml(p, coordStr) {
        const status = (p.status || '').toLowerCase();
        return `<div style="font-family:'Montserrat',sans-serif;font-size:13px;min-width:240px;">
            <div style="background:#f8fafc;padding:16px 14px 12px;border-bottom:1px solid #e2e8f0;">
                <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:4px;">
                    <span style="font-size:10px;font-weight:700;color:#94a3b8;text-transform:uppercase;">KODE PIPA</span>
                    <span style="font-size:10px;font-weight:700;padding:3px 8px;border-radius:5px;background:${BADGE_BG[status]||'#f1f5f9'};color:${BADGE_TEXT[status]||'#64748b'};">${LABEL[status]||(p.status||'—').toUpperCase()}</span>
                </div>
                <div style="font-size:16px;font-weight:700;color:#1e293b;">${p.kode_pipa||'—'}</div>
            </div>
            <div style="padding:12px 14px;">
                <div style="display:grid;grid-template-columns:1fr 1fr;gap:8px;margin-bottom:8px;">
                    <div><div style="font-size:10px;font-weight:700;color:#94a3b8;text-transform:uppercase;margin-bottom:2px;">Fungsi</div><div style="font-weight:600;color:#334155;font-size:12px;">${p.fungsi||'—'}</div></div>
                    <div><div style="font-size:10px;font-weight:700;color:#94a3b8;text-transform:uppercase;margin-bottom:2px;">Diameter</div><div style="font-weight:600;color:#334155;font-size:12px;">${p.pipe_dia!=null?p.pipe_dia+' mm':'—'}</div></div>
                    <div><div style="font-size:10px;font-weight:700;color:#94a3b8;text-transform:uppercase;margin-bottom:2px;">Panjang</div><div style="font-weight:600;color:#334155;font-size:12px;">${p.length_km!=null?Number(p.length_km).toFixed(3)+' km':'—'}</div></div>
                    <div><div style="font-size:10px;font-weight:700;color:#94a3b8;text-transform:uppercase;margin-bottom:2px;">Koordinat</div><div style="font-size:11px;color:#64748b;">${coordStr}</div></div>
                </div>
                <div><div style="font-size:10px;font-weight:700;color:#94a3b8;text-transform:uppercase;margin-bottom:2px;">WILAYAH</div><div style="font-weight:500;color:#334155;font-size:12px;">${p.wilayah||'—'}</div></div>
            </div>
        </div>`;
    }

    function buildManholePopupHtml(p, lat, lng) {
        const status  = (p.status || '').toLowerCase();
        const coordStr = `${Number(lat).toFixed(6)}, ${Number(lng).toFixed(6)}`;
        return `<div style="font-family:'Montserrat',sans-serif;font-size:13px;min-width:240px;">
            <div style="background:#f8fafc;padding:16px 14px 12px;border-bottom:1px solid #e2e8f0;">
                <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:4px;">
                    <span style="font-size:10px;font-weight:700;color:#94a3b8;text-transform:uppercase;">ID MANHOLE</span>
                    <span style="font-size:10px;font-weight:700;padding:3px 8px;border-radius:5px;background:${BADGE_BG[status]||'#f1f5f9'};color:${BADGE_TEXT[status]||'#64748b'};">${LABEL[status]||(p.status||'—').toUpperCase()}</span>
                </div>
                <div style="font-size:16px;font-weight:700;color:#1e293b;">${p.kode_manhole||'—'}</div>
            </div>
            <div style="padding:12px 14px;">
                <div style="display:grid;grid-template-columns:1fr 1fr;gap:8px;margin-bottom:8px;">
                    <div><div style="font-size:10px;font-weight:700;color:#94a3b8;text-transform:uppercase;margin-bottom:2px;">WILAYAH</div><div style="font-weight:600;color:#334155;font-size:12px;">${p.wilayah||'—'}</div></div>
                    <div><div style="font-size:10px;font-weight:700;color:#94a3b8;text-transform:uppercase;margin-bottom:2px;">Kecamatan</div><div style="font-weight:600;color:#334155;font-size:12px;">${p.kecamatan||'—'}</div></div>
                    <div><div style="font-size:10px;font-weight:700;color:#94a3b8;text-transform:uppercase;margin-bottom:2px;">Koordinat</div><div style="font-size:11px;color:#64748b;">${coordStr}</div></div>
                    <div><div style="font-size:10px;font-weight:700;color:#94a3b8;text-transform:uppercase;margin-bottom:2px;">Kondisi</div><div style="font-size:12px;color:#334155;">${p.kondisi_mh||'—'}</div></div>
                </div>
            </div>
        </div>`;
    }

    /* ── Load asset and draw on mini-map ────────────────────── */
    async function loadAndRender() {
        if (!aType || !aId) {
            setOverlay('Tidak ada aset dipilih', 'Klik "Lapor Masalah" dari peta utama');
            miniMap.setView([-7.757, 110.375], 13);
            return;
        }

        try {
            const url = aType === 'pipa'
                ? `${API_BASE}/pipes/${aId}`
                : `${API_BASE}/manholes/${aId}`;

            const res  = await fetch(url);
            const json = await res.json();
            if (!json.success) throw new Error('API error');
            assetData = json.data;

            if (aType === 'pipa') {
                pipaId = assetData.id;
                renderPipa(assetData);
            } else {
                manholeId = assetData.id;
                renderManhole(assetData);
            }
        } catch (e) {
            console.error('loadAndRender:', e);
            setOverlay('Gagal memuat data', 'Silakan kembali ke peta dan coba lagi');
        }
    }

    function renderPipa(p) {
        const geom      = p.geometry;
        const coordSets = buildCoordSets(geom);
        if (!coordSets.length) return;

        const status = (p.status || '').toLowerCase();
        const color  = statusColor(status);
        const boundsArr = [];

        coordSets.forEach((coords, idx) => {
            const outline = L.polyline(coords, {
                color: '#000', weight: 11, opacity: 1,
                lineCap: 'round', lineJoin: 'round', interactive: false,
            }).addTo(miniMap);

            const line = L.polyline(coords, {
                color, weight: 6, opacity: 1,
                lineCap: 'round', lineJoin: 'round',
            });

            const firstSet = coordSets[0];
            const mid      = firstSet[Math.floor(firstSet.length / 2)];
            const coordStr = mid ? `${mid[0].toFixed(6)}, ${mid[1].toFixed(6)}` : '—';

            if (idx === 0) {
                line.bindPopup(buildPipePopupHtml(p, coordStr), { maxWidth: 290, minWidth: 260 }).addTo(miniMap);
                line.openPopup();
            } else {
                line.addTo(miniMap);
            }

            coords.forEach(c => boundsArr.push(c));
        });

        // Update form fields
        const firstSet = coordSets[0];
        const mid      = firstSet[Math.floor(firstSet.length / 2)];
        const coordStr = mid ? `${mid[0].toFixed(6)}, ${mid[1].toFixed(6)}` : (aCoord || '—');

        document.getElementById('field-id-ipal').value   = p.kode_pipa || aKode || '—';
        document.getElementById('field-koordinat').value = coordStr;

        setOverlay(
            `ID PIPA: ${p.kode_pipa || '—'}`,
            `${p.wilayah || ''}\n${p.fungsi || ''}`
        );

        if (boundsArr.length) {
            miniMap.fitBounds(boundsArr, { padding: [40, 40], maxZoom: 18 });
        }
    }

    function renderManhole(p) {
        const lat = parseFloat(p.latitude);
        const lng = parseFloat(p.longitude);

        if (isNaN(lat) || isNaN(lng)) {
            // Try to parse from aCoord
            const parts = (aCoord || '').split(',');
            if (parts.length === 2) {
                renderManholeAt(p, parseFloat(parts[0]), parseFloat(parts[1]));
            } else {
                setOverlay('Koordinat tidak tersedia', '');
            }
            return;
        }
        renderManholeAt(p, lat, lng);
    }

    function renderManholeAt(p, lat, lng) {
        const status = (p.status || '').toLowerCase();
        const color  = statusColor(status);

        const marker = L.circleMarker([lat, lng], {
            radius: 10, fillColor: color,
            color: '#000', weight: 3,
            opacity: 1, fillOpacity: 0.95,
        });

        marker.bindPopup(buildManholePopupHtml(p, lat, lng), { maxWidth: 290, minWidth: 260 })
              .addTo(miniMap);
        marker.openPopup();

        document.getElementById('field-id-ipal').value   = p.kode_manhole || aKode || '—';
        document.getElementById('field-koordinat').value = `${lat.toFixed(6)}, ${lng.toFixed(6)}`;

        setOverlay(
            `ID MANHOLE: ${p.kode_manhole || '—'}`,
            [p.wilayah, p.kecamatan, p.desa].filter(Boolean).join(' · ')
        );

        miniMap.setView([lat, lng], 17);
    }

    function setOverlay(title, subtitle) {
        document.getElementById('overlay-title').textContent    = title;
        document.getElementById('overlay-subtitle').textContent = subtitle;
    }

    /* ── Photo handling ─────────────────────────────────────── */
    window.handleFotoChange = function(input) {
        const files = Array.from(input.files);
        if (files.length > MAX_FOTO) {
            showError(`Maksimal ${MAX_FOTO} foto yang dapat diunggah.`);
            input.value = '';
            return;
        }
        selectedFiles = files;
        renderFotoPreview();
    };

    function renderFotoPreview() {
        const container = document.getElementById('foto-preview');
        container.innerHTML = '';
        selectedFiles.forEach((file, i) => {
            const reader = new FileReader();
            reader.onload = e => {
                const wrap = document.createElement('div');
                wrap.style.cssText = 'position:relative;';
                wrap.innerHTML = `
                    <img src="${e.target.result}"
                         style="width:72px;height:72px;object-fit:cover;border-radius:8px;border:1px solid #e2e8f0;">
                    <button type="button" onclick="removeFile(${i})"
                            style="position:absolute;top:-5px;right:-5px;width:18px;height:18px;
                                   background:#1e293b;color:#fff;border:none;border-radius:50%;
                                   font-size:10px;cursor:pointer;display:flex;align-items:center;justify-content:center;">✕</button>
                `;
                container.appendChild(wrap);
            };
            reader.readAsDataURL(file);
        });
    }

    window.removeFile = function(idx) {
        selectedFiles.splice(idx, 1);
        const dt = new DataTransfer();
        selectedFiles.forEach(f => dt.items.add(f));
        document.getElementById('foto-input').files = dt.files;
        renderFotoPreview();
    };

    /* ── Error helpers ──────────────────────────────────────── */
    function showError(msg) {
        const el = document.getElementById('global-error');
        el.textContent = msg;
        el.style.display = 'block';
        el.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
    }
    function hideError() {
        document.getElementById('global-error').style.display = 'none';
    }

    /* ── Form submission ────────────────────────────────────── */
    document.getElementById('lapor-form').addEventListener('submit', async function(e) {
        e.preventDefault();
        hideError();

        const deskripsi = document.getElementById('field-deskripsi').value.trim();
        const errorDiv  = document.getElementById('deskripsi-error');

        // Validate deskripsi
        if (!deskripsi) {
            document.getElementById('field-deskripsi').classList.add('error');
            errorDiv.style.display = 'block';
            document.getElementById('field-deskripsi').focus();
            return;
        }
        document.getElementById('field-deskripsi').classList.remove('error');
        errorDiv.style.display = 'none';

        // Must have a valid asset
        if (!pipaId && !manholeId) {
            showError('Aset tidak valid. Silakan kembali ke peta dan klik "Lapor Masalah" pada aset yang ingin dilaporkan.');
            return;
        }

        const formData = new FormData();
        if (pipaId)    formData.append('pipa_id',    pipaId);
        if (manholeId) formData.append('manhole_id', manholeId);
        formData.append('deskripsi', deskripsi);
        if (aCoord) formData.append('titik_koordinat', aCoord);

        const fotoInput = document.getElementById('foto-input');
        Array.from(fotoInput.files).forEach((file, i) => {
            formData.append(`foto[${i}]`, file);
        });

        // Loading state
        const btn     = document.getElementById('submit-btn');
        const icon    = document.getElementById('submit-icon');
        const spinner = document.getElementById('submit-spinner');
        const label   = document.getElementById('submit-label');
        btn.disabled       = true;
        icon.style.display = 'none';
        spinner.style.display = 'inline';
        label.textContent  = 'Mengirim…';

        try {
            const res  = await fetch(`${BASE_URL}/api/ipal/aduan`, {
                method: 'POST',
                headers: { 'X-Requested-With': 'XMLHttpRequest' },
                body: formData,
            });
            const json = await res.json();

            if (res.ok && json.success) {
                // Show success banner
                const banner = document.getElementById('success-banner');
                document.getElementById('nomor-tiket').textContent = json.data.nomor_tiket || '';
                banner.style.display = 'flex';
                banner.scrollIntoView({ behavior: 'smooth', block: 'start' });

                // Disable form
                document.getElementById('lapor-form').style.opacity = '.45';
                document.getElementById('lapor-form').style.pointerEvents = 'none';
            } else {
                const errors = json.data;
                if (errors && typeof errors === 'object') {
                    const msgs = Object.values(errors).flat().join(' ');
                    showError(msgs);
                } else {
                    showError(json.message ?? 'Terjadi kesalahan. Silakan coba lagi.');
                }
            }
        } catch (err) {
            showError('Tidak dapat terhubung ke server. Periksa koneksi internet Anda.');
        } finally {
            btn.disabled          = false;
            icon.style.display    = 'inline';
            spinner.style.display = 'none';
            label.textContent     = 'Kirim Laporan';
        }
    });

    /* ── Drag-and-drop on the dropzone ──────────────────────── */
    const dz = document.getElementById('foto-dropzone');
    dz.addEventListener('dragover', e => { e.preventDefault(); dz.style.borderColor = '#3b82f6'; dz.style.background = '#f0f9ff'; });
    dz.addEventListener('dragleave', () => { dz.style.borderColor = ''; dz.style.background = ''; });
    dz.addEventListener('drop', e => {
        e.preventDefault();
        dz.style.borderColor = ''; dz.style.background = '';
        const input = document.getElementById('foto-input');
        const dt    = new DataTransfer();
        Array.from(e.dataTransfer.files).slice(0, MAX_FOTO).forEach(f => dt.items.add(f));
        input.files = dt.files;
        handleFotoChange(input);
    });

    /* ── Boot ───────────────────────────────────────────────── */
    loadAndRender();

})();
</script>

</x-layouts.map>

<style>
    .leaflet-bottom.leaflet-right .leaflet-control-zoom {
        margin-bottom: 50px;
        margin-right: 15px;
    }

    .leaflet-bottom.leaflet-right .map-basemap-control {
        margin-bottom: 112px;
        margin-right: 15px;
        box-shadow: none;
        border: none;
        background: transparent;
    }

    .map-basemap-wrap {
        position: relative;
        pointer-events: auto;
        font-family: 'Montserrat', sans-serif;
    }

    .map-basemap-btn {
        width: 38px;
        height: 38px;
        border: 1px solid #cbd5e1;
        border-radius: 12px;
        background: #ffffff;
        color: #334155;
        position: absolute;
        right: -3px;
        bottom: -60px;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        box-shadow: 0 8px 24px rgba(15, 23, 42, 0.15);
    }
 

    .map-basemap-btn:hover {
        background: #f8fafc;
    }

    .map-basemap-btn:focus-visible {
        outline: 2px solid #3b82f6;
        outline-offset: 2px;
    }

    .map-basemap-panel {
        position: absolute;
        right: 0;
        bottom: -15px;
        width: fit-content;
        border: 1px solid #e2e8f0;
        border-radius: 14px;
        background: #ffffff;
        box-shadow: 0 14px 34px rgba(15, 23, 42, 0.22);
        padding: 10px;
        display: none;
    }

    @media (max-width: 767px) {
        .map-basemap-btn {
            bottom: -122px;
        }
        .map-basemap-panel {
            bottom: -77px;
        }
    }  

    .map-geolocate-btn {
        width: 38px;
        height: 38px;
        border: 1px solid #cbd5e1;
        border-radius: 12px;
        background: #ffffff;
        color: #334155;
        position: absolute;
        right: -3px;
        bottom: -105px;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        box-shadow: 0 8px 24px rgba(15, 23, 42, 0.15);
    }

    .map-geolocate-btn:hover {
        background: #f8fafc;
    }

    .map-geolocate-btn:focus-visible {
        outline: 2px solid #3b82f6;
        outline-offset: 2px;
    }

    .map-geolocate-btn.is-loading {
        cursor: wait;
        opacity: 0.85;
    }

    .map-geolocate-btn.is-loading svg {
        animation: map-geolocate-spin 0.9s linear infinite;
    }

    @keyframes map-geolocate-spin {
        from { transform: rotate(0deg); }
        to { transform: rotate(360deg); }
    }

    .map-basemap-panel.is-open {
        display: block;
        width: max-content;
    }

    .map-basemap-title {
        font-size: 10px;
        font-weight: 700;
        color: #64748b;
        text-transform: uppercase;
        letter-spacing: 0.06em;
        padding: 4px 6px 4px;
    }

    .map-basemap-option {
        width: 100%;
        border-radius: 10px;
        background: #ffffff;
        padding: 8px 9px;
        margin-top: 6px;
        cursor: pointer;
        text-align: middle;
        transition: all 0.16s ease;
        display: block;
    }

    .map-basemap-option:hover {
        border-color: #cbd5e1;
        background: #f8fafc;
    }

    .map-basemap-option.is-active {
        background: #f0f9ff;
        border: 1px solid #38bdf8;
    }

    .map-basemap-option:disabled,
    .map-basemap-option.is-disabled {
        cursor: not-allowed;
        opacity: 0.55;
        background: #f8fafc;
    }

    .map-basemap-option-main {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 8px;
    }

    .map-basemap-option-name {
        font-size: 12px;
        font-weight: 600;
        color: #1e293b;
    }

    .map-basemap-option-tag {
        font-size: 10px;
        font-weight: 700;
        color: #0284c7;
        background: #e0f2fe;
        border-radius: 999px;
        padding: 1px 7px;
        display: none;
    }

    .map-basemap-option.is-active .map-basemap-option-tag {
        display: inline-flex;
    }

    .map-basemap-option-note {
        margin-top: 3px;
        font-size: 10px;
        color: #64748b;
        line-height: 1.3;
    }

    .map-basemap-status {
        margin-top: 8px;
        padding: 7px 8px;
        border-radius: 8px;
        font-size: 10px;
        color: #0369a1;
        background: #f0f9ff;
        line-height: 1.35;
    }

    @media (max-width: 767px) {
        .leaflet-bottom.leaflet-right .leaflet-control-zoom {
            margin-bottom: 110px;
            margin-right: 10px;
        }

        .leaflet-bottom.leaflet-right .map-basemap-control {
            margin-bottom: 172px;
            margin-right: 10px;
        }

        .map-basemap-panel {
            width: min(220px, calc(100vw - 24px));
        }

        .map-geolocate-btn {
            bottom: -167px;
        }
    }

    /* Remove browser focus outline on clicked SVG paths (polylines, circles) */
    .leaflet-pane svg path:focus,
    .leaflet-pane svg circle:focus,
    .leaflet-interactive:focus {
        outline: none !important;
    }

    /* Pipe & manhole hover tooltips — remove default Leaflet chrome */
    .leaflet-pipe-tooltip,
    .leaflet-manhole-tooltip {
        background: transparent !important;
        border: none !important;
        box-shadow: none !important;
        padding: 0 !important;
    }
    .leaflet-pipe-tooltip::before,
    .leaflet-manhole-tooltip::before {
        display: none !important;
    }
</style>
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script src="https://cdn.maptiler.com/maptiler-sdk-js/v2.3.0/maptiler-sdk.umd.js"></script>
<link href="https://cdn.maptiler.com/maptiler-sdk-js/v2.3.0/maptiler-sdk.css" rel="stylesheet" />
<script src="https://cdn.maptiler.com/leaflet-maptilersdk/v2.0.0/leaflet-maptilersdk.js"></script>
<script>
// ─── Map init ─────────────────────────────────────────────────────────────
const map = L.map('map', {
    center: [-7.757, 110.375], // Koordinat awal
    zoom: 13,
    zoomControl: false,
});

L.control.zoom({ position: 'bottomright' }).addTo(map);

const BASEMAP_STORAGE_KEY = 'ipal.map.basemap';
const BASEMAP_DEFAULT_ID = 'maptiler_custom_osm'; 
const USER_LOCATION_ZOOM = 18;

// ─── Konfigurasi Provider ─────────────────────────────────────────────────
const BASEMAP_PROVIDERS = {
    osm: {
        id: 'osm',
        type: 'raster', 
        label: 'Peta Detail',
        note: 'Style OSM bawaan (lebih kontras).',
        attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors',
        url: 'https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png',
        options: { maxZoom: 19 },
        enabled: true,
    },
    maptiler_custom_osm: {
        id: 'maptiler_custom_osm',
        type: 'maptiler', 
        label: 'Peta Simpel',
        note: 'Tampilan bersih tanpa layer bangunan.',
        styleId: '{{ env("VITE_MAPTILER_STYLE_ID") }}', 
        apiKey: '{{ env("VITE_MAPTILER_API_KEY") }}',
        enabled: true,
    },
};

const basemapState = {
    currentId: null,
    currentLayer: null,
    controlEl: null,
    panelEl: null,
    buttonEl: null,
    statusEl: null,
};

const geolocateState = {
    buttonEl: null,
    marker: null,
    accuracyCircle: null,
    isLoading: false,
};

function setGeolocateLoading(isLoading) {
    geolocateState.isLoading = isLoading;
    if (!geolocateState.buttonEl) return;
    geolocateState.buttonEl.classList.toggle('is-loading', isLoading);
    geolocateState.buttonEl.disabled = isLoading;
    geolocateState.buttonEl.setAttribute('aria-busy', isLoading ? 'true' : 'false');
}

function ensureUserLocationLayers(latlng, accuracyMeters) {
    if (!geolocateState.marker) {
        geolocateState.marker = L.circleMarker(latlng, {
            radius: 7,
            fillColor: '#2563eb',
            color: '#ffffff',
            weight: 2,
            fillOpacity: 0.95,
        }).addTo(map);
    } else {
        geolocateState.marker.setLatLng(latlng);
    }

    const accuracyRadius = Math.max(accuracyMeters || 0, 20);
    if (!geolocateState.accuracyCircle) {
        geolocateState.accuracyCircle = L.circle(latlng, {
            radius: accuracyRadius,
            color: '#2563eb',
            weight: 1,
            fillColor: '#3b82f6',
            fillOpacity: 0.12,
            interactive: false,
        }).addTo(map);
    } else {
        geolocateState.accuracyCircle.setLatLng(latlng);
        geolocateState.accuracyCircle.setRadius(accuracyRadius);
    }
}

function getLocationErrorMessage(code) {
    if (code === 1) return 'Izin lokasi ditolak. Mohon aktifkan izin lokasi di browser Anda.';
    if (code === 2) return 'Lokasi tidak tersedia. Coba lagi di area dengan sinyal lebih baik.';
    if (code === 3) return 'Waktu permintaan lokasi habis. Silakan coba lagi.';
    return 'Gagal mengambil lokasi Anda.';
}

function locateUserAndFocusMap() {
    if (geolocateState.isLoading) return;

    if (!navigator.geolocation) {
        alert('Browser Anda tidak mendukung fitur lokasi.');
        return;
    }

    setGeolocateLoading(true);

    navigator.geolocation.getCurrentPosition(
        function (position) {
            const latlng = [position.coords.latitude, position.coords.longitude];
            ensureUserLocationLayers(latlng, position.coords.accuracy);
            map.flyTo(latlng, USER_LOCATION_ZOOM, { duration: 1.1 });
            setGeolocateLoading(false);
        },
        function (error) {
            setGeolocateLoading(false);
            alert(getLocationErrorMessage(error.code));
        },
        {
            enableHighAccuracy: true,
            timeout: 12000,
            maximumAge: 0,
        }
    );
}

function getProvider(id) {
    return BASEMAP_PROVIDERS[id] || null;
}

function getPreferredBasemapId() {
    const saved = localStorage.getItem(BASEMAP_STORAGE_KEY);
    return getProvider(saved) ? saved : BASEMAP_DEFAULT_ID;
}

function savePreferredBasemapId(id) {
    localStorage.setItem(BASEMAP_STORAGE_KEY, id);
}

function createTileLayerByProvider(provider) {
    // Jika tipenya maptiler, gunakan plugin L.maptilerLayer (Vector)
    if (provider.type === 'maptiler') {
        return L.maptilerLayer({
            apiKey: provider.apiKey,
            style: provider.styleId
        });
    } 
    // Jika tipenya raster biasa, gunakan L.tileLayer standar
    else {
        const url = provider.buildUrl ? provider.buildUrl() : provider.url;
        if (!url) return null;
        const opts = Object.assign({ attribution: provider.attribution || '', maxZoom: 19 }, provider.options || {});
        return L.tileLayer(url, opts);
    }
}

function updateBasemapUiState() {
    if (!basemapState.panelEl) return;

    basemapState.panelEl.querySelectorAll('[data-basemap-id]').forEach(el => {
        const id = el.getAttribute('data-basemap-id');
        const provider = getProvider(id);
        const isActive = id === basemapState.currentId;
        const isEnabled = provider && provider.enabled;
        el.classList.toggle('is-active', isActive);
        el.classList.toggle('is-disabled', !isEnabled);
        el.disabled = !isEnabled;
    });

    if (basemapState.statusEl) {
        const activeProvider = getProvider(basemapState.currentId);
        basemapState.statusEl.textContent = activeProvider
            ? 'Peta aktif: ' + activeProvider.label
            : 'Peta aktif: tidak tersedia';
    }
}

function closeBasemapPanel() {
    if (!basemapState.panelEl) return;
    basemapState.panelEl.classList.remove('is-open');
    if (basemapState.buttonEl) basemapState.buttonEl.setAttribute('aria-expanded', 'false');
}

function toggleBasemapPanel() {
    if (!basemapState.panelEl) return;
    const isOpen = basemapState.panelEl.classList.toggle('is-open');
    if (basemapState.buttonEl) basemapState.buttonEl.setAttribute('aria-expanded', isOpen ? 'true' : 'false');
}

function applyBasemap(providerId, persist = true) {
    const provider = getProvider(providerId);
    if (!provider || !provider.enabled) {
        return false;
    }

    const nextLayer = createTileLayerByProvider(provider);
    if (!nextLayer) return false;

    if (basemapState.currentLayer) {
        map.removeLayer(basemapState.currentLayer);
    }

    nextLayer.addTo(map);
    basemapState.currentLayer = nextLayer;
    basemapState.currentId = providerId;
    if (persist) savePreferredBasemapId(providerId);
    updateBasemapUiState();
    return true;
}

function buildBasemapControl() {
    const BasemapControl = L.Control.extend({
        options: { position: 'bottomright' },
        onAdd: function () {
            const container = L.DomUtil.create('div', 'map-basemap-control leaflet-control');
            const wrap = L.DomUtil.create('div', 'map-basemap-wrap', container);

            wrap.innerHTML = `
                <button type="button" class="map-basemap-btn" aria-label="Konfigurasi peta" aria-expanded="false">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M21 14L12 20L3 14M21 10L12 16L3 10L12 4L21 10Z" stroke="#1E293B" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </button>
                <button type="button" class="map-geolocate-btn" aria-label="Ambil lokasi saya" title="Lokasi saya">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M12 8C9.79 8 8 9.79 8 12C8 14.21 9.79 16 12 16C14.21 16 16 14.21 16 12C16 9.79 14.21 8 12 8ZM20.94 11C20.7135 8.97212 19.8042 7.08154 18.3613 5.63869C16.9185 4.19585 15.0279 3.28651 13 3.06V2C13 1.45 12.55 1 12 1C11.45 1 11 1.45 11 2V3.06C8.97212 3.28651 7.08154 4.19585 5.63869 5.63869C4.19585 7.08154 3.28651 8.97212 3.06 11H2C1.45 11 1 11.45 1 12C1 12.55 1.45 13 2 13H3.06C3.28651 15.0279 4.19585 16.9185 5.63869 18.3613C7.08154 19.8042 8.97212 20.7135 11 20.94V22C11 22.55 11.45 23 12 23C12.55 23 13 22.55 13 22V20.94C15.0279 20.7135 16.9185 19.8042 18.3613 18.3613C19.8042 16.9185 20.7135 15.0279 20.94 13H22C22.55 13 23 12.55 23 12C23 11.45 22.55 11 22 11H20.94ZM12 19C8.13 19 5 15.87 5 12C5 8.13 8.13 5 12 5C15.87 5 19 8.13 19 12C19 15.87 15.87 19 12 19Z" fill="#1E293B"/>
                    </svg>
                </button>
                <div class="map-basemap-panel" role="dialog" aria-label="Pilih peta dasar">
                    <div class="map-basemap-title">Konfigurasi Peta</div>
                    ${Object.values(BASEMAP_PROVIDERS).map(provider => `
                        <button type="button" class="map-basemap-option" data-basemap-id="${provider.id}">
                            <div class="map-basemap-option-main">
                                <span class="map-basemap-option-name">${provider.label}</span>
                            </div>
                        </button>
                    `).join('')}
                </div>
            `;

            L.DomEvent.disableClickPropagation(container);
            L.DomEvent.disableScrollPropagation(container);

            basemapState.controlEl = container;
            basemapState.buttonEl = wrap.querySelector('.map-basemap-btn');
            basemapState.panelEl = wrap.querySelector('.map-basemap-panel');
            geolocateState.buttonEl = wrap.querySelector('.map-geolocate-btn');
            // basemapState.statusEl = wrap.querySelector('.map-basemap-status');

            basemapState.buttonEl.addEventListener('click', function () {
                toggleBasemapPanel();
            });

            basemapState.panelEl.querySelectorAll('[data-basemap-id]').forEach(el => {
                el.addEventListener('click', function () {
                    const targetId = this.getAttribute('data-basemap-id');
                    if (!applyBasemap(targetId, true)) {
                        updateBasemapUiState();
                    }
                    closeBasemapPanel();
                });
            });

            geolocateState.buttonEl.addEventListener('click', function () {
                locateUserAndFocusMap();
            });

            return container;
        },
    });

    const control = new BasemapControl();
    map.addControl(control);
    updateBasemapUiState();
}

function setupBasemap() {
    const requested = getPreferredBasemapId();
    if (!applyBasemap(requested, false)) {
        applyBasemap(BASEMAP_DEFAULT_ID, false);
    }

    savePreferredBasemapId(basemapState.currentId || BASEMAP_DEFAULT_ID);
    buildBasemapControl();

    map.on('click', closeBasemapPanel);

    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape') closeBasemapPanel();
    });

    document.addEventListener('click', function (e) {
        if (!basemapState.controlEl) return;
        if (!basemapState.controlEl.contains(e.target)) {
            closeBasemapPanel();
        }
    });
}

setupBasemap();

// ─── Constants ────────────────────────────────────────────────────────────
const API_BASE          = '/api/ipal';
const API_PIPES_GEOJSON = API_BASE + '/pipes/geojson';
const API_MHOLE_GEOJSON = API_BASE + '/manholes/geojson';
const API_PIPES_FILTERS = API_BASE + '/pipes/filters';
const API_STATISTICS    = API_BASE + '/statistics';
const CACHE_TTL         = 5 * 60 * 1000; // 5 minutes

const COLOR      = { aman:'#22c55e', perbaikan:'#eab308', masalah:'#ef4444', rusak:'#ef4444', 'dalam perbaikan':'#eab308' };
const LABEL      = { aman:'AMAN',    perbaikan:'PERBAIKAN', masalah:'MASALAH', rusak:'RUSAK', 'dalam perbaikan':'PERBAIKAN' };
const BADGE_BG   = { aman:'#22C55E1A', perbaikan:'#fef3c7', masalah:'#fee2e2', rusak:'#fee2e2', 'dalam perbaikan':'#fef3c7' };
const BADGE_TEXT = { aman:'#22c55e',   perbaikan:'#a16207', masalah:'#dc2626', rusak:'#dc2626', 'dalam perbaikan':'#a16207' };

// SVG icons
const SVG_WARNING  = `<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" width="16" height="16" style="flex-shrink:0;"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126ZM12 15.75h.007v.008H12v-.008Z"/></svg>`;
const SVG_WRENCH   = `<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" width="16" height="16" style="flex-shrink:0;"><path stroke-linecap="round" stroke-linejoin="round" d="M11.42 15.17 17.25 21A2.652 2.652 0 0 0 21 17.25l-5.877-5.877M11.42 15.17l2.496-3.03c.317-.384.74-.626 1.208-.766M11.42 15.17l-4.655 5.653a2.548 2.548 0 1 1-3.586-3.586l5.654-4.654m5.292-8.317.086.072 3.3 3.3a.612.612 0 0 1 0 .866l-3.088 3.088-3.3-3.3a.612.612 0 0 1 0-.866l3.088-3.088.086-.072ZM7.5 5.25c0-.966.784-1.75 1.75-1.75a1.75 1.75 0 0 1 0 3.5c-.966 0-1.75-.784-1.75-1.75Z"/></svg>`;
const ICON_PIPE    = `<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#13C8EC" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" style="flex-shrink:0;"><path d="M3 6h18M3 18h18M6 6v12M18 6v12"/></svg>`;
const ICON_MANHOLE = `<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#6366f1" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" style="flex-shrink:0;"><circle cx="12" cy="12" r="10"/><circle cx="12" cy="12" r="4"/></svg>`;

// ─── In-memory cache ──────────────────────────────────────────────────────
const _cache = new Map();

function cacheGet(key) {
    const entry = _cache.get(key);
    if (!entry) return null;
    if (Date.now() > entry.expiry) { _cache.delete(key); return null; }
    return entry.data;
}

function cacheSet(key, data) {
    _cache.set(key, { data, expiry: Date.now() + CACHE_TTL });
}

// Build deterministic cache key from URL + params object
function cacheKey(url, params) {
    const sorted = Object.keys(params).sort().map(k => k + '=' + params[k]).join('&');
    return url + (sorted ? '?' + sorted : '');
}

// ─── Fetch helper with caching ────────────────────────────────────────────
async function apiFetch(url, params = {}) {
    const clean = Object.fromEntries(
        Object.entries(params).filter(([, v]) => v !== '' && v != null)
    );
    const key = cacheKey(url, clean);
    const cached = cacheGet(key);
    if (cached) return cached;

    const query   = new URLSearchParams(clean).toString();
    const fullUrl = query ? url + '?' + query : url;
    const resp    = await fetch(fullUrl);
    if (!resp.ok) throw new Error('API error ' + resp.status + ': ' + fullUrl);
    const data = await resp.json();
    cacheSet(key, data);
    return data;
}

// ─── Layer groups ─────────────────────────────────────────────────────────
const pipeLayer    = L.layerGroup().addTo(map);
const manholeLayer = L.layerGroup().addTo(map);

const hoverState = {
    suspended: false,
    resumeTimer: null,
};

function resetFeatureHoverState() {
    const { weight } = getManholeScale();

    pipeLayer.eachLayer(layer => {
        if (typeof layer.closeTooltip === 'function') {
            layer.closeTooltip();
        }
        if (layer._hoverOutline && !layer._hoverOutline._selected) {
            layer._hoverOutline.setStyle({ opacity: 0 });
        }
    });

    manholeLayer.eachLayer(layer => {
        if (typeof layer.closeTooltip === 'function') {
            layer.closeTooltip();
        }
        if (layer instanceof L.CircleMarker && !layer._selected) {
            layer.setStyle({ color: '#ffffff', weight });
        }
    });
}

function suspendHoverDuringMapTransform() {
    hoverState.suspended = true;
    if (hoverState.resumeTimer) {
        clearTimeout(hoverState.resumeTimer);
        hoverState.resumeTimer = null;
    }
    resetFeatureHoverState();
}

function resumeHoverAfterMapTransform() {
    if (hoverState.resumeTimer) {
        clearTimeout(hoverState.resumeTimer);
    }
    // Small delay avoids immediate re-hover while wheel zoom is still settling.
    hoverState.resumeTimer = setTimeout(() => {
        hoverState.suspended = false;
        hoverState.resumeTimer = null;
    }, 120);
}

map.on('zoomstart movestart', suspendHoverDuringMapTransform);
map.on('zoomend moveend', function () {
    resetFeatureHoverState();
    resumeHoverAfterMapTransform();
});

// ─── In-flight feature stores (for search & filter) ───────────────────────
let currentPipeFeatures    = [];
let currentManholeFeatures = [];

// ─── Layer lookup maps (kode → Leaflet layer, for popup-on-select) ────────
const pipeLayerMap    = {};
const manholeLayerMap = {};

// ─── Loading overlay ──────────────────────────────────────────────────────
function setLoading(active) {
    const el = document.getElementById('map-loading');
    if (el) el.style.display = active ? 'flex' : 'none';
}

// ─── Color helper ─────────────────────────────────────────────────────────
function statusColor(status) {
    return COLOR[status] || '#64748b';
}

// ─── Geometry → Leaflet LatLng arrays ────────────────────────────────────
// Handles both LineString and MultiLineString
function buildPipeCoordSets(geometry) {
    if (!geometry) return [];
    if (geometry.type === 'LineString') {
        return [geometry.coordinates.map(c => [c[1], c[0]])];
    }
    if (geometry.type === 'MultiLineString') {
        return geometry.coordinates.map(ring => ring.map(c => [c[1], c[0]]));
    }
    return [];
}

// ─── HTML builder helpers ─────────────────────────────────────────────────

// Status badge span — compact=true uses tighter padding (tooltips)
function badgeHtml(status, compact = false) {
    const pad    = compact ? '2px 7px' : '4px 9px';
    const radius = compact ? '4px'     : '5px';
    const label  = LABEL[status] || (status ? status.toUpperCase() : '—');
    return `<span style="font-size:10px;font-weight:700;padding:${pad};border-radius:${radius};background:${BADGE_BG[status] || '#f1f5f9'};color:${BADGE_TEXT[status] || '#64748b'};">${label}</span>`;
}

// Popup label / value cell
function fieldHtml(label, value, valStyle = 'font-weight:600;color:#334155;') {
    return `<div><div style="font-size:10px;font-weight:700;color:#94a3b8;text-transform:uppercase;margin-bottom:3px;">${label}</div><div style="${valStyle}">${value}</div></div>`;
}

// Tooltip label / value cell
function tooltipFieldHtml(label, value) {
    return `<div><span style="font-size:10px;color:#94a3b8;font-weight:600;">${label}</span><br><span style="font-weight:600;color:#334155;font-size:11px;">${value}</span></div>`;
}

// Shared popup outer container + header block (leaves outer <div> open for body)
function popupHeaderHtml(typeLabel, kode, status, minWidth = '260px') {
    return `<div style="font-family:'Montserrat',sans-serif;font-size:13px;min-width:${minWidth};">
        <div style="background:#f8fafc;padding:20px 16px 14px;border-bottom:1px solid #e2e8f0;">
            <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:6px;">
                <span style="font-size:10px;font-weight:700;color:#94a3b8;text-transform:uppercase;letter-spacing:.06em;">${typeLabel}</span>
                ${badgeHtml(status)}
            </div>
            <div style="font-size:18px;font-weight:700;color:#1e293b;">${kode}</div>
        </div>`;
}

// Shared tooltip outer wrapper (self-contained)
function tooltipWrapHtml(code, status, body, minWidth = '170px') {
    return `<div style="font-family:'Montserrat',sans-serif;font-size:12px;background:#fff;border-radius:8px;padding:10px 13px;box-shadow:0 2px 8px rgba(0,0,0,.15);min-width:${minWidth};pointer-events:none;">
        <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:6px;">
            <span style="font-size:13px;font-weight:700;color:#1e293b;">${code}</span>
            ${badgeHtml(status, true)}
        </div>
        ${body}
    </div>`;
}

// ─── Lapor button ─────────────────────────────────────────────────────────
function buildLaporBtn(type, id, kode, coord, wilayah, status) {
    const base = 'width:100%;padding:9px;border-radius:8px;font-size:13px;font-weight:500;display:flex;align-items:center;justify-content:center;gap:6px;cursor:not-allowed;';
    if (status === 'rusak' || status === 'masalah') {
        return `<div style="${base}background:#fee2e2;color:#dc2626;">${SVG_WARNING}Aset Rusak – Sudah Dilaporkan</div>`;
    }
    if (status === 'dalam perbaikan' || status === 'perbaikan') {
        return `<div style="${base}background:#fef3c7;color:#a16207;">${SVG_WRENCH}Sedang Dalam Perbaikan</div>`;
    }
    const url = '/ipal/lapor-masalah?type=' + encodeURIComponent(type)
        + '&id='      + encodeURIComponent(id      || '')
        + '&kode='    + encodeURIComponent(kode    || '')
        + '&coord='   + encodeURIComponent(coord   || '')
        + '&wilayah=' + encodeURIComponent(wilayah || '');
    return `<a href="${url}" style="width:100%;padding:9px;background:#FFE2E2;color:#9F0712;border:none;border-radius:8px;font-size:13px;font-weight:400;cursor:pointer;font-family:inherit;display:flex;align-items:center;justify-content:center;gap:6px;text-decoration:none;">${SVG_WARNING}Lapor Masalah</a>`;
}

// ─── Popup builders ───────────────────────────────────────────────────────
function buildPipePopup(p, coordStr) {
    const status = (p.status || '').toLowerCase();
    return popupHeaderHtml('KODE PIPA', p.kode_pipa || '—', status, '260px') +
        `<div style="padding:14px 16px;">
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:10px;margin-bottom:10px;">
                ${fieldHtml('Fungsi',    p.fungsi   || '—')}
                ${fieldHtml('Diameter',  p.pipe_dia  != null ? p.pipe_dia + ' mm'                        : '—')}
                ${fieldHtml('Panjang',   p.length_km != null ? Number(p.length_km).toFixed(3) + ' km'    : '—')}
                ${fieldHtml('Koordinat', coordStr, 'font-size:11px;color:#64748b;')}
            </div>
            <div style="margin-bottom:14px;">${fieldHtml('Wilayah', p.wilayah || '—', 'font-weight:500;color:#334155;')}</div>
            ${buildLaporBtn('pipa', p.id, p.kode_pipa, coordStr, p.wilayah, status)}
        </div>
    </div>`;
}

function buildManholePopup(p, lat, lng) {
    const status   = (p.status || '').toLowerCase();
    const coordStr = (lat != null && lng != null) ? `${Number(lat).toFixed(6)}, ${Number(lng).toFixed(6)}` : '—';
    return popupHeaderHtml('KODE MANHOLE', p.kode_manhole || '—', status, '250px') +
        `<div style="padding:14px 16px;">
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:10px;margin-bottom:10px;">
                ${fieldHtml('Kondisi',     p.kondisi_mh || '—')}
                ${fieldHtml('Klasifikasi', p.klasifikasi || '—')}
                ${fieldHtml('Desa',        p.desa       || '—', 'font-weight:500;color:#334155;')}
                ${fieldHtml('Kecamatan',   p.kecamatan  || '—', 'font-weight:500;color:#334155;')}
            </div>
            ${buildLaporBtn('manhole', p.id, p.kode_manhole, coordStr, p.wilayah, status)}
        </div>
    </div>`;
}

// ─── Tooltip builders ─────────────────────────────────────────────────────
function buildPipeTooltip(p) {
    const status = (p.status || '').toLowerCase();
    const body   = `<div style="display:grid;grid-template-columns:1fr 1fr;gap:4px 10px;">
        ${tooltipFieldHtml('Fungsi',   p.fungsi   || '—')}
        ${tooltipFieldHtml('Diameter', p.pipe_dia  != null ? p.pipe_dia + ' mm'                        : '—')}
        <div style="grid-column:1/-1;">${tooltipFieldHtml('Panjang', p.length_km != null ? Number(p.length_km).toFixed(3) + ' km' : '—')}</div>
    </div>`;
    return tooltipWrapHtml(p.kode_pipa || '—', status, body, '170px');
}

function buildManholeTooltip(p) {
    const status = (p.status || '').toLowerCase();
    const body   = `<div style="display:grid;grid-template-columns:1fr 1fr;gap:4px 10px;">
        ${tooltipFieldHtml('Kondisi',   p.kondisi_mh || '—')}
        ${tooltipFieldHtml('Kecamatan', p.kecamatan  || p.desa || '—')}
    </div>`;
    return tooltipWrapHtml(p.kode_manhole || '—', status, body, '160px');
}

// ─── Zoom-responsive manhole scale ────────────────────────────────────────
const ZOOM_SCALE = [
    [12, { radius: 2, weight: 0.5 }],
    [13, { radius: 3, weight: 0.8 }],
    [14, { radius: 4, weight: 1.0 }],
    [15, { radius: 5, weight: 1.5 }],
    [16, { radius: 6, weight: 2.0 }],
    [17, { radius: 7, weight: 2.5 }],
];

function getManholeScale() {
    const z     = map.getZoom();
    const entry = ZOOM_SCALE.find(([maxZ]) => z <= maxZ);
    return entry ? entry[1] : { radius: 8, weight: 3 };
}

// ─── Feature drawing ──────────────────────────────────────────────────────
function drawPipeFeature(feature, layer) {
    const p      = feature.properties || {};
    const status = (p.status || '').toLowerCase();
    const color  = statusColor(status);

    const coordSets = buildPipeCoordSets(feature.geometry);
    if (!coordSets.length) return;

    const firstSet = coordSets[0];
    const mid      = firstSet[Math.floor(firstSet.length / 2)];
    const coordStr = mid ? mid[0].toFixed(6) + ', ' + mid[1].toFixed(6) : '—';

    coordSets.forEach((coords, idx) => {
        // Outline layer (black border effect) — drawn first so it sits behind the main line
        const outline = L.polyline(coords, {
            color: '#000000', weight: 9, opacity: 0,
            lineCap: 'round', lineJoin: 'round',
            interactive: false,
        }).addTo(layer);

        const line = L.polyline(coords, {
            color, weight: 5, opacity: 0.88,
            lineCap: 'round', lineJoin: 'round',
        });
        line._hoverOutline = outline;
        if (idx === 0) {
            line.bindPopup(buildPipePopup(p, coordStr), { maxWidth: 300, minWidth: 280 });
            if (p.kode_pipa) pipeLayerMap[p.kode_pipa] = line;
        }
        line.bindTooltip(buildPipeTooltip(p), {
            sticky: true, direction: 'top', offset: [0, -6],
            opacity: 1, className: 'leaflet-pipe-tooltip',
        });
        line.on('mouseover', function () {
            if (hoverState.suspended) {
                this.closeTooltip();
                return;
            }
            outline.setStyle({ opacity: 1 });
            this.bringToFront();
        });
        line.on('mouseout', function () {
            if (!outline._selected) outline.setStyle({ opacity: 0 });
        });
        line.on('popupopen', function () {
            outline._selected = true;
            outline.setStyle({ opacity: 1 });
            this.bringToFront();
        });
        line.on('popupclose', function () {
            outline._selected = false;
            outline.setStyle({ opacity: 0 });
        });
        line.addTo(layer);
    });
}

function drawManholeFeature(feature, layer) {
    const p      = feature.properties || {};
    const status = (p.status || '').toLowerCase();
    const color  = statusColor(status);
    const geom   = feature.geometry;

    if (!geom || geom.type !== 'Point') return;
    const [lng, lat] = geom.coordinates;

    const { radius, weight } = getManholeScale();
    const marker = L.circleMarker([lat, lng], {
        radius, fillColor: color,
        color: '#ffffff', weight,
        opacity: 1, fillOpacity: 0.9,
    })
    .bindTooltip(buildManholeTooltip(p), {
        sticky: true, direction: 'top', offset: [0, -10],
        opacity: 1, className: 'leaflet-manhole-tooltip',
    })
    .bindPopup(buildManholePopup(p, lat, lng), { maxWidth: 290, minWidth: 270 });
    marker.on('mouseover', function () {
        if (hoverState.suspended) {
            this.closeTooltip();
            return;
        }
        this.setStyle({ color: '#000000', weight: 4.5 });
        this.bringToFront();
    });
    marker.on('mouseout', function () {
        if (!this._selected) this.setStyle({ color: '#ffffff', weight: getManholeScale().weight });
    });
    marker.on('popupopen', function () {
        this._selected = true;
        this.setStyle({ color: '#000000', weight: 4.5 });
        this.bringToFront();
    });
    marker.on('popupclose', function () {
        this._selected = false;
        this.setStyle({ color: '#ffffff', weight: getManholeScale().weight });
    });
    marker.addTo(layer);
    if (p.kode_manhole) manholeLayerMap[p.kode_manhole] = marker;
}

// ─── Render ───────────────────────────────────────────────────────────────
function renderPipes(geojson) {
    pipeLayer.clearLayers();
    Object.keys(pipeLayerMap).forEach(k => delete pipeLayerMap[k]);
    currentPipeFeatures = geojson.features || [];
    currentPipeFeatures.forEach(f => drawPipeFeature(f, pipeLayer));
}

function renderManholes(geojson) {
    manholeLayer.clearLayers();
    Object.keys(manholeLayerMap).forEach(k => delete manholeLayerMap[k]);
    currentManholeFeatures = geojson.features || [];
    currentManholeFeatures.forEach(f => drawManholeFeature(f, manholeLayer));
}

// ─── Load functions ───────────────────────────────────────────────────────
async function loadPipes(filters = {}) {
    try {
        setLoading(true);
        const data = await apiFetch(API_PIPES_GEOJSON, filters);
        renderPipes(data);
    } catch (e) {
        console.error('loadPipes:', e);
    } finally {
        setLoading(false);
    }
}

async function loadManholes(filters = {}) {
    try {
        const data = await apiFetch(API_MHOLE_GEOJSON, filters);
        renderManholes(data);
    } catch (e) {
        console.error('loadManholes:', e);
    }
}

async function loadFilters() {
    try {
        const data     = await apiFetch(API_PIPES_FILTERS);
        const selJenis = document.getElementById('filter-jenis');
        (data?.data?.fungsi || []).forEach(v => {
            const opt = document.createElement('option');
            opt.value = v; opt.textContent = v;
            selJenis.appendChild(opt);
        });
    } catch (e) {
        console.error('loadFilters:', e);
    }
}

async function loadStats() {
    try {
        const resp = await apiFetch(API_STATISTICS);
        const data = resp?.data || {};
        const mh   = data.manhole || {};
        const pipa = data.pipa   || {};

        function setText(id, val) {
            const el = document.getElementById(id);
            if (el) el.textContent = val != null ? Number(val).toLocaleString('id-ID') : '—';
        }

        const elPanjang = document.getElementById('stat-total-panjang');
        if (elPanjang) elPanjang.textContent = pipa.total_panjang_km != null
            ? Number(pipa.total_panjang_km).toLocaleString('id-ID', { minimumFractionDigits: 1, maximumFractionDigits: 1 })
            : '—';

        setText('stat-total-manhole', mh.total);
        setText('stat-total-pipa',    pipa.total);

        const byStatus = mh.by_status || {};
        setText('stat-status-aman',      byStatus['aman']      || 0);
        setText('stat-status-perbaikan', byStatus['perbaikan'] || 0);
        setText('stat-status-masalah',   byStatus['masalah']   || 0);

        const byFungsi = pipa.by_fungsi || {};
        setText('stat-fungsi-glontor', byFungsi['Glontor'] || 0);
        setText('stat-fungsi-induk',   byFungsi['Induk']   || 0);
        setText('stat-fungsi-lateral', byFungsi['Lateral'] || 0);
    } catch (e) {
        console.error('loadStats:', e);
    }
}

// ─── Active filters ───────────────────────────────────────────────────────
const activeStatuses = new Set(['aman', 'perbaikan', 'masalah', 'rusak', 'dalam perbaikan']);
let activeFungsi = '';

function buildPipeFilters() {
    return { fungsi: activeFungsi };
}

// Status filtering is client-side — re-draws from already-fetched features
function applyStatusVisibility() {
    pipeLayer.clearLayers();
    currentPipeFeatures.forEach(f => {
        if (activeStatuses.has((f.properties?.status || '').toLowerCase())) drawPipeFeature(f, pipeLayer);
    });

    manholeLayer.clearLayers();
    currentManholeFeatures.forEach(f => {
        if (activeStatuses.has((f.properties?.status || '').toLowerCase())) drawManholeFeature(f, manholeLayer);
    });
}

// ─── Filter event listeners ───────────────────────────────────────────────
document.querySelectorAll('.status-btn').forEach(btn => {
    btn.addEventListener('click', () => {
        const s = btn.dataset.status;
        if (activeStatuses.has(s)) { activeStatuses.delete(s); btn.classList.add('inactive'); }
        else                        { activeStatuses.add(s);    btn.classList.remove('inactive'); }
        applyStatusVisibility();
    });
});

document.getElementById('filter-jenis').addEventListener('change', function () {
    activeFungsi = this.value;
    loadPipes(buildPipeFilters());
});

// ─── Search & Suggestions ────────────────────────────────────────────────
function debounce(fn, delay) {
    let timer;
    return function (...args) {
        clearTimeout(timer);
        timer = setTimeout(() => fn.apply(this, args), delay);
    };
}

function buildSuggestions(q) {
    if (!q || q.length < 2) return [];
    const results = [];
    const lower   = q.toLowerCase();

    for (const feature of currentPipeFeatures) {
        const p        = feature.properties || {};
        const haystack = [p.kode_pipa, p.id_jalur, p.fungsi].filter(Boolean).join(' ').toLowerCase();
        if (!haystack.includes(lower)) continue;

        const coordSets = buildPipeCoordSets(feature.geometry);
        if (!coordSets.length) continue;
        const mid = coordSets[0][Math.floor(coordSets[0].length / 2)];

        results.push({
            type: 'pipe',
            label: p.kode_pipa || p.id_jalur || '—',
            sublabel: [p.fungsi, p.status ? p.status.toUpperCase() : null].filter(Boolean).join(' · '),
            latlng: mid,
            zoom: 17,
            key: p.kode_pipa,
        });
    }

    for (const feature of currentManholeFeatures) {
        const p        = feature.properties || {};
        const haystack = [p.kode_manhole, p.desa, p.kecamatan].filter(Boolean).join(' ').toLowerCase();
        if (!haystack.includes(lower)) continue;

        const geom = feature.geometry;
        if (!geom || geom.type !== 'Point') continue;
        const [lng, lat] = geom.coordinates;

        results.push({
            type: 'manhole',
            label: p.kode_manhole || '—',
            sublabel: [p.desa, p.kecamatan].filter(Boolean).join(', '),
            latlng: [lat, lng],
            zoom: 18,
            key: p.kode_manhole,
        });
    }

    return results;
}

function hideSuggestions() {
    const el = document.getElementById('search-suggestions');
    if (el) el.style.display = 'none';
}

function renderSuggestions(results) {
    const el = document.getElementById('search-suggestions');
    if (!el) return;

    if (!results.length) {
        el.innerHTML = `<div style="padding:14px 16px;font-size:13px;color:#94a3b8;text-align:center;">Tidak menemukan hasil berdasarkan pencarian Anda.</div>`;
        el.style.display = 'block';
        return;
    }

    el.innerHTML = results.map((r, i) => {
        const icon      = r.type === 'pipe' ? ICON_PIPE : ICON_MANHOLE;
        const tagBg     = r.type === 'pipe' ? '#e0f9ff'  : '#ede9fe';
        const tagColor  = r.type === 'pipe' ? '#0e7490'  : '#6d28d9';
        const tagLabel  = r.type === 'pipe' ? 'PIPA'     : 'MANHOLE';
        const borderTop = i > 0 ? 'border-top:1px solid #f1f5f9;' : '';
        return `<div class="search-suggestion-item" data-index="${i}"
            style="display:flex;align-items:center;gap:10px;padding:10px 14px;cursor:pointer;transition:background .12s;${borderTop}">
            ${icon}
            <div style="min-width:0;">
                <div style="font-size:13px;font-weight:600;color:#1e293b;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">${r.label}</div>
                ${r.sublabel ? `<div style="font-size:11px;color:#94a3b8;margin-top:1px;">${r.sublabel}</div>` : ''}
            </div>
            <div style="margin-left:auto;font-size:10px;font-weight:700;padding:3px 7px;border-radius:5px;background:${tagBg};color:${tagColor}">${tagLabel}</div>
        </div>`;
    }).join('');

    // Hover styles via JS (no stylesheet dependency)
    el.querySelectorAll('.search-suggestion-item').forEach((item, i) => {
        item.addEventListener('mouseenter', () => item.style.background = '#f8fafc');
        item.addEventListener('mouseleave', () => item.style.background = '');
        item.addEventListener('mousedown', e => {
            e.preventDefault(); // prevent input blur before click fires
            selectSuggestion(results[i]);
        });
    });

    el.style.display = 'block';
}

function selectSuggestion(result) {
    const input = document.getElementById('search-input');
    if (input) input.value = result.label;
    hideSuggestions();

    map.flyTo(result.latlng, result.zoom, { duration: 1.2 });
    map.once('moveend', () => {
        const layer = result.type === 'pipe' ? pipeLayerMap[result.key] : manholeLayerMap[result.key];
        if (layer) layer.openPopup();
    });
}

function handleSearch() {
    const q = (document.getElementById('search-input').value || '').trim().toLowerCase();
    hideSuggestions();
    if (!q) return;

    for (const feature of currentPipeFeatures) {
        const p        = feature.properties || {};
        const haystack = [p.kode_pipa, p.id_jalur, p.fungsi].filter(Boolean).join(' ').toLowerCase();
        if (!haystack.includes(q)) continue;
        const coordSets = buildPipeCoordSets(feature.geometry);
        if (!coordSets.length) continue;
        const mid = coordSets[0][Math.floor(coordSets[0].length / 2)];
        selectSuggestion({ type: 'pipe', label: p.kode_pipa, latlng: mid, zoom: 17, key: p.kode_pipa });
        return;
    }

    for (const feature of currentManholeFeatures) {
        const p        = feature.properties || {};
        const haystack = [p.kode_manhole, p.desa, p.kecamatan].filter(Boolean).join(' ').toLowerCase();
        if (!haystack.includes(q)) continue;
        const geom = feature.geometry;
        if (geom && geom.type === 'Point') {
            const [lng, lat] = geom.coordinates;
            selectSuggestion({ type: 'manhole', label: p.kode_manhole, latlng: [lat, lng], zoom: 18, key: p.kode_manhole });
            return;
        }
    }

    // Coordinate fallback: "lat, lng"
    const parts = q.split(',').map(s => parseFloat(s.trim()));
    if (parts.length === 2 && !isNaN(parts[0]) && !isNaN(parts[1])) {
        map.flyTo([parts[0], parts[1]], 16, { duration: 1.2 });
    }
}

// ─── Search event listeners ───────────────────────────────────────────────
const debouncedSuggest = debounce(function () {
    const q = (document.getElementById('search-input').value || '').trim();
    if (q.length < 2) { hideSuggestions(); return; }
    renderSuggestions(buildSuggestions(q));
}, 300);

document.getElementById('search-input').addEventListener('input', debouncedSuggest);

document.getElementById('search-input').addEventListener('keydown', e => {
    if (e.key === 'Enter')  handleSearch();
    if (e.key === 'Escape') hideSuggestions();
});

document.getElementById('search-input').addEventListener('blur', () => {
    // Slight delay so mousedown on suggestion fires first
    setTimeout(hideSuggestions, 150);
});

document.getElementById('search-btn').addEventListener('click', handleSearch);

// Close suggestions when clicking anywhere outside the container
document.addEventListener('click', e => {
    const container = document.getElementById('search-bar-container');
    if (container && !container.contains(e.target)) hideSuggestions();
});

// ─── Resize manhole markers on zoom ─────────────────────────────────────
map.on('zoomend', function () {
    const { radius, weight } = getManholeScale();
    manholeLayer.eachLayer(layer => {
        if (layer instanceof L.CircleMarker) {
            layer.setRadius(radius);
            // Only update weight when not hovered (avoid overriding black border)
            if (layer.options.color !== '#000000') layer.setStyle({ weight });
        }
    });
});

// ─── Bootstrap ────────────────────────────────────────────────────────────
Promise.all([
    loadFilters(),
    loadPipes(),
    loadManholes(),
    loadStats(),
]).catch(e => console.error('Bootstrap error:', e));
</script>

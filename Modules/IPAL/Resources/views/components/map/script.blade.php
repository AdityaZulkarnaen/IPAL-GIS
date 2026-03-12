<style>
    .leaflet-bottom.leaflet-right .leaflet-control-zoom {
        margin-bottom: 50px;
        margin-right: 15px;
    }
    @media (max-width: 767px) {
        .leaflet-bottom.leaflet-right .leaflet-control-zoom {
            margin-bottom: 110px;
            margin-right: 10px;
        }
    }
</style>
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
// ─── Map init ─────────────────────────────────────────────────────────────
const map = L.map('map', {
    center: [-7.757, 110.375],
    zoom: 13,
    zoomControl: false,
});

L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
    attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors',
    maxZoom: 19,
}).addTo(map);

L.control.zoom({ position: 'bottomright' }).addTo(map);

// ─── Constants ────────────────────────────────────────────────────────────
const API_BASE           = '/api/ipal';
const API_PIPES_GEOJSON  = API_BASE + '/pipes/geojson';
const API_MHOLE_GEOJSON  = API_BASE + '/manholes/geojson';
const API_PIPES_FILTERS  = API_BASE + '/pipes/filters';
const API_STATISTICS     = API_BASE + '/statistics';
const CACHE_TTL          = 5 * 60 * 1000; // 5 minutes

const COLOR      = { aman:'#22c55e', perbaikan:'#eab308', masalah:'#ef4444' };
const LABEL      = { aman:'AMAN',    perbaikan:'PERBAIKAN', masalah:'MASALAH' };
const BADGE_BG   = { aman:'#22C55E1A', perbaikan:'#fef3c7', masalah:'#fee2e2' };
const BADGE_TEXT = { aman:'#22c55e',   perbaikan:'#a16207', masalah:'#dc2626' };

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
    // Strip empty values
    const clean = Object.fromEntries(
        Object.entries(params).filter(([, v]) => v !== '' && v != null)
    );
    const key = cacheKey(url, clean);
    const cached = cacheGet(key);
    if (cached) return cached;

    const query = new URLSearchParams(clean).toString();
    const fullUrl = query ? url + '?' + query : url;
    const resp = await fetch(fullUrl);
    if (!resp.ok) throw new Error('API error ' + resp.status + ': ' + fullUrl);
    const data = await resp.json();
    cacheSet(key, data);
    return data;
}

// ─── Layer groups ─────────────────────────────────────────────────────────
const pipeLayer    = L.layerGroup().addTo(map);
const manholeLayer = L.layerGroup().addTo(map);

// ─── In-flight feature stores (for search) ────────────────────────────────
let currentPipeFeatures    = [];
let currentManholeFeatures = [];

// ─── Layer lookup maps (kode → Leaflet layer, for popup-on-select) ────────
const pipeLayerMap    = {};
const manholeLayerMap = {};

// ─── Loading overlay helper ───────────────────────────────────────────────
function setLoading(active) {
    const el = document.getElementById('map-loading');
    if (el) el.style.display = active ? 'flex' : 'none';
}

// ─── Color helpers ────────────────────────────────────────────────────────
function statusColor(status) {
    return COLOR[status] || '#64748b';
}

// ─── Geometry → Leaflet LatLng arrays ────────────────────────────────────
// Returns array of coordinate arrays (each is [[lat,lng], ...])
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

// ─── Popup HTML builders ──────────────────────────────────────────────────
const LAPOR_BTN = `<button onclick="return false;" style="width:100%;padding:9px;background:#FFE2E2;color:#9F0712;border:none;border-radius:8px;font-size:13px;font-weight:400;cursor:pointer;font-family:inherit;display:flex;align-items:center;justify-content:center;gap:6px;"><svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" width="16" height="16" style="flex-shrink:0;"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126ZM12 15.75h.007v.008H12v-.008Z"/></svg>Lapor Masalah</button>`;

function buildPipePopup(p, coordStr) {
    const status = (p.status || '').toLowerCase();
    return `<div style="font-family:'Montserrat',sans-serif;font-size:13px;min-width:260px;">
        <div style="background:#f8fafc;padding:20px 16px 14px;border-bottom:1px solid #e2e8f0;">
            <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:6px;">
                <span style="font-size:10px;font-weight:700;color:#94a3b8;text-transform:uppercase;letter-spacing:.06em;">KODE PIPA</span>
                <span style="font-size:10px;font-weight:700;padding:4px 9px;border-radius:5px;background:${BADGE_BG[status] || '#f1f5f9'};color:${BADGE_TEXT[status] || '#64748b'};">${LABEL[status] || (p.status || '—').toUpperCase()}</span>
            </div>
            <div style="font-size:18px;font-weight:700;color:#1e293b;">${p.kode_pipa || '—'}</div>
        </div>
        <div style="padding:14px 16px;">
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:10px;margin-bottom:10px;">
                <div><div style="font-size:10px;font-weight:700;color:#94a3b8;text-transform:uppercase;margin-bottom:3px;">Fungsi</div><div style="font-weight:600;color:#334155;">${p.fungsi || '—'}</div></div>
                <div><div style="font-size:10px;font-weight:700;color:#94a3b8;text-transform:uppercase;margin-bottom:3px;">Diameter</div><div style="font-weight:600;color:#334155;">${p.pipe_dia != null ? p.pipe_dia + ' mm' : '—'}</div></div>
                <div><div style="font-size:10px;font-weight:700;color:#94a3b8;text-transform:uppercase;margin-bottom:3px;">Panjang</div><div style="font-weight:600;color:#334155;">${p.length_km != null ? Number(p.length_km).toFixed(3) + ' km' : '—'}</div></div>
                <div><div style="font-size:10px;font-weight:700;color:#94a3b8;text-transform:uppercase;margin-bottom:3px;">Koordinat</div><div style="font-size:11px;color:#64748b;">${coordStr}</div></div>
            </div>
            <div style="margin-bottom:14px;"><div style="font-size:10px;font-weight:700;color:#94a3b8;text-transform:uppercase;margin-bottom:3px;">Wilayah</div><div style="font-weight:500;color:#334155;">${p.wilayah || '—'}</div></div>
            ${LAPOR_BTN}
        </div>
    </div>`;
}

function buildManholePopup(p) {
    const status = (p.status || '').toLowerCase();
    return `<div style="font-family:'Montserrat',sans-serif;font-size:13px;min-width:250px;">
        <div style="background:#f8fafc;padding:20px 16px 14px;border-bottom:1px solid #e2e8f0;">
            <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:6px;">
                <span style="font-size:10px;font-weight:700;color:#94a3b8;text-transform:uppercase;letter-spacing:.06em;">KODE MANHOLE</span>
                <span style="font-size:10px;font-weight:700;padding:4px 9px;border-radius:5px;background:${BADGE_BG[status] || '#f1f5f9'};color:${BADGE_TEXT[status] || '#64748b'};">${LABEL[status] || (p.status || '—').toUpperCase()}</span>
            </div>
            <div style="font-size:18px;font-weight:700;color:#1e293b;">${p.kode_manhole || '—'}</div>
        </div>
        <div style="padding:14px 16px;">
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:10px;margin-bottom:10px;">
                <div><div style="font-size:10px;font-weight:700;color:#94a3b8;text-transform:uppercase;margin-bottom:3px;">Kondisi</div><div style="font-weight:600;color:#334155;">${p.kondisi_mh || '—'}</div></div>
                <div><div style="font-size:10px;font-weight:700;color:#94a3b8;text-transform:uppercase;margin-bottom:3px;">Klasifikasi</div><div style="font-weight:600;color:#334155;">${p.klasifikasi || '—'}</div></div>
                <div><div style="font-size:10px;font-weight:700;color:#94a3b8;text-transform:uppercase;margin-bottom:3px;">Desa</div><div style="font-weight:500;color:#334155;">${p.desa || '—'}</div></div>
                <div><div style="font-size:10px;font-weight:700;color:#94a3b8;text-transform:uppercase;margin-bottom:3px;">Kecamatan</div><div style="font-weight:500;color:#334155;">${p.kecamatan || '—'}</div></div>
            </div>
            ${LAPOR_BTN}
        </div>
    </div>`;
}

// ─── Shared layer-draw helpers ────────────────────────────────────────────
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
        const line = L.polyline(coords, {
            color, weight: 5, opacity: 0.88,
            lineCap: 'round', lineJoin: 'round',
        });
        if (idx === 0) {
            line.bindPopup(buildPipePopup(p, coordStr), { maxWidth: 300, minWidth: 280 });
            if (p.kode_pipa) pipeLayerMap[p.kode_pipa] = line;
        }
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

    const marker = L.circleMarker([lat, lng], {
        radius: 7, fillColor: color,
        color: '#ffffff', weight: 2.5,
        opacity: 1, fillOpacity: 0.9,
    })
    .bindTooltip(
        `<b>${p.kode_manhole || '—'}</b><br><span style="font-size:11px;">${p.kecamatan || p.desa || ''}</span>`,
        { direction: 'top', offset: [0, -8] }
    )
    .bindPopup(buildManholePopup(p), { maxWidth: 290, minWidth: 270 })
    .addTo(layer);
    if (p.kode_manhole) manholeLayerMap[p.kode_manhole] = marker;
}

// ─── Render pipes from GeoJSON FeatureCollection ──────────────────────────
function renderPipes(geojson) {
    pipeLayer.clearLayers();
    Object.keys(pipeLayerMap).forEach(k => delete pipeLayerMap[k]);
    currentPipeFeatures = geojson.features || [];
    currentPipeFeatures.forEach(f => drawPipeFeature(f, pipeLayer));
}

// ─── Render manholes from GeoJSON FeatureCollection ───────────────────────
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
        const data = await apiFetch(API_PIPES_FILTERS);
        const fungsiList = data?.data?.fungsi || [];

        const selJenis = document.getElementById('filter-jenis');

        fungsiList.forEach(v => {
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
        const resp  = await apiFetch(API_STATISTICS);
        const data  = resp?.data || {};
        const mh    = data.manhole || {};
        const pipa  = data.pipa   || {};

        function setText(id, val) {
            const el = document.getElementById(id);
            if (el) el.textContent = val != null ? Number(val).toLocaleString('id-ID') : '—';
        }

        const panjang = pipa.total_panjang_km != null
            ? Number(pipa.total_panjang_km).toLocaleString('id-ID', { minimumFractionDigits: 1, maximumFractionDigits: 1 })
            : '—';
        const elPanjang = document.getElementById('stat-total-panjang');
        if (elPanjang) elPanjang.textContent = panjang;

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

// ─── Current active filters ───────────────────────────────────────────────
const activeStatuses = new Set(['aman', 'perbaikan', 'masalah']);
let activeFungsi  = '';

function buildPipeFilters() {
    return { fungsi: activeFungsi };
}

// Status filtering is always client-side (no re-fetch). Re-draws using the
// already-fetched currentPipe/ManholeFeatures filtered by activeStatuses.
function applyStatusVisibility() {
    pipeLayer.clearLayers();
    currentPipeFeatures.forEach(f => {
        const status = (f.properties?.status || '').toLowerCase();
        if (activeStatuses.has(status)) drawPipeFeature(f, pipeLayer);
    });

    manholeLayer.clearLayers();
    currentManholeFeatures.forEach(f => {
        const status = (f.properties?.status || '').toLowerCase();
        if (activeStatuses.has(status)) drawManholeFeature(f, manholeLayer);
    });
}

// ─── Filter event listeners ───────────────────────────────────────────────
document.querySelectorAll('.status-btn').forEach(btn => {
    btn.addEventListener('click', () => {
        const s = btn.dataset.status;
        if (activeStatuses.has(s)) { activeStatuses.delete(s); btn.classList.add('inactive'); }
        else                        { activeStatuses.add(s);    btn.classList.remove('inactive'); }
        // Status filtering is client-side (no re-fetch needed)
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

// Icon SVGs for suggestion items
const ICON_PIPE = `<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#13C8EC" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" style="flex-shrink:0;"><path d="M3 6h18M3 18h18M6 6v12M18 6v12"/></svg>`;
const ICON_MANHOLE = `<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#6366f1" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" style="flex-shrink:0;"><circle cx="12" cy="12" r="10"/><circle cx="12" cy="12" r="4"/></svg>`;

function buildSuggestions(q) {
    if (!q || q.length < 2) return [];
    const results = [];
    const lower = q.toLowerCase();

    for (const feature of currentPipeFeatures) {
        const p = feature.properties || {};
        const haystack = [p.kode_pipa, p.id_jalur, p.fungsi]
            .filter(Boolean).join(' ').toLowerCase();
        if (!haystack.includes(lower)) continue;

        const coordSets = buildPipeCoordSets(feature.geometry);
        if (!coordSets.length) continue;
        const firstSet = coordSets[0];
        const mid = firstSet[Math.floor(firstSet.length / 2)];

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
        const p = feature.properties || {};
        const haystack = [p.kode_manhole, p.desa, p.kecamatan]
            .filter(Boolean).join(' ').toLowerCase();
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

function renderSuggestions(results, query) {
    const el = document.getElementById('search-suggestions');
    if (!el) return;

    if (!results.length) {
        el.innerHTML = `<div style="padding:14px 16px;font-size:13px;color:#94a3b8;text-align:center;">Tidak menemukan hasil berdasarkan pencarian Anda.</div>`;
        el.style.display = 'block';
        return;
    }

    el.innerHTML = results.map((r, i) => {
        const icon = r.type === 'pipe' ? ICON_PIPE : ICON_MANHOLE;
        const borderTop = i > 0 ? 'border-top:1px solid #f1f5f9;' : '';
        return `<div class="search-suggestion-item" data-index="${i}"
            style="display:flex;align-items:center;gap:10px;padding:10px 14px;cursor:pointer;transition:background .12s;${borderTop}">
            ${icon}
            <div style="min-width:0;">
                <div style="font-size:13px;font-weight:600;color:#1e293b;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">${r.label}</div>
                ${r.sublabel ? `<div style="font-size:11px;color:#94a3b8;margin-top:1px;">${r.sublabel}</div>` : ''}
            </div>
            <div style="margin-left:auto;font-size:10px;font-weight:700;padding:3px 7px;border-radius:5px;background:${r.type === 'pipe' ? '#e0f9ff' : '#ede9fe'};color:${r.type === 'pipe' ? '#0e7490' : '#6d28d9'}">${r.type === 'pipe' ? 'PIPA' : 'MANHOLE'}</div>
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

    const openPopup = () => {
        const layer = result.type === 'pipe'
            ? pipeLayerMap[result.key]
            : manholeLayerMap[result.key];
        if (layer) {
            layer.openPopup();
        }
    };

    map.flyTo(result.latlng, result.zoom, { duration: 1.2 });
    map.once('moveend', openPopup);
}

function handleSearch() {
    const q = (document.getElementById('search-input').value || '').trim().toLowerCase();
    hideSuggestions();
    if (!q) return;

    // Try exact/first match in pipes
    for (const feature of currentPipeFeatures) {
        const p = feature.properties || {};
        const haystack = [p.kode_pipa, p.id_jalur, p.fungsi]
            .filter(Boolean).join(' ').toLowerCase();
        if (haystack.includes(q)) {
            const coordSets = buildPipeCoordSets(feature.geometry);
            if (coordSets.length) {
                const firstSet = coordSets[0];
                const mid = firstSet[Math.floor(firstSet.length / 2)];
                selectSuggestion({ type: 'pipe', label: p.kode_pipa, latlng: mid, zoom: 17, key: p.kode_pipa });
                return;
            }
        }
    }

    // Try manholes
    for (const feature of currentManholeFeatures) {
        const p = feature.properties || {};
        const haystack = [p.kode_manhole, p.desa, p.kecamatan]
            .filter(Boolean).join(' ').toLowerCase();
        if (haystack.includes(q)) {
            const geom = feature.geometry;
            if (geom && geom.type === 'Point') {
                const [lng, lat] = geom.coordinates;
                selectSuggestion({ type: 'manhole', label: p.kode_manhole, latlng: [lat, lng], zoom: 18, key: p.kode_manhole });
                return;
            }
        }
    }

    // Coordinate fallback: "lat, lng"
    const parts = q.split(',').map(s => parseFloat(s.trim()));
    if (parts.length === 2 && !isNaN(parts[0]) && !isNaN(parts[1])) {
        map.flyTo([parts[0], parts[1]], 16, { duration: 1.2 });
    }
}

// Debounced input handler
const debouncedSuggest = debounce(function () {
    const q = (document.getElementById('search-input').value || '').trim();
    if (q.length < 2) { hideSuggestions(); return; }
    renderSuggestions(buildSuggestions(q), q);
}, 300);

document.getElementById('search-input').addEventListener('input', debouncedSuggest);

document.getElementById('search-input').addEventListener('keydown', e => {
    if (e.key === 'Enter')  { handleSearch(); }
    if (e.key === 'Escape') { hideSuggestions(); }
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

// ─── Bootstrap ────────────────────────────────────────────────────────────
Promise.all([
    loadFilters(),
    loadPipes(),
    loadManholes(),
    loadStats(),
]).catch(e => console.error('Bootstrap error:', e));
</script>

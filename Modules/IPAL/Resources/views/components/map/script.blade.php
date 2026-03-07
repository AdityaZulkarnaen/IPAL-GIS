66+++++++++++++++++<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
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

// ─── Dummy pipe segments ──────────────────────────────────────────────────
const segments = [
    { id:'MH-001', jenis:'PVC 12"',  wilayah:'Kec. Mlati, Sleman',   status:'aman',
      coords:[[-7.738,110.352],[-7.743,110.360],[-7.749,110.368]] },
    { id:'MH-002', jenis:'PVC 8"',   wilayah:'Kec. Mlati, Sleman',   status:'perbaikan',
      coords:[[-7.749,110.368],[-7.755,110.374],[-7.761,110.380]] },
    { id:'MH-003', jenis:'Beton 15"',wilayah:'Kec. Depok, Sleman',   status:'masalah',
      coords:[[-7.759,110.372],[-7.754,110.379],[-7.750,110.386]] },
    { id:'MH-004', jenis:'PVC 12"',  wilayah:'Kec. Depok, Sleman',   status:'aman',
      coords:[[-7.740,110.385],[-7.745,110.392],[-7.751,110.398]] },
    { id:'MH-005', jenis:'Besi 10"', wilayah:'Kec. Gamping, Sleman', status:'perbaikan',
      coords:[[-7.770,110.344],[-7.773,110.351],[-7.777,110.358]] },
    { id:'MH-006', jenis:'PVC 8"',   wilayah:'Kec. Gamping, Sleman', status:'masalah',
      coords:[[-7.777,110.358],[-7.781,110.364],[-7.785,110.369]] },
    { id:'MH-007', jenis:'PVC 12"',  wilayah:'Kec. Ngaglik, Sleman', status:'aman',
      coords:[[-7.720,110.393],[-7.726,110.399],[-7.731,110.405]] },
    { id:'MH-008', jenis:'Beton 15"',wilayah:'Kec. Ngaglik, Sleman', status:'aman',
      coords:[[-7.731,110.405],[-7.736,110.410],[-7.741,110.414]] },
    { id:'MH-009', jenis:'PVC 8"',   wilayah:'Kec. Mlati, Sleman',   status:'aman',
      coords:[[-7.743,110.360],[-7.743,110.372],[-7.749,110.380]] },
    { id:'MH-010', jenis:'Besi 10"', wilayah:'Kec. Depok, Sleman',   status:'masalah',
      coords:[[-7.762,110.392],[-7.766,110.397],[-7.770,110.402]] },
];

const COLOR      = { aman:'#22c55e', perbaikan:'#eab308', masalah:'#ef4444' };
const LABEL      = { aman:'AMAN',    perbaikan:'PERBAIKAN', masalah:'MASALAH' };
const BADGE_BG   = { aman:'#dcfce7', perbaikan:'#fef3c7', masalah:'#fee2e2' };
const BADGE_TEXT = { aman:'#15803d', perbaikan:'#a16207', masalah:'#dc2626' };

// ─── Draw pipe polylines ──────────────────────────────────────────────────
const polylines = [];

segments.forEach(seg => {
    const mid = seg.coords[Math.floor(seg.coords.length / 2)];
    const coordStr = mid[0].toFixed(6) + ', ' + mid[1].toFixed(6);

    const line = L.polyline(seg.coords, {
        color: COLOR[seg.status], weight: 5, opacity: 0.88,
        lineCap: 'round', lineJoin: 'round',
    }).addTo(map);

    line.bindPopup(`
        <div style="font-family:'Montserrat',sans-serif;font-size:13px;">
            <div style="background:#f8fafc;padding:14px 16px;border-bottom:1px solid #e2e8f0;">
                <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:6px;">
                    <span style="font-size:10px;font-weight:700;color:#94a3b8;text-transform:uppercase;letter-spacing:.06em;">ID SEGMEN</span>
                    <span style="font-size:10px;font-weight:700;padding:2px 9px;border-radius:999px;background:${BADGE_BG[seg.status]};color:${BADGE_TEXT[seg.status]};">${LABEL[seg.status]}</span>
                </div>
                <div style="font-size:20px;font-weight:700;color:#1e293b;">${seg.id}</div>
            </div>
            <div style="padding:14px 16px;">
                <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;margin-bottom:12px;">
                    <div>
                        <div style="font-size:10px;font-weight:700;color:#94a3b8;text-transform:uppercase;margin-bottom:3px;">Jenis</div>
                        <div style="font-weight:600;color:#334155;">${seg.jenis}</div>
                    </div>
                    <div>
                        <div style="font-size:10px;font-weight:700;color:#94a3b8;text-transform:uppercase;margin-bottom:3px;">Koordinat</div>
                        <div style="font-size:11px;color:#64748b;">${coordStr}</div>
                    </div>
                </div>
                <div style="margin-bottom:14px;">
                    <div style="font-size:10px;font-weight:700;color:#94a3b8;text-transform:uppercase;margin-bottom:3px;">Wilayah</div>
                    <div style="font-weight:500;color:#334155;">${seg.wilayah}</div>
                </div>
                <button onclick="return false;" style="width:100%;padding:9px;background:#3b82f6;color:#fff;border:none;border-radius:8px;font-size:13px;font-weight:600;cursor:pointer;font-family:inherit;display:flex;align-items:center;justify-content:center;gap:6px;">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/>
                    </svg>
                    Detail Lengkap
                </button>
            </div>
        </div>`, { maxWidth: 290, minWidth: 270 });

    polylines.push({ line, seg });
});

// ─── Bak Kontrol markers ──────────────────────────────────────────────────
[
    { id:'BK-01', coords:[-7.742,110.357], wilayah:'Kec. Mlati, Sleman'   },
    { id:'BK-02', coords:[-7.754,110.376], wilayah:'Kec. Mlati, Sleman'   },
    { id:'BK-03', coords:[-7.763,110.376], wilayah:'Kec. Depok, Sleman'   },
    { id:'BK-04', coords:[-7.748,110.395], wilayah:'Kec. Depok, Sleman'   },
    { id:'BK-05', coords:[-7.774,110.353], wilayah:'Kec. Gamping, Sleman' },
    { id:'BK-06', coords:[-7.781,110.366], wilayah:'Kec. Gamping, Sleman' },
    { id:'BK-07', coords:[-7.723,110.396], wilayah:'Kec. Ngaglik, Sleman' },
    { id:'BK-08', coords:[-7.736,110.411], wilayah:'Kec. Ngaglik, Sleman' },
].forEach(bk => {
    L.circleMarker(bk.coords, {
        radius: 7, fillColor: '#22d3ee',
        color: '#ffffff', weight: 2.5,
        opacity: 1, fillOpacity: 0.9,
    })
    .bindTooltip(`<b>${bk.id}</b><br><span style="font-size:11px;">${bk.wilayah}</span>`,
        { direction: 'top', offset: [0, -8] })
    .addTo(map);
});

// ─── Filter logic ─────────────────────────────────────────────────────────
const activeStatuses = new Set(['aman', 'perbaikan', 'masalah']);
let activeJenis = '', activeWilayah = '';

function applyFilters() {
    polylines.forEach(({ line, seg }) => {
        const ok = activeStatuses.has(seg.status)
                && (activeJenis   === '' || seg.jenis   === activeJenis)
                && (activeWilayah === '' || seg.wilayah.toLowerCase().includes(activeWilayah.toLowerCase()));
        ok ? map.addLayer(line) : map.removeLayer(line);
    });
}

document.querySelectorAll('.status-btn').forEach(btn => {
    btn.addEventListener('click', () => {
        const s = btn.dataset.status;
        if (activeStatuses.has(s)) { activeStatuses.delete(s); btn.classList.add('inactive'); }
        else                        { activeStatuses.add(s);    btn.classList.remove('inactive'); }
        applyFilters();
    });
});

document.getElementById('filter-jenis').addEventListener('change', function () {
    activeJenis = this.value; applyFilters();
});

document.getElementById('filter-wilayah').addEventListener('input', function () {
    activeWilayah = this.value; applyFilters();
});

// ─── Bottom search ────────────────────────────────────────────────────────
function handleSearch() {
    const q = document.getElementById('search-input').value.trim().toLowerCase();
    if (!q) return;

    const hit = segments.find(s =>
        s.id.toLowerCase().includes(q) || s.wilayah.toLowerCase().includes(q)
    );
    if (hit) {
        const mid = hit.coords[Math.floor(hit.coords.length / 2)];
        map.flyTo(mid, 16, { duration: 1.2 });
        const pl = polylines.find(p => p.seg.id === hit.id);
        if (pl) setTimeout(() => pl.line.openPopup(mid), 1300);
        return;
    }

    // Coordinate fallback: "lat, lng"
    const parts = q.split(',').map(p => parseFloat(p.trim()));
    if (parts.length === 2 && !isNaN(parts[0]) && !isNaN(parts[1])) {
        map.flyTo([parts[0], parts[1]], 16, { duration: 1.2 });
    }
}

document.getElementById('search-btn').addEventListener('click', handleSearch);
document.getElementById('search-input').addEventListener('keydown', e => {
    if (e.key === 'Enter') handleSearch();
});
</script>

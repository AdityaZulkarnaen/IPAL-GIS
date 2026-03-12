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
    const params   = new URLSearchParams(window.location.search);
    const aType    = params.get('type');    // 'pipa' | 'manhole'
    const aId      = params.get('id');      // numeric DB id
    const aKode    = params.get('kode');    // human-readable code (pre-fill while loading)
    const aCoord   = params.get('coord');   // "lat,lng" string (pre-fill while loading)
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

    /* ── Geometry helpers ───────────────────────────────────── */
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

    /* ── Popup HTML builders ────────────────────────────────── */
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
        const status   = (p.status || '').toLowerCase();
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

        const status    = (p.status || '').toLowerCase();
        const color     = statusColor(status);
        const boundsArr = [];

        coordSets.forEach((coords, idx) => {
            L.polyline(coords, {
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

        const firstSet = coordSets[0];
        const mid      = firstSet[Math.floor(firstSet.length / 2)];
        const coordStr = mid ? `${mid[0].toFixed(6)}, ${mid[1].toFixed(6)}` : (aCoord || '—');

        document.getElementById('field-id-ipal').value   = p.kode_pipa || aKode || '—';
        document.getElementById('field-koordinat').value = coordStr;

        setOverlay(`ID PIPA: ${p.kode_pipa || '—'}`, [p.wilayah, p.fungsi].filter(Boolean).join(' · '));

        if (boundsArr.length) {
            miniMap.fitBounds(boundsArr, { padding: [40, 40], maxZoom: 18 });
        }
    }

    function renderManhole(p) {
        const lat = parseFloat(p.latitude);
        const lng = parseFloat(p.longitude);

        if (isNaN(lat) || isNaN(lng)) {
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
    window.handleFotoChange = function (input) {
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

    window.removeFile = function (idx) {
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
    document.getElementById('lapor-form').addEventListener('submit', async function (e) {
        e.preventDefault();
        hideError();

        const deskripsi = document.getElementById('field-deskripsi').value.trim();
        const errorDiv  = document.getElementById('deskripsi-error');

        if (!deskripsi) {
            document.getElementById('field-deskripsi').classList.add('error');
            errorDiv.style.display = 'block';
            document.getElementById('field-deskripsi').focus();
            return;
        }
        document.getElementById('field-deskripsi').classList.remove('error');
        errorDiv.style.display = 'none';

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

        const btn     = document.getElementById('submit-btn');
        const icon    = document.getElementById('submit-icon');
        const spinner = document.getElementById('submit-spinner');
        const label   = document.getElementById('submit-label');
        btn.disabled          = true;
        icon.style.display    = 'none';
        spinner.style.display = 'inline';
        label.textContent     = 'Mengirim…';

        try {
            const res  = await fetch(`${BASE_URL}/api/ipal/aduan`, {
                method: 'POST',
                headers: { 'X-Requested-With': 'XMLHttpRequest' },
                body: formData,
            });
            const json = await res.json();

            if (res.ok && json.success) {
                const banner = document.getElementById('success-banner');
                document.getElementById('nomor-tiket').textContent = json.data.nomor_tiket || '';
                banner.style.display = 'flex';
                banner.scrollIntoView({ behavior: 'smooth', block: 'start' });
                document.getElementById('lapor-form').style.opacity      = '.45';
                document.getElementById('lapor-form').style.pointerEvents = 'none';
            } else {
                const errors = json.data;
                if (errors && typeof errors === 'object') {
                    showError(Object.values(errors).flat().join(' '));
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

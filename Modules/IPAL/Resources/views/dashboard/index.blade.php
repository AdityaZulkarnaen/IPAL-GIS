@extends('ipal::layouts.main')

@push('ipal-styles')
<style>
    @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap');

    .ipal-dashboard-page {
        font-family: 'Plus Jakarta Sans', sans-serif;
        background: #f5f7fb;
        min-height: 100%;
        padding: 18px 22px 26px;
    }

    .ipal-dashboard-shell {
        width: 100%;
        max-width: 1160px;
    }

    .ipal-crumb {
        font-size: 12px;
        color: #7a8498;
        font-weight: 500;
        margin-bottom: 6px;
    }

    .ipal-crumb b {
        color: #172039;
        font-weight: 700;
    }

    .ipal-page-title {
        margin: 0 0 16px;
        color: #172039;
        font-size: 31px;
        line-height: 1.15;
        font-weight: 800;
        letter-spacing: -0.01em;
    }

    .ipal-kpi-grid {
        display: grid;
        grid-template-columns: repeat(3, minmax(0, 1fr));
        gap: 14px;
        margin-bottom: 14px;
    }

    .ipal-kpi-card {
        position: relative;
        border: 1px solid #e8edf5;
        border-radius: 12px;
        background: #ffffff;
        min-height: 108px;
        padding: 16px;
        overflow: hidden;
    }

    .ipal-kpi-card::after {
        content: '';
        position: absolute;
        right: 0;
        top: 0;
        width: 52%;
        height: 100%;
        background-image:
            linear-gradient(#f2f4f8 1px, transparent 1px),
            linear-gradient(90deg, #f2f4f8 1px, transparent 1px);
        background-size: 26px 26px;
        opacity: 0.92;
        pointer-events: none;
    }

    .ipal-kpi-top {
        display: flex;
        align-items: center;
        gap: 10px;
        margin-bottom: 10px;
        position: relative;
        z-index: 1;
    }

    .ipal-kpi-icon {
        width: 34px;
        height: 34px;
        border-radius: 10px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
    }

    .ipal-kpi-icon svg {
        width: 20px;
        height: 20px;
    }

    .ipal-kpi-icon.icon-green {
        background: rgba(52, 211, 153, 0.18);
        color: #00b87f;
    }

    .ipal-kpi-icon.icon-purple {
        background: rgba(167, 139, 250, 0.22);
        color: #7c4dff;
    }

    .ipal-kpi-icon.icon-blue {
        background: rgba(125, 211, 252, 0.26);
        color: #0ea5e9;
    }

    .ipal-kpi-label {
        font-size: 18px;
        font-weight: 700;
        color: #1e2a4a;
        line-height: 1.2;
    }

    .ipal-kpi-value {
        position: relative;
        z-index: 1;
        font-size: 40px;
        font-weight: 800;
        color: #16213f;
        letter-spacing: -0.02em;
        line-height: 1;
    }

    .ipal-kpi-value span {
        font-size: 31px;
        color: #6f7b93;
        margin-left: 4px;
        font-weight: 600;
    }

    .ipal-dashboard-grid {
        display: grid;
        grid-template-columns: 1.03fr 1.47fr;
        gap: 14px;
        align-items: start;
    }

    .ipal-panel,
    .ipal-status-card {
        background: #fff;
        border: 1px solid #e8edf5;
        border-radius: 12px;
    }

    .ipal-panel {
        padding: 16px;
        min-height: 386px;
    }

    .ipal-panel-title,
    .ipal-status-title {
        margin: 0;
        font-size: 17px;
        font-weight: 700;
        color: #1c2746;
        letter-spacing: -0.01em;
    }

    .ipal-pie-wrap {
        position: relative;
        width: min(100%, 330px);
        margin: 10px auto 14px;
    }

    .ipal-pie-wrap canvas {
        width: 100% !important;
        height: auto !important;
        max-height: 280px;
    }

    .ipal-pie-tag {
        position: absolute;
        background: #f8fafc;
        border: 1px solid #dce5f2;
        border-radius: 10px;
        min-width: 88px;
        padding: 7px 10px 8px;
        box-shadow: 0 6px 18px rgba(15, 23, 42, 0.06);
        opacity: 0;
        visibility: hidden;
        transform: translateY(4px);
        transition: opacity 0.18s ease, transform 0.18s ease, visibility 0.18s ease;
        pointer-events: none;
    }

    .ipal-pie-tag.is-visible {
        opacity: 1;
        visibility: visible;
        transform: translateY(0);
    }

    .ipal-pie-tag .name {
        display: block;
        margin-bottom: 4px;
        font-size: 12px;
        color: #4d5a74;
        font-weight: 600;
    }

    .ipal-pie-tag .meta {
        display: flex;
        gap: 8px;
        align-items: baseline;
        line-height: 1;
    }

    .ipal-pie-tag .pct {
        font-size: 26px;
        font-weight: 800;
        letter-spacing: -0.02em;
    }

    .ipal-pie-tag .count {
        font-size: 21px;
        font-weight: 800;
        color: #1b2748;
    }

    .ipal-pie-tag.glontor {
        left: -4px;
        top: 42%;
    }

    .ipal-pie-tag.induk {
        right: -6px;
        top: 16%;
    }

    .ipal-pie-tag.lateral {
        right: 0;
        bottom: 11%;
    }

    .ipal-pie-tag.glontor .pct { color: #7c4dff; }
    .ipal-pie-tag.induk .pct { color: #22c55e; }
    .ipal-pie-tag.lateral .pct { color: #14aaf5; }

    .ipal-legend {
        display: flex;
        justify-content: center;
        gap: 16px;
        flex-wrap: wrap;
        margin-top: 6px;
    }

    .ipal-legend span {
        font-size: 14px;
        color: #44516b;
        display: inline-flex;
        align-items: center;
        gap: 7px;
    }

    .ipal-dot {
        width: 8px;
        height: 8px;
        border-radius: 50%;
        display: inline-block;
    }

    .dot-lateral { background: #14aaf5; }
    .dot-induk { background: #22c55e; }
    .dot-glontor { background: #7c4dff; }

    .ipal-status-stack {
        display: flex;
        flex-direction: column;
        gap: 14px;
    }

    .ipal-status-card {
        padding: 16px;
    }

    .ipal-status-list {
        margin-top: 12px;
        display: flex;
        flex-direction: column;
        gap: 10px;
    }

    .ipal-status-row {
        display: grid;
        grid-template-columns: 70px minmax(0, 1fr) 34px;
        align-items: center;
        gap: 10px;
    }

    .ipal-status-row .label {
        color: #2f3d5b;
        font-size: 14px;
        font-weight: 600;
    }

    .ipal-status-track {
        width: 100%;
        height: 16px;
        border-radius: 999px;
        background: #eef2f7;
        overflow: hidden;
    }

    .ipal-status-fill {
        display: block;
        height: 100%;
        width: 0;
        transition: width 0.35s ease;
    }

    .fill-baik { background: #22c55e; }
    .fill-perbaikan { background: #f4c101; }
    .fill-rusak { background: #f52859; }

    .ipal-status-row .value {
        text-align: right;
        color: #16213f;
        font-size: 14px;
        font-weight: 700;
    }

    @media (max-width: 1199.98px) {
        .ipal-kpi-label {
            font-size: 16px;
        }

        .ipal-kpi-value {
            font-size: 34px;
        }

        .ipal-kpi-value span {
            font-size: 26px;
        }
    }

    @media (max-width: 991.98px) {
        .ipal-dashboard-page {
            padding: 14px;
        }

        .ipal-kpi-grid {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }

        .ipal-dashboard-grid {
            grid-template-columns: 1fr;
        }

        .ipal-panel {
            min-height: auto;
        }
    }

    @media (max-width: 767.98px) {
        .ipal-page-title {
            font-size: 32px;
            margin-bottom: 14px;
        }

        .ipal-kpi-grid {
            grid-template-columns: 1fr 1fr;
            gap: 10px;
        }

        .ipal-kpi-card {
            padding: 12px;
            min-height: 94px;
        }

        .ipal-kpi-icon {
            width: 30px;
            height: 30px;
            border-radius: 8px;
        }

        .ipal-kpi-label {
            font-size: 12px;
        }

        .ipal-kpi-value {
            font-size: 24px;
        }

        .ipal-kpi-value span {
            font-size: 14px;
        }

        .ipal-panel,
        .ipal-status-card {
            border-radius: 12px;
            padding: 14px;
        }

        .ipal-panel-title,
        .ipal-status-title {
            font-size: 16px;
        }

        .ipal-pie-wrap {
            margin-top: 8px;
            max-width: 284px;
        }

        .ipal-pie-tag {
            min-width: 70px;
            padding: 5px 8px 7px;
        }

        .ipal-pie-tag .name {
            font-size: 11px;
        }

        .ipal-pie-tag .pct {
            font-size: 18px;
        }

        .ipal-pie-tag .count {
            font-size: 16px;
        }

        .ipal-pie-tag.glontor {
            left: -5px;
            top: 44%;
        }

        .ipal-pie-tag.induk {
            right: -4px;
            top: 15%;
        }

        .ipal-pie-tag.lateral {
            right: -2px;
            bottom: 8%;
        }

        .ipal-legend {
            gap: 10px;
        }

        .ipal-legend span {
            font-size: 13px;
        }

        .ipal-status-list {
            gap: 8px;
        }

        .ipal-status-row {
            grid-template-columns: 61px minmax(0, 1fr) 30px;
            gap: 8px;
        }

        .ipal-status-row .label,
        .ipal-status-row .value {
            font-size: 12px;
        }

        .ipal-status-track {
            height: 11px;
        }
    }
</style>
@endpush

@section('content')
<div id="kt_app_content" class="app-content flex-column-fluid p-0">
    <div class="ipal-dashboard-page">
        <div class="ipal-dashboard-shell">
            <p class="ipal-crumb">IPAL <b>&rsaquo;</b> Dashboard</p>
            <h1 class="ipal-page-title">Dashboard</h1>

            <div class="ipal-kpi-grid">
                <div class="ipal-kpi-card">
                    <div class="ipal-kpi-top">
                        <span class="ipal-kpi-icon icon-green">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                                <circle cx="12" cy="12" r="8"></circle>
                                <circle cx="12" cy="12" r="3.2"></circle>
                            </svg>
                        </span>
                        <div class="ipal-kpi-label">Total Manhole</div>
                    </div>
                    <div class="ipal-kpi-value" id="dash-total-manhole">0</div>
                </div>

                <div class="ipal-kpi-card">
                    <div class="ipal-kpi-top">
                        <span class="ipal-kpi-icon icon-purple">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                                <path d="M4 6h16M8 6v12M16 6v12M4 18h16"></path>
                            </svg>
                        </span>
                        <div class="ipal-kpi-label">Total Jalur Pipa</div>
                    </div>
                    <div class="ipal-kpi-value" id="dash-total-pipa">0</div>
                </div>

                <div class="ipal-kpi-card">
                    <div class="ipal-kpi-top">
                        <span class="ipal-kpi-icon icon-blue">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                                <path d="M4 7c2 0 2 1.5 4 1.5S10 7 12 7s2 1.5 4 1.5S18 7 20 7M4 12c2 0 2 1.5 4 1.5s2-1.5 4-1.5 2 1.5 4 1.5 2-1.5 4-1.5M4 17c2 0 2 1.5 4 1.5S10 17 12 17s2 1.5 4 1.5S18 17 20 17"></path>
                            </svg>
                        </span>
                        <div class="ipal-kpi-label">Total Panjang Pipa</div>
                    </div>
                    <div class="ipal-kpi-value"><span id="dash-total-panjang">0</span><span>km</span></div>
                </div>
            </div>

            <div class="ipal-dashboard-grid">
                <section class="ipal-panel">
                    <h2 class="ipal-panel-title">Distribusi Jenis Pipa</h2>
                    <div class="ipal-pie-wrap">
                        <canvas id="dash-pipa-donut" aria-label="Distribusi Jenis Pipa" role="img"></canvas>

                        <div class="ipal-pie-tag glontor" data-tag="glontor">
                            <span class="name">Glontor</span>
                            <div class="meta">
                                <span class="pct" id="tag-glontor-pct">0%</span>
                                <span class="count" id="tag-glontor-total">0</span>
                            </div>
                        </div>

                        <div class="ipal-pie-tag induk" data-tag="induk">
                            <span class="name">Induk</span>
                            <div class="meta">
                                <span class="pct" id="tag-induk-pct">0%</span>
                                <span class="count" id="tag-induk-total">0</span>
                            </div>
                        </div>

                        <div class="ipal-pie-tag lateral" data-tag="lateral">
                            <span class="name">Lateral</span>
                            <div class="meta">
                                <span class="pct" id="tag-lateral-pct">0%</span>
                                <span class="count" id="tag-lateral-total">0</span>
                            </div>
                        </div>
                    </div>

                    <div class="ipal-legend">
                        <span><i class="ipal-dot dot-glontor"></i>Glontor</span>
                        <span><i class="ipal-dot dot-induk"></i>Induk</span>
                        <span><i class="ipal-dot dot-lateral"></i>Lateral</span>
                    </div>
                </section>

                <div class="ipal-status-stack">
                    <section class="ipal-status-card">
                        <h2 class="ipal-status-title">Status Jalur Pipa</h2>
                        <div class="ipal-status-list" id="status-pipa-list">
                            <div class="ipal-status-row" data-key="baik">
                                <span class="label">Aman</span>
                                <span class="ipal-status-track"><i class="ipal-status-fill fill-baik"></i></span>
                                <span class="value">0</span>
                            </div>
                            <div class="ipal-status-row" data-key="perbaikan">
                                <span class="label">Perbaikan</span>
                                <span class="ipal-status-track"><i class="ipal-status-fill fill-perbaikan"></i></span>
                                <span class="value">0</span>
                            </div>
                            <div class="ipal-status-row" data-key="rusak">
                                <span class="label">Rusak</span>
                                <span class="ipal-status-track"><i class="ipal-status-fill fill-rusak"></i></span>
                                <span class="value">0</span>
                            </div>
                        </div>
                    </section>

                    <section class="ipal-status-card">
                        <h2 class="ipal-status-title">Status Manhole</h2>
                        <div class="ipal-status-list" id="status-manhole-list">
                            <div class="ipal-status-row" data-key="baik">
                                <span class="label">Aman</span>
                                <span class="ipal-status-track"><i class="ipal-status-fill fill-baik"></i></span>
                                <span class="value">0</span>
                            </div>
                            <div class="ipal-status-row" data-key="perbaikan">
                                <span class="label">Perbaikan</span>
                                <span class="ipal-status-track"><i class="ipal-status-fill fill-perbaikan"></i></span>
                                <span class="value">0</span>
                            </div>
                            <div class="ipal-status-row" data-key="rusak">
                                <span class="label">Rusak</span>
                                <span class="ipal-status-track"><i class="ipal-status-fill fill-rusak"></i></span>
                                <span class="value">0</span>
                            </div>
                        </div>
                    </section>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('ipal-scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.3/dist/chart.umd.min.js"></script>
<script>
    (function () {
        var FALLBACK = {
            manhole: {
                total: 1234,
                by_status: { baik: 430, perbaikan: 70, rusak: 67 }
            },
            pipa: {
                total: 567,
                total_panjang_km: 1234,
                by_status: { baik: 430, perbaikan: 70, rusak: 67 },
                by_fungsi: { Glontor: 234, Induk: 84, Lateral: 149 }
            }
        };

        var donutChart = null;

        function setActivePipaTag(label) {
            var key = (label || '').toString().trim().toLowerCase();
            document.querySelectorAll('.ipal-pie-tag').forEach(function (tag) {
                var tagKey = (tag.getAttribute('data-tag') || '').toLowerCase();
                tag.classList.toggle('is-visible', !!key && tagKey === key);
            });
        }

        function asNumber(val, fallback) {
            var num = Number(val);
            return Number.isFinite(num) ? num : fallback;
        }

        function formatInt(val) {
            return Number(val || 0).toLocaleString('id-ID');
        }

        function formatKm(val) {
            var num = Number(val || 0);
            var hasDecimal = Math.floor(num) !== num;
            return num.toLocaleString('id-ID', {
                minimumFractionDigits: hasDecimal ? 1 : 0,
                maximumFractionDigits: hasDecimal ? 1 : 0
            });
        }

        function setText(id, value) {
            var el = document.getElementById(id);
            if (el) {
                el.textContent = value;
            }
        }

        function normalizeResponse(payload) {
            var mh = payload && payload.manhole ? payload.manhole : {};
            var pp = payload && payload.pipa ? payload.pipa : {};

            var data = {
                manhole: {
                    total: asNumber(mh.total, FALLBACK.manhole.total),
                    by_status: {
                        baik: asNumber(mh.by_status && mh.by_status.baik, FALLBACK.manhole.by_status.baik),
                        perbaikan: asNumber(mh.by_status && mh.by_status.perbaikan, FALLBACK.manhole.by_status.perbaikan),
                        rusak: asNumber(mh.by_status && mh.by_status.rusak, FALLBACK.manhole.by_status.rusak)
                    }
                },
                pipa: {
                    total: asNumber(pp.total, FALLBACK.pipa.total),
                    total_panjang_km: asNumber(pp.total_panjang_km, FALLBACK.pipa.total_panjang_km),
                    by_status: {
                        baik: asNumber(pp.by_status && pp.by_status.baik, FALLBACK.pipa.by_status.baik),
                        perbaikan: asNumber(pp.by_status && pp.by_status.perbaikan, FALLBACK.pipa.by_status.perbaikan),
                        rusak: asNumber(pp.by_status && pp.by_status.rusak, FALLBACK.pipa.by_status.rusak)
                    },
                    by_fungsi: {
                        Glontor: asNumber(pp.by_fungsi && pp.by_fungsi.Glontor, FALLBACK.pipa.by_fungsi.Glontor),
                        Induk: asNumber(pp.by_fungsi && pp.by_fungsi.Induk, FALLBACK.pipa.by_fungsi.Induk),
                        Lateral: asNumber(pp.by_fungsi && pp.by_fungsi.Lateral, FALLBACK.pipa.by_fungsi.Lateral)
                    }
                }
            };

            return data;
        }

        function paintStatus(containerId, statusData) {
            var root = document.getElementById(containerId);
            if (!root) return;

            var baik = asNumber(statusData && statusData.baik, 0);
            var perbaikan = asNumber(statusData && statusData.perbaikan, 0);
            var rusak = asNumber(statusData && statusData.rusak, 0);

            var max = Math.max(baik, perbaikan, rusak, 1);

            root.querySelectorAll('.ipal-status-row').forEach(function (row) {
                var key = row.getAttribute('data-key');
                var value = key === 'baik' ? baik : (key === 'perbaikan' ? perbaikan : rusak);
                var width = (value / max) * 100;

                var fill = row.querySelector('.ipal-status-fill');
                var text = row.querySelector('.value');

                if (fill) fill.style.width = width + '%';
                if (text) text.textContent = formatInt(value);
            });
        }

        function setPipaTags(data) {
            var glontor = asNumber(data.Glontor, 0);
            var induk = asNumber(data.Induk, 0);
            var lateral = asNumber(data.Lateral, 0);
            var total = Math.max(glontor + induk + lateral, 1);

            function pct(val) {
                return Math.round((val / total) * 100) + '%';
            }

            setText('tag-glontor-total', formatInt(glontor));
            setText('tag-induk-total', formatInt(induk));
            setText('tag-lateral-total', formatInt(lateral));
            setText('tag-glontor-pct', pct(glontor));
            setText('tag-induk-pct', pct(induk));
            setText('tag-lateral-pct', pct(lateral));
        }

        function renderDonut(data) {
            var canvas = document.getElementById('dash-pipa-donut');
            if (!canvas || typeof Chart === 'undefined') return;

            var values = [
                asNumber(data.Glontor, 0),
                asNumber(data.Induk, 0),
                asNumber(data.Lateral, 0)
            ];

            var colors = ['#7c4dff', '#22c55e', '#14aaf5'];

            if (donutChart) {
                donutChart.destroy();
                donutChart = null;
            }

            setActivePipaTag('');

            donutChart = new Chart(canvas.getContext('2d'), {
                type: 'doughnut',
                data: {
                    labels: ['Glontor', 'Induk', 'Lateral'],
                    datasets: [{
                        data: values,
                        backgroundColor: colors,
                        borderColor: '#ffffff',
                        borderWidth: 6,
                        hoverOffset: 0
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: true,
                    cutout: '62%',
                    onHover: function (event, activeElements, chart) {
                        if (event && event.native && chart && chart.canvas) {
                            chart.canvas.style.cursor = activeElements.length ? 'pointer' : 'default';
                        }

                        if (!activeElements.length) {
                            setActivePipaTag('');
                            return;
                        }

                        var index = activeElements[0].index;
                        var hoveredLabel = chart.data.labels[index] || '';
                        setActivePipaTag(hoveredLabel);
                    },
                    plugins: {
                        legend: { display: false },
                        tooltip: { enabled: false }
                    }
                }
            });
        }

        function hydrateDashboard(data) {
            setText('dash-total-manhole', formatInt(data.manhole.total));
            setText('dash-total-pipa', formatInt(data.pipa.total));
            setText('dash-total-panjang', formatKm(data.pipa.total_panjang_km));

            setPipaTags(data.pipa.by_fungsi);
            renderDonut(data.pipa.by_fungsi);
            paintStatus('status-pipa-list', data.pipa.by_status);
            paintStatus('status-manhole-list', data.manhole.by_status);
        }

        async function loadDashboard() {
            try {
                var response = await fetch('/api/ipal/statistics', {
                    method: 'GET',
                    headers: { 'Accept': 'application/json' }
                });

                if (!response.ok) {
                    throw new Error('HTTP ' + response.status);
                }

                var json = await response.json();
                hydrateDashboard(normalizeResponse(json && json.data ? json.data : null));
            } catch (e) {
                hydrateDashboard(normalizeResponse(FALLBACK));
            }
        }

        loadDashboard();
    })();
</script>
@endpush

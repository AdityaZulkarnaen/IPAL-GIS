{{-- Override map.css "html, body { overflow: hidden }" so the page can scroll on mobile --}}
<style>
@media (max-width: 767px) {
    html, body { height: auto !important; overflow: visible !important; overflow-x: hidden !important; }
}
</style>

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
    position: relative;
    flex: 0 0 45%;
    height: 100%;
    min-height: 0;
    overflow-y: auto;
    -webkit-overflow-scrolling: touch;
    padding: 32px 36px;
    background: #fff;
    border-left: 1px solid #e2e8f0;
}

.lapor-success-toast {
    position: absolute;
    top: 16px;
    right: 16px;
    z-index: 20;
    max-width: min(360px, calc(100% - 32px));
    background: #f0fdf4;
    border: 1px solid #bbf7d0;
    border-radius: 12px;
    padding: 14px 16px;
    align-items: center;
    gap: 10px;
    box-shadow: 0 12px 30px rgba(15, 23, 42, 0.12);
    animation: toast-slide-in .25s ease-out;
}

@keyframes toast-slide-in {
    from {
        opacity: 0;
        transform: translateY(-8px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
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

/* ── Leaflet popup override (lapor page only) ── */
#lapor-map .leaflet-popup.ipal-lapor-popup .leaflet-popup-content-wrapper {
    padding: 0;
    border-radius: 12px;
    overflow: hidden;
}
#lapor-map .leaflet-popup.ipal-lapor-popup .leaflet-popup-content {
    margin: 0;
}

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
    .lapor-success-toast { top: 12px; right: 12px; max-width: calc(100% - 24px); }
}
</style>

{{-- ══════════════════════════════════════════════════════════════════
     RIGHT: Form panel  (desktop ~45 %, mobile full)
══════════════════════════════════════════════════════════════════════ --}}
<div class="lapor-form-col">

    {{-- ── Success banner (hidden by default) ── --}}
    <div id="success-banner" class="lapor-success-toast" style="display:none;">
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
                    <input id="field-id-ipal" type="text" readonly value="—" class="lapor-input lapor-readonly" />
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
                    <input id="field-koordinat" type="text" readonly value="—" class="lapor-input lapor-readonly" />
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
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path opacity="0.3" d="M22.7502 12H17.6655C16.9638 11.9821 16.2728 11.8245 15.6328 11.5363C14.9928 11.2481 14.4167 10.8352 13.9382 10.3217C13.4597 9.80817 13.0884 9.20437 12.8461 8.54563C12.6038 7.88688 12.4953 7.18643 12.527 6.48525V1.25H7.24874C5.64063 1.27267 4.10691 1.93109 2.98286 3.08133C1.85881 4.23156 1.23588 5.78005 1.25024 7.38825V16.6118C1.23588 18.22 1.85881 19.7684 2.98286 20.9187C4.10691 22.0689 5.64063 22.7273 7.24874 22.75H16.7302C18.3412 22.7302 19.8788 22.0733 21.0069 20.9231C22.1349 19.7729 22.7617 18.2228 22.7502 16.6118V12Z" fill="#90A1B9"/>
                    <path d="M22.7502 10.3123H17.6654C17.1768 10.2975 16.6961 10.1843 16.2522 9.97935C15.8083 9.77445 15.4104 9.48207 15.0822 9.11973C14.754 8.7574 14.5023 8.33255 14.3421 7.87064C14.182 7.40872 14.1167 6.91924 14.1502 6.4315V1.25L22.7502 10.3123ZM9.12992 10.7208L8.99017 10.6347H8.90417C8.81306 10.593 8.71414 10.571 8.61392 10.5703H8.52792C8.45301 10.5601 8.37708 10.5601 8.30217 10.5703L8.19467 10.6347H8.08717L4.79767 13.3653C4.71351 13.4322 4.6434 13.5151 4.59135 13.6092C4.5393 13.7033 4.50633 13.8067 4.49435 13.9136C4.48236 14.0205 4.49159 14.1286 4.52151 14.2319C4.55142 14.3352 4.60143 14.4316 4.66867 14.5155C4.74097 14.6126 4.83505 14.6914 4.94334 14.7455C5.05162 14.7997 5.1711 14.8277 5.29217 14.8273C5.47653 14.8259 5.65485 14.7614 5.79742 14.6445L7.77542 13.075V18.6543C7.77542 18.8681 7.86036 19.0732 8.01157 19.2244C8.16277 19.3756 8.36784 19.4605 8.58167 19.4605C8.7955 19.4605 9.00057 19.3756 9.15178 19.2244C9.30298 19.0732 9.38792 18.8681 9.38792 18.6543V13.1288L11.1079 14.623C11.1876 14.6938 11.2808 14.7478 11.3818 14.7819C11.4828 14.8159 11.5896 14.8293 11.6959 14.8213C11.8022 14.8133 11.9058 14.784 12.0005 14.7352C12.0953 14.6863 12.1792 14.6189 12.2474 14.537C12.3862 14.37 12.4548 14.1557 12.4387 13.9391C12.4227 13.7226 12.3233 13.5207 12.1614 13.376L9.12992 10.7208Z" fill="#90A1B9"/>
                </svg>
                <p style="margin:0;font-size:13px;color:#94a3b8;">Klik atau drag untuk unggah foto</p>
            </div>
            <input id="foto-input" type="file" accept="image/jpeg,image/jpg,image/png,image/webp"
                   multiple style="display:none;" onchange="handleFotoChange(this)" />
            <div id="foto-preview" style="display:flex;flex-wrap:wrap;gap:8px;margin-top:10px;"></div>
        </div>

        @if(config('ipal.aduan_captcha_enabled'))
        {{-- ── Captcha ── --}}
        <div style="margin-bottom:18px;">
            <label class="lapor-label">Verifikasi</label>
            <div style="background:#f8fafc;border:1px solid #e2e8f0;border-radius:10px;padding:14px 16px;">

                {{-- Loading state --}}
                <div id="captcha-loading"
                     style="display:flex;align-items:center;gap:10px;color:#94a3b8;font-size:13px;">
                    <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24"
                         fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"
                         stroke-linejoin="round" style="animation:spin .8s linear infinite;flex-shrink:0;">
                        <path d="M21 12a9 9 0 1 1-6.219-8.56"/>
                    </svg>
                    Memuat soal verifikasi…
                </div>

                {{-- Question row --}}
                <div id="captcha-ready"
                     style="display:none;align-items:center;gap:10px;flex-wrap:wrap;">
                    <span style="font-size:13px;color:#475569;">Berapa hasil dari</span>
                    <span id="captcha-question"
                          style="font-size:18px;font-weight:800;color:#0f172a;font-family:monospace;
                                 background:#fff;border:1px solid #e2e8f0;border-radius:8px;
                                 padding:4px 14px;letter-spacing:1px;"></span>
                    <span style="font-size:16px;color:#475569;font-weight:700;">= ?</span>
                    <input id="field-captcha-answer" type="number" inputmode="numeric"
                           min="0" max="99" placeholder="jawaban"
                           style="width:90px;padding:7px 12px;border:1.5px solid #e2e8f0;
                                  border-radius:8px;font-size:15px;font-weight:700;text-align:center;
                                  color:#0f172a;outline:none;-moz-appearance:textfield;background:#fff;" />
                    <button type="button" id="captcha-refresh" title="Ganti soal"
                            style="padding:6px 10px;background:none;border:1px solid #e2e8f0;
                                   border-radius:7px;cursor:pointer;color:#64748b;display:flex;
                                   align-items:center;gap:5px;font-size:12px;line-height:1;">
                        <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" viewBox="0 0 24 24"
                             fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"
                             stroke-linejoin="round">
                            <path d="M3 12a9 9 0 0 1 9-9 9.75 9.75 0 0 1 6.74 2.74L21 8"/>
                            <path d="M21 3v5h-5"/>
                            <path d="M21 12a9 9 0 0 1-9 9 9.75 9.75 0 0 1-6.74-2.74L3 16"/>
                            <path d="M8 16H3v5"/>
                        </svg>
                        Ganti
                    </button>
                </div>

                <div id="captcha-error"
                     style="display:none;font-size:12px;color:#dc2626;margin-top:8px;">
                    <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" viewBox="0 0 24 24"
                         fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"
                         stroke-linejoin="round" style="vertical-align:middle;margin-right:3px;">
                        <circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/>
                        <line x1="12" y1="16" x2="12.01" y2="16"/>
                    </svg>
                    <span id="captcha-error-text"></span>
                </div>
            </div>
        </div>
        @endif

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

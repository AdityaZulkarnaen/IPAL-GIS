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
                padding:14px 16px;margin-bottom:18px;align-items:center;gap:10px;">
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

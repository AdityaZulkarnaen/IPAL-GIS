@push('ipal-scripts')
<script>
(function() {
    'use strict';

    const API_BASE = '{{ url('/api/ipal') }}';

    const pipeState = {
        page: 1,
        per_page: 10,
        search: '',
        status: '',
        fungsi: ''
    };

    const manholeState = {
        page: 1,
        per_page: 10,
        search: '',
        status: '',
        kondisi_mh: '',
        wilayah: ''
    };

    const pipeTbody = document.getElementById('pipe-tbody');
    const manholeTbody = document.getElementById('manhole-tbody');
    const pipePagination = document.getElementById('pipe-pagination');
    const manholePagination = document.getElementById('manhole-pagination');
    const pipePaginationInfo = document.getElementById('pipe-pagination-info');
    const manholePaginationInfo = document.getElementById('manhole-pagination-info');
    const uploadInput = document.getElementById('upload-file-input');
    const uploadName = document.getElementById('upload-file-name');
    const uploadSelectBtn = document.getElementById('btn-file-select');
    const uploadCancelBtn = document.getElementById('btn-upload-cancel');
    const uploadDropzone = document.getElementById('geojson-dropzone');

    function initUploadPicker() {
        if (!uploadInput || !uploadSelectBtn || !uploadName || !uploadDropzone) {
            return;
        }

        function updateUploadName() {
            uploadName.textContent = uploadInput.files && uploadInput.files[0] ? uploadInput.files[0].name : '';
        }

        uploadSelectBtn.addEventListener('click', function() {
            uploadInput.click();
        });

        uploadInput.addEventListener('change', updateUploadName);

        uploadDropzone.addEventListener('dragover', function(event) {
            event.preventDefault();
            uploadDropzone.classList.add('border-[#77a6dc]', 'bg-[#f2f7fe]');
        });

        uploadDropzone.addEventListener('dragleave', function() {
            uploadDropzone.classList.remove('border-[#77a6dc]', 'bg-[#f2f7fe]');
        });

        uploadDropzone.addEventListener('drop', function(event) {
            event.preventDefault();
            uploadDropzone.classList.remove('border-[#77a6dc]', 'bg-[#f2f7fe]');
            if (!event.dataTransfer || !event.dataTransfer.files || !event.dataTransfer.files.length) {
                return;
            }
            uploadInput.files = event.dataTransfer.files;
            updateUploadName();
        });

        if (uploadCancelBtn) {
            uploadCancelBtn.addEventListener('click', function() {
                uploadInput.value = '';
                uploadName.textContent = '';
            });
        }
    }

    function escapeHtml(value) {
        const input = String(value == null ? '' : value);
        return input
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#039;');
    }

    function normalizeStatus(value) {
        return String(value || '').trim().toLowerCase();
    }

    function statusBadge(status) {
        const normalized = normalizeStatus(status);
        const map = {
            baik: 'bg-emerald-100 text-emerald-700',
            aman: 'bg-emerald-100 text-emerald-700',
            completed: 'bg-emerald-100 text-emerald-700',
            perbaikan: 'bg-amber-100 text-amber-700',
            'dalam perbaikan': 'bg-amber-100 text-amber-700',
            processing: 'bg-amber-100 text-amber-700',
            rusak: 'bg-rose-100 text-rose-700',
            masalah: 'bg-rose-100 text-rose-700',
            failed: 'bg-rose-100 text-rose-700',
            pending: 'bg-slate-100 text-slate-700'
        };
        const cls = map[normalized] || 'bg-slate-100 text-slate-700';
        const label = status ? escapeHtml(status) : '-';
        return '<span class="inline-flex items-center rounded-full px-2 py-1 text-xs font-semibold ' + cls + '">' + label + '</span>';
    }

    function createOption(value, label) {
        const option = document.createElement('option');
        option.value = value;
        option.textContent = label;
        return option;
    }

    function setSelectOptions(selectEl, values, defaultLabel) {
        if (!selectEl) {
            return;
        }
        selectEl.innerHTML = '';
        selectEl.appendChild(createOption('', defaultLabel));
        (values || []).forEach(function(value) {
            if (value === null || value === '') {
                return;
            }
            selectEl.appendChild(createOption(String(value), String(value)));
        });
    }

    async function fetchJson(path, params) {
        const url = new URL(API_BASE + path);
        Object.entries(params || {}).forEach(function(entry) {
            const key = entry[0];
            const value = entry[1];
            if (value !== '' && value !== null && typeof value !== 'undefined') {
                url.searchParams.set(key, value);
            }
        });

        const response = await fetch(url.toString(), {
            headers: { 'Accept': 'application/json' }
        });

        const json = await response.json();
        if (!response.ok || !json.success) {
            throw new Error('API request failed');
        }
        return json;
    }

    function renderPaginator(container, meta, onSelect) {
        container.innerHTML = '';

        if (!meta || meta.last_page <= 1) {
            return;
        }

        const fragment = document.createDocumentFragment();
        const pages = [];
        const start = Math.max(1, meta.current_page - 2);
        const end = Math.min(meta.last_page, meta.current_page + 2);

        for (let page = start; page <= end; page += 1) {
            pages.push(page);
        }

        function buildButton(label, page, disabled, active) {
            const button = document.createElement('button');
            button.type = 'button';
            button.className = 'h-8 min-w-8 rounded-lg border px-2 text-xs font-semibold ' + (active
                ? 'border-blue-600 bg-blue-600 text-white'
                : 'border-blue-200 bg-white text-blue-700 hover:bg-blue-50');
            button.textContent = label;
            button.disabled = disabled;
            button.addEventListener('click', function() {
                onSelect(page);
            });
            return button;
        }

        fragment.appendChild(buildButton('<', meta.current_page - 1, meta.current_page <= 1, false));
        pages.forEach(function(page) {
            fragment.appendChild(buildButton(String(page), page, false, page === meta.current_page));
        });
        fragment.appendChild(buildButton('>', meta.current_page + 1, meta.current_page >= meta.last_page, false));

        container.appendChild(fragment);
    }

    function paginationInfo(meta) {
        if (!meta || meta.total === 0) {
            return 'Tidak ada data.';
        }
        return null;
    }

    function tableError(colspan, message) {
        return '<tr><td colspan="' + colspan + '" class="py-8 text-center text-sm text-rose-500">' + escapeHtml(message) + '</td></tr>';
    }

    function tableEmpty(colspan, message) {
        return '<tr><td colspan="' + colspan + '" class="py-8 text-center text-sm text-slate-500">' + escapeHtml(message) + '</td></tr>';
    }

    function formatActivity(item) {
        const raw = item.updated_at || item.created_at;
        if (!raw) {
            return '-';
        }
        const date = new Date(raw);
        if (Number.isNaN(date.getTime())) {
            return escapeHtml(raw.replace('T', ' ').replace('.000000Z', ''));
        }
        return escapeHtml(new Intl.DateTimeFormat('id-ID', {
            day: '2-digit',
            month: 'short',
            year: 'numeric',
            hour: '2-digit',
            minute: '2-digit'
        }).format(date));
    }

    async function loadPipeFilters() {
        try {
            const res = await fetchJson('/pipes/filters', {});
            const filters = res.data || {};
            setSelectOptions(document.getElementById('pipe-status'), filters.status, 'Status Semua');
            setSelectOptions(document.getElementById('pipe-fungsi'), filters.fungsi, 'Fungsi Semua');
        } catch (error) {
            console.error('loadPipeFilters', error);
        }
    }

    async function loadManholeFilters() {
        try {
            const res = await fetchJson('/manholes/filters', {});
            const filters = res.data || {};
            setSelectOptions(document.getElementById('manhole-status'), filters.status, 'Status Semua');
            setSelectOptions(document.getElementById('manhole-kondisi'), filters.kondisi_mh, 'Kondisi Semua');
            setSelectOptions(document.getElementById('manhole-wilayah'), filters.wilayah, 'Wilayah Semua');
        } catch (error) {
            console.error('loadManholeFilters', error);
        }
    }

    async function loadPipeTable() {
        pipeTbody.innerHTML = tableEmpty(5, 'Memuat data pipa...');
        try {
            const res = await fetchJson('/pipes', pipeState);
            const pageData = res.data;
            const rows = pageData.data || [];

            if (!rows.length) {
                pipeTbody.innerHTML = tableEmpty(5, 'Data pipa tidak ditemukan.');
            } else {
                pipeTbody.innerHTML = rows.map(function(item) {
                    const diameter = item.pipe_dia ? item.pipe_dia + ' mm' : '-';
                    const panjang = item.length_km ? item.length_km + ' km' : '-';

                    return '<tr class="border-b border-slate-100">' +
                        '<td class="px-4 py-3"><div class="font-semibold text-slate-800">' + escapeHtml(item.kode_pipa || '-') + '</div><div class="text-xs text-slate-500">' + escapeHtml(item.id_jalur || '-') + '</div></td>' +
                        '<td class="px-4 py-3">' + statusBadge(item.status) + '</td>' +
                        '<td class="px-4 py-3 text-sm text-slate-700">' + escapeHtml(item.fungsi || '-') + '</td>' +
                        '<td class="px-4 py-3 text-sm text-slate-700">' + escapeHtml(diameter) + '</td>' +
                        '<td class="px-4 py-3 text-sm text-slate-700">' + escapeHtml(panjang) + '</td>' +
                    '</tr>';
                }).join('');
            }

            pipePaginationInfo.textContent = paginationInfo(pageData);
            renderPaginator(pipePagination, pageData, function(page) {
                pipeState.page = page;
                loadPipeTable();
            });
        } catch (error) {
            pipeTbody.innerHTML = tableError(5, 'Gagal memuat data pipa.');
            pipePaginationInfo.textContent = 'Terjadi kesalahan saat memuat data.';
            pipePagination.innerHTML = '';
        }
    }

    async function loadManholeTable() {
        manholeTbody.innerHTML = tableEmpty(5, 'Memuat data manhole...');
        try {
            const res = await fetchJson('/manholes', manholeState);
            const pageData = res.data;
            const rows = pageData.data || [];

            if (!rows.length) {
                manholeTbody.innerHTML = tableEmpty(5, 'Data manhole tidak ditemukan.');
            } else {
                manholeTbody.innerHTML = rows.map(function(item) {
                    const lokasi = [item.desa, item.kecamatan, item.wilayah].filter(Boolean).join(', ');
                    return '<tr class="border-b border-slate-100">' +
                        '<td class="px-4 py-3"><div class="font-semibold text-slate-800">' + escapeHtml(item.kode_manhole || '-') + '</div></td>' +
                        '<td class="px-4 py-3 text-sm text-slate-600">' + escapeHtml(lokasi || '-') + '</td>' +
                        '<td class="px-4 py-3">' + statusBadge(item.status) + '</td>' +
                        '<td class="px-4 py-3 text-sm text-slate-700">' + escapeHtml(item.kondisi_mh || '-') + '</td>' +
                        '<td class="px-4 py-3 text-sm text-slate-700">' + escapeHtml(item.risiko || '-') + '</td>' +
                    '</tr>';
                }).join('');
            }

            manholePaginationInfo.textContent = paginationInfo(pageData);
            renderPaginator(manholePagination, pageData, function(page) {
                manholeState.page = page;
                loadManholeTable();
            });
        } catch (error) {
            manholeTbody.innerHTML = tableError(5, 'Gagal memuat data manhole.');
            manholePaginationInfo.textContent = 'Terjadi kesalahan saat memuat data.';
            manholePagination.innerHTML = '';
        }
    }

    function debounce(callback, wait) {
        let timer = null;
        return function(event) {
            const context = this;
            clearTimeout(timer);
            timer = setTimeout(function() {
                callback.call(context, event);
            }, wait);
        };
    }

    function bindEvents() {
        document.getElementById('pipe-search').addEventListener('input', debounce(function(event) {
            pipeState.search = event.target.value.trim();
            pipeState.page = 1;
            loadPipeTable();
        }, 350));

        document.getElementById('pipe-status').addEventListener('change', function(event) {
            pipeState.status = event.target.value;
            pipeState.page = 1;
            loadPipeTable();
        });

        document.getElementById('pipe-fungsi').addEventListener('change', function(event) {
            pipeState.fungsi = event.target.value;
            pipeState.page = 1;
            loadPipeTable();
        });

        document.getElementById('pipe-per-page').addEventListener('change', function(event) {
            pipeState.per_page = Number(event.target.value || 10);
            pipeState.page = 1;
            loadPipeTable();
        });

        document.getElementById('pipe-reset').addEventListener('click', function() {
            pipeState.page = 1;
            pipeState.per_page = 10;
            pipeState.search = '';
            pipeState.status = '';
            pipeState.fungsi = '';

            document.getElementById('pipe-search').value = '';
            document.getElementById('pipe-status').value = '';
            document.getElementById('pipe-fungsi').value = '';
            document.getElementById('pipe-per-page').value = '10';

            loadPipeTable();
        });

        document.getElementById('manhole-search').addEventListener('input', debounce(function(event) {
            manholeState.search = event.target.value.trim();
            manholeState.page = 1;
            loadManholeTable();
        }, 350));

        document.getElementById('manhole-status').addEventListener('change', function(event) {
            manholeState.status = event.target.value;
            manholeState.page = 1;
            loadManholeTable();
        });

        document.getElementById('manhole-kondisi').addEventListener('change', function(event) {
            manholeState.kondisi_mh = event.target.value;
            manholeState.page = 1;
            loadManholeTable();
        });

        document.getElementById('manhole-wilayah').addEventListener('change', function(event) {
            manholeState.wilayah = event.target.value;
            manholeState.page = 1;
            loadManholeTable();
        });

        document.getElementById('manhole-per-page').addEventListener('change', function(event) {
            manholeState.per_page = Number(event.target.value || 10);
            manholeState.page = 1;
            loadManholeTable();
        });

        document.getElementById('manhole-reset').addEventListener('click', function() {
            manholeState.page = 1;
            manholeState.per_page = 10;
            manholeState.search = '';
            manholeState.status = '';
            manholeState.kondisi_mh = '';
            manholeState.wilayah = '';

            document.getElementById('manhole-search').value = '';
            document.getElementById('manhole-status').value = '';
            document.getElementById('manhole-kondisi').value = '';
            document.getElementById('manhole-wilayah').value = '';
            document.getElementById('manhole-per-page').value = '10';

            loadManholeTable();
        });
    }

    Promise.all([loadPipeFilters(), loadManholeFilters()]).finally(function() {
        initUploadPicker();
        bindEvents();
        loadPipeTable();
        loadManholeTable();
    });
})();
</script>
@endpush

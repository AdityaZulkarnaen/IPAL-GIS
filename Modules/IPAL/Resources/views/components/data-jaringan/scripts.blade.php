@push('ipal-scripts')
<script>
(function () {
    'use strict';

    const API_BASE = '{{ url('/api/ipal') }}';

    const MODE_PIPE = 'pipe';
    const MODE_MANHOLE = 'manhole';

    const state = {
        activeMode: MODE_PIPE,
        mode: {
            pipe: {
                page: 1,
                per_page: 10,
                search: '',
                status: '',
                secondary: '',
                filtersLoaded: false,
                filters: null,
            },
            manhole: {
                page: 1,
                per_page: 10,
                search: '',
                status: '',
                secondary: '',
                filtersLoaded: false,
                filters: null,
            },
        },
    };

    const modeConfig = {
        pipe: {
            label: 'Jalur Pipa',
            tableTitle: 'Data Jalur Pipa',
            uploadButtonLabel: 'Upload Jaringan Pipa',
            uploadModalTitle: 'Upload GeoJSON Jalur Pipa',
            searchPlaceholder: 'Search ID pipa, jalur, fungsi',
            endpoint: '/pipes',
            filterEndpoint: '/pipes/filters',
            secondaryField: 'fungsi',
            secondaryLabel: 'Semua Fungsi',
            columns: [
                { key: 'id', label: 'ID Jalur Pipa' },
                { key: 'status', label: 'Status' },
                { key: 'fungsi', label: 'Fungsi' },
                { key: 'diameter', label: 'Diameter' },
                { key: 'panjang', label: 'Panjang' },
            ],
            renderRow: function (item) {
                const diameter = item.pipe_dia ? item.pipe_dia + ' mm' : '-';
                const panjang = item.length_km ? item.length_km + ' km' : '-';
                const code = escapeHtml(item.kode_pipa || '-');
                const sub = escapeHtml(item.id_jalur || '-');

                return '<tr class="border-b border-[#edf1f6]">'
                    + '<td class="px-4 py-3 text-[#25314d]"><div class="font-semibold">' + code + '</div><div class="text-[11px] text-[#8a95aa]">' + sub + '</div></td>'
                    + '<td class="px-4 py-3">' + statusBadge(item.status) + '</td>'
                    + '<td class="px-4 py-3 text-[#3e4b67]">' + escapeHtml(item.fungsi || '-') + '</td>'
                    + '<td class="px-4 py-3 text-[#3e4b67]">' + escapeHtml(diameter) + '</td>'
                    + '<td class="px-4 py-3 text-[#3e4b67]">' + escapeHtml(panjang) + '</td>'
                    + '</tr>';
            },
        },
        manhole: {
            label: 'Manhole',
            tableTitle: 'Data Manhole',
            uploadButtonLabel: 'Upload Jaringan Manhole',
            uploadModalTitle: 'Upload GeoJSON Manhole',
            searchPlaceholder: 'Search ID Manhole, Desa, Kecamatan',
            endpoint: '/manholes',
            filterEndpoint: '/manholes/filters',
            secondaryField: 'kondisi_mh',
            secondaryLabel: 'Semua Kondisi',
            columns: [
                { key: 'id', label: 'ID Manhole' },
                { key: 'lokasi', label: 'Lokasi' },
                { key: 'status', label: 'Status' },
                { key: 'kondisi', label: 'Kondisi' },
                { key: 'resiko', label: 'Resiko' },
            ],
            renderRow: function (item) {
                const desa = item.desa || '';
                const kecamatan = item.kecamatan || '';
                const lokasi = [desa, kecamatan].filter(Boolean).join(', ');
                const lat = Number(item.latitude);
                const lng = Number(item.longitude);
                const coord = Number.isFinite(lat) && Number.isFinite(lng)
                    ? lat.toFixed(6) + ', ' + lng.toFixed(6)
                    : '-';

                return '<tr class="border-b border-[#edf1f6]">'
                    + '<td class="px-4 py-3 text-[#25314d] font-semibold">' + escapeHtml(item.kode_manhole || '-') + '</td>'
                    + '<td class="px-4 py-3 text-[#55627d]"><div>' + escapeHtml(lokasi || '-') + '</div><div class="text-[11px] text-[#95a0b5]">' + escapeHtml(coord) + '</div></td>'
                    + '<td class="px-4 py-3">' + statusBadge(item.status) + '</td>'
                    + '<td class="px-4 py-3 text-[#3e4b67]">' + escapeHtml(item.kondisi_mh || '-') + '</td>'
                    + '<td class="px-4 py-3 text-[#3e4b67]">' + escapeHtml(item.risiko || '-') + '</td>'
                    + '</tr>';
            },
        },
    };

    const refs = {
        modeToggleButtons: Array.from(document.querySelectorAll('.mode-toggle-btn[data-mode]')),
        tableTitle: document.getElementById('table-title'),
        uploadButtonLabel: document.getElementById('upload-button-label'),
        uploadModalTitle: document.getElementById('upload-modal-title'),
        openUploadModalBtn: document.getElementById('open-upload-modal-btn'),
        assetSearch: document.getElementById('asset-search'),
        assetStatus: document.getElementById('asset-status'),
        assetSecondaryFilter: document.getElementById('asset-secondary-filter'),
        assetReset: document.getElementById('asset-reset'),
        assetPerPage: document.getElementById('asset-per-page'),
        assetThead: document.getElementById('asset-thead'),
        assetTbody: document.getElementById('asset-tbody'),
        assetPaginationInfo: document.getElementById('asset-pagination-info'),
        assetPagination: document.getElementById('asset-pagination'),
        uploadModal: document.getElementById('upload-modal'),
        uploadModalOverlay: document.getElementById('upload-modal-overlay'),
        uploadModalForm: document.getElementById('upload-modal-form'),
        uploadTypeInput: document.getElementById('upload-type-input'),
        uploadInput: document.getElementById('upload-file-input'),
        uploadName: document.getElementById('upload-file-name'),
        uploadSelectBtn: document.getElementById('btn-file-select'),
        uploadCancelBtn: document.getElementById('btn-upload-cancel'),
        uploadSubmitBtn: document.getElementById('btn-upload-submit'),
        uploadDropzone: document.getElementById('geojson-dropzone'),
    };

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
            baik: 'bg-emerald-100 text-emerald-700 border border-emerald-200',
            aman: 'bg-emerald-100 text-emerald-700 border border-emerald-200',
            completed: 'bg-emerald-100 text-emerald-700 border border-emerald-200',
            perbaikan: 'bg-amber-100 text-amber-700 border border-amber-200',
            'dalam perbaikan': 'bg-amber-100 text-amber-700 border border-amber-200',
            processing: 'bg-amber-100 text-amber-700 border border-amber-200',
            rusak: 'bg-rose-100 text-rose-700 border border-rose-200',
            masalah: 'bg-rose-100 text-rose-700 border border-rose-200',
            bermasalah: 'bg-rose-100 text-rose-700 border border-rose-200',
            failed: 'bg-rose-100 text-rose-700 border border-rose-200',
            pending: 'bg-slate-100 text-slate-700 border border-slate-200',
        };

        const cls = map[normalized] || 'bg-slate-100 text-slate-700 border border-slate-200';
        const label = status ? escapeHtml(status) : '-';
        return '<span class="inline-flex items-center rounded-full px-2.5 py-1 text-xs font-semibold ' + cls + '">' + label + '</span>';
    }

    function tableEmpty(colspan, message) {
        return '<tr><td colspan="' + colspan + '" class="px-4 py-9 text-center text-[#8f9ab0]">' + escapeHtml(message) + '</td></tr>';
    }

    function tableError(colspan, message) {
        return '<tr><td colspan="' + colspan + '" class="px-4 py-9 text-center text-[#d84a6a]">' + escapeHtml(message) + '</td></tr>';
    }

    function getModeState(mode) {
        return state.mode[mode];
    }

    function setModeToggleUI(mode) {
        refs.modeToggleButtons.forEach(function (button) {
            const active = button && button.getAttribute('data-mode') === mode;
            if (!button) {
                return;
            }

            button.classList.toggle('bg-white', active);
            button.classList.toggle('border', active);
            button.classList.toggle('border-[#d9dfea]', active);
            button.classList.toggle('text-[#2b3a57]', active);
            button.classList.toggle('shadow-[0_1px_3px_rgba(16,24,40,0.08)]', active);

            button.classList.toggle('bg-transparent', !active);
            button.classList.toggle('border-transparent', !active);
            button.classList.toggle('text-[#8c95aa]', !active);
        });
    }

    function renderThead(mode) {
        const columns = modeConfig[mode].columns;
        refs.assetThead.innerHTML = '<tr>' + columns.map(function (column) {
            return '<th class="px-4 py-3 text-left text-xs font-semibold tracking-wide uppercase border-b border-[#edf1f6]">' + escapeHtml(column.label) + '</th>';
        }).join('') + '</tr>';
    }

    function buildQuery(mode) {
        const modeState = getModeState(mode);
        const query = {
            page: modeState.page,
            per_page: modeState.per_page,
            search: modeState.search,
            status: modeState.status,
        };

        query[modeConfig[mode].secondaryField] = modeState.secondary;
        return query;
    }

    async function fetchJson(path, params) {
        const url = new URL(API_BASE + path);
        Object.entries(params || {}).forEach(function (entry) {
            const key = entry[0];
            const value = entry[1];
            if (value !== '' && value !== null && typeof value !== 'undefined') {
                url.searchParams.set(key, value);
            }
        });

        const response = await fetch(url.toString(), {
            headers: { Accept: 'application/json' },
        });

        const json = await response.json();
        if (!response.ok || !json.success) {
            throw new Error('API request failed');
        }

        return json;
    }

    function createOption(value, label) {
        const option = document.createElement('option');
        option.value = value;
        option.textContent = label;
        return option;
    }

    function setSelectOptions(selectElement, values, defaultLabel) {
        if (!selectElement) {
            return;
        }

        selectElement.innerHTML = '';
        selectElement.appendChild(createOption('', defaultLabel));
        (values || []).forEach(function (value) {
            if (value === null || value === '') {
                return;
            }
            selectElement.appendChild(createOption(String(value), String(value)));
        });
    }

    async function ensureFiltersLoaded(mode) {
        const modeState = getModeState(mode);
        if (!modeState.filtersLoaded) {
            const response = await fetchJson(modeConfig[mode].filterEndpoint, {});
            modeState.filters = response.data || {};
            modeState.filtersLoaded = true;
        }

        const filters = modeState.filters || {};

        setSelectOptions(refs.assetStatus, filters.status || [], 'Semua Status');

        const secondaryValues = filters[modeConfig[mode].secondaryField] || [];
        setSelectOptions(refs.assetSecondaryFilter, secondaryValues, modeConfig[mode].secondaryLabel);

        refs.assetStatus.value = modeState.status;
        refs.assetSecondaryFilter.value = modeState.secondary;
    }

    function paginationInfo(meta) {
        if (!meta || meta.total === 0) {
            return '0 data';
        }

        return String(meta.from || 0) + '-' + String(meta.to || 0) + ' of ' + String(meta.total);
    }

    function renderPaginator(meta, onSelect) {
        refs.assetPagination.innerHTML = '';

        if (!meta || Number(meta.last_page || 1) <= 1) {
            return;
        }

        const pages = [];
        const start = Math.max(1, meta.current_page - 2);
        const end = Math.min(meta.last_page, meta.current_page + 2);

        for (let i = start; i <= end; i += 1) {
            pages.push(i);
        }

        const fragment = document.createDocumentFragment();

        function createPagerButton(label, page, disabled, active) {
            const button = document.createElement('button');
            button.type = 'button';
            button.className = 'h-8 min-w-8 rounded-[7px] border px-2 text-xs font-semibold ' + (active
                ? 'border-[#cfdcf6] bg-[#eef4ff] text-[#2f68c8]'
                : 'border-transparent bg-transparent text-[#6d7992] hover:bg-[#f2f5fb]');
            button.textContent = label;
            button.disabled = disabled;
            button.addEventListener('click', function () {
                onSelect(page);
            });
            return button;
        }

        fragment.appendChild(createPagerButton('<', meta.current_page - 1, meta.current_page <= 1, false));
        pages.forEach(function (page) {
            fragment.appendChild(createPagerButton(String(page), page, false, page === meta.current_page));
        });
        fragment.appendChild(createPagerButton('>', meta.current_page + 1, meta.current_page >= meta.last_page, false));

        refs.assetPagination.appendChild(fragment);
    }

    async function loadActiveTable() {
        const mode = state.activeMode;
        const config = modeConfig[mode];
        const columns = config.columns;

        refs.assetTbody.innerHTML = tableEmpty(columns.length, 'Memuat data...');

        try {
            const query = buildQuery(mode);
            const response = await fetchJson(config.endpoint, query);
            const pageData = response.data || {};
            const rows = pageData.data || [];

            if (!rows.length) {
                refs.assetTbody.innerHTML = tableEmpty(columns.length, 'Data tidak ditemukan.');
            } else {
                refs.assetTbody.innerHTML = rows.map(config.renderRow).join('');
            }

            refs.assetPaginationInfo.textContent = paginationInfo(pageData);
            renderPaginator(pageData, function (page) {
                getModeState(mode).page = page;
                loadActiveTable();
            });
        } catch (error) {
            refs.assetTbody.innerHTML = tableError(columns.length, 'Gagal memuat data.');
            refs.assetPaginationInfo.textContent = 'Terjadi kesalahan saat memuat data.';
            refs.assetPagination.innerHTML = '';
        }
    }

    async function syncModeUI(mode) {
        const config = modeConfig[mode];
        const modeState = getModeState(mode);

        refs.tableTitle.textContent = config.tableTitle;
        refs.uploadButtonLabel.textContent = config.uploadButtonLabel;
        refs.uploadModalTitle.textContent = config.uploadModalTitle;
        refs.uploadTypeInput.value = mode;
        refs.assetSearch.placeholder = config.searchPlaceholder;

        setModeToggleUI(mode);
        renderThead(mode);

        modeState.page = modeState.page || 1;
        refs.assetSearch.value = modeState.search;
        refs.assetPerPage.value = String(modeState.per_page || 10);

        await ensureFiltersLoaded(mode);
        await loadActiveTable();
    }

    function openUploadModal() {
        refs.uploadModal.classList.remove('hidden');
        refs.uploadModal.classList.add('flex');
        document.body.classList.add('overflow-hidden');
    }

    function closeUploadModal() {
        refs.uploadModal.classList.remove('flex');
        refs.uploadModal.classList.add('hidden');
        document.body.classList.remove('overflow-hidden');
    }

    function resetUploadInput() {
        refs.uploadInput.value = '';
        refs.uploadName.textContent = '';
        refs.uploadName.classList.add('hidden');
    }

    function setupUploadPicker() {
        function updateUploadName() {
            const selectedFile = refs.uploadInput.files && refs.uploadInput.files[0]
                ? refs.uploadInput.files[0].name
                : '';

            refs.uploadName.textContent = selectedFile;
            refs.uploadName.classList.toggle('hidden', !selectedFile);
        }

        refs.uploadSelectBtn.addEventListener('click', function () {
            refs.uploadInput.click();
        });

        refs.uploadInput.addEventListener('change', updateUploadName);

        refs.uploadDropzone.addEventListener('dragover', function (event) {
            event.preventDefault();
            refs.uploadDropzone.classList.add('border-[#77a6dc]', 'bg-[#f2f7fe]');
        });

        refs.uploadDropzone.addEventListener('dragleave', function () {
            refs.uploadDropzone.classList.remove('border-[#77a6dc]', 'bg-[#f2f7fe]');
        });

        refs.uploadDropzone.addEventListener('drop', function (event) {
            event.preventDefault();
            refs.uploadDropzone.classList.remove('border-[#77a6dc]', 'bg-[#f2f7fe]');

            if (!event.dataTransfer || !event.dataTransfer.files || !event.dataTransfer.files.length) {
                return;
            }

            refs.uploadInput.files = event.dataTransfer.files;
            updateUploadName();
        });

        refs.uploadCancelBtn.addEventListener('click', function () {
            resetUploadInput();
            closeUploadModal();
        });

        refs.uploadModalOverlay.addEventListener('click', function () {
            closeUploadModal();
        });

        refs.openUploadModalBtn.addEventListener('click', function () {
            openUploadModal();
        });

        document.addEventListener('keydown', function (event) {
            if (event.key === 'Escape' && refs.uploadModal.classList.contains('flex')) {
                closeUploadModal();
            }
        });

        refs.uploadModalForm.addEventListener('submit', function () {
            refs.uploadSubmitBtn.disabled = true;
            refs.uploadSubmitBtn.textContent = 'Mengunggah...';
        });
    }

    function debounce(fn, wait) {
        let timer = null;
        return function (event) {
            const ctx = this;
            clearTimeout(timer);
            timer = setTimeout(function () {
                fn.call(ctx, event);
            }, wait);
        };
    }

    function bindEvents() {
        refs.modeToggleButtons.forEach(function (button) {
            button.addEventListener('click', function () {
                const nextMode = button.getAttribute('data-mode');
                if (!nextMode || !modeConfig[nextMode] || state.activeMode === nextMode) {
                    return;
                }

                state.activeMode = nextMode;
                syncModeUI(nextMode);
            });
        });

        refs.assetSearch.addEventListener('input', debounce(function (event) {
            const modeState = getModeState(state.activeMode);
            modeState.search = event.target.value.trim();
            modeState.page = 1;
            loadActiveTable();
        }, 300));

        refs.assetStatus.addEventListener('change', function (event) {
            const modeState = getModeState(state.activeMode);
            modeState.status = event.target.value;
            modeState.page = 1;
            loadActiveTable();
        });

        refs.assetSecondaryFilter.addEventListener('change', function (event) {
            const modeState = getModeState(state.activeMode);
            modeState.secondary = event.target.value;
            modeState.page = 1;
            loadActiveTable();
        });

        refs.assetPerPage.addEventListener('change', function (event) {
            const modeState = getModeState(state.activeMode);
            modeState.per_page = Number(event.target.value || 10);
            modeState.page = 1;
            loadActiveTable();
        });

        refs.assetReset.addEventListener('click', function () {
            const modeState = getModeState(state.activeMode);
            modeState.page = 1;
            modeState.per_page = 10;
            modeState.search = '';
            modeState.status = '';
            modeState.secondary = '';

            refs.assetSearch.value = '';
            refs.assetPerPage.value = '10';
            refs.assetStatus.value = '';
            refs.assetSecondaryFilter.value = '';

            loadActiveTable();
        });
    }

    function bootstrap() {
        if (!refs.modeToggleButtons.length || !refs.assetTbody || !refs.uploadModal) {
                return;
        }

        setupUploadPicker();
        bindEvents();
        syncModeUI(state.activeMode);
    }

    bootstrap();
})();
</script>
@endpush

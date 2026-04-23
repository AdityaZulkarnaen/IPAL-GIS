@extends('ipal::layouts.main')

@include('ipal::components.data-jaringan.tailwind-assets')

@section('content')
<div id="kt_app_content" class="app-content flex-column-fluid pt-3 sm:pt-4 bg-white">
    <div id="kt_app_content_container" class="app-container container-fluid max-w-[1280px]">
        <div class="mb-4 sm:mb-6 flex flex-col sm:flex-row items-start w-full sm:items-end justify-between gap-4">
            <div>
                <div class="mb-1 text-[12px] sm:text-[13px] text-[#98a2b3] tracking-[0.01em]">
                IPAL
                <span class="mx-1.5">&gt;</span>
                <span class="text-[#667085]">Data Jaringan</span>
            </div>
            <h1 class="text-[24px] leading-tight sm:text-[24px] font-bold tracking-[-0.02em] text-[#1a2744]">Manajemen Data Jaringan</h1>
            <p class="mt-1 text-[12px] sm:text-[14px] text-[#64748b]">Pantau status dan detail jaringan IPAL</p>
            </div>
            <div class="rounded-[14px] border border-[#e4e8f1] sm:hidden bg-[#f7f8fc] p-1 flex w-full">
                    <button
                        id="mode-pipe-btn-mobile"
                        type="button"
                        class="mode-toggle-btn w-[50%] h-[32px] rounded-[10px] bg-white border border-[#d9dfea] text-[#2b3a57] text-[12px] font-semibold inline-flex items-center justify-center gap-2 shadow-[0_1px_3px_rgba(16,24,40,0.08)]"
                        data-mode="pipe"
                    >
                        <svg width="15" height="15" viewBox="0 0 15 15" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <line x1="1.11032" y1="11.0371" x2="12.5177" y2="11.0371" stroke="black" stroke-width="1.03704" stroke-linecap="round"/>
                            <line x1="1.11032" y1="3.48148" x2="12.5177" y2="3.48148" stroke="black" stroke-width="1.03704" stroke-linecap="round"/>
                            <path opacity="0.3" d="M3.48242 4L3.48242 10.963" stroke="black" stroke-width="1.03704"/>
                            <path opacity="0.3" d="M10.0742 4V10.963" stroke="black" stroke-width="1.03704"/>
                        </svg>

                        Jalur Pipa
                    </button>
                    <button
                        id="mode-manhole-btn-mobile"
                        type="button"
                        class="mode-toggle-btn w-[50%] h-[32px] rounded-[10px] border border-transparent bg-transparent text-[#8c95aa] text-[12px] font-semibold inline-flex items-center justify-center gap-2"
                        data-mode="manhole"
                    >
                        <svg width="15" height="15" viewBox="0 0 15 15" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <g clip-path="url(#clip0_1056_10203)">
                            <circle opacity="0.3" cx="7.11031" cy="7.11129" r="5.92593" stroke="#99A1B7" stroke-width="1.18519"/>
                            <circle cx="7.11024" cy="7.11122" r="2.96296" stroke="#99A1B7" stroke-width="1.18519"/>
                            </g>
                            <defs>
                            <clipPath id="clip0_1056_10203">
                            <rect width="14.2222" height="14.2222" fill="white"/>
                            </clipPath>
                            </defs>
                        </svg>
                        Manhole
                    </button>
                </div>
            <div class="flex flex-row items-center justify-between sm:justify-center gap-3 w-full sm:w-auto">
                <a
                    href="{{ route('ipal.upload.history') }}"
                    class="h-fit w-[40%] sm:w-auto py-3 px-2 rounded-[8px] border border-[#e5e7ef] bg-[#f5f6fa] text-[#5f6b84] font-semibold text-[12px] inline-flex items-center justify-center gap-2 hover:bg-[#eef1f8]"
                >
                    <svg width="14" height="14" viewBox="0 0 14 14" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <circle opacity="0.1" cx="7.58398" cy="7" r="5.25" fill="#4B5675"/>
                        <path d="M7.59454 1.12402C6.30279 1.13031 5.04961 1.56494 4.03135 2.35981C3.01308 3.15468 2.28727 4.26488 1.96762 5.51648L1.57661 4.92516C1.5396 4.86325 1.49042 4.80948 1.43206 4.76709C1.3737 4.7247 1.30736 4.69457 1.23704 4.67852C1.16672 4.66247 1.09387 4.66082 1.0229 4.67368C0.951924 4.68655 0.884289 4.71365 0.824074 4.75336C0.763858 4.79307 0.712309 4.84457 0.672535 4.90474C0.63276 4.96491 0.605585 5.03252 0.592647 5.10348C0.579709 5.17444 0.581278 5.24729 0.597257 5.31762C0.613236 5.38796 0.643295 5.45434 0.685622 5.51274L1.86079 7.29366C1.94242 7.40789 2.06278 7.48856 2.19945 7.52068C2.33901 7.54836 2.48387 7.52058 2.60328 7.44322L4.36604 6.25577C4.42681 6.21782 4.47933 6.16802 4.52047 6.10935C4.56161 6.05068 4.59052 5.98433 4.60548 5.91426C4.62045 5.84418 4.62116 5.77181 4.60758 5.70146C4.59401 5.6311 4.56641 5.5642 4.52644 5.50472C4.48647 5.44525 4.43494 5.39443 4.37493 5.35529C4.31491 5.31614 4.24763 5.28947 4.17709 5.27686C4.10656 5.26425 4.0342 5.26596 3.96434 5.28189C3.89448 5.29782 3.82854 5.32764 3.77044 5.36959L2.96865 5.91016C3.18204 5.04395 3.63462 4.25516 4.27474 3.63379C4.91486 3.01242 5.71675 2.58349 6.58892 2.39594C7.46109 2.20838 8.36842 2.26976 9.20738 2.57307C10.0463 2.87638 10.7832 3.4094 11.3338 4.11132C11.8844 4.81325 12.2266 5.6558 12.3214 6.54286C12.4162 7.42992 12.2597 8.32576 11.8698 9.12817C11.48 9.93058 10.8724 10.6073 10.1165 11.081C9.36053 11.5547 8.48665 11.8064 7.59454 11.8074C6.8298 11.8053 6.07686 11.6186 5.39973 11.2632C4.7226 10.9077 4.14131 10.394 3.70527 9.76578C3.66622 9.70582 3.61552 9.65431 3.55617 9.61432C3.49683 9.57433 3.43005 9.54667 3.35982 9.53298C3.28958 9.51929 3.2173 9.51986 3.14728 9.53465C3.07727 9.54943 3.01094 9.57814 2.95223 9.61905C2.89352 9.65997 2.84363 9.71227 2.80552 9.77284C2.76742 9.83341 2.74187 9.90102 2.7304 9.97165C2.71892 10.0423 2.72176 10.1145 2.73874 10.184C2.75572 10.2535 2.78649 10.3189 2.82924 10.3763C3.53811 11.3983 4.55611 12.1659 5.73362 12.5664C6.91112 12.9668 8.18603 12.979 9.37098 12.6011C10.5559 12.2232 11.5884 11.4752 12.3167 10.467C13.0449 9.45877 13.4306 8.24353 13.417 6.99987C13.4222 5.44913 12.8122 3.95962 11.7207 2.85808C10.6291 1.75655 9.14526 1.13293 7.59454 1.12402Z" fill="#4B5675"/>
                        <path d="M7.54198 3.76807C7.40031 3.76807 7.26444 3.82434 7.16427 3.92452C7.06409 4.0247 7.00781 4.16056 7.00781 4.30223V6.99978C7.01008 7.14098 7.06605 7.27602 7.16432 7.37744L8.76683 8.99436C8.86739 9.09334 9.00263 9.14912 9.14372 9.14982C9.28482 9.15052 9.4206 9.09607 9.52214 8.9981C9.62271 8.89835 9.67954 8.76275 9.68014 8.62111C9.68074 8.47946 9.62506 8.34338 9.52534 8.24279L8.07615 6.78024V4.30223C8.07615 4.16056 8.01987 4.0247 7.91969 3.92452C7.81952 3.82434 7.68365 3.76807 7.54198 3.76807Z" fill="#4B5675"/>
                    </svg>
                    Riwayat Upload
                </a>
                <button
                    id="open-upload-modal-btn"
                    type="button"
                    class="h-fit w-[60%] sm:w-auto py-3 px-4 rounded-[8px] border border-[#1b84ff] bg-[#1b84ff] text-white font-semibold text-[12px] inline-flex items-center justify-center gap-2 hover:bg-[#1576e8]"
                >
                    <svg width="13" height="12" viewBox="0 0 13 12" fill="none" xmlns="http://www.w3.org/2000/svg">
                      <path d="M9.83726 11.8972H2.70892C1.96826 11.8728 1.26747 11.5558 0.760121 11.0157C0.25277 10.4755 -0.0197586 9.75627 0.00225666 9.01554V6.42554C-0.0125707 6.05606 0.0457826 5.68729 0.173962 5.34043C0.30214 4.99358 0.497617 4.67548 0.749151 4.40443C1.00069 4.13338 1.30332 3.91472 1.63964 3.76103C1.97597 3.60734 2.33936 3.52164 2.70892 3.50887C2.82705 3.50886 2.94042 3.55538 3.02449 3.63836C3.10856 3.72134 3.15656 3.83409 3.15809 3.95221C3.15658 4.07086 3.10877 4.18423 3.02486 4.26814C2.94095 4.35205 2.82758 4.39986 2.70892 4.40137C2.45601 4.41328 2.20797 4.47527 1.97919 4.58375C1.7504 4.69223 1.54543 4.84504 1.37614 5.03332C1.20685 5.2216 1.07661 5.44161 0.992979 5.68059C0.909345 5.91958 0.873976 6.17278 0.888923 6.42554V9.01554C0.868289 9.52077 1.04788 10.0137 1.38868 10.3873C1.72947 10.7609 2.20392 10.9848 2.70892 11.0105H9.83726C10.3423 10.9848 10.8167 10.7609 11.1575 10.3873C11.4983 10.0137 11.6779 9.52077 11.6573 9.01554V6.42554C11.6794 5.9199 11.5004 5.42608 11.1593 5.05217C10.8182 4.67826 10.3428 4.45474 9.83726 4.43054C9.7186 4.42903 9.60523 4.38122 9.52132 4.29731C9.43741 4.2134 9.3896 4.10003 9.38809 3.98137C9.38962 3.86326 9.43762 3.75051 9.52169 3.66753C9.60576 3.58455 9.71914 3.53803 9.83726 3.53804C10.2068 3.55081 10.5702 3.63651 10.9065 3.7902C11.2429 3.94389 11.5455 4.16255 11.797 4.4336C12.0486 4.70465 12.244 5.02275 12.3722 5.3696C12.5004 5.71645 12.5588 6.08522 12.5439 6.45471V9.04471C12.5581 9.78046 12.2821 10.4922 11.7756 11.026C11.2691 11.5599 10.5727 11.8728 9.83726 11.8972ZM6.27309 7.84887C6.39067 7.84887 6.50343 7.80216 6.58657 7.71902C6.66972 7.63588 6.71642 7.52312 6.71642 7.40554V1.53721L7.69059 2.51137C7.73181 2.553 7.78087 2.58604 7.83494 2.60859C7.88901 2.63114 7.94701 2.64275 8.00559 2.64275C8.06417 2.64275 8.12217 2.63114 8.17624 2.60859C8.23031 2.58604 8.27937 2.553 8.32059 2.51137C8.40324 2.42736 8.44956 2.31423 8.44956 2.19637C8.44956 2.07852 8.40324 1.96539 8.32059 1.88137L6.57059 0.131373C6.52937 0.0897494 6.48031 0.0567084 6.42624 0.0341592C6.37217 0.0116101 6.31417 0 6.25559 0C6.19701 0 6.13901 0.0116101 6.08494 0.0341592C6.03087 0.0567084 5.98181 0.0897494 5.94059 0.131373L4.19059 1.88137C4.10705 1.96956 4.06196 2.08732 4.06524 2.20875C4.06852 2.33018 4.11991 2.44533 4.20809 2.52887C4.29627 2.61242 4.41403 2.65751 4.53546 2.65422C4.65689 2.65094 4.77205 2.59956 4.85559 2.51137L5.82976 1.53721V7.41721C5.8328 7.53275 5.88085 7.64254 5.96366 7.72318C6.04647 7.80381 6.1575 7.84891 6.27309 7.84887Z" fill="white"/>
                    </svg>
                    <span id="upload-button-label" class="text-[12px]">Upload Jaringan Pipa</span>
                </button>
            </div>
        </div>

        @if (session('success'))
        <div class="mb-4 rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-3 text-emerald-800">
            {{ session('success') }}
        </div>
        @endif

        @if (session('error'))
        <div class="mb-4 rounded-lg border border-rose-200 bg-rose-50 px-4 py-3 text-rose-700">
            {{ session('error') }}
        </div>
        @endif

        @if ($errors->any())
        <div class="mb-4 rounded-lg border border-rose-200 bg-rose-50 px-4 py-3 text-rose-700">
            <ul class="mb-0 list-disc pl-5">
                @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        @endif
 

        

        <section class="rounded-[14px] border border-[#e3e7ef] bg-white shadow-panel overflow-hidden">
            <div class="px-4 sm:px-6 py-2 border-b border-[#edf1f6] flex flex-row justify-between items-center">
                <h2 id="table-title" class="text-[14px] sm:text-[16px] font-semibold text-[#1f2a44]">Data Jalur Pipa</h2>
                <div class="rounded-[14px] border border-[#e4e8f1] bg-[#f7f8fc] p-1 hidden sm:flex w-3/12 sm:max-w-[430px]">
                    <button
                        id="mode-pipe-btn-desktop"
                        type="button"
                        class="mode-toggle-btn w-[50%] h-[32px] rounded-[10px] bg-white border border-[#d9dfea] text-[#2b3a57] text-[12px] font-semibold inline-flex items-center justify-center gap-2 shadow-[0_1px_3px_rgba(16,24,40,0.08)]"
                        data-mode="pipe"
                    >
                        <svg width="15" height="15" viewBox="0 0 15 15" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <line x1="1.11032" y1="11.0371" x2="12.5177" y2="11.0371" stroke="black" stroke-width="1.03704" stroke-linecap="round"/>
                            <line x1="1.11032" y1="3.48148" x2="12.5177" y2="3.48148" stroke="black" stroke-width="1.03704" stroke-linecap="round"/>
                            <path opacity="0.3" d="M3.48242 4L3.48242 10.963" stroke="black" stroke-width="1.03704"/>
                            <path opacity="0.3" d="M10.0742 4V10.963" stroke="black" stroke-width="1.03704"/>
                        </svg>

                        Jalur Pipa
                    </button>
                    <button
                        id="mode-manhole-btn-desktop"
                        type="button"
                        class="mode-toggle-btn w-[50%] h-[32px] rounded-[10px] border border-transparent bg-transparent text-[#8c95aa] text-[12px] font-semibold inline-flex items-center justify-center gap-2"
                        data-mode="manhole"
                    >
                        <svg width="15" height="15" viewBox="0 0 15 15" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <g clip-path="url(#clip0_1056_10203)">
                            <circle opacity="0.3" cx="7.11031" cy="7.11129" r="5.92593" stroke="#99A1B7" stroke-width="1.18519"/>
                            <circle cx="7.11024" cy="7.11122" r="2.96296" stroke="#99A1B7" stroke-width="1.18519"/>
                            </g>
                            <defs>
                            <clipPath id="clip0_1056_10203">
                            <rect width="14.2222" height="14.2222" fill="white"/>
                            </clipPath>
                            </defs>
                        </svg>
                        Manhole
                    </button>
                </div>
            </div>

            <div class="px-4 sm:px-6 py-4 border-b border-[#edf1f6]">
                <div class="flex flex-col sm:flex-row gap-2.5 items-center">
                    <div class="flex-1 w-full flex flex-row items-center justify-start pl-4 rounded-[8px] border border-[#d8deea] bg-[#fbfcff]">
                        <svg width="14" height="14" viewBox="0 0 14 14" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M6 0C9.31358 0 11.9998 2.68647 12 6C12 7.45266 11.4831 8.78421 10.624 9.82227L13.0674 12.2666C13.2887 12.4879 13.2887 12.8461 13.0674 13.0674C12.8461 13.2886 12.4879 13.2887 12.2666 13.0674L9.82227 10.624C8.78421 11.483 7.45264 12 6 12C2.68649 11.9998 0 9.31357 0 6C0.000207643 2.68661 2.68662 0.000230965 6 0ZM6 1.1123C3.30077 1.11254 1.11251 3.30076 1.1123 6C1.1123 8.69941 3.30064 10.8884 6 10.8887C8.69955 10.8887 10.8887 8.69955 10.8887 6C10.8885 3.30062 8.69943 1.1123 6 1.1123Z" fill="#99A1B7"/>
                        </svg>
                        <input
                            id="asset-search"
                            type="text"
                            class="h-10 w-full border-0 pl-2 pr-3 text-[13px] text-[#2f3c59] placeholder:text-[#9aa4b9] focus:outline-none focus:ring-0"
                            placeholder="Cari berdasarkan nama atau ID..."
                        >
                    </div>
                    <div class="w-full sm:w-auto">
                        <div class="flex w-full sm:w-auto flex-nowrap items-center gap-2">
                        <select id="asset-status" class="h-10 min-w-0 flex-1 sm:flex-none sm:w-[170px] rounded-[8px] border border-[#d8deea] bg-white px-2 sm:px-3 pr-7 sm:pr-8 text-[11px] sm:text-[13px] text-[#44516d] focus:outline-none focus:ring-2 focus:ring-[#d8e9ff]">
                        <option value="">Semua Status</option>
                        </select>
                        <select id="asset-secondary-filter" class="h-10 min-w-0 flex-1 sm:flex-none sm:w-[170px] rounded-[8px] border border-[#d8deea] bg-white px-2 sm:px-3 pr-7 sm:pr-8 text-[11px] sm:text-[13px] text-[#44516d] focus:outline-none focus:ring-2 focus:ring-[#d8e9ff]">
                            <option value="">Semua Fungsi</option>
                        </select>
                        <button id="asset-reset" type="button" class="h-10 min-w-0 flex-1 sm:flex-none sm:w-auto rounded-[8px] border border-[#cde0ff] bg-[#f2f7ff] px-2 sm:px-4 text-[11px] sm:text-[13px] font-semibold text-[#2f7ee9] hover:bg-[#e7f1ff] whitespace-nowrap">Reset Filter</button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-[880px] w-full border-collapse text-[13px]">
                    <thead id="asset-thead" class="bg-[#f8f9fc] text-[#7c879e]"></thead>
                    <tbody id="asset-tbody"></tbody>
                </table>
            </div>

            <div class="px-4 sm:px-6 py-3.5 flex flex-col sm:flex-row sm:items-center gap-3 text-[12px] text-[#7f8aa3] border-t border-[#edf1f6]">
                <div class="flex items-center gap-2">
                    <span>Show</span>
                    <select id="asset-per-page" class="h-9 w-[64px] rounded-[8px] border border-[#d8deea] bg-white px-2 text-[13px] text-[#51607c] focus:outline-none focus:ring-2 focus:ring-[#d8e9ff]">
                        <option value="5">5</option>
                        <option value="10" selected>10</option>
                        <option value="15">15</option>
                        <option value="25">25</option>
                    </select>
                    <span>per page</span>
                </div>
                <div id="asset-pagination-info" class="sm:ml-auto"></div>
                <div id="asset-pagination" class="flex items-center gap-1"></div>
            </div>
        </section>

        <div id="upload-modal" class="fixed inset-0 z-[140] hidden items-center justify-center p-4 sm:p-6">
            <div id="upload-modal-overlay" class="absolute inset-0 bg-[rgba(15,23,42,0.55)]"></div>
            <div class="relative z-10 w-full max-w-[720px] rounded-[12px] bg-white border border-[#e5e7ef] shadow-[0_30px_80px_rgba(2,6,23,0.24)] p-4 sm:p-6">
                <h3 id="upload-modal-title" class="text-center text-[20px] font-bold text-[#22304d] mb-4">Upload GeoJSON Jalur Pipa</h3>

                <form id="upload-modal-form" action="{{ route('ipal.upload.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <input id="upload-type-input" type="hidden" name="tipe" value="pipe">
                    <div id="geojson-dropzone" class="rounded-[10px] border-2 border-dashed border-[#b9cbe2] bg-[#fbfcff] min-h-[212px] px-4 py-5 flex flex-col items-center justify-center gap-3 text-center">
                        <svg width="30" height="30" viewBox="0 0 30 30" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path opacity="0.3" d="M28.4378 15H22.0819C21.2048 14.9777 20.341 14.7806 19.541 14.4204C18.7409 14.0602 18.0208 13.544 17.4227 12.9021C16.8246 12.2602 16.3605 11.5055 16.0576 10.682C15.7548 9.8586 15.6191 8.98304 15.6587 8.10656V1.5625H9.06093C7.05079 1.59083 5.13363 2.41386 3.72857 3.85166C2.32351 5.28945 1.54485 7.22506 1.5628 9.23531V20.7647C1.54485 22.7749 2.32351 24.7105 3.72857 26.1483C5.13363 27.5861 7.05079 28.4092 9.06093 28.4375H20.9128C22.9265 28.4127 24.8485 27.5916 26.2586 26.1539C27.6687 24.7161 28.4522 22.7785 28.4378 20.7647V15Z" fill="#78829D"/>
                            <path d="M28.4375 12.8903H22.0815C21.4707 12.8719 20.8699 12.7303 20.315 12.4742C19.7602 12.2181 19.2627 11.8526 18.8525 11.3997C18.4422 10.9467 18.1276 10.4157 17.9274 9.83829C17.7272 9.2609 17.6456 8.64905 17.6875 8.03938V1.5625L28.4375 12.8903ZM11.4122 13.4009L11.2375 13.2934H11.13C11.0161 13.2412 10.8924 13.2138 10.7672 13.2128H10.6597C10.566 13.2001 10.4711 13.2001 10.3775 13.2128L10.2431 13.2934H10.1087L5.99684 16.7066C5.89165 16.7902 5.804 16.8939 5.73894 17.0115C5.67388 17.1291 5.63267 17.2584 5.61769 17.392C5.60271 17.5256 5.61425 17.6608 5.65164 17.7899C5.68903 17.919 5.75155 18.0395 5.83559 18.1444C5.92597 18.2658 6.04357 18.3642 6.17893 18.4319C6.31429 18.4996 6.46363 18.5346 6.61497 18.5341C6.84542 18.5324 7.06832 18.4517 7.24653 18.3056L9.71903 16.3438V23.3178C9.71903 23.5851 9.82521 23.8414 10.0142 24.0304C10.2032 24.2194 10.4596 24.3256 10.7268 24.3256C10.9941 24.3256 11.2505 24.2194 11.4395 24.0304C11.6285 23.8414 11.7347 23.5851 11.7347 23.3178V16.4109L13.8847 18.2787C13.9843 18.3672 14.1007 18.4347 14.227 18.4773C14.3532 18.5199 14.4867 18.5367 14.6196 18.5266C14.7525 18.5166 14.882 18.48 15.0004 18.4189C15.1188 18.3579 15.2238 18.2737 15.309 18.1712C15.4825 17.9625 15.5682 17.6946 15.5482 17.4239C15.5281 17.1533 15.4039 16.9009 15.2015 16.72L11.4122 13.4009Z" fill="#78829D"/>
                        </svg>
                        <p class="m-0 text-[13px] text-[#6d7893]">Drag and drop untuk upload file</p>
                        <button id="btn-file-select" type="button" class="h-[38px] px-5 rounded-[8px] border border-[#c8d2e5] bg-white text-[13px] font-semibold text-[#4a5672] hover:border-[#9aaccc]">Pilih File</button>
                        <input id="upload-file-input" type="file" name="file" class="hidden" accept=".geojson,.json" required>
                        <div id="upload-file-name" class="hidden text-[12px] text-[#60708d]"></div>
                        <div class="inline-flex items-center gap-2 rounded-full bg-[#f1f4f9] px-3 py-1 text-[11px] text-[#8893a9]">
                            <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <g clip-path="url(#clip0_1059_3053)">
                                    <path opacity="0.3" d="M11.6073 1.07124L16.7074 4.02004C17.2241 4.31722 17.6531 4.74555 17.951 5.26172C18.249 5.77788 18.4054 6.36358 18.4044 6.95957V12.8572C18.4054 13.4532 18.249 14.0389 17.951 14.555C17.6531 15.0712 17.2241 15.4995 16.7074 15.7967L11.6073 18.7455C11.0908 19.0416 10.5057 19.1974 9.91035 19.1974C9.31497 19.1974 8.72995 19.0416 8.2134 18.7455L3.11327 15.7967C2.59664 15.4995 2.16766 15.0712 1.86968 14.555C1.57171 14.0389 1.41531 13.4532 1.41631 12.8572V6.95957C1.41531 6.36358 1.57171 5.77788 1.86968 5.26172C2.16766 4.74555 2.59664 4.31722 3.11327 4.02004L8.2134 1.07124C8.72995 0.775162 9.31497 0.61939 9.91035 0.61939C10.5057 0.61939 11.0908 0.775162 11.6073 1.07124Z" fill="#78829D"/>
                                    <path d="M9.91124 7.34923C9.27119 7.3492 8.7522 6.83008 8.75206 6.19005C8.75206 5.5499 9.2711 5.0309 9.91124 5.03087C10.5514 5.03087 11.0704 5.54988 11.0704 6.19005C11.0703 6.8301 10.5513 7.34923 9.91124 7.34923ZM9.91124 14.7858C9.66544 14.7857 9.42983 14.688 9.25597 14.5143C9.08206 14.3404 8.98448 14.104 8.98448 13.858V9.5377C8.98448 9.29177 9.08206 9.05536 9.25597 8.88145C9.42983 8.70771 9.66544 8.60999 9.91124 8.60997C10.157 8.60997 10.3926 8.70773 10.5665 8.88145C10.7404 9.05536 10.839 9.29177 10.839 9.5377V13.858C10.839 14.104 10.7404 14.3404 10.5665 14.5143C10.3926 14.688 10.157 14.7858 9.91124 14.7858Z" fill="#78829D"/>
                                </g>
                                <defs>
                                    <clipPath id="clip0_1059_3053">
                                        <rect width="19.8165" height="19.8165" fill="white" transform="matrix(-1 0 0 -1 19.8165 19.8165)"/>
                                    </clipPath>
                                </defs>
                            </svg>
                            Tipe file yang didukung: geojson, json. Ukuran maks. 50MB
                        </div>
                    </div>

                    <div class="mt-4 flex items-center justify-between gap-3">
                        <button id="btn-upload-cancel" type="button" class="h-[40px] min-w-[100px] rounded-[8px] border border-[#c7d1e2] bg-white text-[13px] font-semibold text-[#566684]">Cancel</button>
                        <button id="btn-upload-submit" type="submit" class="h-[40px] min-w-[112px] rounded-[8px] border border-[#1b84ff] bg-[#1b84ff] text-[13px] font-semibold text-white">Unggah</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@include('ipal::components.data-jaringan.scripts')

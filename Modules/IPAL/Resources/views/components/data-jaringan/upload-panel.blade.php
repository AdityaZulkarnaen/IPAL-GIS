<div class="bg-[#f7f8fa] border-2 border-[#e6e8ec] rounded-[14px] shadow-panel p-6 md:p-8 h-full">
    <h3 class="text-ipalText text-[16px] md:text-[24px] font-bold tracking-[-0.01em] text-center mb-5">Upload GeoJSON Jaringan</h3>

    <form action="{{ route('ipal.upload.store') }}" method="POST" enctype="multipart/form-data">
        @csrf

        <div class="mx-auto mb-4 w-[60%] max-w-[620px] bg-[#eceff3] border border-[#e1e4ea] rounded-[10px] p-1.5 flex flex-wrap md:flex-nowrap gap-2">
            <label class="relative flex-1 mb-0 min-w-[140px]">
                <input id="upload-type-pipe" type="radio" name="tipe" value="pipe" class="peer sr-only" checked>
                <span class="w-full min-h-[44px] rounded-lg border border-transparent bg-transparent text-[#7a859d] text-[15px] font-medium cursor-pointer flex items-center justify-center gap-2 transition-all duration-200 peer-checked:!bg-white peer-checked:border-[#d8ddea] peer-checked:text-[#4a5672] peer-checked:shadow-[0_1px_3px_rgba(16,24,40,0.12)]">
                    <svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <line x1="1.25033" y1="12.4167" x2="14.0837" y2="12.4167" stroke="#78829D" stroke-width="1.16667" stroke-linecap="round"/>
                        <line x1="1.25033" y1="3.91667" x2="14.0837" y2="3.91667" stroke="#78829D" stroke-width="1.16667" stroke-linecap="round"/>
                        <path opacity="0.3" d="M3.91699 4.5L3.91699 12.3333" stroke="#78829D" stroke-width="1.16667"/>
                        <path opacity="0.3" d="M11.333 4.5V12.3333" stroke="#78829D" stroke-width="1.16667"/>
                    </svg>
                    Jalur Pipa
                </span>
            </label>
            <label class="relative flex-1 mb-0 min-w-[140px]">
                <input id="upload-type-manhole" type="radio" name="tipe" value="manhole" class="peer sr-only">
                <span class="w-full min-h-[44px] rounded-lg border border-transparent bg-transparent text-[#7a859d] text-[15px] font-medium cursor-pointer flex items-center justify-center gap-2 transition-all duration-200 peer-checked:!bg-white peer-checked:border-[#d8ddea] peer-checked:text-[#4a5672] peer-checked:shadow-[0_1px_3px_rgba(16,24,40,0.12)]">
                    <svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <g clip-path="url(#clip0_782_405)">
                        <circle opacity="0.3" cx="8.00033" cy="8.00008" r="6.66667" stroke="#99A1B7" stroke-width="1.33333"/>
                        <circle cx="8" cy="8" r="3.33333" stroke="#99A1B7" stroke-width="1.33333"/>
                        </g>
                        <defs>
                        <clipPath id="clip0_782_405">
                        <rect width="16" height="16" fill="white"/>
                        </clipPath>
                        </defs>
                    </svg>
                    Manhole
                </span>
            </label>
        </div>

        <div id="geojson-dropzone" class="border-2 border-dashed border-[#a8b9d0] rounded-[10px] min-h-[280px] px-5 py-7 bg-[#f7f8fa] flex flex-col items-center justify-center gap-3 text-center">
            <div class="w-[46px] h-[46px] text-[#8b99b4]" aria-hidden="true">
                <svg width="46" height="46" viewBox="0 0 30 30" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path opacity="0.3" d="M28.4378 15H22.0819C21.2048 14.9777 20.341 14.7806 19.541 14.4204C18.7409 14.0602 18.0208 13.544 17.4227 12.9021C16.8246 12.2602 16.3605 11.5055 16.0576 10.682C15.7548 9.8586 15.6191 8.98304 15.6587 8.10656V1.5625H9.06093C7.05079 1.59083 5.13363 2.41386 3.72857 3.85166C2.32351 5.28945 1.54485 7.22506 1.5628 9.23531V20.7647C1.54485 22.7749 2.32351 24.7105 3.72857 26.1483C5.13363 27.5861 7.05079 28.4092 9.06093 28.4375H20.9128C22.9265 28.4127 24.8485 27.5916 26.2586 26.1539C27.6687 24.7161 28.4522 22.7785 28.4378 20.7647V15Z" fill="#78829D"/>
                    <path d="M28.4375 12.8903H22.0815C21.4707 12.8719 20.8699 12.7303 20.315 12.4742C19.7602 12.2181 19.2627 11.8526 18.8525 11.3997C18.4422 10.9467 18.1276 10.4157 17.9274 9.83829C17.7272 9.2609 17.6456 8.64905 17.6875 8.03938V1.5625L28.4375 12.8903ZM11.4122 13.4009L11.2375 13.2934H11.13C11.0161 13.2412 10.8924 13.2138 10.7672 13.2128H10.6597C10.566 13.2001 10.4711 13.2001 10.3775 13.2128L10.2431 13.2934H10.1087L5.99684 16.7066C5.89165 16.7902 5.804 16.8939 5.73894 17.0115C5.67388 17.1291 5.63267 17.2584 5.61769 17.392C5.60271 17.5256 5.61425 17.6608 5.65164 17.7899C5.68903 17.919 5.75155 18.0395 5.83559 18.1444C5.92597 18.2658 6.04357 18.3642 6.17893 18.4319C6.31429 18.4996 6.46363 18.5346 6.61497 18.5341C6.84542 18.5324 7.06832 18.4517 7.24653 18.3056L9.71903 16.3438V23.3178C9.71903 23.5851 9.82521 23.8414 10.0142 24.0304C10.2032 24.2194 10.4596 24.3256 10.7268 24.3256C10.9941 24.3256 11.2505 24.2194 11.4395 24.0304C11.6285 23.8414 11.7347 23.5851 11.7347 23.3178V16.4109L13.8847 18.2787C13.9843 18.3672 14.1007 18.4347 14.227 18.4773C14.3532 18.5199 14.4867 18.5367 14.6196 18.5266C14.7525 18.5166 14.882 18.48 15.0004 18.4189C15.1188 18.3579 15.2238 18.2737 15.309 18.1712C15.4825 17.9625 15.5682 17.6946 15.5482 17.4239C15.5281 17.1533 15.4039 16.9009 15.2015 16.72L11.4122 13.4009Z" fill="#78829D"/>
                </svg>
            </div>

            <div class="text-ipalMuted text-[12px] md:text-[16px] font-medium leading-[1.35]">Drag and drop untuk upload file</div>

            <button id="btn-file-select" type="button" class="border border-[#c7cfde] bg-white text-[#3d4a67] font-semibold rounded-[9px] px-6 py-2.5 hover:border-[#9ba7bf] hover:text-[#2d3955] transition-colors">
                Pilih File
            </button>

            <input id="upload-file-input" type="file" name="file" class="hidden" accept=".geojson,.json" required>
            <div id="upload-file-name" class="text-[#5f6b86] text-xs min-h-[18px]"></div>

            <div class="mt-1 inline-flex items-center gap-2 text-[#8893a9] bg-[#eef1f6] rounded-full px-3 py-1 text-xs">
                <i class="fas fa-info-circle" aria-hidden="true"></i>
                Tipe file yang didukung: geojson, json. Ukuran maks. 50MB
            </div>
        </div>

        <div class="mt-4 flex flex-col sm:flex-row items-stretch sm:items-center gap-2.5">
            <button id="btn-upload-cancel" type="button" class="min-w-[108px] rounded-[10px] border border-[#b8c3d9] text-[#5a6786] bg-[#f9fafc] px-4 py-2.5 sm:mr-auto">Cancel</button>
            <button type="submit" class="min-w-[136px] rounded-[10px] bg-[#1B84FF] text-white font-semibold px-4 py-2.5 sm:ml-auto">Unggah</button>
        </div>
    </form>
</div>

{{--
    Document Viewer Modal
    Listens for the Alpine 'open-document-viewer' event dispatched by the
    View buttons in the Uploaded Documents infolist section.

    Supported types:
      • pdf   → embedded <iframe>
      • jpg / jpeg / png / gif / webp / bmp / svg  → <img>
      • doc / docx / xls / xlsx / ppt / pptx        → Google Docs Viewer <iframe>
--}}
<div
    x-data="{
        open: false,
        url: '',
        ext: '',
        label: '',

        imageExts: ['jpg','jpeg','png','gif','webp','bmp','svg'],
        officeExts: ['doc','docx','xls','xlsx','ppt','pptx'],

        get isImage()  { return this.imageExts.includes(this.ext); },
        get isPdf()    { return this.ext === 'pdf'; },
        get isOffice() { return this.officeExts.includes(this.ext); },
        get viewerUrl() {
            if (this.isOffice) {
                return 'https://docs.google.com/gview?url=' + encodeURIComponent(this.url) + '&embedded=true';
            }
            return this.url;
        },
    }"
    @open-document-viewer.window="
        url   = $event.detail.url;
        ext   = ($event.detail.ext || '').toLowerCase();
        label = $event.detail.label || 'Document';
        open  = true;
    "
    @keydown.escape.window="open = false"
    x-show="open"
    style="display: none;"
    class="fixed inset-0 z-[9999] flex items-center justify-center p-4"
>
    {{-- Backdrop --}}
    <div
        class="absolute inset-0 bg-black/60 backdrop-blur-sm"
        @click="open = false"
    ></div>

    {{-- Modal panel --}}
    <div
        class="relative z-10 flex flex-col w-full max-w-5xl max-h-[90vh] bg-white dark:bg-gray-900 rounded-xl shadow-2xl overflow-hidden"
        @click.stop
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0 scale-95"
        x-transition:enter-end="opacity-100 scale-100"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100 scale-100"
        x-transition:leave-end="opacity-0 scale-95"
    >
        {{-- Header --}}
        <div class="flex items-center justify-between px-5 py-3 border-b border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800 shrink-0">
            <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-200 capitalize" x-text="label.replace(/_/g, ' ')"></h3>
            <button
                type="button"
                @click="open = false"
                class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-200 transition"
                aria-label="Close viewer"
            >
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>

        {{-- Body --}}
        <div class="flex-1 overflow-auto bg-gray-100 dark:bg-gray-950 flex items-center justify-center min-h-0">

            {{-- PDF --}}
            <template x-if="isPdf">
                <iframe
                    :src="url"
                    class="w-full h-full min-h-[70vh] border-0"
                    title="PDF Viewer"
                ></iframe>
            </template>

            {{-- Image --}}
            <template x-if="isImage">
                <div class="p-4 flex items-center justify-center w-full h-full">
                    <img
                        :src="url"
                        :alt="label"
                        class="max-w-full max-h-[75vh] object-contain rounded shadow"
                    >
                </div>
            </template>

            {{-- Office documents via Google Docs Viewer --}}
            <template x-if="isOffice">
                <div class="w-full h-full flex flex-col items-center justify-center gap-3 p-6 min-h-[70vh]">
                    <p class="text-sm text-gray-500 dark:text-gray-400">
                        Loading document preview via Google Docs Viewer…
                    </p>
                    <iframe
                        :src="viewerUrl"
                        class="w-full h-full min-h-[65vh] border-0 rounded shadow"
                        title="Document Viewer"
                    ></iframe>
                </div>
            </template>

            {{-- Unsupported / unknown --}}
            <template x-if="!isPdf && !isImage && !isOffice">
                <div class="flex flex-col items-center justify-center gap-4 p-10 text-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-14 w-14 text-gray-300 dark:text-gray-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                    <p class="text-gray-500 dark:text-gray-400 text-sm">Preview not available for this file type.</p>
                    <a
                        :href="url"
                        target="_blank"
                        class="inline-flex items-center gap-2 text-sm font-medium text-primary-600 hover:underline"
                    >
                        Open file in new tab
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                        </svg>
                    </a>
                </div>
            </template>
        </div>

        {{-- Footer --}}
        <div class="flex items-center justify-end gap-3 px-5 py-3 border-t border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800 shrink-0">
            <a
                :href="url"
                target="_blank"
                class="text-xs text-gray-500 dark:text-gray-400 hover:underline"
            >Open in new tab</a>
            <button
                type="button"
                @click="open = false"
                class="px-4 py-1.5 text-sm font-medium rounded-lg bg-gray-200 hover:bg-gray-300 dark:bg-gray-700 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-200 transition"
            >
                Close
            </button>
        </div>
    </div>
</div>

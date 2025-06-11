<template x-if="$store.dmcFileManageDetail.show">
    <div x-data="dmcFileManageDetailX" x-bind:style="{ zIndex: $store.libs.getLastIndex() + 1 }"
        class="dialog dmcFileManageDetail reportCustomerDetail_component">
        <div class="dialog-wrapper">
            <div class="dialog-container" style="background: #343639;">
                <div class="dialog-form reportDetail" style="width: 65vw;height: 80vh;">
                    <div class="titleReportDetail" style="border-bottom: 1px solid #757575;">
                        <h3 style="color:#fff">Preview File</h3>
                        <button type="button" @click="$store.dmcFileManageDetail.show = false" class="btnClose"><i
                                class='bx bx-x'></i></button>

                    </div>
                    <div style="height: 100%;">
                        <embed type="application/pdf" :src="pathUrlFile+'#view=Fit'" width="100%" height="700px"
                            alt="pdf" pluginspage="http://www.adobe.com/products/acrobat/readstep2.html"
                            background-color="0xFF525659" top-toolbar-height="0" full-frame="" internalinstanceid="21"
                            title="CHROME" style="height: 100%;"/>

                        {{-- <iframe :src="pathUrlFile+'#toolbar=0'" width="500" height="400"></iframe> --}}
                        {{-- <iframe src="https://docs.google.com/spreadsheets/d/13R8O15c_sZKZT2QRHom1z2SDA3E1O5chUvROnqHCkwE/pubhtml?widget=true&amp;headers=true" style="width:100%;height:100%;"></iframe> --}}
                        {{-- <iframe src="https://drive.google.com/viewerng/viewer?url=http://docs.google.com/fileview?id=0B5ImRpiNhCfGZDVhMGEyYmUtZTdmMy00YWEyLWEyMTQtN2E2YzM3MDg3MTZh&hl=en&pid=explorer&efh=false&a=v&chrome=false&embedded=true" frameborder="0"></iframe> --}}
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
<script>
    Alpine.data("dmcFileManageDetailX", () => ({
        data: null,
        disabled: false,
        loading: false,
        timeCloseAuto: 0,
        pathUrlFile: null,
        async init() {
            this.data = this.$store.dmcFileManageDetail.data;
            this.data.file_type = this.data.file_type == 'invoice_void' ? 'invoice' : this.data.file_type;

            const month = this.data.month <= 9 ? '0' + this.data.month : this.data.month;
            const path = `{!! url('/') !!}/uploads/${this.data.file_type}/${this.data.year}/${month}/${this.data.file_name}`;
            // let file = await this.createFile(path, this.data.file_name, this.data.extension_type);
            this.pathUrlFile = path;
            // await this.readFilePdf(file);
        },
        async createFile(url, name, type) {
            if (typeof window === 'undefined') return false;
            const response = await fetch(url)
            const data = await response.blob()
            const metadata = {
                type: type || 'video/quicktime'
            }
            return new File([data], name, metadata)
        },
        async readFilePdf(file) {
            // let file = await this.createFile(path, this.data.file_name, this.data.extension_type);
            if (file.type == "pdf") {
                var fileReader = new FileReader();
                fileReader.onload = (e) => {
                    var pdfData = new Uint8Array(this.result);
                    // Using DocumentInitParameters object to load binary data.
                    var loadingTask = pdfjsLib.getDocument({
                        data: pdfData
                    });
                    loadingTask.promise.then((pdf) => {
                        console.log('PDF loaded');

                        // Fetch the first page
                        var pageNumber = 1;
                        pdf.getPage(pageNumber).then((page) => {
                            console.log('Page loaded');

                            var scale = 1.5;
                            var viewport = page.getViewport({
                                scale: scale
                            });

                            // Prepare canvas using PDF page dimensions
                            var canvas = $("#pdfViewer")[0];
                            var context = canvas.getContext('2d');
                            canvas.height = viewport.height;
                            canvas.width = viewport.width;

                            // Render PDF page into canvas context
                            var renderContext = {
                                canvasContext: context,
                                viewport: viewport
                            };
                            var renderTask = page.render(renderContext);
                            renderTask.promise.then(() => {
                                console.log('Page rendered');
                            });
                        });
                    }, (reason) => {
                        // PDF loading error
                        console.error(reason);
                    });
                };
                fileReader.readAsArrayBuffer(file);
            }
        },
        open(options) {
            this.data = options.data;
            this.afterClosed = options.afterClosed ?? null;
            this.show = true;
        },
        close(data = null) {
            Alpine.store('animate').leave(this.target, () => {
                this.show = false;
                if (typeof this.afterClosed === 'function') {
                    this.afterClosed(data);
                }
            });
        },
    }));
    Alpine.store('dmcFileManageDetail', {
        target: null,
        data: null,
        show: false,
        afterClosed: () => {},
        open(options) {
            this.data = options.data;
            this.afterClosed = options.afterClosed ?? null;
            this.show = true;
        },
        close(data = null) {
            Alpine.store('animate').leave(this.target, () => {
                this.show = false;
                if (typeof this.afterClosed === 'function') {
                    this.afterClosed(data);
                }
            });
        },
    });
</script>

<div class="dialog" x-data="uploadFile">
    <div class="dialog-container">
        <div class="dialog-form" style="width: 400px">
            <div class="dialog-form-header">
                <h3>@lang('file-manager.upload-file.title')</h3>
            </div>
            <div class="dialog-form-body">
                <div class="form-row no-label">
                    <div class="form-select-file">
                        <div class="select-file" @click="onSelectFile()">
                            <div class="icon">
                                <i data-feather="upload-cloud"></i>
                            </div>
                            <div class="title">
                                <span>@lang('file-manager.upload-file.form.file.placeholder')</span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="file-upload-list">
                    <template x-for="(item, index) in form.value.files">
                        <div class="file-upload-item">
                            <div class="file-upload-item-header">
                                <div class="item-thumbnail">
                                    <img src="{{ asset('images/logo/upload.png') }}" alt="">
                                </div>
                                <div class="file-upload-item-title">
                                    <span x-text="item.file.name"></span>
                                </div>
                            </div>
                            <div class="file-upload-item-body">
                                <div :class="`item-progress-bar ${item.error?'error':''}`"
                                    :style="{ width: item.percent + '%' }">
                                </div>
                            </div>
                        </div>
                    </template>
                </div>
            </div>
            <div class="dialog-form-footer">
                <button type="button" class="close" @click="onClose()">@lang('file-manager.upload-file.button.close')</button>
            </div>
        </div>
    </div>
    <script>
        Alpine.data('uploadFile', () => ({
            data: null,
            id: null,
            form: {
                value: {
                    files: []
                },
                validate_message: {},
                loading: false,
                disabled: false,
            },
            init() {
                this.data = this.dialog.data['uploadFileDialog'];
                this.id = this.dialog.data['uploadFileDialog'];
                this.reloadIcon();
                Alpine.store('animate').enter(this.$root.children[0]);
            },
            onClose(data = null) {
                Alpine.store('animate').leave(this.$root.children[0], () => {
                    this.dialog.close('uploadFileDialog', data);
                });
            },
            onSelectFile() {
                const po_id = this.id;
                const url =
                    `{{ route('admin-po-service-upload') }}?folder_id=&po_id=${po_id}`;
                $onUploadFile(url, true,
                    'image/*, application/pdf',
                    (loading,
                        data, file, percent, key, error) => {
                        if (this.checkExist(key)) {
                            this.form.value.files.map(item => {
                                if (item.key === key) {
                                    if (error) {
                                        item.error = true;
                                    } else {
                                        item.percent = percent;
                                        item.loading = loading;
                                    }
                                }
                            });
                        } else {
                            if (file) {
                                this.form.value.files.push({
                                    key,
                                    file,
                                    percent,
                                    loading
                                });
                            }
                        }
                        setTimeout(() => {
                            if (this.form.value.files.every(item => !item.loading)) {
                                this.$store.uploadFileList.active = false;
                                this.onClose();
                                setTimeout(() => {
                                    uploadFileList({
                                        active: true,
                                        data: this.$store.uploadFileList.options
                                            .data,
                                        title: "File",
                                        config: {
                                            width: "55%",
                                        }
                                    });
                                }, "10");
                            }
                        }, 1000);
                        this.reloadIcon();
                    });
            },
            cancelUpload(file, index) {},
            checkExist(key) {
                return this.form.value.files.some(item => item.key === key);
            },
            onSave() {
                this.onClose();
            }
        }));
    </script>
</div>

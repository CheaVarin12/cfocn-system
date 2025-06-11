<template x-if="$store.authDetail.show">
    <div x-data="XauthDetail"
        class="dialog reportCustomerDetail_component" x-bind:onload="dialogInit" >
        <div class="dialog-wrapper">
            <div class="authDialog">
                <div class="dialog-form reportDetail" style="width: 50vw;">
                    <div class="titleReportDetail">
                        <h3>Personal Account</h3>
                        <button type="button" @click="$store.authDetail.show = false" class="btnClose"><i
                                class='bx bx-x'></i></button>
                    </div>
                    <div class="authDetailLayout">
                        <div class="authLeft">
                            <div class="imgGp">
                                <img :src="data?.image_url">
                            </div>
                            <div class="textGp">
                                <div class="text">
                                    <label><i class='bx bx-user'></i></label>
                                    <span>:</span>
                                    <p x-text="data?.username??''"></p>
                                </div>
                                <div class="text">
                                    <label><i class='bx bx-phone'></i></label>
                                    <span>:</span>
                                    <p x-text="data?.phone??''"></p>
                                </div>
                                <div class="text">
                                    <label><i class='bx bx-at'></i></label>
                                    <span>:</span>
                                    <p x-text="data?.email??''"></p>
                                </div>
                            </div>
                            <div class="authLeftAction">
                                <div class="btnAuth" @click="onEdit()">
                                    <i class='bx bx-edit-alt'></i>
                                    <span>Edit</span>
                                </div>
                                <div class="btnAuth blue" @click="onChangePassword()">
                                    <i class='bx bxs-key'></i>
                                    <span>Change password</span>
                                </div>
                            </div>
                        </div>
                        <div class="authRight">
                            <div class="authTab">
                                <div class="tabItem" :class="tab == 'form' ? 'active' : ''" @click="tab='form'">Edit
                                    Personal Account
                                </div>
                                <div class="tabItem" :class="tab == 'change_password' ? 'active' : ''"
                                    @click="tab='change_password'">
                                    Change
                                    Password</div>
                            </div>
                            <template x-if="tab=='form'">
                                <div class="formLayout">
                                    <div class="formItem">
                                        <label>Name<span>*</span></label>
                                        <input type="text" placeholder="Entere name ..." x-model="form.username"
                                            :disabled="disabled" />
                                        <template x-for="item in dataError?.username">
                                            <div class="error" x-text="item"></div>
                                        </template>
                                    </div>
                                    <div class="formItem">
                                        <label>Phone</label>
                                        <input type="text" placeholder="Enter phone ..." x-model="form.phone"
                                            :disabled="disabled" />
                                        <template x-for="item in dataError?.phone">
                                            <div class="error" x-text="item"></div>
                                        </template>
                                    </div>
                                    <div class="formItem">
                                        <label>Email<span>*</span></label>
                                        <input type="text" placeholder="Enter email ..." x-model="form.email"
                                            :disabled="disabled" />
                                        <template x-for="item in dataError?.email">
                                            <div class="error" x-text="item"></div>
                                        </template>
                                    </div>
                                    <div class="formItem">
                                        <label>Profile</label>
                                        <div class="form-select-photo image"
                                            @click="disabled == false ? selectImage(event):''">
                                            <div class="select-photo" :class='{ active: form?.image }'>
                                                <div class="icon">
                                                    <i class='bx bx-image-alt'></i>
                                                </div>
                                                <div class="title">
                                                    <span>Choose upload</span>
                                                </div>
                                            </div>
                                            <template x-if="form?.image">
                                                <div class="image-view active">
                                                    <img x-bind:src="baseImageUrl + form?.image" alt="">
                                                </div>
                                            </template>
                                            <input type="hidden" x-model="form.image" autocomplete="off"
                                                role="presentation" :disabled="disabled">
                                        </div>
                                        <template x-for="item in dataError?.image">
                                            <span class="error" x-text="item">Error</span>
                                        </template>
                                    </div>
                                </div>
                            </template>
                            <template x-if="tab=='change_password'">
                                <div class="formLayout">
                                    <div class="formItem" x-data={show:false}>
                                        <label>Current Password<span>*</span></label>
                                        <div class="inputGp">
                                            <input x-bind:type="!show ? 'password' : 'text'"
                                                placeholder="Enter current password ..."
                                                x-model="password.current_password" :disabled="disabled" />
                                            <div class="group-item" @click="show = !show">
                                                <div x-show="show" class="icon">
                                                    <span class="material-symbols-outlined">visibility</span>
                                                </div>
                                                <div x-show="!show" class="icon">
                                                    <span class="material-symbols-outlined">
                                                        visibility_off
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                        <template x-for="item in dataError?.current_password">
                                            <div class="error" x-text="item"></div>
                                        </template>
                                    </div>
                                    <div class="formItem" x-data={show:false}>
                                        <label>New Password<span>*</span></label>
                                        <div class="inputGp">
                                            <input x-bind:type="!show ? 'password' : 'text'"
                                                placeholder="Enter new password ..." x-model="password.password"
                                                :disabled="disabled" />
                                            <div class="group-item" @click="show = !show">
                                                <div x-show="show" class="icon">
                                                    <span class="material-symbols-outlined">visibility</span>
                                                </div>
                                                <div x-show="!show" class="icon">
                                                    <span class="material-symbols-outlined">
                                                        visibility_off
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                        <template x-for="item in dataError?.password">
                                            <div class="error" x-text="item"></div>
                                        </template>
                                    </div>
                                    <div class="formItem" x-data={show:false}>
                                        <label>Confirm Password<span>*</span></label>
                                        <div class="inputGp">
                                            <input x-bind:type="!show ? 'password' : 'text'"
                                                placeholder="Enter confirm password ..."
                                                x-model="password.confirm_password" :disabled="disabled" />
                                            <div class="group-item" @click="show = !show">
                                                <div x-show="show" class="icon">
                                                    <span class="material-symbols-outlined">visibility</span>
                                                </div>
                                                <div x-show="!show" class="icon">
                                                    <span class="material-symbols-outlined">
                                                        visibility_off
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                        <template x-for="item in dataError?.confirm_password">
                                            <div class="error" x-text="item"></div>
                                        </template>
                                    </div>
                                </div>
                            </template>
                            <div class="authRightAction">
                                <div class="btnAuth" @click="submitForm()">
                                    <i class='bx bx-loader-alt bx-spin' x-show="submitLoading"></i>
                                    <i :class="'bx bxs-' + (tab == 'form' ? 'save' : 'key')"
                                        x-show="!submitLoading"></i>
                                    <span x-text="tab=='form' ? 'Update':'Reset password'"></span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <template x-if="submitLoading">
                        <div class="disableDialog"></div>
                    </template>

                </div>
            </div>
        </div>
        @include('admin::file-manager.popup')
    </div>
</template>

<script>
    Alpine.data("XauthDetail", () => ({
        data: null,
        disabled: false,
        loading: false,
        timeCloseAuto: 0,
        tab: 'form',
        submitLoading: false,
        dataError: [],
        form: {
            username: null,
            phone: null,
            email: null,
            image: null
        },
        password: {
            current_password: null,
            password: null,
            confirm_password: null
        },
        baseImageUrl: "{{ asset('file_manager') }}",
        init() {
            this.data = this.$store.authDetail.data;
        },
        dialogInit() {
            feather.replace();
            const target = this.$root.querySelector('.dialog-container');
            this.$store.libs.playAnimateOnLoad(target);
            this.$store.authDetail.target = target;
        },
        selectImage() {
            fileManager({
                multiple: false,
                afterClose: (data, basePath) => {
                    if (data?.length > 0) {
                        this.form.image = data[0].path;
                    }
                }
            })
        },
        onEdit() {
            this.form.username = this.data.username;
            this.form.phone = this.data.phone;
            this.form.email = this.data.email;
            this.form.image = this.data.avatar;
            this.tab = "form";
        },
        onChangePassword() {
            this.tab = "change_password";
        },
        submitForm() {
            let status = 'update';
            let url = "/admin/auth/save";
            let form = this.form;
            if (this.tab == "change_password") {
                status = 'change password';
                url = '/admin/auth/change-password';
                form = this.password;
            }
            this.onPost(url, status, form);
        },
        onPost(url, status, form) {
            this.$store.confirmDialog.open({
                data: {
                    title: "Message",
                    message: `Are you sure want to ${status} ?`,
                    btnClose: "Close",
                    btnSave: "Yes",
                },
                afterClosed: (result) => {
                    if (result) {
                        this.submitLoading = true;
                        this.disabled = true;
                        let data = form;
                        data.id = this.data.id;
                        this.dataError = [];
                        let dataTimeOut = null;
                        clearTimeout(dataTimeOut);
                        dataTimeOut = setTimeout(() => {
                            Axios({
                                url: url,
                                method: 'POST',
                                data: {
                                    ...data,
                                }
                            }).then((res) => {
                                if (res.data.message) {
                                    Toast({
                                        message: `Your account ${status} successful`,
                                        status: 'success',
                                        size: 'small',
                                    });
                                    this.data = res.data.data;
                                    this.resetForm();
                                    this.resetChangePassword();
                                }

                                this.submitLoading = false;
                                this.disabled = false;
                            }).catch((e) => {
                                this.dataError = e
                                    .response?.data
                                    .errors;
                                this.submitLoading = false;
                                this.disabled = false;
                            }).finally(() => {
                                this.submitLoading = false;
                                this.disabled = false;
                            });
                        }, 500);
                    }
                }
            });
        },
        resetForm() {
            this.form = {
                username: null,
                phone: null,
                email: null,
                disable: false,
            }
        },
        resetChangePassword() {
            this.password = {
                current_password: null,
                new_password: null,
                confirm_password: null,
                disable: false,
            }
        }
    }))
</script>
<script>
    Alpine.store('authDetail', {
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

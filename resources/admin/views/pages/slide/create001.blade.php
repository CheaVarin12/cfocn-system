@extends('admin::shared.layout')
@section('layout')
    <div class="form-admin" x-data="xService">
        <div class="form-bg"></div>
        <form id="form" class="form-wrapper" action="#" method="POST" enctype="multipart/form-data">
            <div class="form-header">
                <h3>
                    <i data-feather="arrow-left" s-click-link="{!! route('admin-slide-list', 1) !!}"></i>
                    {!! request('id')
                        ? __('adminGlobal.form.title.updateSlide', ['name' => __('adminGlobal.name')])
                        : __('adminGlobal.form.title.createSlide', ['name' => __('adminGlobal.name')]) !!}
                </h3>
            </div>
            {{ csrf_field() }}
            <div class="form-body" x-data="{ tabSta: 'km' }">
                <div class="productFormLayout">
                    <div class="pHeader">
                        <img src="{{ asset('images/logo/profile.png') }}">
                        <div class="pText">
                            <p>Bundle</p>
                            <button>Change type</button>
                        </div>
                    </div>
                    <div class="inputFormLayout">
                        <div class="row2Input">
                            <div class="inputGp">
                                <div class="inputItem">
                                    <label for="name">Name<span>*</span></label>
                                    <textarea name="name"></textarea>
                                    <label class="error">Name is required</label>
                                </div>
                                <div class="inputItem">
                                    <label for="name">SKU<span>*</span></label>
                                    <input type="text" name="sku" value="{!! old('name') !!}"
                                        placeholder="@lang('form.form.name.placeholder.sku')">
                                    <label class="error">Name is required</label>
                                </div>
                            </div>
                            <div class="imgGp">
                                <div class="form-select-photo image">
                                    <div class="select-photo {!! request('id') && isset($data) && $data->profile != null ? 'active' : '' !!}">
                                        <div class="icon">
                                            <i data-feather="image"></i>
                                        </div>
                                        <div class="title">
                                            <span>@lang('adminGlobal.form.image.placeholder')</span>
                                        </div>
                                    </div>
                                    <div class="image-view">
                                        <img src="{!! request('id') && isset($data) && $data->image != null ? asset('file_manager' . $data->image) : null !!}"
                                            onerror="(this).src='{{ asset('images/logo/default.png') }}'" alt="">
                                    </div>
                                    <input type="text" name="image" s-click-fn="selectImage(event)" autocomplete="off"
                                        role="presentation">
                                    <div class="imageGpbtnActionlayout">
                                        <button><i data-feather="edit"></i></button>
                                        <button><i data-feather="trash"></i></button>
                                    </div>
                                </div>
                                @error('image')
                                    <label class="error">{{ $message }}</label>
                                @enderror
                            </div>
                        </div>
                        <div class="inputItem">
                            <label for="name">Description<span>*</span></label>
                            <textarea type="text" name="description" class="des">{!! old('description') !!}</textarea>
                            <label class="error">Description is required</label>
                        </div>
                        <div class="tableProductListingLayout">
                            <div class="itemTableProductGp">
                                <label class="label">Product/services included in the bundle </label>
                                <div class="checkBoxGp">
                                    <input type="checkbox" />
                                    <label>Display bundle components when printing or sending transactions</label>
                                </div>
                                <div class="PTableGp">
                                    <div class="header rowTable">
                                        <div class="colT10">&nbsp;</div>
                                        <div class="colT70">PRODUCT/SERVICE</div>
                                        <div class="colT10 textCenter">QTY</div>
                                        <div class="colT10">&nbsp;</div>
                                    </div>
                                    <div class="body rowTable">
                                        <div class="colT10 textCenter"><i data-feather="grid" class="opacity3"></i></div>
                                        <div class="colT70">
                                            <div class="tableItemListingGp">
                                                <img src="{{ asset('images/logo/default.png') }}">
                                                <div class="tableItemTextGp">
                                                    <h3>WATER FEATURE PLUMBING-LABOR ONLY</h3>
                                                    <p>Install plumbing for water feature</p>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="colT10 textCenter">1</div>
                                        <div class="colT10">
                                            <div class="btnTrash"><i data-feather="trash"></i></div>
                                        </div>
                                    </div>
                                    <div class="body rowTable addInputDynamicLayout">
                                        <div class="inputAddGp">
                                            <div class="colT10 textCenter">
                                                <div class="btnAdd"><i data-feather="plus"></i></div>
                                            </div>
                                            <div class="colT70">
                                                <input type="text" placeholder="Enter text" />
                                            </div>
                                            <div class="colT10 textCenter">
                                                <input type="number" value="1" />
                                            </div>
                                            <div class="colT10">
                                                <div class="btnTrash"><i data-feather="trash"></i></div>
                                            </div>
                                        </div>
                                        <div class="inputAddGp">
                                            <div class="colT10 textCenter">
                                                <div class="btnAdd"><i data-feather="plus"></i></div>
                                            </div>
                                            <div class="colT70">
                                                <input type="text" placeholder="Enter text" />
                                            </div>
                                            <div class="colT10 textCenter">
                                                <input type="number" value="1" />
                                            </div>
                                            <div class="colT10">
                                                <div class="btnTrash"><i data-feather="trash"></i></div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="body rowTable">
                                        <div class="colT10 textCenter"><i data-feather="grid" class="opacity3"></i></div>
                                        <div class="colT70">
                                            <div class="tableItemListingGp">
                                                <img src="{{ asset('images/logo/default.png') }}">
                                                <div class="tableItemTextGp">
                                                    <h3>WATER FEATURE PLUMBING-LABOR ONLY</h3>
                                                    <p>Install plumbing for water feature</p>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="colT10 textCenter">1</div>
                                        <div class="colT10">
                                            <div class="btnTrash"><i data-feather="trash"></i></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="btnAddLineGp">
                                <button type="button">
                                    <i data-feather="plus"></i>
                                    <span>Add line</span>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="form-button">
                    <button type="submit" color="primary">
                        <i data-feather="save"></i>
                        <span>@lang('adminGlobal.form.button.submit')</span>
                    </button>
                    <button color="danger" type="button" s-click-link="{!! route('admin-slide-list', 1) !!}">
                        <i data-feather="x"></i>
                        <span>@lang('adminGlobal.form.button.cancel')</span>
                    </button>
                </div>
            </div>
            <div class="form-footer"></div>
        </form>
    </div>
    @include('admin::file-manager.popup')
@stop

@section('script')
    <script lang="ts">
        function selectImage(e) {
            fileManager({
                multiple: false,
                afterClose: (data, basePath) => {
                    if (data?.length > 0) {
                        const parent = e.target.closest('.form-select-photo');
                        e.target.value = data[0].path;
                        parent
                            .querySelector('.select-photo')
                            .classList.add('active');
                        parent
                            .querySelector('.image-view')
                            .classList
                            .add('active');
                        parent
                            .querySelector('.image-view')
                            .childNodes[0]
                            .nextElementSibling
                            .setAttribute('src', basePath + data[0].path);
                    }
                }
            })
        }
    </script>
    <script>
        const header = {
            headers: {
                "Content-Type": "application/x-www-form-urlencoded;charset=utf-8",
                Accept: "application/json",
            },
            responseType: "json",
        };
        document.addEventListener('alpine:init', () => {
            Alpine.data("xService", () => ({}));
        });
    </script>
@stop

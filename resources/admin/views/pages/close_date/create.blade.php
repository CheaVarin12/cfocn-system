@extends('admin::shared.layout')
@section('layout')
    <div class="form-admin" x-data="xData">
        <style>
            .ui-datepicker-calendar {
                display: none;
            }
        </style>
        <div class="form-bg"></div>
        <form id="form" class="form-wrapper" action="{!! route('admin-close-date-save', request('id')) !!}" method="POST" enctype="multipart/form-data">
            <div class="form-header">
                <h3>
                    <i data-feather="arrow-left" s-click-link="{!! route('admin-close-date-list', 1) !!}"></i>
                    {{ request('id') ? 'Update' : 'Create' }} close date
                </h3>
            </div>
            {{ csrf_field() }}
            <div class="form-body">
                <div class="row-2">
                    <div class="form-row">
                        <label>Date <span>*</span> </label>
                        <input type="text" name="date" id="date" :value="date" placeholder="select date"
                            autocomplete="off" x-init="dateSelect()">
                        <input type="hidden" name="dateValid" :value="date + '-01'" placeholder="select date"
                            autocomplete="off">
                        @error('dateValid')
                            <label class="error">{{ $message }}</label>
                        @enderror
                    </div>
                    <div class="form-row">
                        <label>@lang('user.form.status.label')<span>*</span></label>
                        <select name="status">
                            <option value="1" {!! (request('id') && $data->status == 1) || old('status') == 1 ? 'selected' : '' !!}>Active</option>
                            <option value="2" {!! (request('id') && $data->status == 2) || old('status') == 2 ? 'selected' : '' !!}>Inactive</option>
                        </select>
                    </div>
                </div>
                <div class="form-button">
                    <button type="submit" color="primary">
                        <i data-feather="save"></i>
                        <span>@lang('adminGlobal.form.button.submit')</span>
                    </button>
                    <button color="danger" type="button" s-click-link="{!! route('admin-close-date-list', 1) !!}">
                        <i data-feather="x"></i>
                        <span>@lang('adminGlobal.form.button.cancel')</span>
                    </button>
                </div>
            </div>
            <div class="form-footer"></div>
        </form>
    </div>
@stop

@section('script')
    <script>
        const header = {
            headers: {
                "Content-Type": "application/x-www-form-urlencoded;charset=utf-8",
                Accept: "application/json",
            },
            responseType: "json",
        };
        document.addEventListener('alpine:init', () => {
            Alpine.data("xData", () => ({
                date: moment().format('YYYY-MM'),
                init() {
                    let data = @json(isset($data) && $data ? $data : null);
                    this.date = data ? moment(data.date, "YYYY-MM-DD").format("YYYY-M") : moment()
                        .format('YYYY-M');
                },
                dateSelect() {
                    $("#date").datepicker({
                        dateFormat: "yy-m",
                        changeMonth: true,
                        changeYear: true,
                        showButtonPanel: false,
                        showAnim: "",
                        showButtonPanel: false,
                        onClose: (text, date) => {
                            let month = Number(date.selectedMonth) + 1;
                            let year = date.selectedYear;
                            this.date = `${year}-${month}`;
                            let check = moment(this.date, "YYYY-MM-DD");
                            $("#date").datepicker('setDate', check.toDate());
                        },
                        beforeShow: () => {
                            if (this.date) {
                                let check = moment(this.date, "YYYY-MM-DD");
                                var month = check.format('M');
                                let year = check.format('YYYY');
                                $("#date").datepicker("option", "defaultDate", check
                                    .toDate());
                                $("#date").datepicker('setDate', check.toDate());
                            }
                        }
                    });
                },
            }));
        });
    </script>
@stop

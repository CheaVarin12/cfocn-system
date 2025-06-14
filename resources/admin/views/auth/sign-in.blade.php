@extends('admin::auth.index')
@section('auth')
    <form class="form" id="login-form" action="{!! route('admin-login-post') !!}" method="post" x-data="login">
        @csrf
        <input type="hidden" name="returnUrl" value={{ request()->returnUrl }}>
        <h2 class="title">@lang('auth.sign-in.title')</h2>
        <div class="form-wrapper">
            <div class="form-row">
                <label for="username">
                    <i data-feather="user"></i>
                    @lang('auth.sign-in.form.username.label')
                </label>
                <input name="email" placeholder="@lang('auth.sign-in.form.username.placeholder')" type="text" error-message="@lang('auth.sign-in.form.username.error')"
                    autocomplete="off">
            </div>
            <div class="form-row">
                <label for="password">
                    <i data-feather="key"></i>
                    @lang('auth.sign-in.form.password.label')
                </label>
                <div class="group-input" x-data={show:false}>
                    <input name="password" x-bind:type="!show ? 'password' : 'text'" placeholder="@lang('auth.sign-in.form.password.placeholder')"
                        error-message="@lang('auth.sign-in.form.password.error')" autocomplete="off">
                    <div class="group-item" @click="show = !show">
                        <span x-show="show">
                            <i data-feather="eye"></i>
                        </span>
                        <span x-show="!show">
                            <i data-feather="eye-off"></i>
                        </span>
                    </div>
                </div>
            </div>
            <input type="hidden" id="dataCurrency" x-model="dataCurrency" :value="dataCurrency" name="exchange_rate">
            <div class="option">
                <div class="remember-me">
                    <input name="remember" id="remember" type="checkbox" value="true">
                    <label for="remember">@lang('auth.sign-in.form.remember')</label>
                </div>
                {{-- <div class="link-label">
                    <span s-click-link="{!! route('admin-forgot-password') !!}">@lang('auth.sign-in.form.forgot-password')</span>
                </div> --}}
            </div>
            <button color="primary" class="btn-submit-form" type="submit">
                <span>@lang('auth.sign-in.form.button')</span>
            </button>
        </div>
        @if (Session::has('status'))
            <p class="q-label-error">
                @lang('auth.sign-in.form.error_login')
            </p>
        @endif
    </form>
@stop
@section('script')
    <script>
        $(document).ready(function() {
            let validate = {
                email: {
                    required: true,
                },
                password: {
                    required: true,
                    minLength: 6,
                    maxLength: 20
                }
            };
            $validator("#login-form", validate, {
                inputClass: "error-input",
                messageClass: "error",
                showMessage: false
            });
        });
    </script>
    <script>
        Alpine.data('login', () => ({
            dataCurrency: null,
            async init() {
                let url = `https://data.mef.gov.kh/api/v1/realtime-api/exchange-rate?currency_id=USD`;
                setTimeout(async () => {
                    try {
                        await this.fetchData(url, (res) => {
                             $('#dataCurrency').val(res.data.average);
                        });
                    } catch (e) {
                        console.error("Fetch error:", e);
                    };
                }, 500);
            },
            exchangeRateData: [], 
            async fetchData(url, callback) {
                try {
                    const response = await fetch(url);
                    const json = await response.json();
                    callback(json);
                } catch (err) {
                    console.error("fetchData error:", err);
                }
            },
        }));
    </script>
@stop

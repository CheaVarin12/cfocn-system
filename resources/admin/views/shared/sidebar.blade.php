<div class="sidebar-wrapper" x-data="sidebar">
    <div class="logo">
        <img src="{!! asset('images/logo/imageCfocn.png') !!}" alt="cfocn">
        <span>CFOCN</span>
    </div>
    <div class="menu-list">
        @foreach (config('menu') as $key => $item)
            @if (
                !isset($item['permission']) ||
                    (isset($item['permission']) &&
                        auth()->user()->canany($item['permission'])))
                <a @click="onClick({!! $key !!}, {{ routeActive($item['active']) }})"
                    class="menu-item {{ routeActive($item['active']) ? 'active' : '' }}"
                    @if (isset($item['path'])) s-click-link="{!! url($item['path']) !!}" @endif
                    x-init="firstCheck({!! $key !!}, {{ routeActive($item['active']) }})">
                    <div class="menu-text">
                        <i data-feather="{!! $item['icon'] !!}"></i>
                        <span>{{ Str::limit($item['name'][App::getLocale()], 15) }}</span>
                        @isset($item['children'])
                            <p :class="{ show: show_menu === {!! $key !!} }">
                                <i data-feather="chevron-down" class="angle-icon"></i>
                            </p>
                        @endisset
                    </div>
                </a>
                @isset($item['children'])
                    <div class="sub-menu" x-show="show_menu == {{ $key }}" style="display:none"
                        x-transition:enter.duration.300ms>
                        @foreach ($item['children'] as $child)
                            @if (
                                !isset($child['permission']) ||
                                    (isset($child['permission']) &&
                                        auth()->user()->canany($child['permission'])))
                                <div class="sub-item {!! routeActive($child['active']) ? 'active' : '' !!}" s-click-link="{!! url($child['path']) !!}">
                                    <i data-feather="disc"></i>
                                    <span>{{ Str::limit($child['name'][App::getLocale()], 15) }}</span>
                                </div>
                            @endif
                        @endforeach
                    </div>
                @endisset
            @endif
        @endforeach
    </div>
    <div class="sidebar-option dropend dropendCusSidebar" title="">
        {{-- <div class="lang-selection">
            @if (App::getLocale() == 'km')
                <img s-click-link="{!! route('admin-change-locale', 'en') !!}" src="{!! asset('images/logo/us.svg') !!}" alt="">
            @else
                <img s-click-link="{!! route('admin-change-locale', 'km') !!}" src="{!! asset('images/logo/km.svg') !!}" alt="">
            @endif

        </div> --}}
        <div class="user-setting dropdown-toggle sliderFooterCusLayout slideFooterPath" data-mdb-toggle="dropdown"
            aria-expanded="false">
            <div class="leftFooter">
                {{-- <div class="fLogo">
                    @if (Auth::user()->avatar)
                        <img src="{{ asset('file_manager' . Auth::user()->avatar) }}" onerror="(this).src='{{ asset('images/logo/profile.png') }}'"/>
                    @else
                        <i data-feather="user"></i>
                    @endif
                </div> --}}
                <span>{!! Auth::user()->username !!}</span>
            </div>
            <div class="rightFooter">
                {{-- <i data-feather="power"></i> --}}
                <div class="fLogo">
                    @if (Auth::user()->avatar)
                        <img src="{{ asset('file_manager' . Auth::user()->avatar) }}"
                            onerror="(this).src='{{ asset('images/logo/profile.png') }}'" />
                    @else
                        <i data-feather="user"></i>
                    @endif
                </div>
            </div>
            <div class="fLogoutSlide">
                <label>Logout</label><i data-feather="power"></i>
            </div>
        </div>
        <ul class="dropdown-menu dropdown-menu-dark user-setting-popup">
            @if (Auth::user()->super_admin != 1)
                {{-- <li><a class="dropdown-item" s-click-link="#" s-click-link="{!! route('admin-user-create', Auth::user()->id) !!}">@lang('setting.account')</a>
                </li> --}}
                {{-- <li><a class="dropdown-item" s-click-link="{!! route('admin-user-change-password', Auth::user()->id) !!}">@lang('setting.change_password')</a></li> --}}
                {{-- <li>
                    <hr class="dropdown-divider" />
                </li> --}}
            @endif
            <li>
                <a class="dropdown-item" @click="viewProfile()">
                    <i class='bx bx-user'></i>
                    <span>Profile</span>
                </a>
                <a class="dropdown-item" href="{{ url('admin/clear-optimize') }}">
                    <i class='bx bx-reset'></i>
                    <span>Clear cache</span>
                </a>
                <a class="dropdown-item sign-out"
                    s-click-fn="$onConfirmMessage(
                            '{!! route('admin-sign-out') !!}',
                            '@lang('dialog.msg.sign_out')',
                            {
                                confirm: '@lang('dialog.button.sign_out')',
                                cancel: '@lang('dialog.button.cancel')'
                            }
                        );">
                    <i class='bx bx-log-out-circle'></i>
                    <span>@lang('setting.sign_out')</span>
                </a>
            </li>
        </ul>
    </div>
    @include('admin::auth.detail')
</div>
<script>
    Alpine.data("sidebar", () => ({
        show_menu: null,
        onClick(key, def) {
            this.show_menu != key ? this.show_menu = key : this.show_menu = null;
        },
        firstCheck(key, def) {
            if (def) {
                this.show_menu = key;
            }
        },
        viewProfile() {
            const user = @json(Auth::user());
            this.$store.authDetail.open({
                data: user
            });
        }
    }));
</script>

@section('title')
    | {{ $header_name }}
@stop
<div class="header-wrapper">
    <div class="btn-toggle-sidebar">
        <span style="margin-right: 12px;cursor: pointer;" @click="sidebarOpen = !sidebarOpen"><i data-feather="align-justify"></i></span> 
        <span>{{ $header_name ?? '' }}</span>
    </div>
    <span class="right">
        {{-- <div class="btn-auth">
            <div class="dropdown">
                <i data-feather="user" class="action-btn" id="dropdownMenuButton" data-mdb-toggle="dropdown"
                    aria-expanded="false">
                </i>
                <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                    <li>
                        <a class="dropdown-item sign-out-btn" data-url="{!! route('sign-out') !!}">
                            <i data-feather="log-out"></i>
                            <span>Sign Out</span>
                        </a>
                    </li>
                </ul>
            </div>
        </div> --}}
    </span>
</div>

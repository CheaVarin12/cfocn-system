<div class="data-empty-container" style="{{ isset($style) ? $style : '' }};">
    <div class="data-empty-wrapper">
        <div class="message">
            <i class='bx bxs-info-circle' style="font-size: 30px;"></i>
            <span class="title">{!! $name !!}</span>
            @isset($msg)
                <span class="des">{!! $msg !!}</span>
            @endisset
            @if (isset($url) &&
    isset($button) &&
    (!isset($permission) ||
        (isset($permission) &&
            auth()->user()->can($permission))))
                <button s-click-link="{!! url($url) !!}">
                    <i data-feather="plus-circle"></i>
                    <span>{!! $button !!}</span>
                </button>
            @endif
        </div>
       
    </div>
</div>

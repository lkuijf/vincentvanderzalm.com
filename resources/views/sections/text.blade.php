<div class="text @if($color)bgc-{{ $color }}@endif">
    <div>
        @if ($image['url'])
            <div class="boxit {{ $orientation }}">
                <div><img src="{!! $image['url'] !!}" alt="{{ $image['alt'] }}"></div>
                <div>
        @endif
        {!! $text !!}
        @if ($image['url'])
                </div>
            </div>
        @endif
    </div>
</div>
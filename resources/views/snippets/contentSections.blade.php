@foreach ($data['content_sections'] as $section)
    {{-- @if ($section['type'] == 'hero')
        @include('sections.hero', [
            'image' => $section['img'], 
            ])
    @endif --}}
    {{-- @if ($section['type'] == 'banner')
        @include('sections.banner', [
            'image' => $section['img'], 
            'disableZoomEffect' => $section['checked'],
            // 'wl' => $section['wl_header'],
            // 'bl' => $section['bl_header'],
            't_align' => $section['text_align'],
            't_color' => $section['text_color'],
            'text' => $section['text'],
            ])
    @endif --}}
    @if ($section['type'] == 'text')
        @include('sections.text', [
            'image' => $section['img'], 
            // 'wl' => $section['wl_header'],
            // 'bl' => $section['bl_header'],
            // 'vAlign' => $section['valign_center'],
            'text' => $section['text'],
            'orientation' => $section['orientation'],
            'color' => $section['color'],
            // 'margin' => $section['margin'],
            ])
    @endif
    {{-- @if ($section['type'] == 'information_blocks_holder')
        @include('sections.information_blocks', ['info_blocks' => $section['blocks']])
    @endif --}}
    {{-- @if ($section['type'] == 'people_holder') --}}
        {{-- @include('sections.people_blocks', ['person_blocks' => $section['blocks']]) --}}
    {{-- @endif --}}
    {{-- @if ($section['type'] == 'person_wraps')
        @include('sections.people_blocks', ['person_blocks' => $section['people']])
    @endif --}}
    {{-- @if ($section['type'] == 'solutions')
        @include('sections.solutions', ['solutions' => $section['icon_boxes']])
    @endif
    @if ($section['type'] == 'activities')
        @include('sections.activities', ['activities' => $section['fields']])
    @endif
    @if ($section['type'] == 'services')
        @include('sections.services', ['background' => $section['background'], 'services' => $section['icon_boxes']])
    @endif
    @if ($section['type'] == 'featured_products')
        @include('sections.featured_products', ['products' => $section['fProducts'], 'title' => $section['fCatTitle'], 'url' => $section['fCatUrl']])
    @endif
    @if ($section['type'] == 'contact_form')
        @if ($section['checked'] == 1)
            @include('snippets.contactform')
        @endif
    @endif
    @if ($section['type'] == 'cta_afspraak_maken')
        @if ($section['checked'] == 1)
            @include('snippets.cta_afpraakMaken')
        @endif
    @endif
    @if ($section['type'] == 'media_picture_gallery')
        @include('sections.media_gallery', ['media_gallery' => $section['gallery']])
    @endif
    @if ($section['type'] == 'team_members')
        @include('sections.team_members', ['members' => $section['members']])
    @endif
    @if ($section['type'] == 'advantages_and_testimonials')
        @include('sections.advantages_and_testimonials', ['advantages' => $section['advantages'], 'testimonials' => $section['testimonials']])
    @endif --}}
@endforeach

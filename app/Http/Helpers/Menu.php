<?php
namespace App\Http\Helpers;

class Menu {
    public $allPagesFlattenedPerParent;
    public $html;
    public $html_mobile;
    public $bNestPageUrls = true;
    public function __construct($menu) {
        $this->allPagesFlattenedPerParent = $menu;
    }
    public function generateUlMenu($parentId = 0, $url = '') {
        if(!$this->bNestPageUrls) $url = '';
        if($parentId == 0) $this->html .= '<ul class="uk-navbar-nav" itemscope itemtype="http://www.schema.org/SiteNavigationElement">' . "\n";
            else $this->html .= '<ul>' . "\n";
        if($parentId == 0) $this->html_mobile .= '<ul class="uk-nav uk-nav-primary" itemscope itemtype="http://www.schema.org/SiteNavigationElement">' . "\n";
            else $this->html_mobile .= '<ul>' . "\n";
            // foreach($this->allPagesFlattenedPerParent[$parentId] as $page) {
        for($x=0;$x<count($this->allPagesFlattenedPerParent[$parentId]);$x++) { // using for(), key can be an order integer
            $page = $this->allPagesFlattenedPerParent[$parentId][$x];
            if($page->hide_from_menu) continue;
            // if($page->title == '[HOMEPAGE]') continue;
            if($page->title == '[HOMEPAGE]') $page->title = 'Home';
            $pageUrl = $url . '/' . $page->slug;
            $this->html .= '<li itemprop="name">';
            $this->html_mobile .= '<li itemprop="name">';
            // if(substr_count($pageUrl, '/') == 2) {
                // $this->html .= '<a href="#">'; // only for Miron Marine Service
            // } else {
                $href = $pageUrl;
                if(isset($page->alt_url) && $page->alt_url) $href = $page->alt_url; // mironmarine.nl
                if($page->title == 'Home') $href = '/';
                $this->html .= '<a itemprop="url" href="' . $href . '">';
                $this->html_mobile .= '<a itemprop="url" href="' . $href . '">';
                // $this->html .= '<a itemprop="url" href="#' . substr($href, 1) . '">'; // rotterdamsehorecawandeling.nl (onepager)
            // }
            $this->html .= $page->title;
            $this->html_mobile .= $page->title;
            $this->html .= '</a>';
            $this->html_mobile .= '</a>';
            if(isset($this->allPagesFlattenedPerParent[$page->id])) $this->generateUlMenu($page->id, $pageUrl);
            $this->html .= '</li>' . "\n";
            $this->html_mobile .= '</li>' . "\n";
        }
        $this->html .= '</ul>' . "\n";
        $this->html_mobile .= '</ul>' . "\n";
    }
    public function show() {
        echo $this->html;
    }
}

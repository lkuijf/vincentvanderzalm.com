<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Helpers\ApiCall;
use App\Http\Helpers\SimplePagesApi;
use App\Http\Helpers\SimplePostsApi;
use App\Http\Helpers\SimpleMediaApi;
use App\Http\Helpers\Menu;
use App\Http\Helpers\PageApi;
use App\Http\Helpers\PostApi;
use App\Http\Helpers\WebsiteOptionsApi;
use App\Http\Helpers\WooApiCall;
use App\Http\Helpers\WooCategoriesApi;
// use App\Http\Helpers\WooCategoryApi;
use App\Http\Helpers\WooFilterProductsApi;

class PagesController extends Controller
{
    public $allMediaById = array();
    public $allPagesPerParent = array();

    public function home() {
        return view('page');
    }

    public function showPage($section, $page, $subpage) {
        $simplePages = new SimplePagesApi();
        $htmlMenu = new Menu($simplePages->get());
        $htmlMenu->generateUlMenu();
        $this->allPagesPerParent = $simplePages->pagesPerParent;
        $allSlugsNested = $simplePages->getAllSlugs();
// dd($allSlugsNested);
        if(!isset($allSlugsNested[$section]) || ($page && !isset($allSlugsNested[$section]['children'][$page])) || ($subpage && !isset($allSlugsNested[$section]['children'][$page]['children'][$subpage]))) {
            return abort(404);
        } else {
            $pageId = $allSlugsNested[$section];
            if($page) $pageId = $allSlugsNested[$section]['children'][$page];
            if($subpage) $pageId = $allSlugsNested[$section]['children'][$page]['children'][$subpage];
            if(is_array($pageId)) $pageId = $pageId['id'];
        }

        $content = $this->getContent($pageId);
        $options = $this->getWebsiteOptions();
        // $cartTotalItems = ShopController::getTotalCartItems();
        // $loggedInUserId = ShopController::getLoggedinUser();
// dd($content->contentSections);
        $data= [
            'head_title' => $content->pageTitle,
            'meta_description' => $content->pageMetaDescription,
            'html_menu' => $htmlMenu->html,
            'website_options' => $options,
            // 'cart_total' => $cartTotalItems,
            // 'user_logged_in' => $loggedInUserId,
            'content_sections' => $content->contentSections,
        ];
        if($section == 'contact')
            return view('contact-page')->with('data', $data);
        else if($section == 'producten') {
            $mainCats = $this->getMainMenuItems();
            $data['shop_main_cats'] = $mainCats;
            return view('shop-root-category')->with('data', $data);
        } else if($section == 'afspraak-maken') {
            return view('bookly-page')->with('data', $data);
        } else
        return view('standard-page')->with('data', $data);
    }
    public function getContent($id) {
        $res = new \stdClass();
        $metaDesc = '';
        $hTitle = '';
        $sections = [];
        $reqPage = new PageApi($id);
        $pageData = $reqPage->get();
// dd($pageData);
        foreach($pageData->head_tags as $htag) {
            if(isset($htag->attributes->name) && $htag->attributes->name == 'description') $metaDesc = $htag->attributes->content;
        }
        if($pageData->title->rendered == '[HOMEPAGE]') $hTitle = 'Vincent van der Zalm - Thuisdiners | Popup restaurants | Horeca consultancy';
        else $hTitle = $pageData->title->rendered . ' - Vincent van der Zalm - Thuisdiners | Popup restaurants | Horeca consultancy';

        $simpleMedia = new SimpleMediaApi();
        $simpleMedia->get();
        $this->allMediaById = $simpleMedia->makeListById();
        if($pageData->content->rendered) {
            $s = [];
            $s['type'] = 'text';
            $s['text'] = $pageData->content->rendered;
            $s['color'] = '';
            $s['orientation'] = 'text_left';
            $s['gallery'] = [];
            $sections[] = $s;
        }
// dd($pageData,$this->allMediaById);
// dd($pageData->crb_sections);
        if(isset($pageData->crb_sections) && count($pageData->crb_sections)) {
            $loadWoo_once = true;
            foreach($pageData->crb_sections as $sec) {
                $s = [];
                $s['type'] = $sec->_type;
                // if($sec->_type == 'hero') {
                //     $img = str_replace('_mcfu638b-cms/wp-content/uploads', 'media', $sec->image);
                //     $s['img']['url'] = $img;
                //     $s['img']['alt'] = str_replace(['-', '_'], ' ', pathinfo($img, PATHINFO_FILENAME));
                // }
                // if($sec->_type == 'banner') {
                //     // $s['wl_header'] = $sec->writing_letters_header;
                //     // $s['bl_header'] = $sec->block_letters_header;
                //     $s['text_align'] = $sec->text_align;
                //     $s['text_color'] = $sec->text_color;
                //     $img = str_replace('_mcfu638b-cms/wp-content/uploads', 'media', $sec->image);
                //     $s['img']['url'] = $img;
                //     $s['img']['alt'] = str_replace(['-', '_'], ' ', pathinfo($img, PATHINFO_FILENAME));
                //     $s['checked'] = $sec->disable_zoom_effect;
                //     $s['text'] = $sec->text;
                // }
                if($sec->_type == 'text') {
                    $s['color'] = $sec->color;
                    // $s['orientation'] = 'text_left';
                    // if(isset($sec->orientation)) $s['orientation'] = $sec->orientation;
                    // $s['wl_header'] = $sec->writing_letters_header;
                    // $s['bl_header'] = $sec->block_letters_header;
                    // $s['margin'] = $sec->margin;
                    // $s['valign_center'] = $sec->vertical_align_center;
                    $s['orientation'] = $sec->orientation;
                    $s['text'] = $sec->text;
                    $img = str_replace('_mcfu638b-cms/wp-content/uploads', 'media', $sec->image);
                    $s['img']['url'] = $img;
                    $s['img']['alt'] = str_replace(['-', '_'], ' ', pathinfo($img, PATHINFO_FILENAME));
                    // $s['gallery'] = [];
                    // if(isset($sec->crb_media_gallery) && count($sec->crb_media_gallery)) {
                    //     $s['gallery'] = $this->getMediaGallery($sec->crb_media_gallery);
                    //     // foreach($sec->crb_media_gallery as $mediaId) {
                    //     //     $img = str_replace('_mcfu638b-cms/wp-content/uploads', 'media', $this->allMediaById[$mediaId]->url);
                    //     //     $alt = str_replace(['-', '_'], ' ', pathinfo($img, PATHINFO_FILENAME));
                    //     //     if($this->allMediaById[$mediaId]->alt) $alt = $this->allMediaById[$mediaId]->alt;
                    //     //     $i['img'] = $img;
                    //     //     $i['alt'] = $alt;
                    //     //     $s['gallery'][] = $i;
                    //     // }
                    // }
                }
//                 if($sec->_type == 'information_blocks_holder') {
//                     $s['blocks'] = $sec->information_blocks;
//                     foreach($s['blocks'] as $i => $block) {
//                         if($block->image) {
//                             $s['blocks'][$i]->image = $this->getMediaGallery(array($block->image));
// // dd($block->image);
//                             // $im = parse_url($block->image);
//                             // $arr = array();
//                             // $arr['img'] = $im['path'];
//                             // $arr['alt'] = 'altje';
//                             // $s['blocks'][$i]->image = $arr;

//                             // $s['blocks'][$i]->image['img'] = $block->image;
//                             // $s['blocks'][$i]->image['alt'] = 'altje';
//                         }
//                         if($block->crb_association) {
//                             // dd($block->crb_association);
//                             // dd($this->allPagesPerParent);
//                             // $assocPage = new PageApi($block->crb_association);
//                             foreach($this->allPagesPerParent[0] as $rootPage) {
//                                 // dd($rootPage->id);
//                                 // dd($block->crb_association->id);
//                                 if($rootPage->id == $block->crb_association[0]->id) {
//                                     $block->crb_association[0]->slug = $rootPage->slug;
//                                 }
//                             }
//                         }
//                     }
//                 }
                // if($sec->_type == 'people_holder') {
                //     $s['blocks'] = $sec->people_blocks;
                //     foreach($s['blocks'] as $i => $block) {
                //         if($block->image) {
                //             $s['blocks'][$i]->image = $this->getMediaGallery(array($block->image));
                //         }
                //     }
                // }
                // if($sec->_type == 'person_wraps') {
                //     $s['people'] = array();
                //     $aValuesToRetreive = array('title', 'board_role', 'board_email', 'board_phone', 'image');
                //     foreach($sec->people_associations as $personAssoc) {
                //         $oCustPostType = $this->getCustomPostTypeViaRestApi($personAssoc->subtype, $personAssoc->id, $aValuesToRetreive);
                //         if($oCustPostType->image) $oCustPostType->image = $this->getMediaGallery(array($oCustPostType->image));
                //         $s['people'][] = $oCustPostType;
                //     }
                //     // dd($s);
                //     // $s['people'] = $sec->people_associations;
                //     // foreach($s['blocks'] as $i => $block) {
                //         // if($block->image) {
                //         //     $s['blocks'][$i]->image = $this->getMediaGallery(array($block->image));
                //         // }
                //     // }
                // }
                
                // if($sec->_type == 'solutions') {
                //     $s['icon_boxes'] = [];
                //     if(isset($sec->icon_boxes) && count($sec->icon_boxes)) {
                //         foreach($sec->icon_boxes as $box) {
                //             $b['icon'] = $box->icon;
                //             $b['text'] = $box->text;
                //             $s['icon_boxes'][] = $b;
                //         }
                //     }
                // }
                // if($sec->_type == 'activities') {
                //     $s['fields'] = [];
                //     if(isset($sec->activity_fields) && count($sec->activity_fields)) {
                //         foreach($sec->activity_fields as $field) {
                //             $s['fields'][] = $field->text;
                //         }
                //     }
                // }
                // if($sec->_type == 'services') {
                //     $s['background'] = $sec->background;
                //     $s['icon_boxes'] = [];
                //     if(isset($sec->icon_boxes) && count($sec->icon_boxes)) {
                //         foreach($sec->icon_boxes as $box) {
                //             $b['icon'] = $box->icon;
                //             $b['image']['url'] = '';
                //             $b['image']['alt'] = '';
                //             if($box->image) $b['image']['url'] = $this->generateImageUrl($box->image);
                //             if($box->image) $b['image']['alt'] = $this->generateImageAlt($box->image);
                //             $b['text'] = $box->text;
                //             $s['icon_boxes'][] = $b;
                //         }
                //     }
                // }
                // if($sec->_type == 'featured_products') {
                //     $s['fProducts'] = [];
                //     $s['fCatTitle'] = '';
                //     $s['fCatSlug'] = '';
                //     if(isset($sec->crb_association) && count($sec->crb_association)) {
                //         if($loadWoo_once) {
                //             $wooProducts = new WooFilterProductsApi();
                //             $wooProducts->setHttpBasicAuth();
                //             $wooProducts->parameters['crb[is_featured]'] = 'yes';
                //             $wooProducts->parameters['per_page'] = 99;
                //             $allFeaturedProducts = $wooProducts->get();

                //             $wooCategories = new WooCategoriesApi();
                //             $wooCategories->setHttpBasicAuth();
                //             $wooCategories->get();
                //             $wooCategories->setCategoriesPerParent();
                //             $wooCategories->getAllCatsById();
                            
                //             $loadWoo_once = false;
                //         }

                //         foreach($sec->crb_association as $ass) {
                //             $s['fCatTitle'] = $wooCategories->categoriesById[$ass->id]['name'];
                //             $s['fCatUrl'] = array_key_last($wooCategories->getBreadCrumbUrls($ass->id));
                //             foreach($allFeaturedProducts as $fp) {
                //                 if(in_array($ass->id, $fp->categories) || in_array($ass->id, $fp->ancestors)) {
                //                     $prod = [];
                //                     $prod['id'] = $fp->id;
                //                     $prod['title'] = $fp->name;
                //                     $prod['slug'] = $fp->slug;
                //                     $prod['image'] = ($fp->images && $fp->images[0]?$fp->images[0]:'');
                //                     $prod['price'] = $fp->price;
                //                     $s['fProducts'][] = $prod;
                //                 }
                //             }
                //         }
                //     }
                // }
                // if($sec->_type == 'contact_form') {
                //     $s['checked'] = $sec->show_contact_form;
                //     session(['wt_previous_url' => url()->current()]);  // Using custom session variable, because: URL::previous / url()->previous() uses the header information stored within referrer, the referrer header is not always filled, so can be empty
                // }
                // if($sec->_type == 'cta_afspraak_maken') {
                //     $s['checked'] = $sec->show_afspraak_maken;
                // }
                // if($sec->_type == 'media_picture_gallery') {
                //     $s['gallery'] = [];
                //     if(isset($sec->crb_media_gallery) && count($sec->crb_media_gallery)) {
                //         $s['gallery'] = $this->getMediaGallery($sec->crb_media_gallery);
                //     }
                // }
                // if($sec->_type == 'team_members') {
                //     $s['members'] = $sec->t_members;
                //     foreach($s['members'] as $i => $member) {
                //         if($member->image) {
                //             $s['members'][$i]->image = $this->getMediaGallery(array($member->image));
                //         }
                //     }
                // }
                // if($sec->_type == 'advantages_and_testimonials') {
                //     $s['advantages'] = $sec->advantages;
                    
                //     foreach($sec->testimonials as $i => $tes) {
                //         $imgUrl = '';
                //         $imgAlt = '';
                //         if($tes->image) $imgUrl = $this->generateImageUrl($tes->image);
                //         if($tes->image) $imgAlt = $this->generateImageAlt($tes->image);
                //         $sec->testimonials[$i]->image = new \stdClass();
                //         $sec->testimonials[$i]->image->url = $imgUrl;
                //         $sec->testimonials[$i]->image->alt = $imgAlt;
                //     }
                //     $s['testimonials'] = $sec->testimonials;
                // }
                $sections[] = $s;
            }
        }
// dd($sections);
        $res->pageMetaDescription = $metaDesc;
        $res->pageTitle = $hTitle;
        $res->contentSections = $sections;
        return $res;
    }
    public function getCustomPostTypeViaRestApi($customPostType, $id, $valsToReturn) {
        $res = new \stdClass();
        $call = new ApiCall();
        $call->endpoint = '/index.php/wp-json/wp/v2/' . $customPostType . '/' . $id;
        $oReturned = $call->get();
        foreach($valsToReturn as $val) {
            if($val == 'title') $res->{$val} = $oReturned->{$val}->rendered;
            else $res->{$val} = $oReturned->{$val};
        }
        return $res;
    }
    public function getMediaGallery($gall) {
        $res = [];
        foreach($gall as $mediaId) {
            // $img = str_replace('_mcfu638b-cms/wp-content/uploads', 'media', $this->allMediaById[$mediaId]->url);
            $url = $this->generateImageUrl($mediaId);
            // $alt = str_replace(['-', '_'], ' ', pathinfo($img, PATHINFO_FILENAME));
            $alt = $this->generateImageAlt($mediaId);
            if($this->allMediaById[$mediaId]->alt) $alt = $this->allMediaById[$mediaId]->alt;
            $i['img'] = $url;
            $i['alt'] = $alt;
            $res[] = $i;
        }
        return $res;
    }
    public function generateImageUrl($mediaId) {
        return str_replace('_mcfu638b-cms/wp-content/uploads', 'media', $this->allMediaById[$mediaId]->url);
    }
    public function generateImageAlt($mediaId) {
        return str_replace(['-', '_'], ' ', pathinfo($this->allMediaById[$mediaId]->url, PATHINFO_FILENAME));
    }
    public static function getWebsiteOptions() {
        $allWebsiteOptions = new WebsiteOptionsApi();
        $websiteOptions = $allWebsiteOptions->get();
        return (array)$websiteOptions;
    }
    public function getMainMenuItems() {
        $cats = array();
        $wooCats = new WooCategoriesApi();
        $wooCats->setHttpBasicAuth();
        $wooCats->get();
        $wooCats->setCategoriesPerParent();
        foreach($wooCats->res[0] as $rootCats) {
            if($rootCats->slug == 'uncategorized') continue;
            $cats[$rootCats->slug] = $rootCats->name;
        }
        return $cats;
    }
    public function showOnePager($orderId = false) {
        $simplePages = new SimplePagesApi();
        $spages = $simplePages->get();
        $htmlMenu = new Menu($spages);
        $htmlMenu->generateUlMenu();
        $options = $this->getWebsiteOptions();

        $simpleMedia = new SimpleMediaApi();
        $simpleMedia->get();
        $allMediaById = $simpleMedia->makeListById();

        $allCrbSections = array();
        foreach($spages[0] as $sPage) {
            $pageA = [];
            $pageA['type'] = '_anchor';
            $pageA['value'] = $sPage->slug;
            $allCrbSections[] = $pageA;

            /* check rotterdamsehorecawandeling.nl for details */
            
            // $crbSecs = $this->getPageCrbSections($sPage->id);
            // $allCrbSections = array_merge($allCrbSections, $crbSecs);
        }
        $data= [
            'html_menu' => $htmlMenu->html,
            'website_options' => $options,
            'content_sections' => $allCrbSections,
        ];
        return view('page')->with('data', $data);
    }
}

<?php
use Carbon_Fields\Field;
use Carbon_Fields\Container;

$carbonFieldsArgs = array();

$websiteOptions = array();
// $websiteOptions[] = array('text', 'wt_website_text1', 'Website header e-mail adres');
// $websiteOptions[] = array('text', 'wt_website_text2', 'Website header telefoonnummer');
$websiteOptions[] = array('text', 'instagram', 'Instagram link');
$websiteOptions[] = array('text', 'facebook', 'Facebook link');
$websiteOptions[] = array('text', 'twitter', 'Twitter link');
// $websiteOptions[] = array('textarea', 'wt_website_textarea1', 'Website textarea 1');
// $websiteOptions[] = array('textarea', 'wt_website_textarea2', 'Website textarea 2');
// $websiteOptions[] = array('textarea', 'wt_website_textarea3', 'Website textarea 3');
// $websiteOptions[] = array('rich_text', 'sidebar_text', 'Zijkant tekst');
// $websiteOptions[] = array('rich_text', 'wt_website_footer1', 'Footer blok 1 tekst');
// $websiteOptions[] = array('rich_text', 'wt_website_footer2', 'Footer blok 2 tekst');
// $websiteOptions[] = array('rich_text', 'wt_website_footer3', 'Footer blok 3 tekst');
// $websiteOptions[] = array('rich_text', 'wt_website_footer4', 'Footer blok 4 tekst');
// $websiteOptions[] = array('file', 'wt_algemene_voorwaarden', 'Algemene voorwaarden');

$carbonFieldsArgs['websiteOptions'] = $websiteOptions;

// Our custom post type function
// function create_posttype_board_members() {
  
//     register_post_type( 'board_members',
//     // CPT Options
//         array(
//             'labels' => array(
//                 'name' => __( 'Board members' ),
//                 'singular_name' => __( 'Board members' ),
//                 'add_new_item' => __( 'Add New Board member' ),
//                 'add_new' => __( 'Add New Board member' ),
//                 'edit_item' => __( 'Edit Board member' ),
//                 'update_item' => __( 'Update Board member' ),
//             ),
//             'public' => true,
//             // 'has_archive' => true,
//             // 'rewrite' => array('slug' => 'movies'),
//             'show_in_rest' => true,
//             // 'supports'            => array( 'title', 'editor', 'excerpt', 'author', 'thumbnail', 'comments', 'revisions', 'custom-fields', ),
//             'supports'            => array( 'title'),
//             )
//     );
// }
// // Hooking up our function to theme setup
// add_action( 'init', 'create_posttype_board_members' );


// add_action( 'init', 'change_editor_capabilities' );
// function change_editor_capabilities() {
//     $editor = get_role( 'editor' );
//     $caps = array(
//         'delete_others_pages',
//         'delete_others_posts',
//         'delete_pages',
//         'delete_posts',
//         'delete_private_pages',
//         'delete_private_posts',
//         'delete_published_pages',
//         'delete_published_posts',
//     );
//     foreach($caps as $cap) {
//         $editor->add_cap($cap);
//     }
// }


if (!current_user_can('administrator')) {
    add_filter('bulk_actions-edit-page', 'remove_from_bulk_actions');
    add_filter('page_row_actions', 'remove_page_row_actions', 10, 2);
    add_action('admin_head', 'customBackendStyles');
    add_action('admin_footer', 'customBackendScriptsEditorRol');
    add_filter('carbon_fields_theme_options_container_admin_only_access', '__return_false');
    add_filter('wp_rest_cache/settings_capability', 'wprc_change_settings_capability', 10, 1);

    add_action('admin_menu', 'remove_admin_menus' );
    add_action('init', 'remove_comment_support', 100);
    add_action('wp_before_admin_bar_render', 'remove_admin_bar_menus' );

    add_filter('contextual_help_list','contextual_help_list_remove');
    add_filter('screen_options_show_screen', 'remove_screen_options');
}


add_action('admin_footer', 'customBackendScripts');

add_action('add_meta_boxes', 'set_default_page_template', 1);
add_action('init', 'remove_editor_init');
add_action('carbon_fields_register_fields', function() use ( $carbonFieldsArgs ) { crbRegisterFields( $carbonFieldsArgs ); });
add_action('carbon_fields_theme_options_container_saved', 'deleteWebsiteOptionsRestCache');

add_action('add_attachment', 'deleteSimpleMediaRestCache');
// add_filter('wp_handle_upload', 'deleteSimpleMediaRestCache');


// add_action('admin_head', 'loadAxios');
// function loadAxios() {
//     echo '<script src="https://cdnjs.cloudflare.com/ajax/libs/axios/0.24.0/axios.min.js" integrity="sha512-u9akINsQsAkG9xjc1cnGF4zw5TFDwkxuc9vUp5dltDWYCSmyd0meygbvgXrlc/z7/o4a19Fb5V0OUE58J7dcyw==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>';
// }
function crbRegisterFields($args) {
    Container::make( 'post_meta', __( 'Section Options' ) )
        ->where( 'post_type', '=', 'page' )
        ->where( 'post_template', '=', 'template-section-based.php' )
        ->add_fields( array(
            Field::make( 'complex', 'crb_sections', 'Sections' )->set_visible_in_rest_api($visible = true)
                ->set_layout( 'tabbed-vertical' )
                // ->add_fields( 'hero', 'Banner without text', array(
                //     Field::make( 'image', 'image', 'Afbeelding' )->set_value_type( 'url' ),
                //     // Field::make( 'text', 'writing_letters_header', __( 'Writing letters header (gold)' ) ),
                //     // Field::make( 'text', 'block_letters_header', __( 'Block letters header' ) ),
                //     // Field::make( 'select', 'color', __( 'Choose block letters color' ) )
                //     // ->set_options( array(
                //         // 'white' => __( 'White' ),
                //         // 'black' => __( 'Black' ),
                //     // ) ),
                //     // Field::make( 'checkbox', 'show_reserve_button', __( 'Show reserve button' ) ),
                //     // Field::make( 'image', 'image', 'Afbeelding' ),
                //     // Field::make( 'rich_text', 'text', 'Tekst' ),
                // ) )
                // ->add_fields( 'banner', 'Banner', array(
                //     Field::make( 'image', 'image', 'Afbeelding' )->set_value_type( 'url' ),
                //     Field::make( 'checkbox', 'disable_zoom_effect', __( 'Disable zoom-effect' ) ),
                //     // Field::make( 'text', 'writing_letters_header', __( 'Writing letters header (gold)' ) ),
                //     // Field::make( 'text', 'block_letters_header', __( 'Block letters header' ) ),
                //     Field::make( 'rich_text', 'text', 'Tekst (on top of image)' ),
                //     Field::make( 'select', 'text_align', __( 'Choose text vertical alignment' ) )
                //     ->set_options( array(
                //         'top' => __( 'Top' ),
                //         'center' => __( 'Center' ),
                //         'bottom' => __( 'Bottom' ),
                //     ) ),
                //     Field::make( 'select', 'text_color', __( 'Choose text color' ) )
                //     ->set_options( array(
                //         'white' => __( 'White' ),
                //         'black' => __( 'Black' ),
                //     ) ),
                //     // Field::make( 'image', 'image', 'Afbeelding' ),
                // ) )
                ->add_fields( 'text', 'Tekst', array(
                    Field::make( 'select', 'color', __( 'Choose background color' ) )
                    ->set_options( array(
                        '' => __( 'Blue' ),
                        'gold' => __( 'Gold' ),
                    ) ),
                    // Field::make( 'text', 'writing_letters_header', __( 'Writing letters header (gold)' ) ),
                    // Field::make( 'text', 'block_letters_header', __( 'Block letters header' ) ),
                    // Field::make( 'select', 'margin', __( 'Choose block letters top-margin' ) )
                    // ->set_options( array(
                    //     '0' => __( '0 pixels' ),
                    //     '10' => __( '10 pixels' ),
                    //     '20' => __( '20 pixels' ),
                    //     '30' => __( '30 pixels' ),
                    //     '-10' => __( '-10 pixels' ),
                    //     '-20' => __( '-20 pixels' ),
                    //     '-30' => __( '-30 pixels' ),
                    // ) ),
                    Field::make( 'rich_text', 'text', 'Text' ),
                    // Field::make( 'checkbox', 'vertical_align_center', 'Align text to center (vertically)' ),
                    Field::make( 'image', 'image', 'Afbeelding' )->set_value_type( 'url' ),
                    Field::make( 'select', 'orientation', __( 'Choose orientation' ) )
                    ->set_options( array(
                        'text_left' => __( 'Text left, image right' ),
                        'text_right' => __( 'Text right, image left' ),
                    ) ),
                    // Field::make( 'association', 'crb_association', __( 'Select page for link' ))
                    // ->set_types( array(
                    //     array(
                    //         'type' => 'post',
                    //         'taxonomy' => 'page',
                    //     ),
                    // ) )
                    // Field::make( 'media_gallery', 'crb_media_gallery', __( 'Images' ) . ' (' . __( 'optional' ) . ')' )
                        // ->set_type( array( 'image', ) ),
                        // ->set_value_type( 'url' ),
                ) )
                // ->add_fields( 'text_green', 'Tekst (Groene achtergrond)', array(
                //     Field::make( 'rich_text', 'text', 'Text' ),
                // ) )
                // ->add_fields( 'text_gold', 'Tekst (Gouden achtergrond)', array(
                //     Field::make( 'rich_text', 'text', 'Text' ),
                // ) )
                // ->add_fields( 'images', 'Afbeeldingen', array(
                //     Field::make( 'image', 'image1', 'Afbeelding 1' )->set_value_type( 'url' ),
                //     Field::make( 'image', 'image2', 'Afbeelding 2' )->set_value_type( 'url' ),
                //     Field::make( 'image', 'image3', 'Afbeelding 3' )->set_value_type( 'url' ),
                // ) )

                // ->add_fields( 'iconBoxes', 'Icoon boxen', array(
                //     // Field::make( 'image', 'image', 'Afbeelding' )->set_value_type( 'url' ),
                //     // Field::make( 'image', 'image', 'Afbeelding' ),
                //     Field::make( 'rich_text', 'text', 'Tekst' ),
                // ) )
                // ->add_fields( 'solutions', __( 'Solutions' ) . ' (full-width blue background, icon + text)', array(
                //     Field::make( 'complex', 'icon_boxes', 'Text and an icon from fontawesome.com (use the icon \'name\')' )
                //         ->add_fields( array(
                //             Field::make( 'text', 'icon', __( 'Icon' ) ),
                //             Field::make( 'rich_text', 'text' , __( 'Text' )),
                //         )),
                // ))
                // ->add_fields( 'activities', __( 'Activities' ) . ' (blue text fields)', array(
                //     Field::make( 'complex', 'activity_fields', 'Activity Text' )
                //         ->add_fields( array(
                //             Field::make( 'rich_text', 'text' , __( 'Text' )),
                //         )),
                // ))
                // ->add_fields( 'services', __( 'Services' ) . ' (full-width background, icon + text)', array(
                //     Field::make( 'select', 'background', __( 'Choose background' ) )
                //     ->set_options( array(
                //         // '' => __( 'White' ),
                //         // 'blue' => __( 'Blue' ),
                //         'gold' => __( 'Gold' ),
                //         'black' => __( 'Black' ),
                //     ) ),
                //     Field::make( 'complex', 'icon_boxes', 'Text and an icon from fontawesome.com (use the icon \'name\') OR icon from an image-file' )
                //         ->add_fields( array(
                //             Field::make( 'text', 'icon', __( 'Icon' ) ),
                //             Field::make( 'image', 'image', __( 'Image icon' ) ),
                //             Field::make( 'rich_text', 'text' , __( 'Text' )),
                //         )),
                // ))
                // ->add_fields( 'featured_products', __( 'Featured products' ), array(
                //     Field::make( 'association', 'crb_association', 'Select shop category')
                //     ->set_types( array(
                //         array(
                //             'type' => 'term',
                //             'taxonomy' => 'product_cat',
                //         ),
                //     ) )
                // ))
                // ->add_fields( 'contact_form', 'Contact formulier', array(
                //     Field::make( 'checkbox', 'show_contact_form', 'Contactformulier weergeven' ),
                // ) )
                // ->add_fields( 'cta_afspraak_maken', 'Afspraak maken button', array(
                //     Field::make( 'checkbox', 'show_afspraak_maken', 'Afspraak maken button weergeven' ),
                // ) )
                // ->add_fields( 'media_picture_gallery', 'Media gallery', array(
                //     Field::make( 'media_gallery', 'crb_media_gallery', __( 'Media Gallery' ) )
                //         ->set_type( array( 'image' ) )->set_duplicates_allowed( false )
                // ) )
                // ->add_fields( 'information_blocks_holder', 'Informatie blokken', array(
                //     Field::make( 'complex', 'information_blocks', 'Informatie blokken' )
                //         ->add_fields( array(
                //             Field::make( 'text', 'title', __( 'Title' ) ),
                //             Field::make( 'textarea', 'text', __( 'Text' ) ),
                //             Field::make( 'image', 'image', __( 'Image' ) ),
                //             Field::make( 'association', 'crb_association', __( 'Select page for link' ))
                //             ->set_types( array(
                //                 array(
                //                     'type' => 'post',
                //                     'taxonomy' => 'page',
                //                 ),
                //             ) )
                //          )),
                // ) )
                // ->add_fields( 'people_holder', 'Persoon blokken', array(
                //     Field::make( 'complex', 'people_blocks', 'Persoon blokken' )
                //         ->add_fields( array(
                //             Field::make( 'text', 'name', __( 'Name' ) ),
                //             Field::make( 'text', 'role', __( 'Role' ) ),
                //             Field::make( 'text', 'email', __( 'E-mail' ) ),
                //             Field::make( 'text', 'phone', __( 'Phone' ) ),
                //             Field::make( 'image', 'image', __( 'Image' ) ),
                //             // Field::make( 'rich_text', 'text' , __( 'Text' )),
                //         )),
                // ) )
                // ->add_fields( 'person_wraps', 'Persoon blokken', array(
                //     // Field::make( 'rich_text', 'text', 'Text' ),
                //     Field::make( 'association', 'people_associations', __( 'Select person for link' ))
                //     ->set_types( array(
                //         array(
                //             'type' => 'post',
                //             'post_type' => 'board_members',
                //         ),
                //     ) )
                // ) )
                // ->add_fields( 'advantages_and_testimonials', 'Voordelen en klantervaringen', array(
                //     Field::make( 'complex', 'advantages', 'Onze voordelen' )
                //         ->add_fields( array(
                //             Field::make( 'text', 'icon', __( 'Icon' ) ),
                //             Field::make( 'text', 'title', __( 'Title' ) ),
                //             Field::make( 'rich_text', 'message', __( 'Message' ) ),
                //         )),
                //     Field::make( 'complex', 'testimonials', 'Klantervaringen' )
                //         ->add_fields( array(
                //             Field::make( 'text', 'name', __( 'Name' ) ),
                //             Field::make( 'text', 'item', __( 'Item bought' ) ),
                //             Field::make( 'rich_text', 'text', __( 'Text' ) ),
                //             Field::make( 'image', 'image', __( 'Image' ) ),
                //         )),
                // ) )
                


                // bumper (?)
                // ->add_fields( 'bumper', 'Bumper', array(
                //     Field::make( 'text', 'titel', 'Titel' ),
                //     Field::make( 'rich_text', 'text', 'Text' ),
                //     Field::make( 'text', 'icon', 'Icoon' ),
                //     Field::make( 'image', 'image', 'Afbeelding' ),
                // ) )

                // // Second group will be a list of files for users to download
                // ->add_fields( 'file_list', 'File List', array(
                //     Field::make( 'complex', 'files', 'Files' )
                //         ->add_fields( array(
                //             Field::make( 'file', 'file', 'File' ),
                //         ) ),
                // ) )

                // // Third group will be a list of manually selected posts
                // // used as a simple curated "Related posts" listing
                // ->add_fields( 'related_posts', 'Related Posts', array(
                //     Field::make( 'association', 'posts', 'Posts' )
                //         ->set_types( array(
                //             array(
                //                 'type' => 'post',
                //                 'post_type' => 'post',
                //             ),
                //         ) ),
                // ) ),
                ) );

            // Container::make( 'post_meta', __( 'Page options' ) )
            //     ->where( 'post_type', '=', 'page' )
            //     // ->where( 'post_template', '=', 'template-section-based.php' )
            //     ->add_fields( array(Field::make( 'text', 'crb_alt_url', __( 'Alternative URL' ))) );
            Container::make( 'post_meta', __( 'Attributes' ) )
                ->where( 'post_type', '=', 'board_members' )
                // ->where( 'post_template', '=', 'template-section-based.php' )
                ->add_fields(array(
                        Field::make( 'text', 'board_role', __( 'Role' ))->set_visible_in_rest_api($visible = true),
                        Field::make( 'text', 'board_email', __( 'E-mail' ))->set_visible_in_rest_api($visible = true),
                        Field::make( 'text', 'board_phone', __( 'Phone' ))->set_visible_in_rest_api($visible = true),
                        Field::make( 'image', 'image', __( 'Image' ) )->set_visible_in_rest_api($visible = true),
                    )
                );

    Container::make('term_meta', 'Woo Category Options')
    ->where('term_taxonomy', '=', 'product_cat')
    // ->add_tab( __( 'Profile' ), array(
    ->add_fields( array(
        Field::make( 'radio', 'crb_catalogus_type', __( 'Choose catalogus type' ) )->set_visible_in_rest_api($visible = true)
        ->set_options( array(
            'shop' => 'Shop',
            'list' => 'List',
        ) ),
        Field::make( 'rich_text', 'crb_category_text', __( 'Text' ) )->set_visible_in_rest_api($visible = true),
    ));

    Container::make('post_meta', 'Product Options')
    ->where('post_type', '=', 'product')
    ->add_fields( array(
        // Field::make( 'rich_text', 'crb_extra_product_text', __( 'Product text for testing' ) )->set_visible_in_rest_api($visible = true),
        Field::make('checkbox', 'is_featured', __('Is featured product?'))->set_option_value('yes')->set_visible_in_rest_api($visible = true),
        Field::make('text', 'min_pk', __( 'Minimum pk' ))->set_visible_in_rest_api($visible = true),
        Field::make('text', 'max_pk', __( 'Maximum pk' ))->set_visible_in_rest_api($visible = true),
    ));
        
    $fieldsToAdd = array();
    foreach($args['websiteOptions'] as $opt) {
        $fieldsToAdd[] = Field::make($opt[0], $opt[1], __($opt[2]));
    }
    Container::make('theme_options', 'Website Options')->add_fields($fieldsToAdd );

}

function remove_editor_init() {
    remove_post_type_support('page', 'editor');
}
function wprc_change_settings_capability( $capability ) {
    return 'edit_posts'; // Change the capability to users who can edit posts.
}
function set_default_page_template() {
    global $post;
    $currentScreen = get_current_screen();
    if($post->post_type == 'page' && $currentScreen->action == 'add') {
        $post->page_template = "template-section-based.php";
    }
}
function deleteWebsiteOptionsRestCache() {
    \WP_Rest_Cache_Plugin\Includes\Caching\Caching::get_instance()->delete_cache_by_endpoint( '/_mcfu638b-cms/index.php/wp-json/wtcustom/website-options' );
}
function deleteSimpleMediaRestCache() {
    \WP_Rest_Cache_Plugin\Includes\Caching\Caching::get_instance()->delete_cache_by_endpoint( '/_mcfu638b-cms/index.php/wp-json/wtcustom/simple-media' );
}
/* Remove bulk actions for type: page */
function remove_from_bulk_actions($actions) {
    return array();
}
/* Remove row actions for type: page */
function remove_page_row_actions($actions, $post) {
    if ($post->post_type == 'page') {
        $actions = array();
    }
    return $actions;
}
function customBackendStyles() {
    ?>
    <style type="text/css">
      #taxonomy-category #taxonomy-category-new { display: none; }
      .cf-complex__inserter-button {
          border: 1px solid red;
      }
    </style>
    <?php
}
function customBackendScriptsEditorRol() {
    ?>
    <script>
        jQuery(document).ready(function($) {	
            jQuery('input[value="[HOMEPAGE]"]').attr('disabled', 'disabled').parent().next().find('button').remove();
            jQuery('input[value="Producten"]').attr('disabled', 'disabled').parent().next().find('button').remove();
            jQuery('input[value="Afspraak maken"]').attr('disabled', 'disabled').parent().next().find('button').remove();
            // if(
            //     jQuery('input[value="[HOMEPAGE]"]').length ||
            //     jQuery('input[value="Producten"]').length ||
            //     jQuery('input[value="Afspraak maken"]').length
            // )
            
            
            jQuery('#major-publishing-actions #delete-action').remove();

            /*
            jQuery('.term-display-type-wrap').remove(); // wooCommerce category display type
            jQuery('.term-thumbnail-wrap').remove(); // wooCommerce category thumbnail
            jQuery('h2.nav-tab-wrapper a#settings').remove();
            jQuery('h2.nav-tab-wrapper a#endpoint-api').remove();
            jQuery('input[value="Clear REST Cache"]').parent().remove();
            jQuery('select#dropdown_product_type').remove();
            jQuery('ul.subsubsub li.byorder').remove();
            jQuery('div.row-actions span.inline').remove();
            jQuery('div.row-actions span.view').remove();
            jQuery('a#add-bookly-form').remove();
            jQuery('div#woocommerce-product-data div.postbox-header h2 label[for="_virtual"]').remove();
            jQuery('div#woocommerce-product-data div.postbox-header h2 label[for="_downloadable"]').remove();
            jQuery('div#woocommerce-product-data select#product-type option[value="grouped"]').remove();
            jQuery('div#woocommerce-product-data select#product-type option[value="external"]').remove();
            jQuery('div#woocommerce-product-data select#product-type option[value="variable"]').remove();
            jQuery('ul.product_data_tabs li.shipping_options').remove();
            jQuery('ul.product_data_tabs li.linked_product_options').remove();
            jQuery('ul.product_data_tabs li.advanced_options').remove();
            jQuery('span.description a.sale_schedule').remove();
            jQuery('div#inventory_product_data div.show_if_simple.show_if_variable').remove();
            jQuery('input#attribute_public').parent().parent().remove();
            jQuery('select#attribute_orderby').parent().remove();
            let woomsg = jQuery('.wrap.woocommerce div#message').text();
            if(woomsg.indexOf("With the release of WooCommerce 4.0, these reports are being replaced. There is a new and better Analytics section")) jQuery('.wrap.woocommerce div#message').remove();
            jQuery('aside#woocommerce-activity-panel').remove();
            jQuery('a#post-preview').remove();
            jQuery('a:contains("Preview page")').remove();
            jQuery('a:contains("View page")').remove();
            jQuery('select#post_status option[value=pending]').remove();
            jQuery('div#misc-publishing-actions div#visibility').remove();
            jQuery('div#pageparentdiv p.post-attributes-label-wrapper.menu-order-label-wrapper').remove();
            jQuery('div#pageparentdiv input#menu_order').remove();
            jQuery('div#pageparentdiv p.post-attributes-help-text').remove();
            */


            jQuery('.handle-actions').remove();
        });
    </script>
    <?php
}
function customBackendScripts() {
    ?>
    <script>
        jQuery('#add_description h2').text('Meta Description');
        // customizeCarbonFieldsPlugin();
        customizeNestedPagesPlugin();
        flushSimplePagesCacheOnDrag();

        // function customizeCarbonFieldsPlugin() {
        //     let divStyles = {
        //         width: '100%',
        //         display: 'block',
        //     };
        //     let addBtnStyles = {
        //         backgroundColor : '#b3edb3',
        //         border: '2px solid #000',
        //         width: '100%',
        //         fontSize: '24px',
        //         color: '#000',
        //     };
        //     let collapseBtnStyles = {
        //         marginLeft: 'auto',
        //         marginTop: '10px',
        //         marginRight: '10px',
        //         padding: '0',
        //         paddingLeft: '5px',
        //         paddingRight: '5px',
        //         minHeight: '20px',
        //         lineHeight: '20px',
        //     };
        //     jQuery(document).ready(function($) {	
        //         jQuery('.cf-complex__inserter').css(divStyles);
        //         jQuery('.cf-complex__inserter-button').css(addBtnStyles).text('Content toevoegen');
        //         jQuery('.cf-complex__toggler').css(collapseBtnStyles);
        //         jQuery('.cf-container__fields').prepend(jQuery('.cf-complex__toggler'));
        //         jQuery('.cf-complex--grid').css('paddingTop', 0);
        //     });
        // }
        function customizeNestedPagesPlugin() {
            jQuery(document).ready(function($) {	
                jQuery('.wrap.nestedpages .action-buttons').remove();
                jQuery('.wrap.nestedpages .nestedpages-list-header').remove();
                jQuery('.wrap.nestedpages .np-bulk-checkbox').remove();
                jQuery('.wrap.nestedpages .nestedpages-listing-title a').remove();
            });
        }

        function flushSimplePagesCacheOnDrag() {
            let menuEls = document.querySelectorAll('.wrap.nestedpages .post-type-page');
            menuEls.forEach(el => {
                el.addEventListener("mousedown", function(event) {
                    // axios.get('/_mcfu638b-cms/wp-content/themes/wtcustom/ajax/flushSimplePagesRestCache.php');
                    jQuery.ajax('/_mcfu638b-cms/wp-content/themes/wtcustom/ajax/flushSimplePagesRestCache.php');
                });
            });
        }

    </script>
    <?php
}

/** Register endpoints so they will be cached. */
add_filter('wp_rest_cache/allowed_endpoints', 'wprc_add_simple_pages_endpoint', 10, 1);
add_filter('wp_rest_cache/allowed_endpoints', 'wprc_add_simple_posts_endpoint', 10, 1);
add_filter('wp_rest_cache/allowed_endpoints', 'wprc_add_website_options_endpoint', 10, 1);
add_filter('wp_rest_cache/allowed_endpoints', 'wprc_add_simple_media_endpoint', 10, 1);
// add_filter('wp_rest_cache/allowed_endpoints', 'wprc_add_head_content_endpoint', 10, 1); /** Somehow head-content is not cached. Could be due to no json-response(?). Caching is not important, it is just for the developers **/
add_filter('wp_rest_cache/allowed_endpoints', 'wprc_add_woo_custom_filter_products', 10, 1);
add_filter('wp_rest_cache/allowed_endpoints', 'wprc_add_woo_custom_attributes_terms', 10, 1);
add_filter('wp_rest_cache/allowed_endpoints', 'wprc_add_woo_v3_endpoints', 10, 1);
// add_filter('wp_rest_cache/allowed_endpoints', 'wprc_add_woo_v3_endpoints_term_test', 10, 1);
function wprc_add_simple_pages_endpoint($allowed_endpoints) {
    if(!isset($allowed_endpoints['wtcustom']) || !in_array('simple-pages', $allowed_endpoints['wtcustom'])) $allowed_endpoints['wtcustom'][] = 'simple-pages';
    return $allowed_endpoints;
}
function wprc_add_simple_posts_endpoint($allowed_endpoints) {
    if(!isset($allowed_endpoints['wtcustom']) || !in_array('simple-posts', $allowed_endpoints['wtcustom'])) $allowed_endpoints['wtcustom'][] = 'simple-posts';
    return $allowed_endpoints;
}
function wprc_add_website_options_endpoint($allowed_endpoints) {
  if(!isset($allowed_endpoints['wtcustom']) || !in_array('website-options', $allowed_endpoints['wtcustom'])) $allowed_endpoints['wtcustom'][] = 'website-options';
  return $allowed_endpoints;
}
function wprc_add_simple_media_endpoint($allowed_endpoints) {
  if(!isset($allowed_endpoints['wtcustom']) || !in_array('simple-media', $allowed_endpoints['wtcustom'])) $allowed_endpoints['wtcustom'][] = 'simple-media';
  return $allowed_endpoints;
}
/** Somehow head-content is not cached. Could be due to no json-response(?). Caching is not important, it is just for the developers **/
// function wprc_add_head_content_endpoint($allowed_endpoints) {
  // if(!isset($allowed_endpoints['wtcustom']) || !in_array('head-content', $allowed_endpoints['wtcustom'])) $allowed_endpoints['wtcustom'][] = 'head-content';
  // return $allowed_endpoints;
// }
function wprc_add_woo_custom_filter_products($allowed_endpoints) {
    if(!isset($allowed_endpoints['wtcustom']) || !in_array('filter-products', $allowed_endpoints['wtcustom'])) $allowed_endpoints['wtcustom'][] = 'filter-products';
    return $allowed_endpoints;
}
function wprc_add_woo_custom_attributes_terms($allowed_endpoints) {
    if(!isset($allowed_endpoints['wtcustom']) || !in_array('attributes-terms', $allowed_endpoints['wtcustom'])) $allowed_endpoints['wtcustom'][] = 'attributes-terms';
    return $allowed_endpoints;
}
function wprc_add_woo_v3_endpoints($allowed_endpoints) {
    if(!isset($allowed_endpoints['wc']) || !in_array('v3', $allowed_endpoints['wc'])) $allowed_endpoints['wc'][] = 'v3';
    return $allowed_endpoints;
}

add_action('rest_api_init', function () {
    register_rest_route('wtcustom', '/simple-pages', array(
        'methods' => 'GET',
        'callback' => 'getPagesSimplified',
    ));
});
add_action('rest_api_init', function () {
    register_rest_route('wtcustom', '/simple-posts', array(
        'methods' => 'GET',
        'callback' => 'getPostsSimplified',
    ));
});
add_action('rest_api_init', function () {
    register_rest_route('wtcustom', '/website-options', array(
        'methods' => 'GET',
        'callback' => 'getWebsiteOptions',
    ));
});
add_action('rest_api_init', function () {
    register_rest_route('wtcustom', '/simple-media', array(
        'methods' => 'GET',
        'callback' => 'getMediaSimplified',
    ));
});
/** display <head> section, (for copy-pasting plugin css and js includes) **/
add_action('rest_api_init', function () {
  register_rest_route( 'wtcustom', '/head-content',array(
    'methods'  => 'GET',
    'callback' => 'getHeadContent'
  ));
});

function getMediaSimplified(WP_REST_Request $request) {
    $media = get_posts([
        'numberposts' => -1,
        'post_type' => 'attachment',
    ]);
    $aRes = [];
    foreach ($media as $item) {
        $oP = new stdClass();
        $oP->id = $item->ID;
        $oP->url = $item->guid;
        $topic = '';
        $alt = '';
        if(isset(get_post_meta($item->ID, 'attach_to_topic')[0])) $topic = get_post_meta($item->ID, 'attach_to_topic')[0];
        if(isset(get_post_meta($item->ID, '_wp_attachment_image_alt')[0])) $alt = get_post_meta($item->ID, '_wp_attachment_image_alt')[0];
        $oP->topic = $topic;
        $oP->alt = $alt;
        $aRes[] = $oP;
    }
    $response = new WP_REST_Response($aRes);
    $response->set_status(200);
    return $response;
}
function getPagesSimplified(WP_REST_Request $request) {
    $pages = get_pages();
    $aRes = getPagesCollectionAttrs($pages);
    $response = new WP_REST_Response($aRes);
    $response->set_status(200);
    return $response;
}
function getPostsSimplified(WP_REST_Request $request) {
    $parameters = $request->get_params();
    $orderby = 'date';
    $order = 'DESC';
    if (isset($parameters['orderby'])) {
        $orderby = $parameters['orderby'];
    }
    if (isset($parameters['order'])) {
        $order = $parameters['order'];
    }
    $posts = get_posts([
        'numberposts' => -1,
        'orderby' => $orderby,
        'order' => $order,
    ]);
    $aRes = getPostsCollectionAttrs($posts);
    $response = new WP_REST_Response($aRes);
    $response->set_status(200);
    return $response;
}

function getWebsiteOptions() {
    global $carbonFieldsArgs; // using global. Importing does not work: https://stackoverflow.com/questions/11086773/php-function-use-variable-from-outside
    $aOptions = array();
    foreach($carbonFieldsArgs['websiteOptions'] as $opt) {
        $aOptions[$opt[1]] = carbon_get_theme_option($opt[1]);
    }
    $response = new WP_REST_Response($aOptions);
    $response->set_status(200);
    return $response;
}
function getHeadContent() {
  $res = do_action( 'wp_head' );
  $response = new WP_REST_Response($res);
  $response->set_status(200);
  return $response;
}
function getPagesCollectionAttrs($coll) {
    $aRes = [];
    foreach ($coll as $item) {
        $oP = new stdClass();
        $oP->id = $item->ID;
        $oP->title = $item->post_title;
        $oP->slug = $item->post_name;
        $oP->parent = $item->post_parent;
        $oP->order = $item->menu_order;
        $oP->status = $item->post_status;
        $oP->date = $item->post_date;
        $aRes[] = $oP;
    }
    return $aRes;
}
function getPostsCollectionAttrs($coll) {
    $aRes = [];
    foreach ($coll as $item) {
        $oP = new stdClass();

        $tags = get_the_tags($item->ID);
        $aTags = array();
        if($tags) {
            foreach ($tags as $oTag) {
                $aTags[$oTag->slug] = $oTag->name;
            }
        }

        $groups = get_post_meta($item->ID, 'esplendor_group');
        $group = false;
        if($groups) {
            $group = $groups[0];
        }

        $metaTopics = get_post_meta($item->ID, 'topics');
        $topics = array();
        if($metaTopics && count(array_filter($metaTopics))) {
            $topics = $metaTopics[0];
        }

        $oP->id = $item->ID;
        $oP->title = $item->post_title;
        $oP->slug = $item->post_name;
        $oP->parent = $item->post_parent;
        $oP->order = $item->menu_order;
        $oP->status = $item->post_status;
        $oP->date = $item->post_date;
        $oP->category = get_the_category($item->ID)[0]->name;
        $oP->tags = $aTags;
        $oP->esplendor_group = $group;
        $oP->topics = $topics;
        $aRes[] = $oP;
    }
    return $aRes;
}

// Custum REST API for all attributes + terms for current category
add_action('rest_api_init', 'wp_rest_attributes_terms');
function wp_rest_attributes_terms($request) {
    register_rest_route('wtcustom', '/attributes-terms', array(
        'methods' => 'GET',
        'callback' => 'wp_rest_attributes_terms_handler',
    ));
}
function wp_rest_attributes_terms_handler($request = null) {
    $output = array();
    $params = $request->get_params();
    $category = $params['category'];

    // Use default arguments.
    $args = [
        'post_type'         => 'product',
        // 'posts_per_page'    => 10,
        'post_status'       => 'publish',
        'fields'            => 'ids',
        // 'paged'             => 1,
        // 'no_found_rows'     => true, // can make the query faster ?!?! https://wordpress.stackexchange.com/questions/177908/return-only-count-from-a-wp-query-request
    ];
    $args['tax_query']['relation'] = 'AND';
    // Category filter.
    if ( ! empty( $category ) ) {
        $args['tax_query'][] = [
            'taxonomy' => 'product_cat',
        //   'field'    => 'slug',
            'terms'    => [ $category ],
        ];
    }

    $the_query = new \WP_Query( $args );

    if ( ! $the_query->have_posts() ) {
        return $output;
    }

    $data = array();
    while ( $the_query->have_posts() ) {
        $the_query->the_post();
        $product = wc_get_product( get_the_ID() );  
        foreach( $product->get_attributes() as $taxonomy => $attribute ){
            if(substr($taxonomy, 0, 3) != 'pa_') continue;
            $attribute_name = wc_attribute_label( $taxonomy ); // Attribute name
            $data[$taxonomy]['name'] = $attribute_name;
            foreach ( $attribute->get_terms() as $term ){
                $data[$taxonomy]['values'][$term->term_id]['name'] = $term->name;
                // $data[$taxonomy]['values'][$term->name]['name'] = $term->name;
            }
        }

    }
    $output = $data;

    wp_reset_postdata();

    // return new WP_REST_Response($output, 123);
    $response = new WP_REST_Response($output);
    $response->set_status(200);
    return $response;
}




// Create Custom REST API for Filter (https://stackoverflow.com/questions/59135291/filter-product-list-by-mutiple-attribute-and-its-attribute-terms-in-woocommerce/66421170)
add_action('rest_api_init', 'wp_rest_filterproducts_endpoints');
function wp_rest_filterproducts_endpoints($request) {
    // register_rest_route('wp/v3', 'filter/products', array(
    register_rest_route('wtcustom', '/filter-products', array(
        'methods' => 'GET',
        'callback' => 'wp_rest_filterproducts_endpoint_handler',
    ));
}

function wp_rest_filterproducts_endpoint_handler($request = null) {
    $output = array();
    $params = $request->get_params();

    $category = $params['category'];
    $filters  = $params['filter'];
    $crb      = $params['crb']; // Carbon Fields -filter
    $per_page = $params['per_page'];
    $offset   = $params['offset'];
    $order    = $params['order'];
    $orderby  = $params['orderby'];
    $count    = $params['count'];
    
    // Use default arguments.
    $args = [
        'post_type'         => 'product',
        // 'posts_per_page'    => 10,
        'post_status'       => 'publish',
        // 'paged'             => 1,
        // 'no_found_rows'     => true, // can make the query faster ?!?! https://wordpress.stackexchange.com/questions/177908/return-only-count-from-a-wp-query-request
    ];

    if ( ! empty( $count ) ) { // when counting totals, select only IDs
        $args['fields'] = 'ids';
    }

    // Posts per page.
    if ( ! empty( $per_page ) ) {
      $args['posts_per_page'] = $per_page;
    }
    // Pagination, starts from 1.
    if ( ! empty( $offset ) ) {
      $args['paged'] = $offset;
    }
    // Order condition. ASC/DESC.
    if ( ! empty( $order ) ) {
      $args['order'] = $order;
    }
    // Orderby condition. Name/Price.
    if ( ! empty( $orderby ) ) {
      if ( $orderby === 'price' ) {
        $args['orderby'] = 'meta_value_num';
      } else {
        $args['orderby'] = $orderby;
      }
    }
    // If filter buy category or attributes.
    if ( ! empty( $category ) || ! empty( $filters ) ) {
      $args['tax_query']['relation'] = 'AND';
      // Category filter.
      if ( ! empty( $category ) ) {
        $args['tax_query'][] = [
          'taxonomy' => 'product_cat',
        //   'field'    => 'slug',
          'terms'    => [ $category ],
        ];
      }
      // Attributes filter.
      if ( ! empty( $filters ) ) {
        foreach ( $filters as $filter_key => $filter_value ) {
          if ( $filter_key === 'min_price' || $filter_key === 'max_price' ) {
            continue;
          }

          $args['tax_query'][] = [
            'taxonomy' => $filter_key,
            'field'    => 'term_id',
            // 'field'    => 'slug',
            'terms'    => \explode( ',', $filter_value ),
          ];
        }
      }
      // Min / Max price filter.
      if ( isset( $filters['min_price'] ) || isset( $filters['max_price'] ) ) {
        $price_request = [];
        if ( isset( $filters['min_price'] ) ) {
          $price_request['min_price'] = $filters['min_price'];
        }
        if ( isset( $filters['max_price'] ) ) {
          $price_request['max_price'] = $filters['max_price'];
        }
        $args['meta_query'][] = \wc_get_min_max_price_meta_query( $price_request );
        }
    }
    // Carbon Fields filter.
    if ( ! empty( $crb ) ) {
        foreach ( $crb as $crb_key => $crb_value ) {
            $carbon = [
                'key' => $crb_key,
                'value' => $crb_value,
            ];
            $args['meta_query'][] = $carbon;
        }
    }
// print_r($args);
    // $crb = [
    //     'key' => 'is_featured',
    //     'value' => 'yes',
    // ];
    // $args['meta_query'][] = $crb;
    
    $the_query = new \WP_Query( $args );

    if ( ! $the_query->have_posts() ) {
      return $output;
    }

    if ( ! empty( $count ) ) {
        $output['total'] = $the_query->found_posts;
    } else {
        while ( $the_query->have_posts() ) {
            $the_query->the_post();
            $product = wc_get_product( get_the_ID() );  
    
            // Product Properties
            $wcproduct['id'] = $product->get_id();
            $wcproduct['name'] = $product->get_name();
            $wcproduct['price'] = $product->get_price();
            $wcproduct['regular_price'] = $product->get_regular_price();
            $wcproduct['sale_price'] = $product->get_sale_price();
            $wcproduct['slug'] = $product->get_slug();
            $wcproduct['short_description'] = $product->get_short_description();
            $mainImageId = $product->get_image_id();
            $imageGalleryIds = $product->get_gallery_image_ids();
            $AllImgSrcs = bundleProductImages($mainImageId, $imageGalleryIds);
            $wcproduct['images'] = $AllImgSrcs;
            // $wcproduct['categories'] = wc_get_product_category_list($product->get_id());

            $catIds = [];
            $ancestors = [];
            $catTerms = get_the_terms( get_the_ID(), 'product_cat' );
            foreach($catTerms  as $term  ) {
                $catIds[] = $term->term_id;
                $ancestors = array_merge($ancestors, get_ancestors($term->term_id, 'product_cat'));
            }
            $wcproduct['categories'] = $catIds;
            $wcproduct['ancestors'] = array_unique($ancestors);

            $output[] = $wcproduct;
        }
    }

    wp_reset_postdata();

    // return new WP_REST_Response($output, 123);
    $response = new WP_REST_Response($output);
    $response->set_status(200);
    return $response;
}
function bundleProductImages($mainId, $galleryIds) {
    $images = array();
    if($mainId) $images[] = str_replace('_mcfu638b-cms/wp-content/uploads', 'media', wp_get_attachment_url($mainId));
    foreach($galleryIds as $imgId) $images[] = str_replace('_mcfu638b-cms/wp-content/uploads', 'media', wp_get_attachment_url($imgId));
    return $images;
}
function remove_admin_menus() {
    remove_menu_page( 'edit-comments.php' );
    remove_menu_page( 'options-general.php' );
    remove_menu_page( 'tools.php' );
    remove_menu_page( 'edit.php' );
    global $submenu;
    unset($submenu['edit.php?post_type=page'][10]); // Removes 'Add New'.
}
function remove_comment_support() {
    remove_post_type_support( 'post', 'comments' );
    remove_post_type_support( 'page', 'comments' );
}
function remove_admin_bar_menus() {
    global $wp_admin_bar;
    $wp_admin_bar->remove_menu('comments');
    $wp_admin_bar->remove_menu('new-content');
    $wp_admin_bar->remove_menu('wp-logo');
    $wp_admin_bar->remove_menu('site-name');
    $wp_admin_bar->remove_menu('view');
}
function contextual_help_list_remove(){
    global $current_screen;
    $current_screen->remove_help_tabs();
}
function remove_screen_options() {
    return false;
}
?>
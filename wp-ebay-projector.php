<?php
/*
Plugin Name: Wp Ebay Projector
Plugin URI:  
Description: List items from an ebay store with ability to filter by search manually or by the user
Version:     0.9.8
Author:      Paul Gemignani
Author URI:  http://www.paulgemignani.info
License:     GPL2
License URI: https://www.gnu.org/licenses/gpl-2.0.html
Text Domain: wpep-wp-ebay-projector
Domain Path: /languages

Wp Ebay Projector is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 2 of the License, or
any later version.

You should have received a copy of the GNU General Public License
along with Wp Ebay Projector. If not, see https://www.gnu.org/licenses/gpl-2.0.html.
*/


if ( ! function_exists( 'wpep_initialize_extension' ) ):
    /**
     * Creates the extension's main class instance.
     *
     * @since 1.0.0
     */
    function wpep_initialize_extension() {
        // Load DIVI extension
	    require_once plugin_dir_path( __FILE__ ) . 'includes/WpEbayProjector.php';
        // Load WpepEbay Class
        require_once plugin_dir_path( __FILE__ ) . 'includes/WpepEbay.php';
        require_once plugin_dir_path( __FILE__ ) . 'includes/WpepTemplate.php';
    }
    add_action( 'divi_extensions_init', 'wpep_initialize_extension' );
endif;

// load classes on init
function load_classes(){
    require_once plugin_dir_path( __FILE__ ) . 'includes/WpepEbay.php';
    require_once plugin_dir_path( __FILE__ ) . 'includes/WpepTemplate.php';

}
add_action('init','load_classes');


// create custom plugin settings menu
add_action('admin_menu', 'wpep_create_menu');

function wpep_create_menu() {

    //create new top-level menu
    add_options_page('Divi Ebay Settings', 'Divi Ebay Settings', 'administrator', __FILE__, 'wpep_settings_page');

    //call register settings function
    add_action( 'admin_init', 'register_wpep_plugin_settings' );
}

function sd_wpep_scripts(){
    
       // JS file
       wp_enqueue_script('jquery');
       wp_enqueue_script( 'wpep-js', plugins_url( 'scripts/wpep.js', __FILE__ ));
       wp_enqueue_script( 'wpep-slick-js', plugins_url( 'scripts/slick.js', __FILE__ ));
       // CSS file
       wp_enqueue_style( 'wpep-styles', plugins_url( 'styles/wpep.css', __FILE__ ));
       wp_enqueue_style( 'wpep-slick-styles', plugins_url( 'styles/slick.css', __FILE__ ));
       wp_enqueue_style( 'wpep-slick-theme-styles', plugins_url( 'styles/slick-theme.css', __FILE__ ));

       // Set Javascrip variables for JS object     
       $wpep_nonce = wp_create_nonce( 'wpep_nonce' );  
       wp_add_inline_script( 'wpep-js', 'const wpep = ' . json_encode( array(
           'ajaxUrl' => admin_url( 'admin-ajax.php' ),
           'nonce' => $wpep_nonce,
       ) ), 'before' );
       
}
add_action( 'wp_enqueue_scripts', 'sd_wpep_scripts' );

function register_wpep_plugin_settings() {
    //register our settings
    register_setting( 'wpep-settings-group', 'wpep_default_store_id' );
    register_setting( 'wpep-settings-group', 'wpep_default_ebay_site' );
    register_setting( 'wpep-settings-group', 'wpep_default_display' );
    register_setting( 'wpep-settings-group', 'wpep_default_cache' );
    register_setting( 'wpep-settings-group', 'wpep_default_orientation' );
}


add_action( 'wp_ajax_nopriv_wpep_search_ajax', 'wpep_search_ajax' );
add_action( 'wp_ajax_wpep_search_ajax', 'wpep_search_ajax' );

function wpep_search_ajax() {
    //global $wpdb; // this is how you get access to the database
    $Ebay = new WpepEbay();
    $Template = new WpepTemplate();
    
    $props = $Ebay->get_ajax_params();    


    $data = $Ebay->get_items($props);
    $data['module'] = $props;
    
    $template = $Template->get_item_template($props['template'], $data);
    $pagination_html = $Template->template_pagination($data['pagination']);   

    echo json_encode(array(
        'success'   => true,
        'result'    => $template['body'],
        'pagination'    => $pagination_html,
        'pagi-data'    => $data['pagination']
    ));


/*    $status_code = 200;
    $options = ['testOption'=>'yes'];
    
    wp_send_json( $response, $status_code, $options );
  */
    wp_die(); // All ajax handlers die when finished
}


function wpep_settings_page() {
?>
<div class="wrap">
<h1>WP Ebay Projector Settings</h1>

<form method="post" action="options.php">
    <?php settings_fields( 'wpep-settings-group' ); ?>
    <?php do_settings_sections( 'wpep-settings-group' ); ?>
    <?php 
        $ebay_site = get_option('wpep_default_ebay_site');
        $ebay_display = get_option('wpep_default_display');
        $ebay_orientation = get_option('wpep_default_orientation');
        $ebay_cache = get_option('wpep_default_cache');
    ?>
    <h3>Set the default variables for your module</h3>
    <P></P>
    <table class="form-table">
        <tr valign="top">
        <th scope="row">Ebay Store ID</th>
        <td><input type="text" name="wpep_default_store_id" value="<?php echo esc_attr( get_option('wpep_default_store_id') ); ?>" /></td>
        </tr>
         
        <tr valign="top">
        <th scope="row">Ebay Global ID</th>
        <td><select name="wpep_default_ebay_site" id="wpep_default_ebay_site" class="form-control">
            <option value="EBAY-US" <?php echo $ebay_site == 'EBAY-US'? 'selected': '';?>>eBay US</option>
            <option value="EBAY-GB" <?php echo $ebay_site == 'EBAY-GB'? 'selected': '';?>>eBay UK</option>
            <option value="EBAY-FRCA" <?php echo $ebay_site == 'EBAY-FRCA'? 'selected': '';?>>eBay Canada</option>
            <option value="EBAY-AU" <?php echo $ebay_site == 'EBAY-AU'? 'selected': '';?>>eBay Australia</option>
            <option value="EBAY-FRBE" <?php echo $ebay_site == 'EBAY-FRBE'? 'selected': '';?>>eBay Belgium</option>
            <option value="EBAY-" <?php echo $ebay_site == 'EBAY-'? 'selected': '';?>>eBay Germany</option>
            <option value="EBAY-" <?php echo $ebay_site == 'EBAY-'? 'selected': '';?>>eBay France</option>
            <option value="EBAY-" <?php echo $ebay_site == 'EBAY-'? 'selected': '';?>>eBay Spain</option>
            <option value="16"<?php echo $ebay_site == 'EBAY-'? 'selected': '';?>>eBay Austria</option>
            <option value="101"<?php echo $ebay_site == 'EBAY-'? 'selected': '';?>>eBay Italy</option>
            <option value="146"<?php echo $ebay_site == 'EBAY-'? 'selected': '';?>>eBay Netherlands</option>
            <option value="205"<?php echo $ebay_site == 'EBAY-'? 'selected': '';?>>eBay Ireland</option>
            <option value="193"<?php echo $ebay_site == 'EBAY-'? 'selected': '';?>>eBay Switzerland</option>
            </select></td>
            <td><p>Select the global ID for the Ebay store location you will be using to display products from.</p></td>
        </tr>
       
        <tr valign="top">
        <th scope="row">Default Display</th>
        <td>
            <select name="wpep_default_display" id="wpep_default_display" class="form-control">
            <option value="table" <?php echo $ebay_display == 'table'? 'selected': '';?>>Table</option>
            <option value="responsive" <?php echo $ebay_display == 'responsive'? 'selected': '';?>>Responsive</option>
            <option value="flip" <?php echo $ebay_display == 'flip'? 'selected': '';?>>Flip Card</option>
            <option value="poster" <?php echo $ebay_display == 'poster'? 'selected': '';?>>Poster Card</option>
            <option value="image" <?php echo $ebay_display == 'image'? 'selected': '';?>>Image Only</option>
            <option value="slideshow" <?php echo $ebay_display == 'slide'? 'selected': '';?>>Slideshow</option>

            </select>
        </td>
        <td><p>Select the default display you would like to use.</p></td>
        </tr>
        <tr>
            <th scope="row">Default Display Orientation</th>
            <td>
                <select name="wpep_default_orientation" id="wpep_default_orientation" class="form-control">
                <option value="landscape" <?php echo $ebay_orientation == 'landscape'? 'selected': '';?>>Landscape</option>
                <option value="portrait" <?php echo $ebay_orientation == 'portrait'? 'selected': '';?>>Portrait</option>
                </select>
            </td>
            <td><p>Select the default orientation for a image wider or taller</p></td>
        </tr>
       
        <tr valign="top">
        <th scope="row">Cache On</th>
        <td>
            <select name="wpep_default_cache" id="wpep_default_cache" class="form-control">
            <option value="on" <?php echo $ebay_cache == 'on'? 'selected': '';?>>On</option>
            <option value="off" <?php echo $ebay_cache == 'off'? 'selected': '';?>>Off</option>
            </select>
        </td>
        </tr>
    </table>
    
    <?php submit_button(); ?>

</form>
</div>
<?php } 
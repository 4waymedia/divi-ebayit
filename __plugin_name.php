<?php
/*
Plugin Name: Wp Ebay Projector
Plugin URI:  
Description: List items from an ebay store with ability to filter by search manually or by the user
Version:     1.0.0
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

Wp Ebay Projector is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
GNU General Public License for more details.

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
	require_once plugin_dir_path( __FILE__ ) . 'includes/WpEbayProjector.php';
}
add_action( 'divi_extensions_init', 'wpep_initialize_extension' );
endif;

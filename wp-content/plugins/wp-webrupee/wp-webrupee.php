<?php
/*
Plugin Name: WP WebRupee
Plugin URI: http://www.kdclabs.com/?p=74
Description: WP WebRupee provides a simple, cross browser method for using the Rupee symbol on your WordPress. It converts all the Rs / Rs. symbol to Rupee Symbol.
Version: 1.0.2
Author: _KDC-Labs
Author URI: http://www.kdclabs.com/
License: GNU General Public License v2.0
License URI: http://www.gnu.org/licenses/gpl-2.0.html
*/

add_action( 'wp_enqueue_scripts', 'wp_webrupee_js' );

function wp_webrupee_js() {
	wp_enqueue_script( 'wp-webrupee-script', 'http://cdn.webrupee.com/js' );
}
<?php
/**
 * Plugin Name: YITH WooCommerce Zoom Magnifier
 * Plugin URI: http://yithemes.com/
 * Description: YITH WooCommerce Zoom Magnifier enables you to add a zoom effect to product images.
 * Version: 1.1.5
 * Author: Your Inspiration Themes
 * Author URI: http://yithemes.com/
 * Text Domain: yit
 * Domain Path: /languages/
 *
 * @author Your Inspiration Themes
 * @package YITH WooCommerce Magnifier
 * @version 1.1.2
 */

if ( !defined( 'ABSPATH' ) ) { exit; } // Exit if accessed directly

if (function_exists('yith_wcmg_is_enabled')){
    return;
}

if( !function_exists('is_plugin_active') ) {
	include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
}

global $woocommerce;
if ( ! isset( $woocommerce ) ) return;

if ( ! is_plugin_active( 'yith-woocommerce-zoom-magnifier/init.php' ) ) {

    /**
     * Required functions
     */
    if ( ! defined( 'YITH_FUNCTIONS' ) ) {
        require_once( 'yit-common/yit-functions.php' );
    }

    add_option( 'yith_wcmg_slider_direction', apply_filters( 'yith_wcmg_slider_direction', 'left' ) );

    define( 'YITH_WCMG', true );

    define( 'YITH_WCMG_URL', YIT_THEME_PLUGINS_URL . '/yith_magnifier/' );
    define( 'YITH_WCMG_DIR', dirname( __FILE__ ) . '/' );

// Load required classes and functions
    require_once( 'functions.yith-wcmg.php' );
    require_once( 'class.yith-wcmg-admin.php' );
    require_once( 'class.yith-wcmg-frontend.php' );
    require_once( 'class.yith-wcmg.php' );

// Let's start the game!
    global $yith_wcmg;
    $yith_wcmg = new YITH_WCMG();
}
else {
    return;
}
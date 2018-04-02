<?php
/**
 * WC Dependency Checker
 *
 * Checks if WooCommerce is enabled
 */
class WC_Dependencies {

	private static $active_plugins;

	function init() {

		self::$active_plugins = (array) get_option( 'active_plugins', array() );

		if ( is_multisite() )
			self::$active_plugins = array_merge( self::$active_plugins, get_site_option( 'active_sitewide_plugins', array() ) );
	}

	function woocommerce_active_check() {

		if ( ! self::$active_plugins ) self::init();
        $woo = self::_get_plugin_basename_from_slug( 'woocommerce' );
        return in_array( $woo, self::$active_plugins ) || array_key_exists( $woo, self::$active_plugins );;

//         global $woocommerce;
//         return ( isset( $woocommerce ) ) ? true : false;

	}

	/**
	 * Helper function to extract the file path of the plugin file from the
	 * plugin slug, if the plugin is installed.
	 *
	 * @param string $slug Plugin slug (typically folder name) as provided by the developer
	 * @return string Either file path for plugin if installed, or just the plugin slug
	 */
	function _get_plugin_basename_from_slug( $slug ) {

        include_once ABSPATH . '/wp-admin/includes/plugin.php';

		$keys = array_keys( get_plugins() );

		foreach ( $keys as $key ) {
			if ( preg_match( '|^' . $slug .'|', $key ) )
				return $key;
		}

		return $slug;

	}

}



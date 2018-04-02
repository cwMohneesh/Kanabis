<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

include_once( 'api-requests/class-wc-shipstation-api-request.php' );

/**
 * WC_Shipstation_API Class
 */
class WC_Shipstation_API extends WC_Shipstation_API_Request {

	/**
	 * Constructor
	 */
	public function __construct() {
		nocache_headers();

		$this->request();
	}

	/**
	 * Handle the request
	 */
	public function request() {

		if ( empty( $_GET['auth_key'] ) ) {
			$this->trigger_error( __( 'Authentication key is required!', 'woocommerce-shipstation' ) );
		}

		if ( sanitize_text_field( $_GET['auth_key'] ) !== WC_ShipStation_Integration::$auth_key ) {
			$this->trigger_error( __( 'Invalid authentication key', 'woocommerce-shipstation' ) );
		}

		$request = $_GET;

		if ( isset( $request['action'] ) ) {
			$this->request = array_map( 'sanitize_text_field', $request );
		} else {
			$this->trigger_error( __( 'Invalid request', 'woocommerce-shipstation' ) );
		}

		if ( in_array( $this->request['action'], array( 'export', 'shipnotify' ) ) ) {
			$this->log( sprintf( __( 'Input params: %s', 'woocommerce-shipstation' ), http_build_query( $this->request ) ) );
			$request_class = include( 'api-requests/class-wc-shipstation-api-' . $this->request['action'] . '.php' );
			$request_class->request();
		} else {
			$this->trigger_error( __( 'Invalid request', 'woocommerce-shipstation' ) );
		}

		exit;
	}
}

new WC_Shipstation_API();
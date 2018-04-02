<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class WOOMULTI_CURRENCY_F_Frontend_Location
 */
class WOOMULTI_CURRENCY_F_Frontend_Location {
	protected $settings;

	public function __construct() {
		$settings       = new WOOMULTI_CURRENCY_F_Data();
		$this->settings = $settings;
		if ( $this->settings->get_enable() ) {
			add_action( 'init', array( $this, 'init' ) );
		}
	}

	public function init() {
		if ( is_ajax() ) {
			return;
		}
		$settings            = $this->settings;
		$auto_detect         = $settings->get_auto_detect();
		$currencies          = $settings->get_currencies();
		$selected_currencies = $settings->get_currencies();

		/*Check change currency*/
		if ( isset( $_GET['wmc-currency'] ) && in_array( $_GET['wmc-currency'], $selected_currencies ) ) {
			if ( is_admin() ) {
				return;
			}
			$current_currency = $_GET['wmc-currency'];
			$this->settings->set_current_currency( $current_currency );
		}
		/*Check auto detect*/
		switch ( $auto_detect ) {
			case 1:
				/*Auto select currency*/
				if ( isset( $_COOKIE['wmc_current_currency'] ) ) {
					return;
				} else {
					$detect_ip_currency = $this->detect_ip_currency();
					if ( $settings->get_enable_currency_by_country() && isset( $detect_ip_currency['country_code'] ) && $detect_ip_currency['country_code'] ) {

						foreach ( $currencies as $currency ) {
							$currency_detected = '';
							$data              = $settings->get_currency_by_countries( $currency );
							if ( in_array( $detect_ip_currency['country_code'], $data ) ) {
								$currency_detected = $currency;
								break;
							}
						}
						if ( $currency_detected ) {
							$this->settings->set_current_currency( $currency_detected );

						} else {
							$this->settings->set_current_currency( $detect_ip_currency['currency_code'] );

						}
					} elseif ( isset( $detect_ip_currency['currency_code'] ) && in_array( $detect_ip_currency['currency_code'], $currencies ) ) {

						$this->settings->set_current_currency( $detect_ip_currency['currency_code'] );


					} else {
						$this->settings->set_current_currency( $settings->get_default_currency() );

					}
				}
				break;
			case 2:
				/*Create approximately*/
				if ( isset( $_COOKIE['wmc_currency_rate'] ) ) {
					return;
				} else {
					$detect_ip_currency = $this->detect_ip_currency();
					if ( isset( $detect_ip_currency['currency_code'] ) ) {
						setcookie( 'wmc_currency_rate', $detect_ip_currency['currency_rate'], time() + 60 * 60 * 24, '/' );
						$_COOKIE['wmc_currency_rate'] = $detect_ip_currency['currency_rate'];
						setcookie( 'wmc_currency_symbol', $detect_ip_currency['currency_symbol'], time() + 60 * 60 * 24, '/' );
						$_COOKIE['wmc_currency_symbol'] = $detect_ip_currency['currency_symbol'];
					}
				}
				break;
			default:

		}
	}


	/**
	 * Get informations about client as current country and currency code, current rate
	 * @return array|mixed|string
	 */
	protected function detect_ip_currency() {

		if ( isset( $_COOKIE['wmc_ip_info'] ) && $_COOKIE['wmc_ip_info'] ) {
			$geoplugin_arg = json_decode( base64_decode( $_COOKIE['wmc_ip_info'] ), true );
		} else {

			if ( ! $this->settings->get_geo_api() ) {
				$ip            = new WC_Geolocation();
				$geo_ip        = $ip->geolocate_ip();
				$country_code  = isset( $geo_ip['country'] ) ? $geo_ip['country'] : '';
				$geoplugin_arg = array(
					'country'       => $country_code,
					'currency_code' => $this->settings->get_currency_code( $country_code )
				);
			} else {
				$ip_add = $this->get_ip();
				//				$ip_add = '14.171.25.187';
				setcookie( 'wmc_ip_add', $ip_add, time() + 60 * 60 * 24, '/' );
				$_COOKIE['wmc_ip_add'] = $ip_add;

				@$geoplugin = file_get_contents( 'http://www.geoplugin.net/php.gp?ip=' . $ip_add );
				if ( $geoplugin ) {
					$geoplugin_arg = unserialize( $geoplugin );
				}


				$geoplugin_arg = array(
					'country'       => isset( $geoplugin_arg['geoplugin_countryCode'] ) ? $geoplugin_arg['geoplugin_countryCode'] : 'US',
					'currency_code' => isset( $geoplugin_arg['geoplugin_currencyCode'] ) ? $geoplugin_arg['geoplugin_currencyCode'] : 'USD',
				);
			}

			if ( $geoplugin_arg['country'] ) {
				setcookie( 'wmc_ip_info', base64_encode( json_encode( $geoplugin_arg ) ), time() + 60 * 60 * 24, '/' );
				$_COOKIE['wmc_ip_info'] = base64_encode( json_encode( $geoplugin_arg ) );
			} else {
				return array();
			}
		}

		$auto_detect = $this->settings->get_auto_detect();
		if ( $auto_detect == 1 ) {
			/*Auto select currency*/
			if ( is_array( $geoplugin_arg ) and isset( $geoplugin_arg['currency_code'] ) ) {
				return array(
					'currency_code' => $geoplugin_arg['currency_code'],
					'country_code'  => $geoplugin_arg['country']
				);
			}
		} elseif ( $auto_detect == 2 ) {
			/*Approximately price*/
			if ( is_array( $geoplugin_arg ) and isset( $geoplugin_arg['currency_code'] ) ) {
				$currency_code = $geoplugin_arg['currency_code'];
				$symbol        = get_woocommerce_currency_symbol( $geoplugin_arg['currency_code'] );
			} else {
				return false;
			}
			$currencies      = $this->settings->get_currencies();
			$main_currency   = $this->settings->get_default_currency();
			$list_currencies = $this->settings->get_list_currencies();
			if ( in_array( $currency_code, $currencies ) ) {
				return array(
					'currency_code'   => $currency_code,
					'currency_rate'   => $list_currencies[$currency_code]['rate'],
					'currency_symbol' => get_woocommerce_currency_symbol( $currency_code )
				);
			} else {
				$exchange_rate = $this->settings->get_exchange( $main_currency, $currency_code );
				if ( is_array( $exchange_rate ) && isset( $exchange_rate[$currency_code] ) ) {
					return array(
						'currency_code'   => $currency_code,
						'currency_rate'   => $exchange_rate[$currency_code],
						'currency_symbol' => $symbol
					);
				}

			}

		}
	}

	/**
	 * Return IP
	 * @return string
	 */
	protected function get_ip() {


		$ipaddress = '';
		if ( isset( $_SERVER['REMOTE_ADDR'] ) ) {
			$ipaddress = $_SERVER['REMOTE_ADDR'];
		} else if ( isset( $_SERVER['HTTP_CLIENT_IP'] ) ) {
			$ipaddress = $_SERVER['HTTP_CLIENT_IP'];
		} else if ( isset( $_SERVER['HTTP_X_FORWARDED_FOR'] ) ) {
			$ipaddress = $_SERVER['HTTP_X_FORWARDED_FOR'];
		} else if ( isset( $_SERVER['HTTP_X_FORWARDED'] ) ) {
			$ipaddress = $_SERVER['HTTP_X_FORWARDED'];
		} else if ( isset( $_SERVER['HTTP_FORWARDED_FOR'] ) ) {
			$ipaddress = $_SERVER['HTTP_FORWARDED_FOR'];
		} else if ( isset( $_SERVER['HTTP_FORWARDED'] ) ) {
			$ipaddress = $_SERVER['HTTP_FORWARDED'];
		} else {
			$ipaddress = 'UNKNOWN';
		}

		return $ipaddress;
	}
}
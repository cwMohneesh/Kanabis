<?php
/*
Plugin Name: WooCommerce - PayU Money
Plugin URI: http://www.kdclabs.com/?p=64
Description: PayU Money Payment Gateway for WooCommerce. Redefining Payments, Simplifying Lives! Empowering any business to collect money online within minutes that helps you sell anything. Beautifully.
Version: 1.3.7
Author: _KDC-Labs
Author URI: http://www.kdclabs.com/
License: GNU General Public License v3.0
License URI: http://www.gnu.org/licenses/gpl-3.0.html
*/

add_action('plugins_loaded', 'woocommerce_payupaisa_init', 0);
define('payupaisa_imgdir', WP_PLUGIN_URL . "/" . plugin_basename(dirname(__FILE__)) . '/assets/img/');

function woocommerce_payupaisa_init(){
	if(!class_exists('WC_Payment_Gateway')) return;

    if( isset($_GET['msg']) && !empty($_GET['msg']) ){
        add_action('the_content', 'payupaisa_showMessage');
    }
    function payupaisa_showMessage($content){
            return '<div class="'.htmlentities($_GET['type']).'">'.htmlentities(urldecode($_GET['msg'])).'</div>'.$content;
    }

    /**
     * Gateway class
     */
	class WC_payupaisa extends WC_Payment_Gateway{
		public function __construct(){
			$this->id 					= 'payupaisa';
			$this->method_title 		= 'PayU Money';
			$this->method_description	= "Redefining Payments, Simplifying Lives";
			$this->has_fields 			= false;
			$this->init_form_fields();
			$this->init_settings();
			if ( $this->settings['showlogo'] == "yes" ) {
				$this->icon 			= payupaisa_imgdir . 'logo.png';
			}			
			$this->title 			= $this->settings['title'];
			$this->redirect_page_id = $this->settings['redirect_page_id'];
			if ( $this->settings['testmode'] == "yes" ) {
				$this->liveurl 			= 'https://test.payu.in/_payment';
				$this->merchant_id 		= "JBZaLc";
				$this->salt 			= "GQs7yium";
				$this->description 		= $this->settings['description'].
										"<br/><br/><u>Test Mode is <strong>ACTIVE</strong>, use following Credit Card details:-</u><br/>".
										"Test Card Name: <strong><em style='#999999;'>any name</em></strong><br/>".
										"Test Card Number: <strong>5123456789012346</strong><br/>".
										"Test Card CVV: <strong>123</strong><br/>".
										"Test Card Expiry: <strong>May 2017</strong><br/>";
			} else {
				$this->liveurl 			= 'https://secure.payu.in/_payment';
				$this->merchant_id 		= $this->settings['merchant_id'];
				$this->salt 			= $this->settings['salt'];
				$this->description 		= $this->settings['description'];
			}					
			$this->msg['message'] 	= "";
			$this->msg['class'] 	= "";
					
			add_action('init', array(&$this, 'check_payupaisa_response'));
			//update for woocommerce >2.0
			add_action( 'woocommerce_api_' . strtolower( get_class( $this ) ), array( $this, 'check_payupaisa_response' ) );
			
			if ( version_compare( WOOCOMMERCE_VERSION, '2.0.0', '>=' ) ) {
				/* 2.0.0 */
				add_action( 'woocommerce_update_options_payment_gateways_' . $this->id, array( &$this, 'process_admin_options' ) );
			} else {
				/* 1.6.6 */
				add_action( 'woocommerce_update_options_payment_gateways', array( &$this, 'process_admin_options' ) );
			}
			
			add_action('woocommerce_receipt_payupaisa', array(&$this, 'receipt_page'));
		}
    
		function init_form_fields(){
			$this->form_fields = array(
				'enabled' => array(
					'title' 		=> __('Enable/Disable', 'kdc'),
					'type' 			=> 'checkbox',
					'label' 		=> __('Enable PayU Money Payment Module.', 'kdc'),
					'default' 		=> 'no',
					'description' 	=> 'Show in the Payment List as a payment option'
				),
      			'title' => array(
					'title' 		=> __('Title:', 'kdc'),
					'type'			=> 'text',
					'default' 		=> __('Online Payments', 'kdc'),
					'description' 	=> __('This controls the title which the user sees during checkout.', 'kdc'),
					'desc_tip' 		=> true
				),
      			'description' => array(
					'title' 		=> __('Description:', 'kdc'),
					'type' 			=> 'textarea',
					'default' 		=> __('Pay securely by Credit or Debit Card or Internet Banking through PayU Secure Servers.', 'kdc'),
					'description' 	=> __('This controls the description which the user sees during checkout.', 'kdc'),
					'desc_tip' 		=> true
				),
      			'merchant_id' => array(
					'title' 		=> __('Merchant KEY', 'kdc'),
					'type' 			=> 'text',
					'description' 	=> __('Given to Merchant by PayU Money'),
					'desc_tip' 		=> true
				),
      			'salt' => array(
					'title' 		=> __('Merchant SALT', 'kdc'),
					'type' 			=> 'text',
					'description' 	=>  __('Given to Merchant by PayU Money', 'kdc'),
					'desc_tip' 		=> true
                ),
				'showlogo' => array(
					'title' 		=> __('Show Logo', 'kdc'),
					'type' 			=> 'checkbox',
					'label' 		=> __('Show the "PayU Money" logo in the Payment Method section for the user', 'kdc'),
					'default' 		=> 'yes',
					'description' 	=> __('Tick to show "PayU Money" logo'),
					'desc_tip' 		=> true
                ),
      			'testmode' => array(
					'title' 		=> __('TEST Mode', 'kdc'),
					'type' 			=> 'checkbox',
					'label' 		=> __('Enable PayU Money TEST Transactions.', 'kdc'),
					'default' 		=> 'no',
					'description' 	=> __('Tick to run TEST Transaction on the PayU Money platform'),
					'desc_tip' 		=> true
                ),
      			'redirect_page_id' => array(
					'title' 		=> __('Return Page'),
					'type' 			=> 'select',
					'options' 		=> $this->payupaia_get_pages('Select Page'),
					'description' 	=> __('URL of success page', 'kdc'),
					'desc_tip' 		=> true
                )
			);
		}
        /**
         * Admin Panel Options
         * - Options for bits like 'title' and availability on a country-by-country basis
         **/
		public function admin_options(){
			echo '<h3>'.__('PayU Money', 'kdc').'</h3>';
			echo '<p>'.__('Redefining Payments, Simplifying Lives! Empowering any business to collect money online within minutes').'</p>';
			echo '<table class="form-table">';
			// Generate the HTML For the settings form.
			$this->generate_settings_html();
			echo '</table>';
		}
        /**
         *  There are no payment fields for techpro, but we want to show the description if set.
         **/
		function payment_fields(){
			if($this->description) echo wpautop(wptexturize($this->description));
		}
		/**
		* Receipt Page
		**/
		function receipt_page($order){
			echo '<p>'.__('Thank you for your order, please click the button below to pay with PayU.', 'kdc').'</p>';
			echo $this->generate_payupaisa_form($order);
		}
		/**
		* Generate payu button link
		**/
		function generate_payupaisa_form($order_id){
			global $woocommerce;
			$order = new WC_Order( $order_id );
			$txnid = $order_id.'_'.date("ymds");
			
			
			if ( $this->redirect_page_id == "" || $this->redirect_page_id == 0 ) {
				$redirect_url = $order->get_checkout_order_received_url();
			} else {
				$redirect_url = get_permalink($this->redirect_page_id);
			}

			//For wooCoomerce 2.0
			if ( version_compare( WOOCOMMERCE_VERSION, '2.0.0', '>=' ) ) {
				$redirect_url = add_query_arg( 'wc-api', get_class( $this ), $redirect_url );
			}

			$productinfo = "Order $order_id";

			$str = "$this->merchant_id|$txnid|$order->order_total|$productinfo|$order->billing_first_name|$order->billing_email|$order_id||||||||||$this->salt";
			$hash = strtolower(hash('sha512', $str));

			$payupaisa_args = array(
				'key' 			=> $this->merchant_id,
				'hash' 			=> $hash,
				'txnid' 		=> $txnid,
				'amount' 		=> $order->order_total,
				'firstname'		=> $order->billing_first_name,
				'email' 		=> $order->billing_email,
				'phone' 		=> $order->billing_phone,
				'productinfo'	=> $productinfo,
				'surl' 			=> $redirect_url,
				'furl' 			=> $redirect_url,
				'lastname' 		=> $order->billing_last_name,
				'address1' 		=> $order->billing_address_1,
				'address2' 		=> $order->billing_address_2,
				'city' 			=> $order->billing_city,
				'state' 		=> $order->billing_state,
				'country' 		=> $order->billing_country,
				'zipcode' 		=> $order->billing_postcode,
				'curl'			=> $redirect_url,
				'pg' 			=> 'NB',
				'udf1' 			=> $order_id,
				'service_provider'	=> 'payu_paisa' // must be "payu_paisa"
			);
			$payupaisa_args_array = array();
			foreach($payupaisa_args as $key => $value){
				$payupaisa_args_array[] = "<input type='hidden' name='$key' value='$value'/>";
			}
			
			return '	<form action="'.$this->liveurl.'" method="post" id="payupaisa_payment_form">
  				' . implode('', $payupaisa_args_array) . '
				<input type="submit" class="button-alt" id="submit_payupaisa_payment_form" value="'.__('Pay via PayU Money', 'kdc').'" /> <a class="button cancel" href="'.$order->get_cancel_order_url().'">'.__('Cancel order &amp; restore cart', 'kdc').'</a>
					<script type="text/javascript">
					jQuery(function(){
					jQuery("body").block({
						message: "'.__('Thank you for your order. We are now redirecting you to Payment Gateway to make payment.', 'kdc').'",
						overlayCSS: {
							background		: "#fff",
							opacity			: 0.6
						},
						css: {
							padding			: 20,
							textAlign		: "center",
							color			: "#555",
							border			: "3px solid #aaa",
							backgroundColor	: "#fff",
							cursor			: "wait",
							lineHeight		: "32px"
						}
					});
					jQuery("#submit_payupaisa_payment_form").click();});
					</script>
				</form>';
		}
		/**
		* Process the payment and return the result
		**/
		function process_payment($order_id){
			global $woocommerce;
			$order = new WC_Order( $order_id );
			
			if ( version_compare( WOOCOMMERCE_VERSION, '2.1.0', '>=' ) ) {
				/* 2.1.0 */
				$checkout_payment_url = $order->get_checkout_payment_url( true );
			} else {
				/* 2.0.0 */
				$checkout_payment_url = get_permalink( get_option ( 'woocommerce_pay_page_id' ) );
			}

			return array(
				'result' => 'success', 
				'redirect' => add_query_arg(
					'order', 
					$order->id, 
					add_query_arg(
						'key', 
						$order->order_key, 
						$checkout_payment_url						
					)
				)
        	);
		}
		/**
		* Check for valid payu server callback
		**/
		function check_payupaisa_response(){
			global $woocommerce;
			if( isset($_REQUEST['txnid']) && isset($_REQUEST['mihpayid']) ){
				$order_id = $_REQUEST['udf1'];
				if($order_id != ''){
					try{
						$order = new WC_Order( $order_id );
						$hash = $_REQUEST['hash'];
						$status = $_REQUEST['status'];
						$checkhash = hash('sha512', "$this->salt|$_REQUEST[status]||||||||||$_REQUEST[udf1]|$_REQUEST[email]|$_REQUEST[firstname]|$_REQUEST[productinfo]|$_REQUEST[amount]|$_REQUEST[txnid]|$this->merchant_id");
						$transauthorised = false;
						
						if( $order->status !=='completed' ){
							if($hash == $checkhash){
								$status = strtolower($status);
								if($status=="success"){
									$transauthorised = true;
									$this->msg['message'] = "Thank you for shopping with us. Your account has been charged and your transaction is successful.";
									$this->msg['class'] = 'woocommerce-message';
									if($order->status == 'processing'){
										$order->add_order_note('PayU Money ID: '.$_REQUEST['mihpayid'].' ('.$_REQUEST['txnid'].')<br/>PG: '.$_REQUEST['PG_TYPE'].'<br/>Bank Ref: '.$_REQUEST['bank_ref_num']);
									}else{
										$order->payment_complete();
										$order->add_order_note('PayU Money payment successful.<br/>PayU Money ID: '.$_REQUEST['mihpayid'].' ('.$_REQUEST['txnid'].')<br/>PG: '.$_REQUEST['PG_TYPE'].'<br/>Bank Ref: '.$_REQUEST['bank_ref_num']);
										$woocommerce -> cart -> empty_cart();
									}
								}else if($status=="pending"){
									$this->msg['message'] = "Thank you for shopping with us. Right now your payment status is pending. We will keep you posted regarding the status of your order through eMail";
									$this->msg['class'] = 'woocommerce-info';
									$order->add_order_note('PayU Money payment status is pending<br/>PayU Money ID: '.$_REQUEST['mihpayid'].' ('.$_REQUEST['txnid'].')<br/>PG: '.$_REQUEST['PG_TYPE'].'<br/>Bank Ref: '.$_REQUEST['bank_ref_num']);
									$order->update_status('on-hold');
									$woocommerce -> cart -> empty_cart();
								}else{
									$this->msg['class'] = 'woocommerce-error';
									$this->msg['message'] = "Thank you for shopping with us. However, the transaction has been declined.";
									$order->add_order_note('Transaction ERROR: '.$_REQUEST['error'].'<br/>PayU Money ID: '.$_REQUEST['mihpayid'].' ('.$_REQUEST['txnid'].')');
								}
							}else{
								$this->msg['class'] = 'error';
								$this->msg['message'] = "Security Error. Illegal access detected.";
							}
							if($transauthorised==false){
								$order->update_status('failed');
							}
							//removed for WooCOmmerce 2.0
							//add_action('the_content', array(&$this, 'payupaisa_showMessage'));
						}
					}catch(Exception $e){
                        // $errorOccurred = true;
                        $msg = "Error";
					}
				}

                $redirect_url = ($this->redirect_page_id=="" || $this->redirect_page_id==0)?get_site_url() . "/":get_permalink($this->redirect_page_id);
                //For wooCoomerce 2.0
                $redirect_url = add_query_arg( array('msg'=> urlencode($this->msg['message']), 'type'=>$this->msg['class']), $redirect_url );

                wp_redirect( $redirect_url );
                exit;

			}
		
		}
		
		/*
        //Removed For WooCommerce 2.0
		function payupaisa_showMessage($content){
			return '<div class="box '.$this->msg['class'].'">'.$this->msg['message'].'</div>'.$content;
		}
		*/
		
		// get all pages
		function payupaia_get_pages($title = false, $indent = true) {
			$wp_pages = get_pages('sort_column=menu_order');
			$page_list = array();
			if ($title) $page_list[] = $title;
			foreach ($wp_pages as $page) {
				$prefix = '';
				// show indented child pages?
				if ($indent) {
                	$has_parent = $page->post_parent;
                	while($has_parent) {
                    	$prefix .=  ' - ';
                    	$next_page = get_post($has_parent);
                    	$has_parent = $next_page->post_parent;
                	}
            	}
            	// add to page list array array
            	$page_list[$page->ID] = $prefix . $page->post_title;
        	}
        	return $page_list;
    		}
		}
		/**
		* Add the Gateway to WooCommerce
		**/
		function woocommerce_add_payupaisa_gateway($methods) {
			$methods[] = 'WC_payupaisa';
			return $methods;
		}

		add_filter('woocommerce_payment_gateways', 'woocommerce_add_payupaisa_gateway' );
	}

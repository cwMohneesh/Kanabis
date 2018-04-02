<?php
/*
Plugin Name: Woo Aramex Shipping
Plugin URI: http://woothemes.com/woocommerce
Description: Aramex shipping API Integration.
Version: 1.0.0
Author: Mohneesh Bhargava
Author URI: http://computerware.in
*/

define('ARAMEX_URL', WP_PLUGIN_URL."/".dirname( plugin_basename( __FILE__ ) ) ); 
/**
 * Check if WooCommerce is active
 */
if ( in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) )
{
	/**
	 * aramex_shipping_method_init function.
	 */
	add_action( 'woocommerce_shipping_init', 'aramex_shipping_method_init' ); 
	function aramex_shipping_method_init() {
		if ( ! class_exists( 'WC_Aramex_Shipping' ) ) {
			include_once( 'classes/class_aramex_shipping.php' );
		}
		//$aramex = new WC_Aramex_Shipping_Method();
	}
	/**
	 * add_aramex_shipping_method function.
	 */
	add_filter( 'woocommerce_shipping_methods', 'add_aramex_shipping_method' );
	function add_aramex_shipping_method( $methods ) {
		$methods[] = 'WC_Aramex_Shipping';
		return $methods;
	}
	
	/**
	 * Display a notice ...
	 */
	add_action( 'admin_notices', 'my_aramex_notices' ); 
	function my_aramex_notices() {
		global $woocommerce;
		$missings = array();	
		if ( ! class_exists( 'WC_Aramex_Shipping' ) ){
			include_once( 'classes/class_aramex_shipping.php' );
		}
		$aramex = new WC_Aramex_Shipping();
		
		if( empty($aramex->AccountNumber) ){$missings[] = "Account Number";}
		if( empty($aramex->UserName) ){$missings[] = "User Name";}
		if( empty($aramex->Password) ){$missings[] = "Password";}
		if( empty($aramex->AccountPin) ){$missings[] = "Account Pin";}
		if( empty($aramex->AccountEntity) ){$missings[] = "Account Entity";}
		if( empty( $missings ) ){ return false;}
		
		$url = self_admin_url( 'admin.php?page=' . ( version_compare($woocommerce->version, '2.1.0') >= 0 ? 'wc-settings' : 'woocommerce_settings' ) . '&tab=shipping&section=wc_aramex_shipping_method' );
		$message = sprintf( __( 'Aramex error, some fields are missing: %s' , 'aramex' ), implode( ", ", $missings ) );
		echo '<div class="error fade"><p><a href="' . $url . '">' . $message . '</p></div>' . "\n";
	}
	
	
	add_action( 'woocommerce_order_status_completed', 'productShipped' );
	function productShipped(){
		include_once( 'classes/class_aramex_shipping.php' );
		$aramex = new WC_Aramex_Shipping();
		$aramex->requestShipment();
	}
	/**
	 * Display Metabox Shipment Tracking on order admin page
	**/
	add_action( 'add_meta_boxes', 'woocommerce_metaboxes' );
	add_action( 'woocommerce_order_items_table','track_page_shipping_details' );
	add_action( 'woocommerce_process_shop_order_meta', 'woocommerce_process_shop_ordermeta', 5, 2 );
	add_action( 'woocommerce_email_before_order_table', 'email_shipping_details' );
	add_action( 'manage_edit-shop_order_columns', 'add_shipping_column',11);
	add_action( 'manage_shop_order_posts_custom_column', 'add_shipping_column_details');
	
	/**
	 * Add fields to the metabox
	 **/
	function add_shipping_column_details($column){ 
			global $post, $woocommerce, $the_order;
			
			if ( empty( $the_order ) || $the_order->id != $post->ID )
				$the_order = new WC_Order( $post->ID );
				 
			switch ( $column ) { 	
				case "tracking_number" :
					$order_meta = get_post_custom( $the_order->id );
								
					for ($i=0; $i<=4; $i++)
				  	{
						if($i == 0){
							if(isset($order_meta['_order_trackno']) && isset($order_meta['_order_trackurl'])){
					  			admin_shipping_details( $order_meta['_order_trackno'], $order_meta['_order_trackurl'] , $order);
							}
						}else{
							if(isset($order_meta['_order_trackno'.$i]) && isset($order_meta['_order_trackurl'.$i])){
					  			admin_shipping_details($order_meta['_order_trackno'.$i] , $order_meta['_order_trackurl'.$i] , $order);
							}					
						}
				  	}
				break;	
			}
		}
		function add_shipping_column($columns){ 
			$columns["tracking_number"] 	= __('Tracking Number', 'aramex');
			return $columns;	
		}
		
	function woocommerce_process_shop_ordermeta( $post_id, $post ) { 
		global $wpdb, $woocommerce;
		$woocommerce_errors = array();
		
		add_post_meta( $post_id, '_order_key', uniqid('order_') );
		update_post_meta( $post_id, '_order_trackno', stripslashes( $_POST['_order_trackno'] ));
		update_post_meta( $post_id, '_order_trackurl', stripslashes( $_POST['_order_trackurl'] ));
	}
		
	function woocommerce_metaboxes() {
		add_meta_box( 'woocommerce-order-my-custom', __('Shipping Details', 'aramex'), 'woocommerce_order_shippingdetails', 
			'shop_order', 'side', 'high');
	}
		
	function woocommerce_order_shippingdetails($post) {
			$data = get_post_custom( $post->ID );
			$options = get_option( 'woo_ship_options' );
			?>
			<div id="sdetails">
				<ul class="totals">
					<li>
						<label><?php _e('Tracking Number:', 'aramex'); ?></label>
						<br />
						<input type="text" id="_order_trackno" name="_order_trackno" placeholder="Enter Tracking No" value="<?php if (isset($data['_order_trackno'][0])) echo $data['_order_trackno'][0]; ?>" class="first" />
					</li>		
					<li>
						<label><?php _e('Shipping Company:', 'aramex'); ?></label><br />
						<select id="_order_trackurl" name="_order_trackurl" onselect="javascript:toggle();" onclick="javascript:toggle();" >
							<option value="NOTRACK" <?php if ( isset($data['_order_trackurl'][0]) && $data['_order_trackurl'][0] == 'NOTRACK') {
								echo 'selected="selected"';
							} ?>><?php _e('No Tracking', 'aramex'); ?></option>
							<?php shipping_details_options( $data, $options, '' ); ?>
						</select>
					</li>
					<li id="shownzcourierinfo" style="display: none">
					<h4><?php _e('Enter the Tracking Number as', 'aramex'); ?> <b style="color:red;">LH-14148561</b>.</h4>
					<img src="<?php echo SDURL.'/img/lab1.jpg'; ?>"/>
					</li>
					<li id="showpostnllinfo" style="display: none">
					<h4><?php _e('Enter the Tracking Number as', 'aramex'); ?> <br><b style="color:red;"><?php _e('TrackingNo-PostalCode', 'aramex'); ?></b>.</h4>
					</li>
					<li id="showapcovernight" style="display: none">
					<h4><?php _e('Enter the Tracking Number as', 'aramex'); ?> <br><b style="color:red;"><?php _e('PostalCode-TrackingNo', 'aramex'); ?></b>.</h4>
					</li>
				</ul>
			</div>
			<div class="clear"></div>
			<?php 
		}
	
		function shipping_details_options($data, $options, $part){ 
			if ($part == '0' || $part == '' ) {
				$part = '';
			}
			$shipping_companies = get_shipping_list();
			foreach( $shipping_companies as $k => $v ){
				if (isset($options[$k]) == '1') {
					echo '<option value="'.$k.'" ';
					if (isset($data['_order_trackurl'.$part][0]) && $data['_order_trackurl'.$part][0] == $k) {
						echo 'selected="selected"';
					}
					echo '>'.$v.'</option>'; 
				}
			}
		}
		
		function track_page_shipping_details( $order ){	
			$order_meta = get_post_custom( $order->id );
			for ($i=0; $i<=4; $i++)
		  	{
				if($i == 0){
					if(isset($order_meta['_order_trackno']) && isset($order_meta['_order_trackurl'])){
			  			shipping_details( $order_meta['_order_trackno'], $order_meta['_order_trackurl'] , $order);
					}
				}else{
					if(isset($order_meta['_order_trackno'.$i]) && isset($order_meta['_order_trackurl'.$i])){
			  			shipping_details($order_meta['_order_trackno'.$i] , $order_meta['_order_trackurl'.$i] , $order);
					}					
				}
		  	}
		}
		
		function shipping_details($trackno , $trackurl , $order){
			$options = get_option( 'woo_ship_options' );
			$shipping_companies = get_shipping_list();
			if ($trackurl[0] == 'ARAMEX'){
				$urltrack = 'http://www.aramex.com/track_results_multiple.aspx?ShipmentNumber='.$trackno[0];
			}
			if ($trackno[0] != null && $trackurl[0] != null && $trackurl[0] != 'NOTRACK' ) { ?>
				<h3><?php _e('Your Order has been shipped via', 'aramex'); ?> <?php echo $shipping_companies[$trackurl[0]]; ?>.</h3>
				<?php if ($trackurl[0] == 'POSTNLL'){?>
				<STRONG><?php _e('Tracking #', 'aramex'); ?> </STRONG><?php echo $track[0]; ?><br/>
				<STRONG><?php _e('Postal Code' , 'aramex');?> </STRONG><?php echo $track[1]; ?>
				<?php } else if ($trackurl[0] == 'APCOVERNIGHT'){?>
				<STRONG><?php _e('Consignment #', 'aramex'); ?> </STRONG><?php echo $track[1]; ?><br/>
				<STRONG><?php _e('Postal Code' , 'aramex');?> </STRONG><?php echo $track[0]; ?>
				<?php } else { ?>
				<STRONG><?php _e('Tracking #', 'aramex'); ?></STRONG><?php echo $trackno[0]; ?>
				<?php } ?>
				<br/>
				<?php 
				$ch = __('CLICK HERE', 'aramex');
				$ch2 = __('to track your shipment.', 'aramex');
				
				if($form == 'yes'){ 
					echo $urltrack;
					?>
					<a href="#" onclick="document.forms['<?php echo $trackurl[0]; ?>'].submit();"><STRONG><?php echo $ch; ?></STRONG></a> <?php echo $ch2; ?>
				<?php }else{ ?>
				<a href="<?php echo $urltrack; ?>" target="_blank" ><STRONG><?php echo $ch; ?></STRONG></a> <?php echo $ch2; ?>
				<?php } ?>
				<br/><br/>	
			<?php } 
		}
		
		function admin_shipping_details($trackno , $trackurl , $order){
			$options = get_option( 'woo_ship_options' );
			$shipping_companies = get_shipping_list();
			if ($trackurl[0] == 'ARAMEX'){
				$urltrack = 'http://www.aramex.com/track_results_multiple.aspx?ShipmentNumber='.$trackno[0];
			}
			if ($trackno[0] != null && $trackurl[0] != null && $trackurl[0] != 'NOTRACK' ) { ?>
				<STRONG><?php echo $shipping_companies[$trackurl[0]]; ?></STRONG><br/>
				<?php if ($trackurl[0] == 'POSTNLL'){?>
				<STRONG><?php _e('Tracking #', 'aramex'); ?> </STRONG><?php echo $track[0]; ?><br/>
				<STRONG><?php _e('Postal Code' , 'aramex');?> </STRONG><?php echo $track[1]; ?>
				<?php } else if ($trackurl[0] == 'APCOVERNIGHT'){?>
				<STRONG><?php _e('Consignment #', 'aramex'); ?> </STRONG><?php echo $track[1]; ?><br/>
				<STRONG><?php _e('Postal Code' , 'aramex');?> </STRONG><?php echo $track[0]; ?>
				<?php } else { ?>
				<STRONG><?php _e('Tracking #', 'aramex'); ?></STRONG><?php echo $trackno[0]; ?>
				<?php } ?>
				<br/><br/>
			<?php } 
		}
		
		function email_shipping_details( $order ) { 
			$order_meta = get_post_custom( $order->id );
			for ($i=0; $i<=4; $i++)
		  	{
				if($i == 0){
					if(isset($order_meta['_order_trackno']) && isset($order_meta['_order_trackurl'])){
			  			shipping_details($order_meta['_order_trackno'] , $order_meta['_order_trackurl'] , $order);
					}
				}else{
					if(isset($order_meta['_order_trackno'.$i]) && isset($order_meta['_order_trackurl'.$i])){
			  			shipping_details($order_meta['_order_trackno'.$i] , $order_meta['_order_trackurl'.$i] , $order);
					}					
				}
		  	}
		}	
		
		function get_shipping_list(){
			return $shipping_companies = Array('ARAMEX' => __('Aramex', 'aramex'));
		}
	
} // is active woocommerce 
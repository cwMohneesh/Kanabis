<?php
/**
 * Plugin Name: Shipping Details for WooCommerce
 * Plugin URI: http://www.patsatech.com/shop/shipping-details-plugin-for-woocommerce
 * Description: WooCommerce Plugin for Displaying Shipping Tracking Number.
 * Version: 1.7.0
 * Author: PatSaTECH
 * Author URI: http://www.patsatech.com
 * Requires at least: 3.5
 * Tested up to: 3.7.1
 *
 * Text Domain: wshipinfo-patsatech
 * Domain Path: /lang/
 * 
 * @package Shipping Details for WooCommerce
 * @author PatSaTECH
 */

define('SDURL', WP_PLUGIN_URL."/".dirname( plugin_basename( __FILE__ ) ) );  

if ( ! class_exists( 'wooshippinginfo' ) ) {
			
	load_plugin_textdomain('wshipinfo-patsatech', false, dirname( plugin_basename( __FILE__ ) ) . '/lang');
		
	class wooshippinginfo {
		
		function __construct() {
	
			add_action( 'add_meta_boxes', array( &$this, 'woocommerce_metaboxes' ) );
		
			add_action( 'woocommerce_order_items_table', array( &$this, 'track_page_shipping_details' ) );
						
			add_action( 'woocommerce_process_shop_order_meta', array( &$this, 'woocommerce_process_shop_ordermeta' ), 5, 2 );
			
			add_action( 'woocommerce_email_before_order_table', array( &$this, 'email_shipping_details' ) );
	
			add_action( 'admin_menu', array( &$this, 'ship_select_menu'));
			
			add_action( 'admin_init', array( &$this, 'ship_register_settings'));
			
			add_action( 'manage_edit-shop_order_columns', array( &$this, 'add_shipping_column'));
			
			add_action( 'manage_shop_order_posts_custom_column', array( &$this, 'add_shipping_column_details'));
		
		}
		
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
					  			$this->admin_shipping_details( $order_meta['_order_trackno'], $order_meta['_order_trackurl'] , $order);
							}
						}else{
							if(isset($order_meta['_order_trackno'.$i]) && isset($order_meta['_order_trackurl'.$i])){
					  			$this->admin_shipping_details($order_meta['_order_trackno'.$i] , $order_meta['_order_trackurl'.$i] , $order);
							}					
						}
				  	}
				break;
			}
			
		}
		
		function add_shipping_column($columns){ 
		
			$columns["tracking_number"] 	= __('Tracking Number', 'wshipinfo-patsatech');
			
			return $columns;
			
		}
		
		function shipping_details_options($data, $options, $part){ 
		 
			if ($part == '0' || $part == '' ) {
				$part = '';
			}
			
			$shipping_companies = $this->get_shipping_list();
			
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
		
		function woocommerce_order_shippingdetails($post) {
	
			$data = get_post_custom( $post->ID );
			$options = get_option( 'woo_ship_options' );
			$style1 = 'style="display: none"';
			$btn1 = '';
			$style2 = 'style="display: none"';
			$btn2 = '';
			$style3 = 'style="display: none"';
			$btn3 = '';
			$style4 = 'style="display: none"';
			$btn4 = '';
			
			if( isset( $data['_order_trackno1'][0]) && $data['_order_trackno1'][0] != '' ){
				$style1 = '';
				$btn1 = 'style="display: none"';
			}
			if( isset( $data['_order_trackno2'][0]) && $data['_order_trackno2'][0] != '' ){
				$style2 = '';
				$btn2 = 'style="display: none"';
			}
			if( isset( $data['_order_trackno3'][0]) && $data['_order_trackno3'][0] != '' ){
				$style3 = '';
				$btn3 = 'style="display: none"';
			}
			if( isset( $data['_order_trackno4'][0]) && $data['_order_trackno4'][0] != '' ){
				$style4 = '';
				$btn4 = 'style="display: none"';
			}
			
			?>
			<div id="sdetails">
				<ul class="totals">
					<li>
						<label><?php _e('Tracking Number:', 'wshipinfo-patsatech'); ?></label>
						<br />
						<input type="text" id="_order_trackno" name="_order_trackno" placeholder="Enter Tracking No" value="<?php if (isset($data['_order_trackno'][0])) echo $data['_order_trackno'][0]; ?>" class="first" />
					</li>		
					<li>
						<label><?php _e('Shipping Company:', 'wshipinfo-patsatech'); ?></label><br />
						<select id="_order_trackurl" name="_order_trackurl" onselect="javascript:toggle();" onclick="javascript:toggle();" >
							<option value="NOTRACK" <?php if ( isset($data['_order_trackurl'][0]) && $data['_order_trackurl'][0] == 'NOTRACK') {
								echo 'selected="selected"';
							} ?>><?php _e('No Tracking', 'wshipinfo-patsatech'); ?></option>
							<?php $this->shipping_details_options( $data, $options, '' ); ?>
						</select>
					</li>
					<li id="shownzcourierinfo" style="display: none">
					<h4><?php _e('Enter the Tracking Number as', 'wshipinfo-patsatech'); ?> <b style="color:red;">LH-14148561</b>.</h4>
					<img src="<?php echo SDURL.'/img/lab1.jpg'; ?>"/>
					</li>
					<li id="showpostnllinfo" style="display: none">
					<h4><?php _e('Enter the Tracking Number as', 'wshipinfo-patsatech'); ?> <br><b style="color:red;"><?php _e('TrackingNo-PostalCode', 'wshipinfo-patsatech'); ?></b>.</h4>
					</li>
					<li id="showapcovernight" style="display: none">
					<h4><?php _e('Enter the Tracking Number as', 'wshipinfo-patsatech'); ?> <br><b style="color:red;"><?php _e('PostalCode-TrackingNo', 'wshipinfo-patsatech'); ?></b>.</h4>
					</li>
	
				</ul>
				<input type="button" class="button button-primary" name="save" value="Add Second" id="add1" <?php echo $btn1; ?> onclick="javascript:sdetails1display();" />
			</div>
			<div id="sdetails1" <?php echo $style1; ?>>
				<ul class="totals">
					<li>
						<label><?php _e('Tracking Number 2:', 'wshipinfo-patsatech'); ?></label>
						<br />
						<input type="text" id="_order_trackno1" name="_order_trackno1" placeholder="Enter Tracking No" value="<?php if (isset($data['_order_trackno1'][0])) echo $data['_order_trackno1'][0]; ?>" class="first" />
					</li>		
					<li>
						<label><?php _e('Shipping Company 2:', 'wshipinfo-patsatech'); ?></label><br />
						<select id="_order_trackurl1" name="_order_trackurl1" onclick="javascript:toggle1();"  onselect="javascript:toggle1();" >
							<option value="NOTRACK" <?php if ( isset($data['_order_trackurl1'][0]) && $data['_order_trackurl1'][0] == 'NOTRACK') {
								echo 'selected="selected"';
							} ?>><?php _e('No Tracking', 'wshipinfo-patsatech'); ?></option>
							<?php $this->shipping_details_options( $data, $options, '1' ); ?>
						</select>
					</li>
					<li id="shownzcourierinfo1" style="display: none">
					<h4><?php _e('Enter the Tracking Number as', 'wshipinfo-patsatech'); ?> <b style="color:red;">LH-14148561</b>.</h4>
					<img src="<?php echo SDURL.'/img/lab1.jpg'; ?>"/>
					</li>
					<li id="showpostnllinfo1" style="display: none">
					<h4><?php _e('Enter the Tracking Number as', 'wshipinfo-patsatech'); ?> <br><b style="color:red;"><?php _e('TrackingNo-PostalCode', 'wshipinfo-patsatech'); ?></b>.</h4>
					</li>
					<li id="showapcovernight1" style="display: none">
					<h4><?php _e('Enter the Tracking Number as', 'wshipinfo-patsatech'); ?> <br><b style="color:red;"><?php _e('PostalCode-TrackingNo', 'wshipinfo-patsatech'); ?></b>.</h4>
					</li>
					
				</ul>
				<input type="button" class="button button-primary" name="save" value="Add Third" id="add2" <?php echo $btn2; ?> onclick="javascript:sdetails2display();" />
				<input type="button" class="button button-primary" name="save" value="Remove"  id="remove1" <?php echo $btn1; ?> onclick="javascript:sdetails1remove();" />
			</div>
			<div id="sdetails2" <?php echo $style2; ?>>
				<ul class="totals">
					<li>
						<label><?php _e('Tracking Number 3:', 'wshipinfo-patsatech'); ?></label>
						<br />
						<input type="text" id="_order_trackno2" name="_order_trackno2" placeholder="Enter Tracking No" value="<?php if (isset($data['_order_trackno2'][0])) echo $data['_order_trackno2'][0]; ?>" class="first" />
					</li>		
					<li>
						<label><?php _e('Shipping Company 3:', 'wshipinfo-patsatech'); ?></label><br />
						<select id="_order_trackurl2" name="_order_trackurl2" onclick="javascript:toggle2();"  onselect="javascript:toggle2();" >
							<option value="NOTRACK" <?php if ( isset($data['_order_trackurl2'][0]) && $data['_order_trackurl2'][0] == 'NOTRACK') {
								echo 'selected="selected"';
							} ?>><?php _e('No Tracking', 'wshipinfo-patsatech'); ?></option>
							<?php $this->shipping_details_options( $data, $options, '2' ); ?>
						</select>
					</li>
					<li id="shownzcourierinfo2" style="display: none">
					<h4><?php _e('Enter the Tracking Number as', 'wshipinfo-patsatech'); ?> <b style="color:red;">LH-14148561</b>.</h4>
					<img src="<?php echo SDURL.'/img/lab1.jpg'; ?>"/>
					</li>
					<li id="showpostnllinfo2" style="display: none">
					<h4><?php _e('Enter the Tracking Number as', 'wshipinfo-patsatech'); ?> <br><b style="color:red;"><?php _e('TrackingNo-PostalCode', 'wshipinfo-patsatech'); ?></b>.</h4>
					</li>
					<li id="showapcovernight2" style="display: none">
					<h4><?php _e('Enter the Tracking Number as', 'wshipinfo-patsatech'); ?> <br><b style="color:red;"><?php _e('PostalCode-TrackingNo', 'wshipinfo-patsatech'); ?></b>.</h4>
					</li>
	
				</ul>
				<input type="button" class="button button-primary" name="save" id="add3" value="Add Fourth" <?php echo $btn3; ?> onclick="javascript:sdetails3display();" />
				<input type="button" class="button button-primary" name="save" value="Remove"  id="remove2" <?php echo $btn2; ?> onclick="javascript:sdetails2remove();" />
			</div>
			<div id="sdetails3" <?php echo $style3; ?>>
				<ul class="totals">
					<li>
						<label><?php _e('Tracking Number 4:', 'wshipinfo-patsatech'); ?></label>
						<br />
						<input type="text" id="_order_trackno3" name="_order_trackno3" placeholder="Enter Tracking No" value="<?php if (isset($data['_order_trackno3'][0])) echo $data['_order_trackno3'][0]; ?>" class="first" />
					</li>		
					<li>
						<label><?php _e('Shipping Company 4:', 'wshipinfo-patsatech'); ?></label><br />
						<select id="_order_trackurl3" name="_order_trackurl3" onclick="javascript:toggle3();"  onselect="javascript:toggle3();" >
							<option value="NOTRACK" <?php if ( isset($data['_order_trackurl3'][0]) && $data['_order_trackurl3'][0] == 'NOTRACK') {
								echo 'selected="selected"';
							} ?>><?php _e('No Tracking', 'wshipinfo-patsatech'); ?></option>
							<?php $this->shipping_details_options( $data, $options, '3' ); ?>
						</select>
					</li>
					<li id="shownzcourierinfo3" style="display: none">
					<h4><?php _e('Enter the Tracking Number as', 'wshipinfo-patsatech'); ?> <b style="color:red;">LH-14148561</b>.</h4>
					<img src="<?php echo SDURL.'/img/lab1.jpg'; ?>"/>
					</li>
					<li id="showpostnllinfo3" style="display: none">
					<h4><?php _e('Enter the Tracking Number as', 'wshipinfo-patsatech'); ?> <br><b style="color:red;"><?php _e('TrackingNo-PostalCode', 'wshipinfo-patsatech'); ?></b>.</h4>
					</li>
					<li id="showapcovernight3" style="display: none">
					<h4><?php _e('Enter the Tracking Number as', 'wshipinfo-patsatech'); ?> <br><b style="color:red;"><?php _e('PostalCode-TrackingNo', 'wshipinfo-patsatech'); ?></b>.</h4>
					</li>
	
				</ul>
				<input type="button" class="button button-primary" name="save" value="Add Fifth" id="add4" <?php echo $btn4; ?> onclick="javascript:sdetails4display();" />
				<input type="button" class="button button-primary" name="save" value="Remove"  id="remove3" <?php echo $btn3; ?> onclick="javascript:sdetails3remove();" />
			</div>
			<div id="sdetails4" <?php echo $style4; ?>>
				<ul class="totals">
					<li>
						<label><?php _e('Tracking Number 5:', 'wshipinfo-patsatech'); ?></label>
						<br />
						<input type="text" id="_order_trackno4" name="_order_trackno4" placeholder="Enter Tracking No" value="<?php if (isset($data['_order_trackno4'][0])) echo $data['_order_trackno4'][0]; ?>" class="first" />
					</li>		
					<li>
						<label><?php _e('Shipping Company 5:', 'wshipinfo-patsatech'); ?></label><br />
						<select id="_order_trackurl4" name="_order_trackurl4" onclick="javascript:toggle4();"  onselect="javascript:toggle4();" >
							<option value="NOTRACK" <?php if ( isset($data['_order_trackurl4'][0]) && $data['_order_trackurl4'][0] == 'NOTRACK') {
								echo 'selected="selected"';
							} ?>><?php _e('No Tracking', 'wshipinfo-patsatech'); ?></option>
							<?php $this->shipping_details_options( $data, $options, '4' ); ?>
						</select>
					</li>
					<li id="shownzcourierinfo4" style="display: none">
					<h4><?php _e('Enter the Tracking Number as', 'wshipinfo-patsatech'); ?> <b style="color:red;">LH-14148561</b>.</h4>
					<img src="<?php echo SDURL.'/img/lab1.jpg'; ?>"/>
					</li>
					<li id="showpostnllinfo4" style="display: none">
					<h4><?php _e('Enter the Tracking Number as', 'wshipinfo-patsatech'); ?> <br><b style="color:red;"><?php _e('TrackingNo-PostalCode', 'wshipinfo-patsatech'); ?></b>.</h4>
					</li>
					<li id="showapcovernight4" style="display: none">
					<h4><?php _e('Enter the Tracking Number as', 'wshipinfo-patsatech'); ?> <br><b style="color:red;"><?php _e('PostalCode-TrackingNo', 'wshipinfo-patsatech'); ?></b>.</h4>
					</li>
	
				</ul>
				<input type="button" class="button button-primary" name="save" value="Remove" id="remove4" <?php echo $btn4; ?> onclick="javascript:sdetails4remove();" />
			</div>
			<div class="clear"></div>
			<?php 

		}			
	
		function woocommerce_process_shop_ordermeta( $post_id, $post ) {
	
			global $wpdb, $woocommerce;
			
			$woocommerce_errors = array();
				
			add_post_meta( $post_id, '_order_key', uniqid('order_') );
		
			update_post_meta( $post_id, '_order_trackno', stripslashes( $_POST['_order_trackno'] ));
		
			update_post_meta( $post_id, '_order_trackurl', stripslashes( $_POST['_order_trackurl'] ));
		
			update_post_meta( $post_id, '_order_trackno1', stripslashes( $_POST['_order_trackno1'] ));
		
			update_post_meta( $post_id, '_order_trackurl1', stripslashes( $_POST['_order_trackurl1'] ));
		
			update_post_meta( $post_id, '_order_trackno2', stripslashes( $_POST['_order_trackno2'] ));
		
			update_post_meta( $post_id, '_order_trackurl2', stripslashes( $_POST['_order_trackurl2'] ));
		
			update_post_meta( $post_id, '_order_trackno3', stripslashes( $_POST['_order_trackno3'] ));
		
			update_post_meta( $post_id, '_order_trackurl3', stripslashes( $_POST['_order_trackurl3'] ));
		
			update_post_meta( $post_id, '_order_trackno4', stripslashes( $_POST['_order_trackno4'] ));
		
			update_post_meta( $post_id, '_order_trackurl4', stripslashes( $_POST['_order_trackurl4'] ));
		}
	
		function woocommerce_metaboxes() {

			add_meta_box( 'woocommerce-order-ship', __('Shipping Details', 'wshipinfo-patsatech'), array( &$this, 'woocommerce_order_shippingdetails' ), 'shop_order', 'side', 'high');

		}
	
		function ship_register_settings(){
			register_setting('woo_ship_group','woo_ship_options');
			wp_enqueue_script('shippingdetails-js', SDURL.'/js/shippingdetails.js', array('jquery')); 
		}
			
		function ship_select_menu(){
			
			if (!function_exists('current_user_can') || !current_user_can('manage_options') )
			return;
				
			if ( function_exists( 'add_options_page' ) )
			{
				add_options_page(
					__('Shipping Details Settings', 'wshipinfo-patsatech'),
					__('Shipping Details', 'wshipinfo-patsatech'),
					'manage_options',
					'woo_ship_buttons',
					array( &$this, 'admin_options' ) );
			}
		}
			
			
		public function admin_options() {
			$options = get_option( 'woo_ship_options' );
			ob_start();
		   	?>
			<div class="wrap">
				<?php screen_icon("options-general"); ?>
				<h2>Shipping Details Settings</h2>
				<br>
				<h3><b>Select Shipping Company that you will be using to ship the Products.</b></h3>
				<form action="options.php" method="post"  style="padding-left:20px">
				<?php settings_fields('woo_ship_group'); 
					if( isset($options['CANPAR'] )){ ?>	
					<br>
					<b>Canpar Shipper Code : </b>
					<input type="text" name="woo_ship_options[CANPARSCODE]" id="CANPARSCODE" value="<?php if(isset($options['CANPARSCODE'])) echo $options['CANPARSCODE']; ?>" />
					<br>
					<br>
					<?php } ?> 
					<table cellpadding="10px">
					<?php
							
						$shipping_companies = $this->get_shipping_list();
													
						$i = 0;
						foreach( $shipping_companies as $k => $v ){
							
							if($i%5==0){
								echo '<tr>';
							}
								
							$checked = '';
								
							if(1 == isset($options[$k])){
								$checked = "checked='checked'";
							}
										
							echo "<td><td class='forminp'>
									<input type='checkbox' name='woo_ship_options[$k]' id='$k' value='1' $checked />
								</td>
						        <td scope='row'><label for='$k' >$v</label></td>
								</td>";
									
							$i++;
							if($i%5==0){
								echo '</tr>';
							}
						}
						if($i%5!=0){
							echo '</tr>';
						}
							
					?>
					</table>
					<p class="submit">
						<input name="Submit" type="submit" class="button-primary" value="<?php esc_attr_e('Save Changes', 'wshipinfo-patsatech'); ?>" />
					</p>
				</form>
			</div>
			<?php
			echo ob_get_clean();
		}	
	
		function track_page_shipping_details( $order ){
					
			$order_meta = get_post_custom( $order->id );
						
			for ($i=0; $i<=4; $i++)
		  	{
				if($i == 0){
					if(isset($order_meta['_order_trackno']) && isset($order_meta['_order_trackurl'])){
			  			$this->shipping_details( $order_meta['_order_trackno'], $order_meta['_order_trackurl'] , $order);
					}
				}else{
					if(isset($order_meta['_order_trackno'.$i]) && isset($order_meta['_order_trackurl'.$i])){
			  			$this->shipping_details($order_meta['_order_trackno'.$i] , $order_meta['_order_trackurl'.$i] , $order);
					}					
				}
		  	}
		}
		
		
		function email_shipping_details( $order ) {
					
			$order_meta = get_post_custom( $order->id );
						
			for ($i=0; $i<=4; $i++)
		  	{
				if($i == 0){
					if(isset($order_meta['_order_trackno']) && isset($order_meta['_order_trackurl'])){
			  			$this->shipping_details($order_meta['_order_trackno'] , $order_meta['_order_trackurl'] , $order);
					}
				}else{
					if(isset($order_meta['_order_trackno'.$i]) && isset($order_meta['_order_trackurl'.$i])){
			  			$this->shipping_details($order_meta['_order_trackno'.$i] , $order_meta['_order_trackurl'.$i] , $order);
					}					
				}
		  	}
		}
		
		function shipping_details($trackno , $trackurl , $order){
			
			$options = get_option( 'woo_ship_options' );
			
			$shipping_companies = $this->get_shipping_list();
			
			include 'includes/url_list.php';
			
			if ($trackno[0] != null && $trackurl[0] != null && $trackurl[0] != 'NOTRACK' ) { ?>
				<h3><?php _e('Your Order has been shipped via', 'wshipinfo-patsatech'); ?> <?php echo $shipping_companies[$trackurl[0]]; ?>.</h3>
				<?php if ($trackurl[0] == 'POSTNLL'){?>
				<STRONG><?php _e('Tracking #', 'wshipinfo-patsatech'); ?> </STRONG><?php echo $track[0]; ?><br/>
				<STRONG><?php _e('Postal Code' , 'wshipinfo-patsatech');?> </STRONG><?php echo $track[1]; ?>
				<?php } else if ($trackurl[0] == 'APCOVERNIGHT'){?>
				<STRONG><?php _e('Consignment #', 'wshipinfo-patsatech'); ?> </STRONG><?php echo $track[1]; ?><br/>
				<STRONG><?php _e('Postal Code' , 'wshipinfo-patsatech');?> </STRONG><?php echo $track[0]; ?>
				<?php } else { ?>
				<STRONG><?php _e('Tracking #', 'wshipinfo-patsatech'); ?></STRONG><?php echo $trackno[0]; ?>
				<?php } ?>
				<br/>
				<?php 
				$ch = __('CLICK HERE', 'wshipinfo-patsatech');
				$ch2 = __('to track your shipment.', 'wshipinfo-patsatech');
				
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
			
			$shipping_companies = $this->get_shipping_list();
			
			include 'includes/url_list.php';
			
			if ($trackno[0] != null && $trackurl[0] != null && $trackurl[0] != 'NOTRACK' ) { ?>
				<STRONG><?php echo $shipping_companies[$trackurl[0]]; ?></STRONG><br/>
				<?php if ($trackurl[0] == 'POSTNLL'){?>
				<STRONG><?php _e('Tracking #', 'wshipinfo-patsatech'); ?> </STRONG><?php echo $track[0]; ?><br/>
				<STRONG><?php _e('Postal Code' , 'wshipinfo-patsatech');?> </STRONG><?php echo $track[1]; ?>
				<?php } else if ($trackurl[0] == 'APCOVERNIGHT'){?>
				<STRONG><?php _e('Consignment #', 'wshipinfo-patsatech'); ?> </STRONG><?php echo $track[1]; ?><br/>
				<STRONG><?php _e('Postal Code' , 'wshipinfo-patsatech');?> </STRONG><?php echo $track[0]; ?>
				<?php } else { ?>
				<STRONG><?php _e('Tracking #', 'wshipinfo-patsatech'); ?></STRONG><?php echo $trackno[0]; ?>
				<?php } ?>
				<br/><br/>
			<?php } 
			
		}
		
		function get_shipping_list(){
		
			include 'includes/courier_list.php';
			
			ksort($shipping_companies);
			
			return $shipping_companies;
			
		}

	}
}
$GLOBALS['wooshippinginfo'] = new wooshippinginfo();
?>
<?php //include('img/social.png'); ?>
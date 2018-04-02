<?php
/**
 * Checkout Form Multistep
 *
 * Your Inspiration Themes
 * 
 * @package WordPress
 * @subpackage Your Inspiration Themes
 * @author Your Inspiration Themes Team <info@yithemes.com>
 *
 * This source file is subject to the GNU GENERAL PUBLIC LICENSE (GPL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-3.0.txt
 */

global $woocommerce; $woocommerce_checkout = $woocommerce->checkout();

wp_enqueue_script('yit_checkout', YIT_CORE_ASSETS_URL . '/js/yit/jquery.yit_checkout.js');

wc_print_notices(); ?>

<?php remove_action( 'woocommerce_before_checkout_form', 'woocommerce_checkout_login_form', 10 ); ?>
<?php do_action('woocommerce_before_checkout_form', $checkout);

// If checkout registration is disabled and not logged in, the user cannot checkout
if ( ! $checkout->enable_signup && ! $checkout->enable_guest_checkout && ! is_user_logged_in() ) {
    echo apply_filters( 'woocommerce_checkout_must_be_logged_in_message', __( 'You must be logged in to checkout.', 'woocommerce' ) );
    return;
}

// filter hook for include new pages inside the payment method
$get_checkout_url = apply_filters( 'woocommerce_get_checkout_url', WC()->cart->get_checkout_url() ); 
$data_step_1 = ( !is_user_logged_in() && get_option('woocommerce_enable_signup_and_login_from_checkout')=="yes" ) ? 'data-step="1"': '';  ?>

<div id="checkout_multistep">

<div id="multistep_resume">
	<div><span class="checkout_progress"><img src="<?php echo get_stylesheet_directory_uri() ?>/woocommerce/images/multistep_cart.png" alt="<?php _e('Checkout Progress', 'yit') ?>" /> <?php _e('Checkout Progress', 'yit') ?></span></div>
	<div><a href="#" <?php echo $data_step_1 ?> class="<?php if(!is_user_logged_in()): ?>current <?php else: ?>user_logged_in <?php endif ?>multistep_section"><?php _e('Login', 'yit') ?></a></div>
	<div><a href="#" data-step="2" class="<?php if(is_user_logged_in()): ?>current <?php endif ?>multistep_section"><?php _e('Billing Address', 'yit') ?></a></div>
	<?php if ( ( $woocommerce->cart->needs_shipping() || get_option('woocommerce_require_shipping_address') == 'yes' ) ) : ?>
	<div><a href="#" data-step="3" class="multistep_section"><?php _e('Shipping Address', 'yit') ?></a></div>
	<?php endif; ?>
	<div><a href="#" data-step="4" class="multistep_section"><?php _e('Payment Method', 'yit') ?></a></div>
	<div><a href="#" data-step="5" class="multistep_section"><?php _e('Order Review', 'yit') ?></a></div>
</div>

<div class="clear"></div>

<div class="row">
	<div id="multistep_progress" class="span<?php echo yit_get_sidebar_layout() == 'sidebar-no' ? 12 : 9 ?> progress progress-striped">
		<div class="bar" style="width: 0%;"></div>
	</div>
</div>

<div id="multistep_steps" class="row">

	<!-- step 1 -->
	<?php if (!is_user_logged_in() && get_option('woocommerce_enable_signup_and_login_from_checkout')=="yes") : ?>
	<div class="current multistep_step span<?php echo yit_get_sidebar_layout() == 'sidebar-no' ? 12 : 9 ?> user_not_logged_in" id="multistep_step1" data-step="1">
    	<div class="box_style">
        
            <div class="row">
                <?php wc_get_template('checkout/form-login.php', array('woocommerce'=>$woocommerce)) ?>
                
                <?php if( get_option('woocommerce_enable_guest_checkout')=="yes" ): ?>
                <input type="submit" class="button next" name="next[]" value="<?php _e('Checkout as Guest', 'yit'); ?>" data-next="2" />				
                <?php endif ?>
            </div>
            
            <div class="clear"></div>
    	</div>
	</div>
  <?php elseif( !is_user_logged_in() ): ?>
        <div class="multistep_step user_not_logged_in guest_checkout" id="multistep_step1" data-step="1"></div>
	<?php else: ?>
	<div class="multistep_step user_logged_in" id="multistep_step1" data-step="1"></div>
	<?php endif ?>


	<form name="checkout" method="post" class="checkout" action="<?php echo esc_url( $get_checkout_url ); ?>">

	<?php do_action( 'woocommerce_checkout_before_customer_details'); ?>

	<!-- step 2 -->
	<div class="<?php if (is_user_logged_in() || ( !is_user_logged_in() && get_option('woocommerce_enable_signup_and_login_from_checkout')!="yes") ): ?>current <?php endif ?>multistep_step span<?php echo yit_get_sidebar_layout() == 'sidebar-no' ? 12 : 9 ?>" id="multistep_step2" data-step="2">
    	<div class="box_style">
		
			<?php do_action('woocommerce_checkout_billing'); ?>
    
            <?php if ( !is_user_logged_in() && get_option('woocommerce_enable_signup_and_login_from_checkout')=="yes" ): ?>
            <input type="submit" class="button prev" name="login" value="<?php _e('&larr; Login', 'yit'); ?>" data-next="1" />
            <?php endif ?>
            
            <input type="submit" class="button next" name="login" value="<?php _e('Payment Method &rarr;', 'yit'); ?>" data-next="4" data-alternativelabel="<?php _e('Shipping Method &rarr;', 'yit'); ?>" />
		
        	<div class="clear"></div>
        </div>
	</div>


	<!-- step 3 -->
	<div class="multistep_step span<?php echo yit_get_sidebar_layout() == 'sidebar-no' ? 12 : 9 ?>" id="multistep_step3" data-step="3">
    	<div class="box_style">

			<?php do_action('woocommerce_checkout_shipping'); ?>

			<input type="submit" class="button prev" name="login" value="<?php _e('&larr; Billing Address', 'yit'); ?>" data-next="2" />
			<input type="submit" class="button next" name="login" value="<?php _e('Payment Method &rarr;', 'yit'); ?>" data-next="4" />

        	<div class="clear"></div>
        </div>
	</div>

	<?php do_action( 'woocommerce_checkout_after_customer_details'); ?>

	<!-- step 4 -->
	<div class="multistep_step span<?php echo yit_get_sidebar_layout() == 'sidebar-no' ? 12 : 9 ?>" id="multistep_step4" data-step="4">
    	<div class="box_style">
        
			<?php wc_get_template('checkout/form-payment.php', array('woocommerce' => $woocommerce)); ?>
    
            <input type="submit" class="button prev" name="login" value="<?php _e('&larr; Billing Address', 'yit'); ?>" data-next="2" data-alternativelabel="<?php _e('&larr; Shipping Method', 'yit'); ?>" />
            <input type="submit" class="button next" name="login" value="<?php _e('Order Review &rarr;', 'yit'); ?>" data-next="5" />
			
            <div class="clear"></div>
        </div>
    </div>

	<!-- step 5 -->
	<div class="multistep_step span<?php echo yit_get_sidebar_layout() == 'sidebar-no' ? 12 : 9 ?>" id="multistep_step5" data-step="5">
    	<div class="box_style">
        
            <h3 id="order_review_heading"><?php _e('Your order', 'yit'); ?></h3>             
            <?php do_action('woocommerce_checkout_order_review'); ?>
    
            <input type="submit" class="button prev" name="login" value="<?php _e('&larr; Payment Method', 'yit'); ?>" data-next="4" />
            
            <div class="clear"></div>
    	</div>
	</div>

</form>
</div>


</div>

<?php do_action('woocommerce_after_checkout_form', $checkout); ?>


<script type="text/javascript" charset="utf-8">
jQuery(document).ready(function(){
    <?php if ( is_plugin_active('woocommerce-gateway-stripe/gateway-stripe.php') || is_plugin_active('woocommerce-gateway-stripe-connect/gateway-stripe.php') ) : ?>
        jQuery(document).on('click', '#multistep_steps #order_review input#place_order', function() { return stripeFormHandler(); });
    <?php endif; ?>
	jQuery('#checkout_multistep').yit_checkout();
});
</script>
<?php
/**
 * Checkout Form Multistep
 *
 * Your Inspiration Themes
 *
 * @package    WordPress
 * @subpackage Your Inspiration Themes
 * @author     Your Inspiration Themes Team <info@yithemes.com>
 *
 * This source file is subject to the GNU GENERAL PUBLIC LICENSE (GPL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-3.0.txt
 */

global $sitepress;

$is_wpml_active = ( isset( $sitepress ) && ! empty( $sitepress ) );

if ( $is_wpml_active ) {

    $payment_fix    = yit_icl_translate( 'theme', 'yit', 'form-payment-method',  'Payment method' );
    $please_fill    = yit_icl_translate( 'theme', 'yit', 'form-payment-method-fill-details', 'Please fill in your details above to see available payment methods.' );
    $sorry_it_seems = yit_icl_translate( 'theme', 'yit', 'form-payment-method-no-available', 'Sorry, it seems that there are no available payment methods for your state. Please contact us if you require assistance or wish to make alternate arrangements.' );

}
else {

    $payment_fix    = __( 'Payment method', 'yit' );
    $please_fill    = __( 'Please fill in your details above to see available payment methods.', 'yit' );
    $sorry_it_seems = __( 'Sorry, it seems that there are no available payment methods for your state. Please contact us if you require assistance or wish to make alternate arrangements.', 'yit' );
}

?>


<h3><?php echo $payment_fix; ?></h3>
<div class="clear"></div>

<div id="payment">
    <?php if ( $woocommerce->cart->needs_payment() ) : ?>
        <ul class="payment_methods methods">
            <?php
            $available_gateways = $woocommerce->payment_gateways->get_available_payment_gateways();
            if ( ! empty( $available_gateways ) ) {

                // Chosen Method
                if ( isset( $woocommerce->session->chosen_payment_method ) && isset( $available_gateways[$woocommerce->session->chosen_payment_method] ) ) {
                    $available_gateways[$woocommerce->session->chosen_payment_method]->set_current();
                }
                elseif ( isset( $available_gateways[get_option( 'woocommerce_default_gateway' )] ) ) {
                    $available_gateways[get_option( 'woocommerce_default_gateway' )]->set_current();
                }
                else {
                    current( $available_gateways )->set_current();
                }

                foreach ( $available_gateways as $gateway ) {
                    ?>
                    <li>
                        <input type="radio" id="payment_method_<?php echo $gateway->id; ?>" class="input-radio" name="payment_method" value="<?php echo esc_attr( $gateway->id ); ?>" <?php checked( $gateway->chosen, true ); ?> />
                        <label for="payment_method_<?php echo $gateway->id; ?>"><?php echo $gateway->get_title(); ?> <?php echo $gateway->get_icon(); ?></label>
                        <?php
                        if ( $gateway->has_fields() || $gateway->get_description() ) :
                            echo '<div class="payment_box payment_method_' . $gateway->id . '" ' . ( $gateway->chosen ? '' : 'style="display:none;"' ) . '>';
                            $gateway->payment_fields();
                            echo '</div>';
                        endif;
                        ?>
                    </li>
                <?php
                }
            }
            else {

                if ( ! $woocommerce->customer->get_country() ) {
                    echo '<p>' . $please_fill . '</p>';
                }
                else {
                    echo '<p>' . $sorry_it_seems . '</p>';
                }

            }
            ?>
        </ul>
    <?php endif; ?>


    <?php if ( ! yit_get_option( 'shop-checkout-multistep' ) ): ?>
        <?php wc_get_template( 'checkout/form-place-order.php', array( 'woocommerce' => $woocommerce ) ); ?>
    <?php endif ?>

    <div class="clear"></div>

</div>

<?php do_action( 'woocommerce_review_order_after_payment' ); ?>
<?php
/**
 * Cart totals
 *
 * @author        WooThemes
 * @package       WooCommerce/Templates
 * @version       2.1.0
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
} // Exit if accessed directly


global $sitepress;

if ( isset( $sitepress ) && ! empty( $sitepress ) ) {

    $default_lang = $sitepress->get_default_language();
    $lang         = ICL_LANGUAGE_CODE;

    if ( $sitepress->get_default_language() == ICL_LANGUAGE_CODE ) {
        $sitepress->switch_lang( $default_lang, true );
    }
    else {
        $sitepress->switch_lang( $lang, true );
    }

    $cart_tatals_fix = yit_icl_translate( 'theme', 'yit', 'cart-totals', 'Cart Totals' );
    $fix_coupon      = yit_icl_translate( 'theme', 'yit', 'cart-totals-coupon', 'Coupon:' );
    $cart_subtotal   = yit_icl_translate( 'theme', 'yit', 'cart-totals-subtotals', 'Cart Subtotal' );
    $order_total_fix = yit_icl_translate( 'theme', 'yit', 'cart-totals-order', 'Order Total' );

}
else {

    $cart_tatals_fix = __( 'Cart Totals', 'yit' );
    $fix_coupon      = __( 'Coupon:', 'yit' );
    $cart_subtotal   = __( 'Cart Subtotal', 'yit' );
    $order_total_fix = __( 'Order Total', 'yit' );

}

global $woocommerce;

?>
<div class="span6 cart_totals <?php if ( WC()->customer->has_calculated_shipping() ) {
    echo 'calculated_shipping';
} ?>">
    <div class="border-1 border">
        <div class="border-2 border">
            <?php do_action( 'woocommerce_before_cart_totals' ); ?>
            <h2><?php echo $cart_tatals_fix; ?></h2>
            <table align="right" cellspacing="0" cellpadding="0">
                <tbody>

                <tr class="cart-subtotal">
                    <th><strong><?php echo $cart_subtotal; ?></strong></th>
                    <td><strong><?php wc_cart_totals_subtotal_html(); ?></strong></td>
                </tr>

                <?php foreach ( WC()->cart->get_coupons( 'cart' ) as $code => $coupon ) : ?>
                    <tr class="discount coupon-<?php echo esc_attr( $code ); ?>">
                        <th><?php echo $fix_coupon; ?> <?php echo esc_html( $code ); ?></th>
                        <td><?php wc_cart_totals_coupon_html( $coupon ); ?></td>
                    </tr>
                <?php endforeach; ?>

                <?php if ( WC()->cart->needs_shipping() && WC()->cart->show_shipping() ) : ?>

                    <?php do_action( 'woocommerce_cart_totals_before_shipping' ); ?>

                    <?php wc_cart_totals_shipping_html(); ?>

                    <?php do_action( 'woocommerce_cart_totals_after_shipping' ); ?>

                <?php endif; ?>

                <?php foreach ( WC()->cart->get_fees() as $fee ) : ?>
                    <tr class="fee fee-<?php echo $fee->id ?>">
                        <th><?php echo esc_html( $fee->name ); ?></th>
                        <td><?php wc_cart_totals_fee_html( $fee ); ?></td>
                    </tr>
                <?php endforeach; ?>

                <?php if ( WC()->cart->tax_display_cart == 'excl' ) : ?>
                    <?php if ( get_option( 'woocommerce_tax_total_display' ) == 'itemized' ) : ?>
                        <?php foreach ( WC()->cart->get_tax_totals() as $code => $tax ) : ?>
                            <tr class="tax-rate tax-rate-<?php echo sanitize_title( $code ); ?>">
                                <th><?php echo esc_html( $tax->label ); ?></th>
                                <td><?php echo wp_kses_post( $tax->formatted_amount ); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else : ?>
                        <tr class="tax-total">
                            <th><?php echo esc_html( WC()->countries->tax_or_vat() ); ?></th>
                            <td><?php echo wc_price( WC()->cart->get_taxes_total() ); ?></td>
                        </tr>
                    <?php endif; ?>
                <?php endif; ?>

                <?php foreach ( WC()->cart->get_coupons( 'order' ) as $code => $coupon ) : ?>
                    <tr class="order-discount coupon-<?php echo esc_attr( $code ); ?>">
                        <th><?php echo $fix_coupon; ?> <?php echo esc_html( $code ); ?></th>
                        <td><?php wc_cart_totals_coupon_html( $coupon ); ?></td>
                    </tr>
                <?php endforeach; ?>

                <?php do_action( 'woocommerce_cart_totals_before_order_total' ); ?>

                <tr class="total">
                    <th><strong><?php echo $order_total_fix; ?></strong></th>
                    <td><strong><?php  wc_cart_totals_order_total_html() ?></strong></td>
                </tr>

                <?php do_action( 'woocommerce_cart_totals_after_order_total' ); ?>
                </tbody>
            </table>

            <?php if ( $woocommerce->cart->get_cart_tax() ) : ?>
                <p>
                    <small><?php
                        $estimated_text = ( $woocommerce->customer->is_customer_outside_base() && ! $woocommerce->customer->has_calculated_shipping() ) ? sprintf( ' ' . __( ' (taxes estimated for %s)', 'yit' ), $woocommerce->countries->estimated_for_prefix() . __( $woocommerce->countries->countries[$woocommerce->countries->get_base_country()], 'yit' ) ) : '';
                        printf( __( 'Note: Shipping and taxes are estimated%s and will be updated during checkout based on your billing and shipping information.', 'yit' ), $estimated_text );
                        ?>
                    </small>
                </p>
            <?php endif; ?>

            <?php do_action( 'woocommerce_after_cart_totals' ); ?>
            <div class="clear"></div>
        </div>
    </div>
</div>
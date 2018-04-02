<?php
/**
 * Review order form
 *
 * @author        WooThemes
 * @package       WooCommerce/Templates
 * @version       2.1.8
 */
if ( ! defined( 'ABSPATH' ) ) {
    exit;
} // Exit if accessed directly

global $sitepress;

$is_wpml_active = ( isset( $sitepress ) && ! empty( $sitepress ) );

if ( $is_wpml_active ) {

    $default_lang = $sitepress->get_default_language();
    $lang         = ICL_LANGUAGE_CODE;

    if ( $sitepress->get_default_language() == ICL_LANGUAGE_CODE ) {
        $sitepress->switch_lang( $default_lang, true );
    }
    else {
        $sitepress->switch_lang( $lang, true );
    }

    $fix_product     = yit_icl_translate( 'theme', 'yit', 'review-order-product', 'Product' );
    $fix_qty         = yit_icl_translate( 'theme', 'yit', 'review-order-qty', 'Qty' );
    $fix_totals      = yit_icl_translate( 'theme', 'yit', 'review-order-totals', 'Totals' );
    $cart_subtotal   = yit_icl_translate( 'theme', 'yit', 'review-order-cart-subtotal', 'Cart Subtotal' );
    $fix_coupon      = yit_icl_translate( 'theme', 'yit', 'review-order-coupon', 'Coupon:' );
    $order_total_fix = yit_icl_translate( 'theme', 'yit', 'review-order-total', 'Order Total' );
    $additional_info = yit_icl_translate( 'theme', 'yit', 'review-order-additional-info', 'Additional Information' );

} else {

    $fix_product     = __( 'Product', 'yit' );
    $fix_qty         = __( 'Qty', 'yit' );
    $fix_totals      = __( 'Totals', 'yit' );
    $cart_subtotal   = __( 'Cart Subtotal', 'yit' );
    $fix_coupon      = __( 'Coupon:', 'yit' );
    $order_total_fix = __( 'Order Total', 'yit' );
    $additional_info = __( 'Additional Information', 'yit' );
}

global $woocommerce;

//$available_methods = $woocommerce->shipping->get_available_shipping_methods();
?>
<div id="order_review">

    <table class="shop_table">
        <thead>
        <tr>
            <th class="product-name"><?php echo $fix_product; ?></th>
            <th class="product-quantity"><?php echo $fix_qty; ?></th>
            <th class="product-total"><?php echo $fix_totals; ?></th>
        </tr>
        </thead>
        <tfoot>

        <tr class="cart-subtotal">
            <th><?php echo $cart_subtotal; ?></th>
            <td><?php wc_cart_totals_subtotal_html(); ?></td>
        </tr>

        <?php foreach ( WC()->cart->get_coupons( 'cart' ) as $code => $coupon ) : ?>
            <tr class="cart-discount coupon-<?php echo esc_attr( $code ); ?>">
                <th ><?php echo $fix_coupon; ?> <?php echo esc_html( $code ); ?></th>
                <td><?php wc_cart_totals_coupon_html( $coupon ); ?></td>
            </tr>
        <?php endforeach; ?>

        <?php if ( WC()->cart->needs_shipping() && WC()->cart->show_shipping() ) : ?>

            <?php do_action( 'woocommerce_review_order_before_shipping' ); ?>

            <?php wc_cart_totals_shipping_html(); ?>

            <?php do_action( 'woocommerce_review_order_after_shipping' ); ?>
        <?php endif; ?>

        <?php foreach ( WC()->cart->get_fees() as $fee ) : ?>
            <tr class="fee fee-<?php echo $fee->id ?>">
                <th><?php echo esc_html( $fee->name ); ?></th>
                <td><?php wc_cart_totals_fee_html( $fee ); ?></td>
            </tr>
        <?php endforeach; ?>

        <?php if ( WC()->cart->tax_display_cart === 'excl' ) : ?>
            <?php if ( get_option( 'woocommerce_tax_total_display' ) === 'itemized' ) : ?>
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
                <th><?php $fix_coupon; ?> <?php echo esc_html( $code ); ?></th>
                <td><?php wc_cart_totals_coupon_html( $coupon ); ?></td>
            </tr>
        <?php endforeach; ?>

        <?php do_action( 'woocommerce_review_order_before_order_total' ); ?>

        <tr class="total">
            <th><?php echo $order_total_fix; ?></th>
            <td><strong><?php wc_cart_totals_order_total_html(); ?></strong></td>
        </tr>

        <?php do_action( 'woocommerce_review_order_after_order_total' ); ?>

        </tfoot>
        <tbody>
        <?php
        do_action( 'woocommerce_review_order_before_cart_contents' );
        foreach ( WC()->cart->get_cart() as $cart_item_key => $cart_item ) :
            $_product = apply_filters( 'woocommerce_cart_item_product', $cart_item['data'], $cart_item, $cart_item_key );

            if ( $_product && $_product->exists() && $cart_item['quantity'] > 0 && apply_filters( 'woocommerce_checkout_cart_item_visible', true, $cart_item, $cart_item_key ) ) {
                ?>
                <tr class="<?php echo esc_attr( apply_filters( 'woocommerce_cart_item_class', 'cart_item', $cart_item, $cart_item_key ) ); ?>">
                    <td class="product-name">
                        <?php echo apply_filters( 'woocommerce_cart_item_name', $_product->get_title(), $cart_item, $cart_item_key ); ?>
                        <?php echo apply_filters( 'woocommerce_checkout_cart_item_quantity', '', $cart_item, $cart_item_key ); ?>
                        <?php echo WC()->cart->get_item_data( $cart_item ); ?>
                    </td>
                    <td class="product-quantity"><?php echo sprintf( '%s', $cart_item['quantity'] ) ?></td>
                    <td class="product-total">
                        <?php echo apply_filters( 'woocommerce_cart_item_subtotal', WC()->cart->get_product_subtotal( $_product, $cart_item['quantity'] ), $cart_item, $cart_item_key ); ?>
                    </td>
                </tr>
            <?php

            }
        endforeach;
        do_action( 'woocommerce_review_order_after_cart_contents' );
        ?>
        </tbody>
    </table>

    <?php if ( ! yit_get_option( 'shop-checkout-multistep' ) ): ?>
        <?php wc_get_template( 'checkout/form-payment.php', array( 'woocommerce' => $woocommerce ) ); ?>
    <?php else: ?>
        <?php $checkout = $woocommerce->checkout(); ?>
        <?php do_action( 'woocommerce_before_order_notes', $checkout ); ?>

        <?php if ( get_option( 'woocommerce_enable_order_comments' ) != 'no' ) : ?>

            <h3><?php echo $additional_info; ?></h3>

            <?php foreach ( $checkout->checkout_fields['order'] as $key => $field ) : ?>

                <?php woocommerce_form_field( $key, $field, $checkout->get_value( $key ) ); ?>

            <?php endforeach; ?>

        <?php endif; ?>

        <?php do_action( 'woocommerce_after_order_notes', $checkout ); ?>

        <?php wc_get_template( 'checkout/form-place-order.php', array( 'woocommerce' => $woocommerce ) ); ?>
    <?php endif ?>

</div>
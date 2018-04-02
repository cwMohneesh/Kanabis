<?php
/**
 * Shipping Methods Display
 *
 * In 2.1 we show methods per package. This allows for multiple methods per order if so desired.
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

    $shipping_fix     = yit_icl_translate( 'theme', 'yit', 'cart-shipping-num', 'Shipping #%d' );
    $shipping_and     = yit_icl_translate( 'theme', 'yit', 'cart-shipping-handling', 'Shipping and Handling' );
    $noshipping_found = yit_icl_translate( 'theme', 'yit', 'cart-shipping-no-shipping-method', 'No shipping methods were found; please recalculate your shipping or continue to checkout and enter your full address to see if there is shipping available to your location.' );

    $please_fill    = yit_icl_translate( 'theme', 'yit', 'cart-shipping-fill-details', 'Please fill in your details to see available shipping methods.' );
    $sorry_shipping = yit_icl_translate( 'theme', 'yit', 'cart-shipping-unavailable', 'Sorry, shipping is unavailable %s.' );
    $if_you_require = yit_icl_translate( 'theme', 'yit', 'cart-shipping-require-assistace', 'If you require assistance or wish to make alternate arrangements please contact us.' );
    $fix_shipping   = yit_icl_translate( 'theme', 'yit', 'cart-shipping', 'Shipping' );

}
else {

    $shipping_fix     = __( 'Shipping #%d', 'yit' );
    $shipping_and     = __( 'Shipping and Handling', 'yit' );
    $noshipping_found = __( 'No shipping methods were found; please recalculate your shipping or continue to checkout and enter your full address to see if there is shipping available to your location.', 'yit' );

    $please_fill    = __( 'Please fill in your details to see available shipping methods.', 'yit' );
    $sorry_shipping = __( 'Sorry, shipping is unavailable %s.', 'yit' );
    $if_you_require = __( 'If you require assistance or wish to make alternate arrangements please contact us.', 'yit' );
    $fix_shipping   = __( 'Shipping', 'yit' );

}


?>
<tr class="shipping">
    <th colspan="1" ><?php
        if ( $show_package_details ) {
            printf( $shipping_fix, $index + 1 );
        }
        else {
            echo $shipping_and;
        }
        ?></th>
    <td>
        <?php if ( ! empty( $available_methods ) ) : ?>

            <?php if ( 1 === count( $available_methods ) ) :
                $method = current( $available_methods );

                echo wp_kses_post( wc_cart_totals_shipping_method_label( $method ) ); ?>
                <input type="hidden" name="shipping_method[<?php echo $index; ?>]" data-index="<?php echo $index; ?>" id="shipping_method_<?php echo $index; ?>" value="<?php echo esc_attr( $method->id ); ?>" class="shipping_method" />

            <?php elseif ( get_option( 'woocommerce_shipping_method_format' ) === 'select' ) : ?>

                <select name="shipping_method[<?php echo $index; ?>]" data-index="<?php echo $index; ?>" id="shipping_method_<?php echo $index; ?>" class="shipping_method">
                    <?php foreach ( $available_methods as $method ) : ?>
                        <option value="<?php echo esc_attr( $method->id ); ?>" <?php selected( $method->id, $chosen_method ); ?>><?php echo wp_kses_post( wc_cart_totals_shipping_method_label( $method ) ); ?></option>
                    <?php endforeach; ?>
                </select>

            <?php
            else : ?>

                <ul id="shipping_method">
                    <?php foreach ( $available_methods as $method ) : ?>
                        <li>
                            <input type="radio" name="shipping_method[<?php echo $index; ?>]" data-index="<?php echo $index; ?>" id="shipping_method_<?php echo $index; ?>_<?php echo sanitize_title( $method->id ); ?>" value="<?php echo esc_attr( $method->id ); ?>" <?php checked( $method->id, $chosen_method ); ?> class="shipping_method" />
                            <label for="shipping_method_<?php echo $index; ?>_<?php echo sanitize_title( $method->id ); ?>"><?php echo wp_kses_post( wc_cart_totals_shipping_method_label( $method ) ); ?></label>
                        </li>
                    <?php endforeach; ?>
                </ul>

            <?php endif; ?>

        <?php elseif ( ! WC()->customer->get_shipping_state() || ! WC()->customer->get_shipping_postcode() ) : ?>

            <?php if ( is_cart() ) : ?>

                <p><?php echo $noshipping_found; ?></p>

            <?php else : ?>

                <p><?php echo $please_fill; ?></p>

            <?php endif; ?>

        <?php
        else : ?>

            <?php if ( is_cart() ) : ?>

                <?php echo apply_filters( 'woocommerce_cart_no_shipping_available_html',
                    '<div class="woocommerce-error"><p>' .
                        sprintf( __( 'Sorry, shipping is unavailable %s.', 'yit' ) . ' ' . $if_you_require, WC()->countries->shipping_to_prefix() . ' ' . WC()->countries->countries[WC()->customer->get_shipping_country()] ) .
                        '</p></div>'
                ); ?>

            <?php else : ?>

                <?php echo apply_filters( 'woocommerce_no_shipping_available_html',
                    '<p>' .
                        sprintf( $sorry_shipping . ' ' . $if_you_require, WC()->countries->shipping_to_prefix() . ' ' . WC()->countries->countries[WC()->customer->get_shipping_country()] ) .
                        '</p>'
                ); ?>

            <?php endif; ?>

        <?php endif; ?>

        <?php if ( $show_package_details ) : ?>
            <?php
            foreach ( $package['contents'] as $item_id => $values ) {
                if ( $values['data']->needs_shipping() ) {
                    $product_names[] = $values['data']->get_title() . ' &times;' . $values['quantity'];
                }
            }

            echo '<p class="woocommerce-shipping-contents"><small>' . $fix_shipping . ': ' . implode( ', ', $product_names ) . '</small></p>';
            ?>
        <?php endif; ?>
    </td>
</tr>

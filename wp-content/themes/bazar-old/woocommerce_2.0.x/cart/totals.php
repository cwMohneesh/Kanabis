<?php
/**
 * Cart totals
 *
 * @author 		WooThemes
 * @package 	WooCommerce/Templates
 * @version     1.6.4
 */

global $woocommerce;

$available_methods = $woocommerce->shipping->get_available_shipping_methods(); ?>
<div class="span6 cart_totals <?php if ( $woocommerce->customer->has_calculated_shipping() ) echo 'calculated_shipping'; ?>">
    <div class="border-1 border">
        <div class="border-2 border">

            <?php do_action('woocommerce_before_cart_totals'); ?>

            <?php if ( ! $woocommerce->shipping->enabled || $available_methods || ! $woocommerce->customer->get_shipping_country() || ! $woocommerce->customer->has_calculated_shipping() ) : ?>

                <h2><?php _e('Cart Totals', 'yit'); ?></h2>
                <table align="right" cellspacing="0" cellpadding="0">
                    <tbody>

                    <tr class="cart-subtotal">
                        <th><strong><?php _e('Cart Subtotal', 'yit'); ?></strong></th>
                        <td><strong><?php echo $woocommerce->cart->get_cart_subtotal(); ?></strong></td>
                    </tr>

                    <?php if ($woocommerce->cart->get_discounts_before_tax()) : ?>

                        <tr class="discount">
                            <th><?php _e('Cart Discount', 'yit'); ?> <a href="<?php echo add_query_arg('remove_discounts', '1', $woocommerce->cart->get_cart_url()) ?>"><?php _e('[Remove]', 'yit'); ?></a></th>
                            <td>-<?php echo $woocommerce->cart->get_discounts_before_tax(); ?></td>
                        </tr>

                    <?php endif; ?>

                    <?php if ( $woocommerce->cart->needs_shipping() && $woocommerce->cart->show_shipping() && ( $available_methods || get_option( 'woocommerce_enable_shipping_calc' ) == 'yes' ) ) : ?>

                        <tr class="shipping">
                            <th <?php if( is_checkout() ){ ?> colspan="2" <?php } ?>><?php _e('Shipping', 'yit'); ?></th>
                            <td><?php woocommerce_get_template( 'cart/shipping-methods.php', array( 'available_methods' => $available_methods ) ); ?></td>
                        </tr>

                    <?php endif ?>

                    <?php foreach ( $woocommerce->cart->get_fees() as $fee ) : ?>

                        <tr class="fee fee-<?php echo $fee->id ?>">
                            <th><?php echo $fee->name ?></th>
                            <td><?php
                                if ( $woocommerce->cart->tax_display_cart == 'excl' )
                                    echo woocommerce_price( $fee->amount );
                                else
                                    echo woocommerce_price( $fee->amount + $fee->tax );
                                ?></td>
                        </tr>

                    <?php endforeach; ?>

                    <?php
                    // Show the tax row if showing prices exclusive of tax only
                    if ( $woocommerce->cart->tax_display_cart == 'excl' ) {
                        foreach ( $woocommerce->cart->get_tax_totals() as $code => $tax ) {
                            echo '<tr class="tax-rate tax-rate-' . $code . '">
								<th>' . $tax->label . '</th>
								<td>' . $tax->formatted_amount . '</td>
							</tr>';
                        }
                    }
                    ?>

                    <?php if ( $woocommerce->cart->get_discounts_after_tax() ) : ?>

                        <tr class="discount">
                            <th><?php _e( 'Order Discount', 'yit' ); ?> <a href="<?php echo add_query_arg( 'remove_discounts', '2', $woocommerce->cart->get_cart_url() ) ?>"><?php _e( '[Remove]', 'yit' ); ?></a></th>
                            <td>-<?php echo $woocommerce->cart->get_discounts_after_tax(); ?></td>
                        </tr>

                    <?php endif; ?>

                    <?php do_action( 'woocommerce_cart_totals_before_order_total' ); ?>

                    <tr class="total">
                        <th><strong><?php _e( 'Order Total', 'yit' ); ?></strong></th>
                        <td>
                            <strong><?php echo $woocommerce->cart->get_total(); ?></strong>
                            <?php
                            // If prices are tax inclusive, show taxes here
                            if (  $woocommerce->cart->tax_display_cart == 'incl' ) {
                                $tax_string_array = array();

                                foreach ( $woocommerce->cart->get_tax_totals() as $code => $tax ) {
                                    $tax_string_array[] = sprintf( '%s %s', $tax->formatted_amount, $tax->label );
                                }

                                if ( ! empty( $tax_string_array ) ) {
                                    echo '<small class="includes_tax">' . sprintf( __( '(Includes %s)', 'yit' ), implode( ', ', $tax_string_array ) ) . '</small>';
                                }
                            }
                            ?>
                        </td>
                    </tr>

                    <?php do_action( 'woocommerce_cart_totals_after_order_total' ); ?>

                    </tbody>
                </table>

                <?php if ( $woocommerce->cart->get_cart_tax() ) : ?>

                    <p><small><?php

                            $estimated_text = ( $woocommerce->customer->is_customer_outside_base() && ! $woocommerce->customer->has_calculated_shipping() ) ? sprintf( ' ' . __( ' (taxes estimated for %s)', 'yit' ), $woocommerce->countries->estimated_for_prefix() . __( $woocommerce->countries->countries[ $woocommerce->countries->get_base_country() ], 'yit' ) ) : '';

                            printf( __( 'Note: Shipping and taxes are estimated%s and will be updated during checkout based on your billing and shipping information.', 'yit' ), $estimated_text );

                            ?></small></p>

                <?php endif; ?>

            <?php elseif( $woocommerce->cart->needs_shipping() ) : ?>

                <?php if ( ! $woocommerce->customer->get_shipping_state() || ! $woocommerce->customer->get_shipping_postcode() ) : ?>

                    <div class="woocommerce-info">

                        <p><?php _e( 'No shipping methods were found; please recalculate your shipping and enter your state/county and zip/postcode to ensure there are no other available methods for your location.', 'yit' ); ?></p>

                    </div>

                <?php else : ?>

                    <?php

                    $customer_location = $woocommerce->countries->countries[ $woocommerce->customer->get_shipping_country() ];

                    echo apply_filters( 'woocommerce_cart_no_shipping_available_html',
                        '<div class="woocommerce-error"><p>' .
                            sprintf( __( 'Sorry, it seems that there are no available shipping methods for your location (%s).', 'yit' ) . ' ' . __( 'If you require assistance or wish to make alternate arrangements please contact us.', 'yit' ), $customer_location ) .
                            '</p></div>'
                    );

                    ?>

                <?php endif; ?>

            <?php endif; ?>

            <?php do_action('woocommerce_after_cart_totals'); ?>

            <div class="clear"></div>
        </div>
    </div>
</div>
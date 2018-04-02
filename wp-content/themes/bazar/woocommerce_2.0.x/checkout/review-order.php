<?php
/**
 * Review order form
 *
 * @author 		WooThemes
 * @package 	WooCommerce/Templates
 * @version     1.6.4
 */

global $woocommerce;

$available_methods = $woocommerce->shipping->get_available_shipping_methods();
?>
<div id="order_review">

	<table class="shop_table">
		<thead>
			<tr>
				<th class="product-name"><?php _e('Product', 'yit'); ?></th>
				<th class="product-quantity"><?php _e('Qty', 'yit'); ?></th>
				<th class="product-total"><?php _e('Totals', 'yit'); ?></th>
			</tr>
		</thead>
		<tfoot>
			
			<tr class="cart-subtotal">
				<th colspan="2"><?php _e('Cart Subtotal', 'yit'); ?></th>
				<td><?php echo $woocommerce->cart->get_cart_subtotal(); ?></td>
			</tr>

			<?php if ($woocommerce->cart->get_discounts_before_tax()) : ?>

			<tr class="discount">
				<th colspan="2"><?php _e('Cart Discount', 'yit'); ?></th>
				<td>-<?php echo $woocommerce->cart->get_discounts_before_tax(); ?></td>
			</tr>

			<?php endif; ?>

			<?php if ( $woocommerce->cart->needs_shipping() && $woocommerce->cart->show_shipping() ) : ?>

			<?php do_action('woocommerce_review_order_before_shipping'); ?>

			<tr class="shipping">
				<th <?php if( is_checkout() ){ ?> colspan="2" <?php } ?>><?php _e('Shipping', 'yit'); ?></th>
				<td><?php woocommerce_get_template( 'cart/shipping-methods.php', array( 'available_methods' => $available_methods ) ); ?></td>

			</tr>

			<?php do_action('woocommerce_review_order_after_shipping'); ?>

			<?php endif; ?>

            <?php foreach ( $woocommerce->cart->get_fees() as $fee ) : ?>

                <tr class="fee fee-<?php echo $fee->id ?>">
                    <th colspan="2"><?php echo $fee->name ?></th>
                    <td><?php
                        if ( $woocommerce->cart->tax_display_cart == 'excl' )
                            echo woocommerce_price( $fee->amount );
                        else
                            echo woocommerce_price( $fee->amount + $fee->tax );
                        ?></td>
                </tr>

            <?php endforeach; ?>

			<?php
				if ($woocommerce->cart->get_cart_tax()) :

					$taxes = $woocommerce->cart->get_formatted_taxes();

					if (sizeof($taxes)>0) :

						$has_compound_tax = false;

						foreach ($taxes as $key => $tax) : if ($woocommerce->cart->tax->is_compound( $key )) : $has_compound_tax = true; continue; endif;

							?>
							<tr class="tax-rate tax-rate-<?php echo $key; ?>">
								<th colspan="2"><?php
									if ( get_option('woocommerce_prices_include_tax') == 'yes' )
										_e('incl.&nbsp;', 'yit');
									echo $woocommerce->cart->tax->get_rate_label( $key );
								?></th>
								<td><?php echo $tax; ?></td>
							</tr>
							<?php

						endforeach;

						if ($has_compound_tax && !$woocommerce->cart->prices_include_tax) :
							?>
							<tr class="order-subtotal">
								<th colspan="2"><strong><?php _e('Order Subtotal', 'yit'); ?></strong></th>
								<td><?php echo $woocommerce->cart->get_cart_subtotal( true ); ?></td>
							</tr>
							<?php
						endif;

						foreach ($taxes as $key => $tax) : if (!$woocommerce->cart->tax->is_compound( $key )) continue;

							?>
							<tr class="tax-rate tax-rate-<?php echo $key; ?>">
								<th colspan="2"><?php
									if ( get_option('woocommerce_prices_include_tax') == 'yes' )
										_e('incl.&nbsp;', 'yit');
									echo $woocommerce->cart->tax->get_rate_label( $key );
								?></th>
								<td><?php echo $tax; ?></td>
							</tr>
							<?php

						endforeach;

					else :

						?>
						<tr class="tax">
							<th colspan="2"><?php echo $woocommerce->countries->tax_or_vat(); ?></th>
							<td><?php echo $woocommerce->cart->get_cart_tax(); ?></td>
						</tr>
						<?php

					endif;
				elseif ( get_option( 'woocommerce_display_cart_taxes_if_zero' ) == 'yes' ) :
				?>

					<tr class="tax">
						<th colspan="2"><?php echo $woocommerce->countries->tax_or_vat(); ?></th>
						<td><?php _ex( 'N/A', 'Relating to tax', 'yit' ); ?></td>
					</tr>

				<?php
				endif;
			?>

			<?php if ($woocommerce->cart->get_discounts_after_tax()) : ?>

			<tr class="discount">
				<th colspan="2"><?php _e('Order Discount', 'yit'); ?></th>
				<td>-<?php echo $woocommerce->cart->get_discounts_after_tax(); ?></td>
			</tr>

			<?php endif; ?>

			<?php do_action('woocommerce_before_order_total'); ?>

			<tr class="total">
				<th colspan="2"><?php _e('Order Total', 'yit'); ?></th>
				<td><strong><?php echo $woocommerce->cart->get_total(); ?></strong></td>
			</tr>

			<?php do_action('woocommerce_after_order_total'); ?>

		</tfoot>
		<tbody>
			<?php
				if (sizeof($woocommerce->cart->get_cart())>0) :
					foreach ($woocommerce->cart->get_cart() as $item_id => $values) :
						$_product = $values['data'];
						if ($_product->exists() && $values['quantity']>0) :
							echo '
								<tr class = "' . esc_attr( apply_filters('woocommerce_checkout_table_item_class', 'checkout_table_item', $values, $item_id ) ) . '">
									<td class="product-name">'.$_product->get_title().$woocommerce->cart->get_item_data( $values ).'</td>
									<td class="product-quantity">'.$values['quantity'].'</td>
									<td class="product-total">' . apply_filters( 'woocommerce_checkout_item_subtotal', $woocommerce->cart->get_product_subtotal( $_product, $values['quantity'] ), $values, $item_id ) . '</td>
								</tr>';
						endif;
					endforeach;
				endif;

				do_action( 'woocommerce_cart_contents_review_order' );
			?>
		</tbody>
	</table>
    
	<?php if( !yit_get_option('shop-checkout-multistep') ): ?>
		<?php woocommerce_get_template('checkout/form-payment.php', array('woocommerce' => $woocommerce)); ?>
	<?php else: ?>
		<?php $checkout = $woocommerce->checkout(); ?>
		<?php do_action('woocommerce_before_order_notes', $checkout); ?>
		
		<?php if (get_option('woocommerce_enable_order_comments')!='no') : ?>
		
			<h3><?php _e('Additional Information', 'yit'); ?></h3>
		
			<?php foreach ($checkout->checkout_fields['order'] as $key => $field) : ?>
		
				<?php woocommerce_form_field( $key, $field, $checkout->get_value( $key ) ); ?>
		
			<?php endforeach; ?>
		
		<?php endif; ?>
		
		<?php do_action('woocommerce_after_order_notes', $checkout); ?>

		<?php woocommerce_get_template('checkout/form-place-order.php', array('woocommerce' => $woocommerce)); ?>
	<?php endif ?>

</div>
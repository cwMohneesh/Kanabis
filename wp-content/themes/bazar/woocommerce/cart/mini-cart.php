<?php
/**
 * Mini-cart
 *
 * Contains the markup for the mini-cart, used by the cart widget
 *
 * @author        WooThemes
 * @package       WooCommerce/Templates
 * @version       2.1.0
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

global $sitepress;

if ( isset( $sitepress ) && ! empty( $sitepress ) ) {

    $no_products_in_the_cart = yit_icl_translate( 'theme', 'yit', 'min-cart-product-in', apply_filters( 'yit_no_product_in_cart', 'No products in the cart.' ) );
    $go_to_shop              = yit_icl_translate( 'theme', 'yit', 'min-cart-go-to-shop', 'Go to the shop' );
    $remove_this_item        = yit_icl_translate( 'theme', 'yit', 'min-cart-remove-item', 'Remove this item' );
    $remove_item             = yit_icl_translate( 'theme', 'yit', 'min-cart-remove', 'remove' );
    $subtotal                = yit_icl_translate( 'theme', 'yit', 'min-cart-subototal', 'SUBTOTAL' );
    $go_to_cart              = yit_icl_translate( 'theme', 'yit', 'min-cart-view-cart', 'View Cart' );
    $go_to_checkout          = yit_icl_translate( 'theme', 'yit', 'min-cart-checkout', 'Checkout' );

}
else {

    $no_products_in_the_cart = apply_filters( 'yit_no_product_in_cart', __( 'No products in the cart.', 'yit' ) );
    $go_to_shop              = __( 'Go to the shop', 'yit' );
    $remove_this_item        = __( 'Remove this item', 'yit' );
    $remove_item             = __( 'remove', 'yit' );
    $subtotal                = __( 'SUBTOTAL', 'yit' );
    $go_to_cart              = __( 'View Cart', 'yit' );
    $go_to_checkout          = __( 'Checkout', 'yit' );
}
?>

<ul class="cart_list product_list_widget <?php echo $args['list_class']; ?>">

    <?php if ( sizeof( WC()->cart->get_cart() ) > 0 ) : ?>

        <?php foreach ( WC()->cart->get_cart() as $cart_item_key => $cart_item ) :

            $_product                = apply_filters( 'woocommerce_cart_item_product', $cart_item['data'], $cart_item, $cart_item_key );
            $cart_item['product_id'] = apply_filters( 'woocommerce_cart_item_product_id', $cart_item['product_id'], $cart_item, $cart_item_key );


            // Only display if allowed
            if ( ! apply_filters( 'woocommerce_widget_cart_item_visible', true, $cart_item, $cart_item_key ) || ! $_product->exists() || $cart_item['quantity'] == 0 ) {
                continue;
            }

            $product_name  = apply_filters( 'woocommerce_cart_item_name', $_product->get_title(), $cart_item, $_product );
            $product_price = get_option( 'woocommerce_tax_display_cart' ) == 'excl' || WC()->customer->is_vat_exempt() ? $_product->get_price_excluding_tax() : $_product->get_price_including_tax();
            $product_price = apply_filters( 'woocommerce_cart_item_price', wc_price( $product_price ), $cart_item, $cart_item_key );
            $thumbnail     = apply_filters( 'woocommerce_cart_item_thumbnail', $_product->get_image(), $cart_item, $cart_item_key );
            ?>

            <li>
                <a href="<?php echo yit_ssl_url( get_permalink( $cart_item['product_id'] ) ); ?>">
                    <?php echo $thumbnail; ?>
                </a>

                <a href="<?php echo yit_ssl_url( get_permalink( $cart_item['product_id'] ) ); ?>" class="name">
                    <?php echo $product_name; ?>
                </a>

                <?php
                $remove_link_fix = apply_filters( 'woocommerce_cart_item_remove_link', sprintf( '<a href="%s" class="remove_item" title="%s">%s</a>', esc_url( WC()->cart->get_remove_url( $cart_item_key ) ), $remove_this_item, $remove_item ), $cart_item_key );
                echo $remove_link_fix;
                ?>

                <?php echo WC()->cart->get_item_data( $cart_item ); ?>

                <?php echo apply_filters( 'woocommerce_widget_cart_item_quantity', '<span class="quantity">' . sprintf( '%s &times; %s', $cart_item['quantity'], $product_price ) . '</span>', $cart_item, $cart_item_key ); ?>


                <div class="border clear"></div>
            </li>

        <?php endforeach; ?>

    <?php else : ?>

        <li class="empty"><?php echo $no_products_in_the_cart; ?></li>

        <div class="empty-buttons">
            <a href="<?php echo get_permalink( wc_get_page_id( 'shop' ) ); ?>" class="button"><?php echo $go_to_shop; ?></a>
        </div>

    <?php endif; ?>

</ul><!-- end product list -->

<?php if ( sizeof( WC()->cart->get_cart() ) > 0 ) : ?>

    <p class="total"><?php echo $subtotal; ?> <?php echo WC()->cart->get_cart_subtotal(); ?></p>

    <?php do_action( 'woocommerce_widget_shopping_cart_before_buttons' ); ?>

    <p class="buttons">
        <a href="<?php echo WC()->cart->get_cart_url(); ?>" class="button"><?php echo $go_to_cart; ?></a>
        <a href="<?php echo WC()->cart->get_checkout_url(); ?>" class="button checkout"><?php echo $go_to_checkout; ?></a>
    </p>

<?php endif; ?>

<?php do_action( 'woocommerce_after_mini_cart' ); ?>
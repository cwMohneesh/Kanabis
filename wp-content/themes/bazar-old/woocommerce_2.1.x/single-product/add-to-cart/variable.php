<?php
/**
 * Variable product add to cart
 *
 * @author        WooThemes
 * @package       WooCommerce/Templates
 * @version       2.1.0
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
} // Exit if accessed directly
global $woocommerce, $product, $post;

// resize the main image of each variations
if ( function_exists( 'yith_wcmg_is_enabled' ) && yith_wcmg_is_enabled() ) {
    foreach ( $available_variations as $variation_id => $variation ) {
        $available_variations[$variation_id]['image_src']       = yit_image( "src=$variation[image_src]&size=shop_single&output=url", false );
        $available_variations[$variation_id]['image_magnifier'] = yit_image( "src=$variation[image_magnifier]&size=shop_magnifier&output=url", false );
    }
}

/* woocommerce subscription price fix */
$class_subscription = "";
if ( $product->product_type == 'variable-subscription' ) {
    $class_subscription = "subscription";
    $woo_option         = get_option( 'woocommerce_subscriptions_add_to_cart_button_text' );
    $label              = $woo_option ? $woo_option : apply_filters( 'subscription_add_to_cart_text', __( 'Sign Up Now', 'yit' ) );
}
else {
    $label = apply_filters( 'single_add_to_cart_text',yit_icl_translate( "theme", "yit", "add_to_cart_text", yit_get_option( 'add-to-cart-text' ) ));
}


?>

<?php do_action( 'woocommerce_before_add_to_cart_form' ); ?>

<form action="<?php echo esc_url( $product->add_to_cart_url() ); ?>" class="variations_form cart group" method="post" enctype='multipart/form-data' data-product_id="<?php echo $post->ID; ?>" data-product_variations="<?php echo esc_attr( json_encode( $available_variations ) ) ?>">

    <?php if ( is_shop_enabled() && yit_get_option( 'shop-detail-add-to-cart' ) && yit_product_form_position_is( 'in-sidebar' ) ) : ?>
        <div class="quantity-wrap group">
            <label><?php _e( 'Quantity', 'yit' ) ?></label>
            <?php woocommerce_quantity_input(); ?>
        </div>
    <?php endif; ?>

    <div class="variations">
        <?php $loop = 0; foreach ( $attributes as $name => $options ) : $loop ++; ?>
            <label for="<?php echo sanitize_title( $name ); ?>"><?php echo wc_attribute_label( $name ); ?></label>
            <div class="select-wrapper">
                <select id="<?php echo esc_attr( sanitize_title( $name ) ); ?>" name="attribute_<?php echo sanitize_title( $name ); ?>">
                    <option value=""><?php echo __( 'Choose an option', 'yit' ) ?>&hellip;</option>
                    <?php
                    if ( is_array( $options ) ) {

                        if ( empty( $_POST ) ) {
                            $selected_value = ( isset( $selected_attributes[sanitize_title( $name )] ) ) ? $selected_attributes[sanitize_title( $name )] : '';
                        }
                        else {
                            $selected_value = isset( $_POST['attribute_' . sanitize_title( $name )] ) ? $_POST['attribute_' . sanitize_title( $name )] : '';
                        }

                        // Get terms if this is a taxonomy - ordered
                        if ( taxonomy_exists( $name ) ) {

                            $orderby = wc_attribute_orderby( $name );

                            switch ( $orderby ) {
                                case 'name' :
                                    $args = array( 'orderby' => 'name', 'hide_empty' => false, 'menu_order' => false );
                                    break;
                                case 'id' :
                                    $args = array( 'orderby' => 'id', 'order' => 'ASC', 'menu_order' => false );
                                    break;
                                case 'menu_order' :
                                    $args = array( 'menu_order' => 'ASC' );
                                    break;
                            }

                            $terms = get_terms( $name, $args );

                            foreach ( $terms as $term ) {
                                if ( ! in_array( $term->slug, $options ) ) {
                                    continue;
                                }

                                echo '<option value="' . esc_attr( $term->slug ) . '" ' . selected( $selected_value, $term->slug, false ) . '>' . apply_filters( 'woocommerce_variation_option_name', $term->name ) . '</option>';
                            }
                        }
                        else {

                            foreach ( $options as $option ) {
                                echo '<option value="' . esc_attr( sanitize_title( $option ) ) . '" ' . selected( sanitize_title( $selected_value ), sanitize_title( $option ), false ) . '>' . esc_html( apply_filters( 'woocommerce_variation_option_name', $option ) ) . '</option>';
                            }

                        }
                    }
                    ?>
                </select>
            </div>
        <?php endforeach;?><?php
        if ( yit_product_form_position_is( 'in-sidebar' ) && sizeof( $attributes ) == $loop ) {
            echo '<a class="reset_variations" href="#reset">' . __( 'Clear selection', 'yit' ) . '</a>';
        }

        ?>
    </div>

    <?php do_action( 'woocommerce_before_add_to_cart_button' ); ?>

    <div class="single_variation_wrap" style="display:none;">
        <?php do_action( 'woocommerce_before_single_variation' ); ?>

        <div class="single_variation <?php echo $class_subscription ?>"></div>

        <?php if ( $class_subscription != "" ): ?>
            <div class="clear"></div>
        <?php endif; ?>

        <div class="variations_button <?php echo $class_subscription ?>">
            <?php if ( yit_product_form_position_is( 'in-content' ) ) :
                echo '<a class="reset_variations" href="#reset">' . __( 'Clear selection', 'yit' ) . '</a>';
                ?>
                <div class="quantity-wrap group">
                    <label><?php _e( 'Quantity', 'yit' ) ?></label>
                    <?php woocommerce_quantity_input(); ?>

                </div>
            <?php endif; ?>
            <button type="submit" class="single_add_to_cart_button button alt"><?php echo $label ?></button>
        </div>
        <input type="hidden" name="add-to-cart" value="<?php echo $product->id; ?>" />
        <input type="hidden" name="product_id" value="<?php echo esc_attr( $post->ID ); ?>" />
        <input type="hidden" name="variation_id" value="" />

        <?php do_action( 'woocommerce_after_single_variation' ); ?>
    </div>
    <div>
        <input type="hidden" name="product_id" value="<?php echo esc_attr( $post->ID ); ?>" /></div>

    <?php do_action( 'woocommerce_after_add_to_cart_button' ); ?>

</form>

<?php do_action( 'woocommerce_after_add_to_cart_form' ); ?>

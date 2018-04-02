<?php
/**
 * Variable product add to cart
 *
 * @author 		WooThemes
 * @package 	WooCommerce/Templates
 * @version     1.6.5
 */

global $woocommerce, $product, $post;

// resize the main image of each variations
if( yith_wcmg_is_enabled() ) {
    foreach ( $available_variations as $variation_id => $variation ) {
        $available_variations[$variation_id]['image_src']  = yit_image( "src=$variation[image_src]&size=shop_single&output=url", false );
        $available_variations[$variation_id]['image_magnifier'] = yit_image( "src=$variation[image_magnifier]&size=shop_magnifier&output=url", false );
    }
}
?>

<?php do_action('woocommerce_before_add_to_cart_form'); ?>

<form action="<?php echo esc_url( $product->add_to_cart_url() ); ?>" class="variations_form cart group" method="post" enctype='multipart/form-data' data-product_id="<?php echo $post->ID; ?>" data-product_variations="<?php echo esc_attr( json_encode( $available_variations ) ) ?>">

    <?php if( is_shop_enabled() && yit_get_option('shop-detail-add-to-cart') && yit_product_form_position_is('in-sidebar') ) : ?>
        <div class="quantity-wrap group">
            <label><?php _e( 'Quantity', 'yit' ) ?></label>
            <?php woocommerce_quantity_input(); ?>
        </div>
    <?php endif; ?>

    <div class="variations">
        <?php $loop = 0; foreach ( $attributes as $name => $options ) : $loop++; ?>
            <label for="<?php echo sanitize_title($name); ?>"><?php echo $woocommerce->attribute_label($name); ?></label>
            <div class="select-wrapper">
                <select id="<?php echo esc_attr( sanitize_title($name) ); ?>" name="attribute_<?php echo sanitize_title($name); ?>">
                    <option value=""><?php echo __('Choose an option', 'yit') ?>&hellip;</option>
                    <?php
                    if ( is_array( $options ) ) {

                        if ( empty( $_POST ) )
                            $selected_value = ( isset( $selected_attributes[ sanitize_title( $name ) ] ) ) ? $selected_attributes[ sanitize_title( $name ) ] : '';
                        else
                            $selected_value = isset( $_POST[ 'attribute_' . sanitize_title( $name ) ] ) ? $_POST[ 'attribute_' . sanitize_title( $name ) ] : '';

                        // Get terms if this is a taxonomy - ordered
                        if ( taxonomy_exists( $name ) ) {

                            $orderby = $woocommerce->attribute_orderby( $name );

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

                            $terms = get_terms( $name , $args );

                            foreach ( $terms as $term ) {
                                if ( ! in_array( $term->slug, $options ) )
                                    continue;

                                echo '<option value="' . esc_attr( $term->slug ) . '" ' . selected( $selected_value, $term->slug, false ) . '>' . apply_filters( 'woocommerce_variation_option_name', $term->name ) . '</option>';
                            }
                        } else {

                            foreach ( $options as $option ) {
                                echo '<option value="' . esc_attr( sanitize_title( $option ) ) . '" ' . selected( sanitize_title( $selected_value ), sanitize_title( $option ), false ) . '>' . esc_html( apply_filters( 'woocommerce_variation_option_name', $option ) ) . '</option>';
                            }

                        }
                    }
                    ?>
                </select>
            </div>
        <?php endforeach;?><?php
        if ( yit_product_form_position_is('in-sidebar') && sizeof($attributes) == $loop )
            echo '<a class="reset_variations" href="#reset">'.__('Clear selection', 'yit').'</a>';
        ?>
    </div>

    <?php do_action('woocommerce_before_add_to_cart_button'); ?>

    <div class="single_variation_wrap" style="display:none;">
        <div class="variations_button">
            <input type="hidden" name="variation_id" value="" />
            <?php if ( yit_product_form_position_is('in-content') ) : ?>
                <div class="quantity-wrap group">
                    <label><?php _e( 'Quantity', 'yit' ) ?></label>
                    <?php woocommerce_quantity_input(); ?>
                    <?php if( is_shop_enabled() && yit_get_option('shop-detail-add-to-cart') && yit_product_form_position_is('in-content') ) : ?>
                        <button type="submit" class="single_add_to_cart_button button"><?php echo apply_filters('single_add_to_cart_text', __('Add to cart', 'yit'), $product->product_type); ?></button>
                    <?php endif ?>
                </div>
            <?php endif; ?>
            <?php if( is_shop_enabled() && yit_get_option('shop-detail-add-to-cart') && yit_product_form_position_is('in-sidebar') ) : ?>
                <button type="submit" class="single_add_to_cart_button button"><?php echo apply_filters('single_add_to_cart_text', __('Add to cart', 'yit'), $product->product_type); ?></button>
            <?php endif ?>
        </div>
        <div class="single_variation"></div>
    </div>
    <div><input type="hidden" name="product_id" value="<?php echo esc_attr( $post->ID ); ?>" /></div>

    <?php
    if ( yit_product_form_position_is('in-content') && sizeof($attributes) == $loop )
        echo '<a class="reset_variations" href="#reset">'.__('Clear selection', 'yit').'</a>';
    ?>

    <?php do_action('woocommerce_after_add_to_cart_button'); ?>

</form>

<?php do_action('woocommerce_after_add_to_cart_form'); ?>

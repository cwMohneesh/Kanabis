<?php
/**
 * Single Product Image
 *
 * @author 		WooThemes
 * @package 	WooCommerce/Templates
 * @version     1.6.4
 */

global $post, $woocommerce;

?>
<div class="images">

    <?php
    if ( has_post_thumbnail() ) {

        $image       		= get_the_post_thumbnail( $post->ID, apply_filters( 'single_product_large_thumbnail_size', 'shop_single' ) );
        $image_title 		= esc_attr( get_the_title( get_post_thumbnail_id() ) );
        $image_link  		= wp_get_attachment_url( get_post_thumbnail_id() );
        $attachment_count   = count( get_children( array( 'post_parent' => $post->ID, 'post_mime_type' => 'image', 'post_type' => 'attachment' ) ) );

        if ( $attachment_count != 1 ) {
            $gallery = '[product-gallery]';
        } else {
            $gallery = '';
        }

        echo apply_filters( 'woocommerce_single_product_image_html', sprintf( '<a href="%s" itemprop="image" class="woocommerce-main-image zoom" title="%s"  rel="prettyPhoto' . $gallery . '">%s</a>', $image_link, $image_title, $image ), $post->ID );

    } else {

        echo apply_filters( 'woocommerce_single_product_image_html', sprintf( '<img src="%s" alt="Placeholder" />', woocommerce_placeholder_img_src() ), $post->ID );

    }
    ?>

	<?php do_action('woocommerce_product_thumbnails'); ?>

</div>

<script>

    /* -------------- *
     * Temporary fix! *
     * -------------- */

    jQuery(document).ready(function($){

        var yith_wcmg = $('.images');
        var yith_wcmg_zoom  = $('.woocommerce-main-image');
        var yith_wcmg_image = $('.woocommerce-main-image img');
        var yith_wcmg_default_zoom = yith_wcmg.find('.woocommerce-main-image').attr('href');
        var yith_wcmg_default_image = yith_wcmg.find('.woocommerce-main-image img').attr('src');

        $( document ).on( 'found_variation', 'form.variations_form', function( event, variation ) {

            var image_magnifier = variation.image_magnifier ? variation.image_magnifier : yith_wcmg_default_zoom;
            var image_src       = variation.image_src ? variation.image_src : yith_wcmg_default_image;
            yith_wcmg_zoom.attr('href', image_magnifier);
            yith_wcmg_image.attr('src', image_src);

        }).on( 'reset_image', function( event ) {

                yith_wcmg_zoom.attr('href', yith_wcmg_default_zoom);

        });

        $( 'form.variations_form .variations select').trigger('change');

    });

</script>
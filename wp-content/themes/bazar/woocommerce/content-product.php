<?php
/**
 * The template for displaying product content within loops.
 *
 * Override this template by copying it to yourtheme/woocommerce/content-product.php
 *
 * @author 		WooThemes
 * @package 	WooCommerce/Templates
 * @version     1.6.4
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

global $product, $woocommerce, $woocommerce_loop, $yit_is_page, $yit_is_feature_tab;

wp_enqueue_script( 'jquery-tipTip' );
if ( class_exists('WC_Compare_Hook_Filter') && method_exists('WC_Compare_Hook_Filter','woocp_print_scripts') ) { add_action('wp_footer', array('WC_Compare_Hook_Filter', 'woocp_print_scripts') ); }

// the classes for the <li> tag
$woocommerce_loop['li_class'] = array();

// add product id
$woocommerce_loop['li_class'][] = 'product-' . $product->id;

// Store loop count we're currently on
if ( empty( $woocommerce_loop['loop'] ) )
    $woocommerce_loop['loop'] = 0;

// Store column count for displaying the grid
if ( empty( $woocommerce_loop['columns'] ) )
    $woocommerce_loop['columns'] = apply_filters( 'loop_shop_columns', 4 );

// Ensure visibilty
if ( ! $product->is_visible() )
    return;

// Increase loop count
$woocommerce_loop['loop']++;

if ( !( isset( $woocommerce_loop['layout'] ) && ! empty( $woocommerce_loop['layout'] ) ) )
    $woocommerce_loop['layout'] = yit_get_option( 'shop-layout', 'with-hover' );

if ( !( isset( $woocommerce_loop['view'] ) && ! empty( $woocommerce_loop['view'] ) ) )
    $woocommerce_loop['view'] = yit_get_option( 'shop-view', 'grid' );

// remove the shortcode from the short description, in list view
remove_filter( 'woocommerce_short_description', 'do_shortcode', 11 );
add_filter( 'woocommerce_short_description', 'strip_shortcodes' );

// li classes
$woocommerce_loop['li_class'][] = 'product';
$woocommerce_loop['li_class'][] = 'group';
$woocommerce_loop['li_class'][] = $woocommerce_loop['view'];
$woocommerce_loop['li_class'][] = $woocommerce_loop['layout'];
if ( yit_get_option('shop-view-show-border') ) {
    $woocommerce_loop['li_class'][] = 'with-border';
}

// width of each product for the grid
$content_width = yit_get_sidebar_layout() == 'sidebar-no' ? 1170 : 870;
if ( isset( $yit_is_feature_tab ) && $yit_is_feature_tab ) $content_width -= 300;
$product_width = yit_shop_small_w() + 10 + 2;  // 10 = padding & 2 = border
$is_span = false;
if ( get_option('woocommerce_responsive_images') == 'yes' ) {
    $is_span = true;
    if ( yit_get_sidebar_layout() == 'sidebar-no' ) {
        if ( $product_width >= 0   && $product_width < 120 ) { $woocommerce_loop['li_class'][] = 'span1'; $woocommerce_loop['columns'] = 12; }
        elseif ( $product_width >= 120 && $product_width < 220 ) { $woocommerce_loop['li_class'][] = 'span2'; $woocommerce_loop['columns'] = 6;  }
        elseif ( $product_width >= 220 && $product_width < 320 ) { $woocommerce_loop['li_class'][] = 'span3'; $woocommerce_loop['columns'] = 4;  }
        elseif ( $product_width >= 320 && $product_width < 470 ) { $woocommerce_loop['li_class'][] = 'span4'; $woocommerce_loop['columns'] = 3;  }
        elseif ( $product_width >= 470 && $product_width < 620 ) { $woocommerce_loop['li_class'][] = 'span6'; $woocommerce_loop['columns'] = 2;  }
        else $is_span = false;

    } else {
        if ( $product_width >= 0   && $product_width < 150 ) { $woocommerce_loop['li_class'][] = 'span1'; $woocommerce_loop['columns'] = 9; }
        elseif ( $product_width >= 150 && $product_width < 620 ) { $woocommerce_loop['li_class'][] = 'span3'; $woocommerce_loop['columns'] = 3;  }
        else $is_span = false;

    }

} else {
    $grid = yit_get_span_from_width( $product_width );
    $woocommerce_loop['li_class'][] = 'span' . $grid;
    $product_width = yit_width_of_span( $grid );
}
if ( $yit_is_feature_tab || ! $is_span ) $woocommerce_loop['columns'] = floor( ( $content_width + 30 ) / ( $product_width + 30 ) );

// put the percentual width
// if ( ! $is_span ) {
//     $woocommerce_loop['li_class'][] = 'no-span';
//     $perc = ( 100 - $woocommerce_loop['columns'] * 3 ) / $woocommerce_loop['columns'];
//     $style_attr = " style='width:$perc%;'";
// }

$woocommerce_loop['columns'] = apply_filters( 'loop_shop_columns', $woocommerce_loop['columns'] );

// first and last
if ( $woocommerce_loop['loop'] % $woocommerce_loop['columns'] == 0 )         $woocommerce_loop['li_class'][] = 'last';
elseif ( ( $woocommerce_loop['loop'] - 1 ) % $woocommerce_loop['columns'] == 0 ) $woocommerce_loop['li_class'][] = 'first';

// title classes
$title_class = array();
if ( !yit_get_option('shop-view-show-title') ) $title_class[] = 'hide';
if (  yit_get_option('shop-title-uppercase') ) $title_class[] = 'upper';
$title_class = empty( $title_class ) ? '' : ' class="' . implode( ' ', $title_class ) . '"';

// if css3
if ( yit_ie_version() == -1 || yit_ie_version() > 9 ) $woocommerce_loop['li_class'][] = 'css3';

// configuration
if ( ! yit_get_option('shop-view-show-price') ) remove_action( 'woocommerce_after_shop_loop_item_title', 'woocommerce_template_loop_price' );

// force open hover
if ( yit_get_option( 'shop-open-hover' ) ) $woocommerce_loop['li_class'][] = 'force-open-hover';

// open the hover on mobile
if ( yit_get_option( 'responsive-open-hover' ) ) $woocommerce_loop['li_class'][] = 'open-on-mobile';

// open the hover on mobile
if ( yit_get_option( 'responsive-force-classic' ) && $woocommerce_loop['layout'] == 'with-hover' ) $woocommerce_loop['li_class'][] = 'force-classic-on-mobile';
?>
<li class="<?php echo implode( ' ', $woocommerce_loop['li_class'] ) ?>">

    <div class="product-thumbnail group">

        <?php do_action( 'woocommerce_before_shop_loop_item' ); ?>

        <div class="thumbnail-wrapper">
            <?php
            /**
             * woocommerce_before_shop_loop_item_title hook
             *
             * @hooked woocommerce_show_product_loop_sale_flash - 10
             * @hooked woocommerce_template_loop_product_thumbnail - 10
             */
            do_action( 'woocommerce_before_shop_loop_item_title' );
            ?>
        </div>

        <?php if ( $woocommerce_loop['layout'] == 'classic' && yit_get_option('shop-view-show-shadow') ) : ?>
            <div class="product-shadow"></div>
        <?php endif; ?>

        <div class="product-meta" <?php if ($woocommerce_loop['view'] == 'list') echo 'style="width: ' . yit_shop_small_w() . 'px;"'; ?>>
            <?php if ( yit_get_option('shop-view-show-title') ): ?>
                <h3<?php echo $title_class ?>><?php the_title(); ?></h3>
            <?php endif ?>

            <?php
            /**
             * woocommerce_after_shop_loop_item_title hook
             *
             * @hooked woocommerce_template_loop_price - 10
             */
            do_action( 'woocommerce_after_shop_loop_item_title' );
            ?>
        </div>

        <?php do_action( 'woocommerce_after_shop_loop_item' ); ?>

    </div>

    <?php if ( yit_get_option('shop-view-show-description') ) : ?>
        <div class="description">
            <?php woocommerce_template_single_excerpt(); ?>
            <a href="<?php the_permalink() ?>" class="view-detail"><?php echo apply_filters('yit_details_button', __( 'Details', 'yit' )) ?></a>
            <?php do_action( 'yit_additional_info_on_list_view' ); ?>
        </div>
    <?php endif; ?>

</li>
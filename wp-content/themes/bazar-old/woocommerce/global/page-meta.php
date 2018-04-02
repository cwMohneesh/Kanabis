<?php
/**
 * Content Wrappers
 */

if ( is_product() ) return;

$tag_title      = apply_filters( 'yit_page_meta_title_tag', 'h1' );
?>
            <!-- START PAGE META -->
            <div id="page-meta" class="group<?php if ( is_product() ) echo ' span12' ?>">
                <?php if ( ! is_single() && ( ( !is_product_category() && yit_get_option('shop-products-title') ) || ( is_product_category() && yit_get_option('shop-category-title') ) ) ) : ?>
        		<<?php echo $tag_title ?> class="product-title page-title"><?php woocommerce_page_title() ?></<?php echo $tag_title ?>>
        		<?php endif; ?>
        		                                     
        		<?php do_action( 'shop_page_meta' ) ?>
        		
        		<?php if ( is_single() && yit_get_option('shop-show-back') ) : ?>
        			<div class="back-shop"> <a href="<?php echo get_permalink( wc_get_page_id( 'shop' ) ) ?>">&lsaquo; <?php echo apply_filters( 'yit_back_shop_link', __( 'Back to the shop', 'yit' ) ); ?></a> </div>
				<?php endif; ?>
        	</div>
        	<!-- END PAGE META -->   
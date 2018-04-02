<?php
/**
 * Content wrappers
 *
 * @author 		WooThemes
 * @package 	WooCommerce/Templates
 * @version     1.6.4
 */
?>
<div id="primary" class="<?php yit_sidebar_layout() ?>">
    <div class="container group">
	    <div class="row">
	        <?php do_action( 'yit_before_content' ) ?>
	        <!-- START CONTENT -->
	        <div id="content-shop" class="span<?php echo yit_get_sidebar_layout() == 'sidebar-no' && ! is_single() ? 12 : ( yit_get_option( 'shop-sidebar-width' ) == '2' ? 10 : 9 ) ?> content group">
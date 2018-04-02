<?php
/**
 * Product loop sale flash
 *
 * @author 		WooThemes
 * @package 	WooCommerce/Templates
 * @version     1.6.4
 */
 
global $post, $product;

$is_active     = get_post_meta( $product->id, '_active_custom_onsale', true );
$preset        = get_post_meta( $product->id, '_preset_onsale_icon', true );
$custom        = get_post_meta( $product->id, '_custom_onsale_icon', true );
$regular_price = get_post_meta( $product->id, '_regular_price', true );
$regular_price_var = get_post_meta( $product->id, '_min_variation_price', true );

//if ( !$is_active || !$product->is_on_sale() ) return; 
       
$img = '';                                                               

// set a preset image 
if ( $is_active && $is_active != 'no' ) {
    switch ( $preset ) {
        case 'onsale' : $img = get_stylesheet_directory_uri() . '/woocommerce/images/bullets/sale.png'; break;
        case '-50%'   : $img = get_stylesheet_directory_uri() . '/woocommerce/images/bullets/50.png'; break;
        case '-25%'   : $img = get_stylesheet_directory_uri() . '/woocommerce/images/bullets/25.png'; break;
        case '-10%'   : $img = get_stylesheet_directory_uri() . '/woocommerce/images/bullets/10.png'; break;
        case 'custom' : $img = $custom; break;        
    }
    
} elseif ( $product->is_on_sale() && ( !empty( $regular_price ) || !empty( $regular_price_var ) ) ) {

    $img = 'sale.png';
    if( function_exists('icl_get_default_language') ){
        if( ICL_LANGUAGE_CODE == icl_get_default_language() )  $img = 'sale.png';
        else $img = icl_get_default_language().'_sale.png';
    }

    $img = get_stylesheet_directory_uri() . '/woocommerce/images/bullets/'.$img;
}

if ( empty( $img ) ) return;


$image = apply_filters('woocommerce_sale_flash', yit_image( "echo=no&src=$img&getimagesize=1&class=onsale&alt=" . __( 'On sale!', 'yit' ) ), $post, $product );

echo $image;
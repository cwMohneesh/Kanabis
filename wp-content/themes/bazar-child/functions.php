<?php
/**
 * Your Inspiration Themes
 * 
 * @package WordPress
 * @subpackage Your Inspiration Themes
 * @author Your Inspiration Themes Team <info@yithemes.com>
 *
 * This source file is subject to the GNU GENERAL PUBLIC LICENSE (GPL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-3.0.txt
 */
 /**
 * Proper way to enqueue scripts and styles
 */


add_filter( 'woocommerce_currencies', 'add_my_currency' );
function add_my_currency( $currencies ) {
     $currencies['INR'] = __( 'Indian Rupee', 'woocommerce' );
     return $currencies;
}
add_filter('woocommerce_currency_symbol', 'add_my_currency_symbol', 10, 2);
function add_my_currency_symbol( $currency_symbol, $currency ) {
     switch( $currency ) {
          //case 'INR': $currency_symbol = 'Rs '; break;
		  case 'INR': $currency_symbol = '&#8377;'; break;
     }
     return $currency_symbol;
}

/* size chart added by mohneesh */
add_filter('woocommerce_after_add_to_cart_button', 'add_size_chart');
function add_size_chart() {
	global $post, $woocommerce;
	$linkSizeChart = strtolower(trim(get_post_meta(get_the_ID(), 'size_chart', true )));
	$theme_uri = get_template_directory_uri().'-child';	
	if($linkSizeChart == 'kanabis-fashion'){
		echo $im = '<h3 class="clr"><a class="thumb img cboxElement" href="'.$theme_uri.'/size-chart.html" rel="prettyphoto">View Size Chart</a></h3>';
	}
	if($linkSizeChart == 'kanabis-basics'){
		echo $im = '<h3 class="clr"><a class="thumb img cboxElement" href="'.$theme_uri.'/basics-size-chart.html" rel="prettyphoto">View Size Chart</a></h3>';
	}	
}

// woocommerce tabs 
add_filter( 'woocommerce_product_tabs', 'woo_remove_product_tabs', 98 );
function woo_remove_product_tabs( $tabs ) {
    unset( $tabs['description'] );      	// Remove the description tab
    //unset( $tabs['reviews'] ); 			// Remove the reviews tab
    //unset( $tabs['additional_information'] );  	// Remove the additional information tab
    return $tabs;
}
add_filter( 'woocommerce_product_tabs', 'woo_new_product_tab' );
function woo_new_product_tab( $tabs ) {
	// Adds the new tab
	$tabs['test_tab'] = array(
		'title' 	=> __( 'Instructions', 'woocommerce' ),
		'priority' 	=> 12,
		'callback' 	=> 'woo_new_product_tab_content'
	);
	return $tabs;
}
function woo_new_product_tab_content() {
	// The new tab content ?>
	<h2>Wash and care instructions</h2>
	<ul>
		<li>Do not machine or hand-wash</li> 
		<li>PU can be cleaned with soft damp cloth </li> 
		<li>Use the paper stuffing and shoe-holder to retain shape</li> 
		<li>These are casual, fashionable shoes- dirty beyond a point is a hint to buy a new pair </li> 
	</ul> 
<?php	
}


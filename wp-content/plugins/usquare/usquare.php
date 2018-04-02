<?php   
/*
Plugin Name: uSquare
Plugin URI: http://codecanyon.net/item/usquare-universal-responsive-grid-for-wordpress/3352677
Description: HTML grid for items
Author: ShindiriStudio
Version: 1.6.8
Author URI: http://codecanyon.net/item/usquare-universal-responsive-grid-for-wordpress/3352677
*/

$usquare_version='1.6.8';

if (isset($_GET['get_version'])) {echo $usquare_version; exit;}

if (!class_exists("usquareAdmin")) {
	require_once dirname( __FILE__ ) . '/usquare_class.php';	
	$usquare = new usquareAdmin (__FILE__, 'uSquare', $usquare_version);
}

?>
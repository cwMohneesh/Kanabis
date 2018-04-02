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

yit_meta_tags() ?>

<title><?php yit_title() ?></title>

<?php /*
<!-- RESET STYLESHEET -->
<link rel="stylesheet" type="text/css" media="all" href="<?php echo YIT_THEME_CSS_URL . '/reset.css'; ?>" />
*/ ?>

<!-- BOOTSTRAP STYLESHEET -->
<link rel="stylesheet" type="text/css" media="all" href="<?php echo yit_ssl_url( YIT_CORE_ASSETS_URL ) . '/css/reset-bootstrap.css'; ?>" />
<!-- MAIN THEME STYLESHEET -->
<link rel="stylesheet" type="text/css" media="all" href="<?php bloginfo( 'stylesheet_url' ); ?>" />

<!-- PINGBACK & WP STANDARDS -->
<link rel="pingback" href="<?php bloginfo( 'pingback_url' ); ?>" />

<?php

global $is_IE;

yit_enqueue_style( 10, 'font-awesome', YIT_CORE_ASSETS_URL . '/css/font-awesome.css', false, '2.0', 'all' );
if( yit_ie_version() == 7 )
    { yit_enqueue_style( 10, 'font-awesome-ie7', YIT_CORE_ASSETS_URL . '/css/font-awesome-ie7.css', false, '2.0', 'all' ); }

// colorbox - easing - flexslider - images loaded - tiptip
//wp_enqueue_script( 'jquery-colorbox', get_template_directory_uri() .'/theme/assets/js/jquery.colorbox-min.js', array('jquery'), '1.0', true);
//wp_enqueue_script( 'jquery-easing', get_template_directory_uri() .'/theme/assets/js/jquery.easing.js', array('jquery'), '1.3', true);
//wp_enqueue_script( 'jquery-flexslider', get_template_directory_uri() .'/theme/assets/js/jquery.flexslider-min.js', array('jquery'), '', true);
//wp_enqueue_script( 'jquery-imagesLoaded', get_template_directory_uri() .'/theme/assets/js/jquery.imagesLoaded.js' );
//wp_register_script( 'jquery-tipTip', get_template_directory_uri() .'/theme/assets/js/jquery.tipTip.minified.js', array('jquery'), '1.0', true);
//wp_enqueue_script( 'jquery-tipTip' );
wp_enqueue_script( 'jquery-colorbox-easing-flexslider-imagesloaded-tiptip', get_template_directory_uri() .'/theme/assets/js/jquery.commonlibraries.js', array('jquery'), '1.1', true);

//place holder
if( $is_IE ) {
    wp_enqueue_script( 'jquery-placeholder-plugin', YIT_CORE_ASSETS_URL . '/js/jquery.placeholder.js', array('jquery'), '1.0', true );
}

// masonry
wp_register_script( 'yit-jquery-masonry', get_template_directory_uri() .'/theme/assets/js/jquery.masonry.js', array('jquery'), '1.0', true);

// carouFredSel
wp_register_script( 'caroufredsel', get_template_directory_uri() .'/theme/assets/js/jquery.carouFredSel-6.1.0-packed.js' );

// jquery cookie
wp_register_script( 'jquery-cookie', YIT_CORE_ASSETS_URL . '/js/jq-cookie.js', array('jquery') );

wp_register_script( 'touch-swipe', get_template_directory_uri() .'/theme/assets/js/jquery.touchSwipe.min.js' );
wp_register_script( 'ba-throttle-debounce', get_template_directory_uri() .'/theme/assets/js/jquery.ba-throttle-debounce.min.js' );
wp_register_script( 'mousewheel', get_template_directory_uri() .'/theme/assets/js/jquery.mousewheel.min.js' );

// BlackAndWhite
wp_register_script( 'black-and-white', get_template_directory_uri() .'/theme/assets/js/jQuery.BlackAndWhite.js' );

// Resize
wp_register_script( 'resize', get_template_directory_uri() .'/theme/assets/js/jquery.resize.js', array('jquery'), '1.0', true);

// Selectbox
wp_register_script( 'jquery-selectbox', get_template_directory_uri() .'/theme/assets/js/jquery.selectbox.js', array('jquery'), '0.2', true);


//Custom Javascript
//wp_enqueue_script( 'yit-browser', YIT_CORE_ASSETS_URL . '/js/yit/yit_browser.js', array('jquery'), '1.0', true);
wp_enqueue_script( 'yit-layout', get_template_directory_uri() .'/theme/assets/js/yit/jquery.layout.js', array('jquery'), '1.0', true);

if( is_child_theme() && file_exists( get_stylesheet_directory() . '/js/jquery.custom.js' ) ) {
    wp_enqueue_script( 'jquery-custom', get_stylesheet_directory_uri() . '/js/jquery.custom.js' , array( 'jquery' ), '1.0', true );
} else {
    wp_enqueue_script( 'jquery-custom', get_template_directory_uri() . '/js/jquery.custom.js' , array( 'jquery' ), '1.0', true );
}


$jquery_custom_l10n = array(
    'map_close' => __( '[x] Close', 'yit' ),
    'map_open' => __( '[x] Open', 'yit' )
);
wp_localize_script( 'jquery-custom', 'l10n_handler', $jquery_custom_l10n );

/* We add some JavaScript to pages with the comment form
 * to support sites with threaded comments (when in use).
 */
if ( is_singular() && get_option( 'thread_comments' ) )
    wp_enqueue_script( 'comment-reply' );

// Enqueue blog styles
do_action( 'yit_enqueue_blog_stuffs' );

yit_enqueue_style( 10, 'colorbox', get_template_directory_uri() .'/theme/assets/css/colorbox.css' );
yit_enqueue_style( 10, 'comments', YIT_THEME_TEMPLATES_URL . '/comments/css/style.css' );
?>

<script type="text/javascript">
	var yit_responsive_menu_type = "<?php echo yit_get_option('responsive-menu'); ?>";
	var yit_responsive_menu_text = "<?php _e('NAVIGATE TO...','yit'); ?>";
</script>

<!-- [favicon] begin -->
<link rel="shortcut icon" type="image/x-icon" href="<?php yit_favicon() ?>" />
<link rel="icon" type="image/x-icon" href="<?php yit_favicon() ?>" />
<!-- [favicon] end -->

<!-- Touch icons more info: http://mathiasbynens.be/notes/touch-icons -->
<?php yit_print_mobile_favicons() ;?>

<!-- Feed RSS2 URL -->
<link rel="alternate" type="application/rss+xml" title="<?php bloginfo('name') ?> Feed" href="<?php bloginfo('rss2_url')?>" />
<link rel="alternate" type="application/rss+xml" title="<?php bloginfo('name') ?> Comments Feed" href="<?php bloginfo('comments_rss2_url')?>" />
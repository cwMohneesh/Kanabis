<?php

if( ! defined("DVKSS_VERSION") ) {
    header( 'Status: 403 Forbidden' );
    header( 'HTTP/1.1 403 Forbidden' );
    exit;
}

/**
* Returns a string containing the sharing buttons HTML 
*
* @param array $args
* @return string 
*/
function dvk_social_sharing( $args = array() ) {

    $opts = dvkss_get_options();
	$defaults = array(
		'element' => 'p',
		'social_options' => 'twitter, facebook, googleplus',
        'twitter_username' => $opts['twitter_username'],
        'before_text' => $opts['before_text'],
        'twitter_text' => __( 'on Twitter', 'dvk-social-sharing' ),
        'facebook_text' => __( 'on Facebook', 'dvk-social-sharing' ),
        'googleplus_text' => __( 'on Google+', 'dvk-social-sharing' ),
	);

	// create final arguments array
	$args = wp_parse_args( $args, $defaults );
	$args['social_options'] = array_filter( array_map( 'trim', explode( ',', $args['social_options'] ) ) );

	$title = urlencode( get_the_title() );
	$url = urlencode( get_permalink() );

	ob_start();
	?>
	<!-- Social Sharing By Danny v<?php echo DVKSS_VERSION; ?> - http://wordpress.org/plugins/dvk-social-sharing/ -->
    <<?php echo $args['element']; ?> class="dvk-social-sharing">

        <?php if( ! empty( $args['before_text'] ) ) { ?>
			<span class="ss-ask"><?php echo $args['before_text']; ?></span>
		<?php } ?>

        <?php foreach($args['social_options'] as $o) {
        	switch($o) {
        		case 'twitter':
        			?><a rel="external nofollow" class="ss-twitter" href="http://twitter.com/intent/tweet/?text=<?php echo $title; ?>&url=<?php echo $url; ?><?php if( ! empty( $args['twitter_username'] ) ) {  echo '&via=' . sanitize_text_field( $args['twitter_username'] ); } ?>" target="_blank"><span class="ss-icon-twitter"></span><span class="ss-text"><?php echo $args['twitter_text']; ?></span></a> <?php
        		break;

        		case 'facebook':
        			?><a rel="external nofollow" class="ss-facebook" href="http://www.facebook.com/sharer/sharer.php?s=100&p[url]=<?php echo $url; ?>&p[title]=<?php echo $title; ?>" target="_blank" ><span class="ss-icon-facebook"></span><span class="ss-text"><?php echo $args['facebook_text']; ?></span></a> <?php
        		break;

        		case 'googleplus':
        			?><a rel="external nofollow" class="ss-googleplus" href="https://plus.google.com/share?url=<?php echo $url; ?>" target="_blank" ><span class="ss-icon-googleplus"></span><span class="ss-text"><?php echo $args['googleplus_text']; ?></span></a> <?php
        		break;
        	}
        } ?>
    </<?php echo $args['element']; ?>>
    <!-- / Social Sharing By Danny -->
   <?php
  	$output = ob_get_contents();
	ob_end_clean();

	return $output;
}


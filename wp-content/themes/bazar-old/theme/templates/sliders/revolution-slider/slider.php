<?php
/**
 * @package WordPress
 * @subpackage Your Inspiration Themes
 */                         
 
global $is_primary;                     

$sliderID = $this->get('slider_name');
$the_slider = new RevSlider();
$the_slider->initByMixed($sliderID);            

$slider_class = '';
$slider_class .= yit_slide_get('align') != '' ? ' align' . yit_slide_get('align') : '';
$slider_class .= ' ' . $the_slider->getParam('slider_type');

$is_fixed = false;
if ( ! $is_primary && in_array( $the_slider->getParam('slider_type'), array( 'fixed', 'responsitive' ) ) ) $is_fixed = true;

if ( $is_fixed && ! has_action( 'yit_after_header', 'yit_slider_space' ) ) add_action( 'yit_after_header', 'yit_slider_space' );

// text align
$slider_text = yit_slide_get( 'slider_text' );
if ( $is_primary && $the_slider->getParam('slider_type') == 'fullwidth' ) $slider_text = '';
if ( !empty( $slider_text ) ) $slider_class .= ' align' . ( yit_slide_get( 'slider_align' ) == 'left' ? 'right' : 'left' ); 
?>
 
<!-- START SLIDER -->
<div class="revolution-wrapper<?php if ( $the_slider->getParam('slider_type') != 'fullwidth' ) echo ' container'; ?>">
    <div id="<?php echo $slider_id ?>"<?php yit_slider_class($slider_class) ?>> 
        <div class="shadowWrapper">
            <?php echo do_shortcode('[rev_slider ' . yit_slide_get( 'slider_name' ) . ']'); ?>
        </div>          
    </div>      
    
    <?php if ( !empty( $slider_text ) ) : ?>
    <div class="revolution-slider-text">
        <?php echo yit_clean_text( $slider_text ) ?>
    </div>
    <div class="clear"></div>
    <?php endif; ?>
</div>
<!-- END SLIDER -->
<?php
/**
 * YITH WooCommerce Ajax Search template
 *
 * @author Your Inspiration Themes
 * @package YITH WooCommerce Ajax Search
 * @version 1.0.0
 */

if ( !defined( 'YITH_WCAS' ) ) { exit; } // Exit if accessed directly


wp_enqueue_script('yith_wcas_jquery-autocomplete' );

?>

<div class="yith-ajaxsearchform-container widget widget_search_mini">

    <form role="search" method="get" id="yith-ajaxsearchform" action="<?php echo esc_url( home_url( '/'  ) ) ?>"  class="search_mini">
        <div>
            <input type="search" value="<?php echo get_search_query() ?>" name="s" id="search_mini" placeholder="<?php echo get_option('yith_wcas_search_input_label') ?>" />
            <input type="hidden" name="post_type" value="product" />
            <input type="submit" id="mini-search-submit" value="" />
        </div>
    </form>

</div>
<script type="text/javascript">
    jQuery(function($){
        var search_loader_url = <?php echo apply_filters('yith_wcas_ajax_search_icon', 'woocommerce_params.ajax_loader_url') ?>;
        var bg = $('#search_mini').css('background');

        $('#search_mini').autocomplete({
            minChars: <?php echo get_option('yith_wcas_min_chars') * 1; ?>,
            appendTo: '.yith-ajaxsearchform-container',
            serviceUrl: woocommerce_params.ajax_url + '?action=yith_ajax_search_products',
            onSearchStart: function(){
                $(this).css('background', 'url('+search_loader_url+') no-repeat right center');
            },
            onSearchComplete: function(){
                $(this).css('background', bg);
            },
            onSelect: function (suggestion) {
                if( suggestion.id != -1 ) {
                    window.location.href = suggestion.url;
                }
            }
        });
    });
</script>
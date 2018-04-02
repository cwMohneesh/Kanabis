<?php
/*
Plugin Name: WP Social Buttons
Plugin URI: http://www.mrwebsolution.in/
Description: "wp-social-buttons" is the very simple plugin for add to social buttons on your site!.
Author: Raghunath
Author URI: http://raghunathgurjar.wordpress.com
Version: 1.2
*/

/*  Copyright 2014  Raghunath Gurjar  (email : raghunath.0087@gmail.com)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License, version 2, as 
    published by the Free Software Foundation.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

/*
 * WP Social Buttons
 * @add_action
 * @register_setting
 * 
 * */
//Admin "WP Social Buttons" Menu Item
add_action('admin_menu','wpsb_sidebar_menu');

function wpsb_sidebar_menu(){

add_options_page('WP Social Buttons','WP Social Buttons','manage_options','wpsb-settings','wpsb_sidebar_admin_option_page');

}

//Define Action for register "WP Social Buttons" Options
add_action('admin_init','wpsb_sidebar_init');


//Register "WP Social Buttons" options
function wpsb_sidebar_init(){
	register_setting('wpsb_sidebar_options','wpsb_active');
	register_setting('wpsb_sidebar_options','wpsb_position');
	register_setting('wpsb_sidebar_options','wpsb_top_margin');
	register_setting('wpsb_sidebar_options','wpsb_delayTimeBtn');
	register_setting('wpsb_sidebar_options','wpsb_page_hide_home');
	register_setting('wpsb_sidebar_options','wpsb_page_hide_post');
	register_setting('wpsb_sidebar_options','wpsb_page_hide_page');
	register_setting('wpsb_sidebar_options','wpsb_fpublishBtn');	
	register_setting('wpsb_sidebar_options','wpsb_tpublishBtn');	
	register_setting('wpsb_sidebar_options','wpsb_gpublishBtn');	
	register_setting('wpsb_sidebar_options','wpsb_ppublishBtn');	
	register_setting('wpsb_sidebar_options','wpsb_lpublishBtn');
	register_setting('wpsb_sidebar_options','wpsb_fb_url');
	register_setting('wpsb_sidebar_options','wpsb_tw_url');
	register_setting('wpsb_sidebar_options','wpsb_li_url');		
	register_setting('wpsb_sidebar_options','wpsb_gp_url');	
	register_setting('wpsb_sidebar_options','wpsb_pin_url');		
	register_setting('wpsb_sidebar_options','wpsb_deactive_for_mob');	

} 

// Add settings link to plugin list page in admin
        function wpsb_add_settings_link( $links ) {
            $settings_link = '<a href="options-general.php?page=wpsb-settings">' . __( 'Settings', 'wpsb' ) . '</a>';
            array_unshift( $links, $settings_link );
            return $links;
        }

        $plugin = plugin_basename( __FILE__ );
        add_filter( "plugin_action_links_$plugin", 'wpsb_add_settings_link' );

/* 

*Display Options form for WP Social Buttons 

*/

function wpsb_sidebar_admin_option_page(){ ?>

	<div style="width: 80%; padding: 10px; margin: 10px;"> 

	<h1>WP Social Buttons Settings</h1>

<!-- Start Options Form -->

	<form action="options.php" method="post" id="wpsb-sidebar-admin-form">
		
		<div id="pwa-tab-menu"><a id="pwa-general" class="pwa-tab-links active" >General</a> <a  id="pwa-advance" class="pwa-tab-links">Advance Settings</a> <a  id="pwa-support" class="pwa-tab-links">Support</a> </div>

	<div class="pwa-setting">
	<!-- General Setting -->	
	<div class="first pwa-tab" id="div-pwa-general">
	<h2>General Settings</h2>
	<p><label>Enable:</label><input type="checkbox" id="wpsb_active" name="wpsb_active" value='1' <?php if(get_option('wpsb_active')!=''){ echo ' checked="checked"'; }?>/></p>
	
	<p><label><?php echo 'Siderbar Position:';?></label>
				<select id="wpsb_position" name="wpsb_position" >
				<option value="left" <?php if(get_option('wpsb_position')=='left'){echo 'selected="selected"';}?>>Left</option>
				<option value="right" <?php if(get_option('wpsb_position')=='right'){echo 'selected="selected"';}?>>Right</option>
				</select></p>
	<p><label><?php echo 'Delay Time: '; ?><label> <input type="text" name="wpsb_delayTimeBtn" id="wpsb_delayTimeBtn" value="<?php echo get_option('wpsb_delayTimeBtn')?get_option('wpsb_delayTimeBtn'):2000;?>"  size="15"><br><i>Publish share buttons after given time(millisecond)</i></p>	
	
	</div>
	
	<!-- Advance Setting -->	
	<div class="pwa-tab" id="div-pwa-advance">
	<h2>Advance Settings</h2>
   <p><label><?php echo 'Top Margin:';?></label><input type="text" id="wpsb_top_margin" name="wpsb_top_margin" value="<?php echo get_option('wpsb_top_margin'); ?>" placeholder="10% OR 10px" size="15"/></p>
   <p><label><?php echo 'Publish Buttons:';?></label></p>
   <p><label>&nbsp;</label><input type="checkbox" id="publish1" value="yes" name="wpsb_fpublishBtn" <?php if(get_option('wpsb_fpublishBtn')=='yes'){echo 'checked="checked"';}?>/> <b>Facebook Button</b><br>
				<input type="checkbox" id="publish2" name="wpsb_tpublishBtn" value="yes" <?php if(get_option('wpsb_tpublishBtn')=='yes'){echo 'checked="checked"';}?>/> <b>Twitter Button</b><br>
				<input type="checkbox" id="publish3" name="wpsb_gpublishBtn" value="yes" <?php if(get_option('wpsb_gpublishBtn')=='yes'){echo 'checked="checked"';}?>/> <b>Google Button</b><br>
				<input type="checkbox" id="publish4" name="wpsb_lpublishBtn" value="yes" <?php if(get_option('wpsb_lpublishBtn')=='yes'){echo 'checked="checked"';}?>/> <b>Linkdin Button</b><br>
				<input type="checkbox" id="publish6" name="wpsb_ppublishBtn" value="yes" <?php if(get_option('wpsb_ppublishBtn')=='yes'){echo 'checked="checked"';}?>/> <b>Pinterest Button</b></p>
	
	<p><label>&nbsp;</label>
				<td><input type="checkbox" id="wpsb_deactive_for_mob" name="wpsb_deactive_for_mob" value="yes" <?php if(get_option('wpsb_deactive_for_mob')=='yes'){echo 'checked="checked"';}?>/> <strong><?php _e('Disable For Mobile','wpsb');?></strong>
			</p>
	</div>

	<!-- Support -->
	<div class="last author pwa-tab" id="div-pwa-support">
	<h2>Plugin Support</h2>
	
	<p><a href="https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=WN785E5V492L4" target="_blank" style="font-size: 17px; font-weight: bold;"><img src="https://www.paypal.com/en_US/i/btn/btn_donate_LG.gif" title="Donate for this plugin"></a></p>
	
	<p><strong>Plugin Author:</strong><br><img src="<?php echo  plugins_url( 'images/raghu.jpg' , __FILE__ );?>" width="75" height="75"><br><a href="http://raghunathgurjar.wordpress.com" target="_blank">Raghunath Gurjar</a></p>
	<p><a href="mailto:raghunath.0087@gmail.com" target="_blank" class="contact-author">Contact Author</a></p>
	<p><strong>My Other Plugins:</strong><br>
	<ul>
		<li><a href="https://wordpress.org/plugins/protect-wp-admin/" target="_blank">Protect WP-Admin</a></li>
		<li><a href="https://wordpress.org/plugins/custom-share-buttons-with-floating-sidebar" target="_blank">Custom Share Buttons with Floating Sidebar</a></li>
		<li><a href="https://wordpress.org/plugins/simple-testimonial-rutator/" target="_blank">Simple Testimonial Rutator</a></li>
		<li><a href="https://wordpress.org/plugins/wp-easy-recipe/" target="_blank">WP Easy Recipe</a></li>
		<li><a href="https://wordpress.org/plugins/wp-youtube-gallery/" target="_blank">WP Youtube Gallery</a></li>
		</ul></p>
	</div>

	</div>
	
<span class="submit-btn"><?php echo get_submit_button('Save Settings','button-primary','submit','','');?></span>

    <?php settings_fields('wpsb_sidebar_options'); ?>
	
	</form>

<!-- End Options Form -->
	</div>

<?php
}

require dirname(__FILE__).'/wpsb-class.php';

/** add js into admin footer */
add_action('admin_footer','init_wpsb_admin_scripts');
function init_wpsb_admin_scripts()
{
wp_register_style( 'wpsb_admin_style', plugins_url( 'css/wpsb-admin.css',__FILE__ ) );
wp_enqueue_style( 'wpsb_admin_style' );

echo $script='<script type="text/javascript">
	/* WP Social Buttons js for admin */
	jQuery(document).ready(function(){
		jQuery(".pwa-tab").hide();
		jQuery("#div-pwa-general").show();
	    jQuery(".pwa-tab-links").click(function(){
		var divid=jQuery(this).attr("id");
		jQuery(".pwa-tab-links").removeClass("active");
		jQuery(".pwa-tab").hide();
		jQuery("#"+divid).addClass("active");
		jQuery("#div-"+divid).fadeIn();
		})
		})
	</script>';

	}	
/* 

*Delete the options during disable the plugins 

*/

if( function_exists('register_uninstall_hook') )

	register_uninstall_hook(__FILE__,'wpsb_sidebar_uninstall');   

//Delete all Custom Tweets options after delete the plugin from admin
function wpsb_sidebar_uninstall(){
	delete_option('wpsb_active');
	delete_option('wpsb_position');
	delete_option('wpsb_top_margin');
	delete_option('wpsb_page_hide_home');
	delete_option('wpsb_page_hide_post');
	delete_option('wpsb_page_hide_page');
	delete_option('wpsb_fpublishBtn');
	delete_option('wpsb_tpublishBtn');
	delete_option('wpsb_gpublishBtn');	
	delete_option('wpsb_ppublishBtn');	
	delete_option('wpsb_lpublishBtn');
	delete_option('wpsb_fb_url');
	delete_option('wpsb_tw_url');
	delete_option('wpsb_li_url');		
	delete_option('wpsb_gp_url');	
	delete_option('wpsb_pin_url');		
	delete_option('wpsb_deactive_for_mob');	

} 
?>

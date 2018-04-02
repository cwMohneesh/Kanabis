<?php
require_once dirname( __FILE__ ) . '/usquare_functions.php';
require_once dirname( __FILE__ ) . '/usquare_image_functions.php';

$fonts_main_array = usquare_get_font_array($this->settings, $this->path);
$fonts_assoc_array = $fonts_main_array['assoc'];
$fonts_num_array = $fonts_main_array['num'];

?>

<div class="wrap">
	<?php 
	$title = '';
	require_once dirname( __FILE__ ) . '/usquare_settings.php';
	$default_settings=$settings;
	global $wpdb;
	if(isset($_GET['id'])) {
		global $wpdb;
		$usquare = $wpdb->get_results('SELECT * FROM ' . $wpdb->base_prefix . 'usquare WHERE id='.$_GET['id']);
		$usquare = $usquare[0];
		$pageName = 'Edit uSquare';
	}
	else {
		$pageName = 'New uSquare';
		$usquare = new stdClass();
		$usquare->name='Enter here a name for this uSquare';
		$usquare->settings='';
		$usquare->items='';
	}
	$title = $usquare->name;
	$title = str_replace('"', '&quot;', $title);
	if ($usquare->settings!='') {
		foreach(explode('||',$usquare->settings) as $val) {
			$expl = explode('::',$val);
			$settings[$expl[0]] = $expl[1];
			$settings[$expl[0]] = str_replace('#|#|', '||', $settings[$expl[0]]);
			$settings[$expl[0]] = str_replace('#:#:', '::', $settings[$expl[0]]);
		}
	}

	$settings['item-image-width']=$settings['item-width']/2;
	$settings['item-image-height']=$settings['item-height'];
	
	if ($settings['grayscale']==1) $settings['grayscale']='checked="checked" ';
	if ($settings['push_content_below']==1) $settings['push_content_below']='checked="checked" ';
	
	if (isset($settings['title-font-weight']) && $settings['title-font-style']) {
		$settings['title-font-variant']=usquare_generate_font_variants_for_listbox_from_weight_and_style($settings['title-font-weight'], $settings['title-font-style']);
	}
	if (isset($settings['description-font-weight']) && $settings['description-font-style']) {
		$settings['description-font-variant']=usquare_generate_font_variants_for_listbox_from_weight_and_style($settings['description-font-weight'], $settings['description-font-style']);
	}
	if (isset($settings['content-font-weight']) && $settings['content-font-style']) {
		$settings['content-font-variant']=usquare_generate_font_variants_for_listbox_from_weight_and_style($settings['content-font-weight'], $settings['content-font-style']);
	}
	if (isset($settings['info-font-weight']) && $settings['info-font-style']) {
		$settings['info-font-variant']=usquare_generate_font_variants_for_listbox_from_weight_and_style($settings['info-font-weight'], $settings['info-font-style']);
	}
/*
	echo $settings['title-font-variant']."<br />";
	echo $settings['description-font-variant']."<br />";
	echo $settings['content-font-variant']."<br />";
	echo $settings['info-font-variant']."<br />";
	exit;
*/	
	?>
	
	
	<input type="hidden" id="plugin-url" value="<?php echo $this->url; ?>"/>
	<h2><?php echo $pageName; ?>
		<a href="<?php echo admin_url( "admin.php?page=usquare" ); ?>" class="add-new-h2">Cancel</a>
	</h2>
	
	<div class="form_result"></div>
	<form name="post_form"  method="post" id="post_form">
	<input type="hidden" name="usquare_id" id="usquare_id" value="<?php echo $_GET['id']; ?>" />
	<div id="poststuf">
	
		<div id="post-body" class="metabox-holder columns-2" style="margin-right:300px; padding:0;">
		
			<div id="post-body-content">
				<div id="titlediv">
					<div id="titlewrap">
						<label class="hide-if-no-js" style="visibility:hidden" id="title-prompt-text" for="title">Enter name here</label>
						<input type="text" name="usquare_title" size="30" tabindex="1" value="<?php echo $title; ?>" id="title" autocomplete="off" />
					</div>
				</div>
				<h2 class="alignleft" style="padding:0 0 10px 0;">Items</h2>
				<a id="tsort-add-new" class="alignleft button button-highlighted" style="display:block; padding:0px 15px; margin:4px 10px;" href="#">+ Add new item</a>
				<a id="tsort-add-new2" class="alignleft button button-highlighted" style="display:block; padding:0px 15px; margin:4px 10px;" href="#">+ Add new from post</a>
				<a id="tsort-add-new3" class="alignleft button button-highlighted" style="display:block; padding:0px 15px; margin:4px 10px;" href="#">+ Add new from category</a>
				<div class="clear"></div>
				<ul id="usquare-sortable">
				<?php 
				if ($usquare->items != '') {
					$explode = explode('||',$usquare->items);
					$itemsArray = array();
					$icons = array();
					$icon_link_count=array();
					$icon_image_count=array();
					foreach ($explode as $it) {
						$ex2 = explode('::', $it);
						$key = substr($ex2[0],0,strpos($ex2[0],'-'));
						$subkey = substr($ex2[0],strpos($ex2[0],'-')+1);
						$itemsArray[$key][$subkey] = $ex2[1];
						$itemsArray[$key][$subkey] = str_replace('#|#|', '||', $itemsArray[$key][$subkey]);
						$itemsArray[$key][$subkey] = str_replace('#:#:', '::', $itemsArray[$key][$subkey]);
						
						$itemsArray[$key][$subkey] = str_replace('"', '&quot;', $itemsArray[$key][$subkey]);

						if (substr($subkey,0,9)=="item-icon")
						{
							if (strpos($subkey, '-link')!==FALSE) {
									$num = substr($key,4);
									if (!isset($icon_link_count[$num])) $icon_link_count[$num]=0;
									$ikey=$icon_link_count[$num];
									$icons[$num][$ikey]['link']=$itemsArray[$key][$subkey];
									$icon_link_count[$num]++;
							}
							if (strpos($subkey, '-image')!==FALSE) {
									$num = substr($key,4);
									if (!isset($icon_image_count[$num])) $icon_image_count[$num]=0;
									$ikey=$icon_image_count[$num];
									$icons[$num][$ikey]['image']=$itemsArray[$key][$subkey];
									$icon_image_count[$num]++;
							}
						}
					}
					usquare_cache_images ($settings, $itemsArray, 180, 125, 0, 1);
					
					//print_r($icons);
					foreach ($itemsArray as $key => $arr) {
						$num = substr($key,4);
						if (!isset($arr['item-background-color'])) $arr['item-background-color']='#ef4939';
						if ($arr['item-background-color']=='') $arr['item-background-color']='#ef4939';
						
						if (!isset($arr['item-background-image'])) $arr['item-background-image']='';
						if (!isset($arr['item-alt'])) $arr['item-alt']='';
						if (!isset($arr['item-image-position'])) $arr['item-image-position']=0;						
						if (!isset($arr['item-link-image-url'])) $arr['item-link-image-url']='';						
						if (!isset($arr['item-link-image-rel'])) $arr['item-link-image-rel']='';
						if (!isset($arr['item-link-image-target'])) $arr['item-link-image-target']='';						
						if (!isset($arr['item-link-image'])) $arr['item-link-image']=0;
						if ($arr['item-link-image']==0) $arr['item-link-image']='';
						if ($arr['item-link-image']==1) $arr['item-link-image']=' checked="checked" ';

						if (!isset($arr['item-link-image-opened'])) $arr['item-link-image-opened']=0;
						if ($arr['item-link-image-opened']==0) $arr['item-link-image-opened']='';
						if ($arr['item-link-image-opened']==1) $arr['item-link-image-opened']=' checked="checked" ';

						if (!isset($arr['item-www'])) $arr['item-www']='';
						if (!isset($arr['item-email'])) $arr['item-email']='';
						if (!isset($arr['item-icons-target'])) $arr['item-icons-target']='';

						if (!isset($arr['item-title-color'])) $arr['item-title-color']='#ffffff';
						if (!isset($arr['item-description-color'])) $arr['item-description-color']='#ffffff';
						if (!isset($arr['item-content-color'])) $arr['item-content-color']='#ffffff';
						if (!isset($arr['item-info-color'])) $arr['item-info-color']='#ffffff';

						if (!isset($arr['item-dont-open'])) $arr['item-dont-open']=0;
						if ($arr['item-dont-open']==0) $arr['item-dont-open']='';
						if ($arr['item-dont-open']==1) $arr['item-dont-open']=' checked="checked" ';

						if (!isset($arr['item-dont-move'])) $arr['item-dont-move']=0;
						if ($arr['item-dont-move']==0) $arr['item-dont-move']='';
						if ($arr['item-dont-move']==1) $arr['item-dont-move']=' checked="checked" ';

						if (!isset($arr['item-link-square'])) $arr['item-link-square']=0;
						if ($arr['item-link-square']==0) $arr['item-link-square']='';
						if ($arr['item-link-square']==1) $arr['item-link-square']=' checked="checked" ';
						?>
					<li id="<?php echo $key; ?>" class="sortableItem">
						<div class="tsort-plus">+</div>
						<div class="tsort-header">Item <?php echo $num; ?> <small><i>- <?php echo $arr['item-title']; ?></i></small> &nbsp;<a href="#" class="tsort-delete"><i>delete</i></a></div>
						<div class="tsort-content">
							<div class="tsort-content-left">
								<table class="fields-group">
									<tr class="field-row">
										<td style="width: 130px;">
											<label for="<?php echo $key; ?>-item-title">Title:</label>
										</td>
										<td style="width: auto;">
											<input name="<?php echo $key; ?>-item-title" value="<?php echo $arr['item-title']; ?>" type="text"  />
										</td>
									</tr>
									<tr class="field-row">
										<td style="width: 130px;">
											<label for="<?php echo $key; ?>-item-description">Description:</label>
										</td>
										<td style="width: auto;">
											<input name="<?php echo $key; ?>-item-description" value="<?php echo $arr['item-description']; ?>" type="text" />
										</td>
									</tr>
									<tr class="field-row">
										<td style="width: 130px;">
											<label>Image:</label>
										</td>
										<td>
											<div class="tsort-image">
												<img id="<?php echo $key; ?>-item-image" src="<?php echo(($arr['item-image'] != '') ? $arr['item-image'] : $this->url . 'images/no_image.jpg'); ?>" width="180" height="125" /><a href="#" id="<?php echo $key; ?>-item-image-change" class="tsort-change">Change</a>
												<a href="#" id="<?php echo $key; ?>-item-image-remove" class="tsort-remove">Remove</a>
											</div>
											URL: <input id="<?php echo $key; ?>-item-image-input" name="<?php echo $key; ?>-item-image" type="text" style="width: 187px;" class="image_orig_field" value="<?php if (isset($arr['item-image-original'])) echo $arr['item-image-original']; ?>" />
										</td>
									</tr>
									<tr class="field-row">
										<td style="width: 130px;">
											<label for="<?php echo $key; ?>-item-alt">Image alt tag:</label>
										</td>
										<td style="width: auto;">
											<input name="<?php echo $key; ?>-item-alt" value="<?php echo $arr['item-alt']; ?>" type="text" />
										</td>
									</tr>
									<tr class="field-row">
										<td style="width: 130px;">
											<label for="<?php echo $key; ?>-item-content">Content:</label>
										</td>
										<td>
											<textarea class="tsort-contarea" name="<?php echo $key; ?>-item-content"><?php echo $arr['item-content']; ?></textarea>
										</td>
									</tr>
									<tr class="field-row">
										<td style="width: 130px;">
										</td>
										<td>
											<label for="<?php echo $key; ?>-item-dont-open"><input style="display: inline-block; width: 15px; height: 15px; clear: none; padding:0; margin:0;" type="checkbox" name="<?php echo $key; ?>-item-dont-open" id="<?php echo $key; ?>-item-dont-open" value="1" <?php echo $arr['item-dont-open']; ?> /> Don't open extended content</label>
										</td>
									</tr>
									<tr class="field-row">
										<td style="width: 130px;">
											<span class="usquare-help">? <span class="usquare-tooltip">Color of title</span></span>
											<label for="item-title-color">Title color:</label>
										</td>
										<td style="width: auto;">
											<input id="<?php echo $key; ?>-item-title-color" name="<?php echo $key; ?>-item-title-color" value="<?php echo $arr['item-title-color']; ?>" type="text" style="background:<?php echo $arr['item-title-color']; ?>;">	
											<div class="cw-color-picker-holder" style="left:-70px;">
												<div id="<?php echo $key; ?>-item-title-color-picker" class="cw-color-picker" rel="<?php echo $key; ?>-item-title-color"></div>
											</div>
										</td>
									</tr>
									<tr class="field-row">
										<td style="width: 130px;">
											<span class="usquare-help">? <span class="usquare-tooltip">Color of description</span></span>
											<label for="item-description-color">Description color:</label>
										</td>
										<td style="width: auto;">
											<input id="<?php echo $key; ?>-item-description-color" name="<?php echo $key; ?>-item-description-color" value="<?php echo $arr['item-description-color']; ?>" type="text" style="background:<?php echo $arr['item-description-color']; ?>;">	
											<div class="cw-color-picker-holder" style="left:-70px;">
												<div id="<?php echo $key; ?>-item-description-color-picker" class="cw-color-picker" rel="<?php echo $key; ?>-item-description-color"></div>
											</div>
										</td>
									</tr>
									<tr class="field-row">
										<td style="width: 130px;">
											<span class="usquare-help">? <span class="usquare-tooltip">Color of content</span></span>
											<label for="item-content-color">Content color:</label>
										</td>
										<td style="width: auto;">
											<input id="<?php echo $key; ?>-item-content-color" name="<?php echo $key; ?>-item-content-color" value="<?php echo $arr['item-content-color']; ?>" type="text" style="background:<?php echo $arr['item-content-color']; ?>;">	
											<div class="cw-color-picker-holder" style="left:-70px;">
												<div id="<?php echo $key; ?>-item-content-color-picker" class="cw-color-picker" rel="<?php echo $key; ?>-item-content-color"></div>
											</div>
										</td>
									</tr>
									<tr class="field-row">
										<td style="width: 130px;">
											<span class="usquare-help">? <span class="usquare-tooltip">Color of 'info' label</span></span>
											<label for="item-info-color">Info label color:</label>
										</td>
										<td style="width: auto;">
											<input id="<?php echo $key; ?>-item-info-color" name="<?php echo $key; ?>-item-info-color" value="<?php echo $arr['item-info-color']; ?>" type="text" style="background:<?php echo $arr['item-info-color']; ?>;">	
											<div class="cw-color-picker-holder" style="left:-70px;">
												<div id="<?php echo $key; ?>-item-info-color-picker" class="cw-color-picker" rel="<?php echo $key; ?>-item-info-color"></div>
											</div>
										</td>
									</tr>
									<tr class="field-row">
										<td style="width: 130px;">
											Linking image:
										</td>
										<td style="text-align: right;">
											<label for="<?php echo $key; ?>-item-link-image" style="float: left;"><input style="display: inline-block; width: 15px; height: 15px; clear: none; padding:0; margin:0;" type="checkbox" name="<?php echo $key; ?>-item-link-image" id="<?php echo $key; ?>-item-link-image" value="1" <?php echo $arr['item-link-image']; ?> /> &nbsp;&nbsp;Enable image linking</label><br />
											<span class="usquare-help" style="padding: 2px 0 0 3px; font-size: 12px;">?<span class="usquare-tooltip">What link to open when someone click on image; Leave it empty if you want to open already selected item image</span></span> Link: <input name="<?php echo $key; ?>-item-link-image-url" value="<?php echo $arr['item-link-image-url']; ?>" type="text" style="width: 165px;" /><br />
											<span class="usquare-help" style="padding: 2px 0 0 3px; font-size: 12px;">?<span class="usquare-tooltip">Enter here "lightbox" (without a quotes) if you want opening in Lightbox, otherwise leave it empty</span></span> Rel: <input name="<?php echo $key; ?>-item-link-image-rel" value="<?php echo $arr['item-link-image-rel']; ?>" type="text" style="width: 165px;" /><br />
											<span class="usquare-help" style="padding: 2px 0 0 3px; font-size: 12px;">?<span class="usquare-tooltip">Enter here "_blank" (without a quotes) if you want opening in new tab</span></span> Target: <input name="<?php echo $key; ?>-item-link-image-target" value="<?php echo $arr['item-link-image-target']; ?>" type="text" style="width: 165px;" /><br />
											<label for="<?php echo $key; ?>-item-link-image-opened" style="float: left;"><input style="display: inline-block; width: 15px; height: 15px; clear: none; padding:0; margin:0;" type="checkbox" name="<?php echo $key; ?>-item-link-image-opened" id="<?php echo $key; ?>-item-link-image-opened" value="1" <?php echo $arr['item-link-image-opened']; ?> /> Link img only when item is open</label><br />
											<label for="<?php echo $key; ?>-item-link-square" style="float: left;"><input style="display: inline-block; width: 15px; height: 15px; clear: none; padding:0; margin:0;" type="checkbox" name="<?php echo $key; ?>-item-link-square" id="<?php echo $key; ?>-item-link-square" value="1" <?php echo $arr['item-link-square']; ?> /> Also make square linkable</label>
										</td>
									</tr>
								</table>
							</div>
							<div class="tsort-content-right">
								<table class="fields-group" id="item-right-table-<?php echo $num;?>">
									<tr class="field-row">
										<td style="width: 130px;">
											<span class="usquare-help">? <span class="usquare-tooltip">Color of background</span></span>
											<label for="background-color">Background color:</label>
										</td>
										<td style="width: auto;">
											<input id="<?php echo $key; ?>-item-background-color" name="<?php echo $key; ?>-item-background-color" value="<?php echo $arr['item-background-color']; ?>" type="text" style="background:<?php echo $arr['item-background-color']; ?>;">	
											<div class="cw-color-picker-holder" style="left:-70px;">
												<div id="<?php echo $key; ?>-item-background-color-picker" class="cw-color-picker" rel="<?php echo $key; ?>-item-background-color"></div>
											</div>
										</td>
									</tr>
									<tr class="field-row">
										<td style="width: 130px;"><span class="usquare-help">? <span class="usquare-tooltip">Image for background</span></span>
											<label for="background-image">Background image:</label>
										</td>
										<td style="width: auto;">
											<div class="tsort-image" style="height: 125px; width:216px;">
												<img id="<?php echo $key; ?>-item-background-image" src="<?php echo(($arr['item-background-image'] != '') ? $arr['item-background-image'] : $this->url . 'images/no_image.jpg'); ?>" width="180" height="125" /><a href="#" id="<?php echo $key; ?>-item-background-image-change" class="tsort-change" style="left:175px;">Change</a>
												<a href="#" id="<?php echo $key; ?>-item-background-image-remove" class="tsort-remove">Remove</a>
											</div>
											URL: <input id="<?php echo $key; ?>-item-background-image-input" name="<?php echo $key; ?>-item-background-image" type="text" style="width: 187px;" class="image_orig_field" value="<?php if (isset($arr['item-background-image-original'])) echo $arr['item-background-image-original']; ?>" />
										</td>
									</tr>
									
									<tr class="field-row">
										<td style="width: 130px;"><span class="usquare-help">? <span class="usquare-tooltip">Where to put main image. "Full space" will remove title and description and put image on both square.</span></span>
											<label for="image-position">Image position:</label>
										</td>
										<td style="width: auto;">
											<select name="<?php echo $key; ?>-item-image-position">
												<option value="0"<?php if ($arr['item-image-position']==0) echo ' selected="selected"'; ?>>Left</option>
												<option value="1"<?php if ($arr['item-image-position']==1) echo ' selected="selected"'; ?>>Right</option>
												<option value="2"<?php if ($arr['item-image-position']==2) echo ' selected="selected"'; ?>>Full space</option>
											</select>
										</td>
									</tr>
									<tr class="field-row">
										<td style="width: 130px;"><span class="usquare-help">? <span class="usquare-tooltip">Some other options...</span></span>
											<label for="options">Other options:</label>
										</td>
										<td>
											<label for="<?php echo $key; ?>-item-dont-move"><input style="display: inline-block; width: 15px; height: 15px; clear: none; padding:0; margin:0;" type="checkbox" name="<?php echo $key; ?>-item-dont-move" id="<?php echo $key; ?>-item-dont-move" value="1" <?php echo $arr['item-dont-move']; ?> /> Don't move whole item up</label>
										</td>
									</tr>
									<tr class="field-row">
										<td style="width: 130px;">
											<span class="usquare-help">? <span class="usquare-tooltip">Leave it empty if there is no web page for this item</span></span>
											<label for="www">Web page:</label>
										</td>
										<td style="width: auto;">
											<input name="<?php echo $key; ?>-item-www" value="<?php echo $arr['item-www']; ?>" type="text"  />
										</td>
									</tr>
									<tr class="field-row">
										<td style="width: 130px;">
											<span class="usquare-help">? <span class="usquare-tooltip">Leave it empty if there is no email for this item</span></span>
											<label for="email">Email:</label>
										</td>
										<td style="width: auto;">
											<input name="<?php echo $key; ?>-item-email" value="<?php echo $arr['item-email']; ?>" type="text"  />
										</td>
									</tr>
									<tr class="field-row">
										<td style="width: 130px;">
											<span class="usquare-help">? <span class="usquare-tooltip">Leave it empty if there is no social account for this item</span></span>
											<label for="facebook">Facebook:</label>
										</td>
										<td style="width: auto;">
											<input name="<?php echo $key; ?>-item-facebook" value="<?php echo $arr['item-facebook']; ?>" type="text"  />
										</td>
									</tr>
									<tr class="field-row">
										<td style="width: 130px;">
											<span class="usquare-help">? <span class="usquare-tooltip">Leave it empty if there is no social account for this item</span></span>
											<label for="twitter">Twitter:</label>
										</td>
										<td style="width: auto;">
											<input name="<?php echo $key; ?>-item-twitter" value="<?php echo $arr['item-twitter']; ?>" type="text"  />
										</td>
									</tr>
									<tr class="field-row">
										<td style="width: 130px;">
											<span class="usquare-help">? <span class="usquare-tooltip">Leave it empty if there is no social account for this item</span></span>
											<label for="linkedin">LinkedIn:</label>
										</td>
										<td style="width: auto;">
											<input name="<?php echo $key; ?>-item-linkedin" value="<?php echo $arr['item-linkedin']; ?>" type="text"  />
										</td>
									</tr>
									<tr class="field-row">
										<td style="width: 130px;">
											<span class="usquare-help">? <span class="usquare-tooltip">Leave it empty if there is no social account for this item</span></span>
											<label for="pinterest">Pinterest:</label>
										</td>
										<td style="width: auto;">
											<input name="<?php echo $key; ?>-item-pinterest" value="<?php echo $arr['item-pinterest']; ?>" type="text"  />
										</td>
									</tr>
									<tr class="field-row">
										<td style="width: 130px;">
											<span class="usquare-help">? <span class="usquare-tooltip">Leave it empty if there is no social account for this item</span></span>
											<label for="yahoo">Yahoo:</label>
										</td>
										<td style="width: auto;">
											<input name="<?php echo $key; ?>-item-yahoo" value="<?php echo $arr['item-yahoo']; ?>" type="text"  />
										</td>
									</tr>
									<tr class="field-row">
										<td style="width: 130px;">
											<span class="usquare-help">? <span class="usquare-tooltip">Leave it empty if there is no social account for this item</span></span>
											<label for="digg">Digg:</label>
										</td>
										<td style="width: auto;">
											<input name="<?php echo $key; ?>-item-digg" value="<?php echo $arr['item-digg']; ?>" type="text"  />
										</td>
									</tr>
									<tr class="field-row">
										<td style="width: 130px;">
											<span class="usquare-help">? <span class="usquare-tooltip">Enter here "_blank" (without a quotes) if you want icons open links in new tab</span></span>
											<label for="icons-target">Icons target:</label>
										</td>
										<td style="width: auto;">
											<input name="<?php echo $key; ?>-item-icons-target" value="<?php echo $arr['item-icons-target']; ?>" type="text"  />
										</td>
									</tr>
									<tr class="field-row">
										<td style="width: 130px;">
											<label for="other">Other...</label>
										</td>
										<td style="width: auto;">
											<a id="<?php echo $key; ?>-item-add-icon" class="itemaddicon alignleft button button-highlighted" style="display:block; padding:0px 15px; margin:4px 10px;" href="#">+ Additional icons</a>
										</td>
									</tr>
<?php
if (isset($icons[$num]))
{
	$n=1;
	foreach ($icons[$num] as $icon_id => $icon_arr)
	{
		$id=$num;
		$link=$icon_arr['link'];
		$image=$icon_arr['image'];

		echo '							<tr class="field-row" id="icon'.$id.'-'.$n.'-row">
								<td style="width: 130px;">
								<label for="icon'.$id.'-'.$n.'">Icon '.$n.':</label><br />
								<a href="#" style="padding: 0px; margin: 0px; top: 3px;" class="add-new-h2 itemdelicon">[Remove]</a>
								</td>
								<td style="width: auto; text-align: right;">
									Link: <input class="sort'.$id.'-item-icon" name="sort'.$id.'-item-icon'.$n.'-link" style="width: 175px;" value="'.$link.'" type="text"  />
									<span style="padding-top: 20px; display: inline-block;">Image:</span> 
									<div class="tsort-image" style="height: 50px; width:175px; float: right;"><img src="'.$image.'" style="float: left; height: 20px; width: 30px;" id="sort'.$id.'-item-icon'.$n.'-image" /><a href="#" id="sort'.$id.'-item-icon'.$n.'-image-change" style="left: 120px;" class="tsort-change2">Change</a>
										<input id="sort'.$id.'-item-icon'.$n.'-image-input" name="sort'.$id.'-item-icon'.$n.'-image" type="hidden" value="'.$image.'" />
										<a href="#" id="sort'.$id.'-item-icon'.$n.'-image-remove" class="tsort-remove">Remove</a>
									</div>
								</td>
							</tr>';
		$n++;
	}

}
?>
								</table>
							</div>
							<div class="clear"></div>
						</div>
					</li>
					
					<?php 	
					}
				} ?>
				
				</ul>
				<div class="clear"></div>
<div style="margin-top:50px;">
<?php
require_once dirname( __FILE__ ) . '/usquare_functions.php';
usquare_check_upload_folder();
?>
<br /><br />
<style>
ol.insideol li h3 {font-size: 1.17em; margin: 1em 0; font-weight: bold; padding: 0; font-family: sans-serif;}
</style>

<h2>Step by step:</h2>
<ol class="insideol">
	<li><h3>Enter some name for this uSquare, something associative (name will not be shown on page)</h3></li>
	<li><h3>Add items</h3></li>
	<li><h3><a href="#" class="save_link">Save</a> and go to uSquare <a href="<?php echo admin_url( "admin.php?page=usquare" ); ?>">module main page</a></h3></li>
</ol>
</div>
				<div class="clear"></div>
				
				<div id="style_preview">
				
				
				</div>
			
			</div>
		
			<div id="postbox-container-1" class="postbox-container">
				<div class="postbox">
					<h3 class='hndle' style="cursor:auto"><span>Publish</span></h3>
					<div class="inside">
						<div id="save-progress" class="waiting ajax-saved" style="background-image: url(<?php echo esc_url( admin_url( 'images/wpspin_light.gif' ) ); ?>)" ></div>
						<input name="preview-usquare" id="preview-usquare" value="Preview" class="button button-highlighted" style="padding:3px 25px" type="submit" />
						<input name="save-usquare" id="save-usquare" value="Save uSquare" class="alignright button button-primary" style="padding:3px 15px" type="submit" />
						<img id="save-loader" src="<?php echo $this->url; ?>images/ajax-loader.gif" class="alignright" />
						<br class="clear" />		
					</div>
				</div>
				<div id="side-sortables" class="meta-box-sortables ui-sortable">
					
					
					<div id="bla1" class="postbox" >
						<div class="handlediv" title="Click to toggle"><br /></div>
						<h3 class='hndle'><span>General Options</span></h3>
						<div class="inside">
							<table class="fields-group misc-pub-section">						
							<tr class="field-row">
								<td>
									<span class="usquare-help">? <span class="usquare-tooltip">Transition speed (default <?php echo $default_settings['opening-speed']; ?>ms).</span></span>
									<label for="scroll-speed">Opening Speed</label>
								</td>
								<td>
									<input id="opening-speed" name="opening-speed" value="<?php echo $settings['opening-speed']; ?>" size="5" type="text">	
									<span class="unit">ms</span>
								</td>				
							</tr>
							<tr class="field-row">
								<td>
									<span class="usquare-help">? <span class="usquare-tooltip">Transition speed (default <?php echo $default_settings['closing-speed']; ?>ms).</span></span>
									<label for="scroll-speed">Closing Speed</label>
								</td>
								<td>
									<input id="closing-speed" name="closing-speed" value="<?php echo $settings['closing-speed']; ?>" size="5" type="text">	
									<span class="unit">ms</span>
								</td>				
							</tr>
							
							<tr class="field-row">
								<td>
									<span class="usquare-help">? <span class="usquare-tooltip">Transition easing function (default '<?php echo $default_settings['easing']; ?>').</span></span>
									<label for="easing" >Easing</label>
								</td>
								<td>
									<select name="easing">
										<?php 
											$easingArray = array('swing', 'linear', 'easeInQuad', 'easeOutQuad','easeInOutQuad','easeInCubic','easeOutCubic','easeInOutCubic','easeInQuart','easeOutQuart','easeInOutQuart','easeInQuint','easeOutQuint','easeInOutQuint','easeInSine','easeOutSine','easeInOutSine','easeInExpo','easeOutExpo','easeInOutExpo','easeInCirc','easeOutCirc','easeInOutCirc','easeInElastic','easeOutElastic','easeInOutElastic','easeInBack','easeOutBack','easeInOutBack','easeInBounce','easeOutBounce','easeInOutBounce');
											foreach ($easingArray as $item) {
												echo '
										<option value="'.$item.'" '.(($item == $settings['easing']) ? 'selected="selected"' : '').'>'.$item.'</option>';
											}
											
										?>
										
									</select>
								</td>				
							</tr>

							<tr class="field-row">
								<td>
									<span class="usquare-help">? <span class="usquare-tooltip">Images will be in grayscale until visitors open an item</span></span>
									<label for="scroll-speed">Grayscale</label>
								</td>
								<td>
									<label for="grayscale"><input type="checkbox" name="grayscale" id="grayscale" value="1" <?php echo $settings['grayscale']; ?> /> Yes, by default</label>
								</td>				
							</tr>

							<tr class="field-row">
								<td>
									<span class="usquare-help">? <span class="usquare-tooltip">Extended part of item will not overlap the content below uSquare, instead it will push the content below</span></span>
									<label for="push_content_below">Push cont. below</label>
								</td>
								<td>
									<label for="push_content_below"><input type="checkbox" name="push_content_below" id="push_content_below" value="1" <?php echo $settings['push_content_below']; ?> /> Yes</label>
								</td>				
							</tr>

							</table>
						</div>
					</div><!-- /GENERAL OPTIONS -->

					<div id="bla2" class="postbox" >
						<div class="handlediv" title="Click to toggle"><br /></div>
						<h3 class='hndle'><span>Item Styling Options</span></h3>
						<div class="inside">
							<table class="fields-group misc-pub-section">	
							<tr class="field-row">
								<td>
									<span class="usquare-help">? <span class="usquare-tooltip">Number of items per row (default <?php echo $default_settings['usquare-items-per-line']; ?>).</span></span>
									<label for="usquare-items-per-line" >Items per row</label>
								</td>
								<td>
									<input id="usquare-items-per-line" name="usquare-items-per-line" value="<?php echo $settings['usquare-items-per-line']; ?>" size="5" type="text">
								</td>				
							</tr>
							<tr class="field-row">
								<td>
									<span class="usquare-help">? <span class="usquare-tooltip">Width of items (default <?php echo $default_settings['item-width']; ?>px).</span></span>
									<label for="item-width">Width</label>
								</td>
								<td>
									<input id="item-width" name="item-width" value="<?php echo $settings['item-width']; ?>" size="5" type="text">	
									<span class="unit">px</span>
								</td>				
							</tr>
							
							<tr class="field-row">
								<td>
									<span class="usquare-help">? <span class="usquare-tooltip">Height of items (default <?php echo $default_settings['item-height']; ?>px).</span></span>
									<label for="item-height">Height</label>
								</td>
								<td>
									<input id="item-height" name="item-height" value="<?php echo $settings['item-height']; ?>" size="5" type="text">	
									<span class="unit">px</span>
								</td>				
							</tr>

							<tr class="field-row">
								<td>
									<span class="usquare-help">? <span class="usquare-tooltip">Height of extended part of item (default <?php echo $default_settings['item-extended-height']; ?>px).</span></span>
									<label for="item-extended-height">Extended height</label>
								</td>
								<td>
									<input id="item-extended-height" name="item-extended-height" value="<?php echo $settings['item-extended-height']; ?>" size="5" type="text">	
									<span class="unit">px</span>
								</td>				
							</tr>

							<tr class="field-row">
								<td>
									<span class="usquare-help">? <span class="usquare-tooltip">Width of image is always half of item width.</span></span>
									<label for="item-image-width">Image width</label>
								</td>
								<td>
									<input id="item-image-width" name="item-image-width" value="<?php echo $settings['item-image-width']; ?>" size="5" type="text" readonly="readonly">	
									<span class="unit">px</span>
								</td>				
							</tr>

							<tr class="field-row">
								<td>
									<span class="usquare-help">? <span class="usquare-tooltip">Height of image is always as item height.</span></span>
									<label for="item-image-height">Image height</label>
								</td>
								<td>
									<input id="item-image-height" name="item-image-height" value="<?php echo $settings['item-image-height']; ?>" size="5" type="text" readonly="readonly">	
									<span class="unit">px</span>
								</td>				
							</tr>

							<tr class="field-row">
								<td>
									<span class="usquare-help">? <span class="usquare-tooltip">Space at the bottom line of extended content (default <?php echo $default_settings['item-bottom-space']; ?>px).</span></span>
									<label for="item-bottom-space">Bottom space</label>
								</td>
								<td>
									<input id="item-bottom-space" name="item-bottom-space" value="<?php echo $settings['item-bottom-space']; ?>" size="5" type="text">	
									<span class="unit">px</span>
								</td>				
							</tr>
							</table>
						</div>
					</div><!-- /ITEM STYLING OPTIONS -->

					<div id="bla25" class="postbox" >
						<div class="handlediv" title="Click to toggle"><br /></div>
						<h3 class='hndle'><span>Fonts Options</span><img id="font-loader" src="<?php echo $this->url; ?>images/ajax-loader.gif" class="alignright" /></h3>
						<div class="inside">
							<table class="fields-group misc-pub-section">	
							<tr class="field-row">
								<td>
									<span class="usquare-help">? <span class="usquare-tooltip">Font for uSquare title</span></span>
									<label for="title-font-name">Title font</label>
								</td>
								<td>
									<?php echo usquare_generate_font_list($fonts_assoc_array, 'title-font-name', $settings['title-font-name']); ?><br />
									&nbsp;<a href="http://www.google.com/webfonts" target="_blank">Fonts preview here</a>
								</td>				
							</tr>
							<tr class="field-row">
								<td>
									<span class="usquare-help">? <span class="usquare-tooltip">Font style for uSquare title</span></span>
									<label for="title-font-style">Title font style</label>
								</td>
								<td>
									<?php
										$style_array = usquare_generate_font_variants_array($fonts_assoc_array, $settings['title-font-name']);
										echo usquare_generate_font_variants_list ($style_array, $settings['title-font-variant'], 'title-font-variant');
									?>
								</td>				
							</tr>
							<tr class="field-row">
								<td>
									<span class="usquare-help">? <span class="usquare-tooltip">Font size for uSquare title</span></span>
									<label for="title-font-size">Title font size</label>
								</td>
								<td>
									<input id="title-font-size" name="title-font-size" value="<?php echo $settings['title-font-size']; ?>" size="5" type="text">	
									<span class="unit">px</span>
								</td>				
							</tr>
							<tr class="field-row">
								<td>
									<span class="usquare-help">? <span class="usquare-tooltip">Font for uSquare description</span></span>
									<label for="description-font-name">Description font</label>
								</td>
								<td>
									<?php echo usquare_generate_font_list($fonts_assoc_array, 'description-font-name', $settings['description-font-name']); ?>
								</td>				
							</tr>
							<tr class="field-row">
								<td>
									<span class="usquare-help">? <span class="usquare-tooltip">Font style for uSquare description</span></span>
									<label for="description-font-style">Desc. font style</label>
								</td>
								<td>
									<?php
										$style_array = usquare_generate_font_variants_array($fonts_assoc_array, $settings['description-font-name']);
										echo usquare_generate_font_variants_list ($style_array, $settings['description-font-variant'], 'description-font-variant');
									?>
								</td>				
							</tr>
							<tr class="field-row">
								<td>
									<span class="usquare-help">? <span class="usquare-tooltip">Font size for uSquare description</span></span>
									<label for="description-font-size">Desc. font size</label>
								</td>
								<td>
									<input id="description-font-size" name="description-font-size" value="<?php echo $settings['description-font-size']; ?>" size="5" type="text">	
									<span class="unit">px</span>
								</td>				
							</tr>
							<tr class="field-row">
								<td>
									<span class="usquare-help">? <span class="usquare-tooltip">Font for uSquare content</span></span>
									<label for="content-font-name">Content font</label>
								</td>
								<td>
									<?php echo usquare_generate_font_list($fonts_assoc_array, 'content-font-name', $settings['content-font-name']); ?>
								</td>				
							</tr>
							<tr class="field-row">
								<td>
									<span class="usquare-help">? <span class="usquare-tooltip">Font style for uSquare content</span></span>
									<label for="content-font-style">Cont. font style</label>
								</td>
								<td>
									<?php
										$style_array = usquare_generate_font_variants_array($fonts_assoc_array, $settings['content-font-name']);
										echo usquare_generate_font_variants_list ($style_array, $settings['content-font-variant'], 'content-font-variant');
									?>
								</td>				
							</tr>
							<tr class="field-row">
								<td>
									<span class="usquare-help">? <span class="usquare-tooltip">Font size for uSquare content</span></span>
									<label for="content-font-size">Cont. font size</label>
								</td>
								<td>
									<input id="content-font-size" name="content-font-size" value="<?php echo $settings['content-font-size']; ?>" size="5" type="text">	
									<span class="unit">px</span>
								</td>				
							</tr>
							<tr class="field-row">
								<td>
									<span class="usquare-help">? <span class="usquare-tooltip">Font for 'info' label</span></span>
									<label for="info-font-name">Info label font</label>
								</td>
								<td>
									<?php echo usquare_generate_font_list($fonts_assoc_array, 'info-font-name', $settings['info-font-name']); ?>
								</td>				
							</tr>
							<tr class="field-row">
								<td>
									<span class="usquare-help">? <span class="usquare-tooltip">Font style for uSquare 'info' label</span></span>
									<label for="info-font-style">Info font style</label>
								</td>
								<td>
									<?php
										$style_array = usquare_generate_font_variants_array($fonts_assoc_array, $settings['info-font-name']);
										echo usquare_generate_font_variants_list ($style_array, $settings['info-font-variant'], 'info-font-variant');
									?>
								</td>				
							</tr>
							<tr class="field-row">
								<td>
									<span class="usquare-help">? <span class="usquare-tooltip">Font size for 'info' label</span></span>
									<label for="info-font-size">Info font size</label>
								</td>
								<td>
									<input id="info-font-size" name="info-font-size" value="<?php echo $settings['info-font-size']; ?>" size="5" type="text">	
									<span class="unit">px</span>
								</td>				
							</tr>
							<tr class="field-row">
								<td>
									<span class="usquare-help">? <span class="usquare-tooltip">Info label</span></span>
									<label for="info-label">Info label</label>
								</td>
								<td>
									<input id="info-label" name="info-label" value="<?php echo $settings['info-label']; ?>" style="width: 130px;" type="text">
								</td>				
							</tr>

							</table>
						</div>
					</div><!-- /ITEM STYLING OPTIONS -->


					<div id="bla4" class="postbox" >
						<div class="handlediv" title="Click to toggle"><br /></div>
						<h3 class='hndle'><span>Under 960px reolution</span></h3>
						<div class="inside">
							<table class="fields-group misc-pub-section">			
							<tr class="field-row">
								<td>
									<span class="usquare-help">? <span class="usquare-tooltip">Number of items per row (default <?php echo $default_settings['usquare-items-per-row-960']; ?>).</span></span>
									<label for="usquare-items-per-row-960" >Items per row</label>
								</td>
								<td>
									<input id="usquare-items-per-row-960" name="usquare-items-per-row-960" value="<?php echo $settings['usquare-items-per-row-960']; ?>" size="5" type="text">
								</td>				
							</tr>

							<tr class="field-row">
								<td>
									<span class="usquare-help">? <span class="usquare-tooltip">Width of items (default <?php echo $default_settings['item-width-960']; ?>px).</span></span>
									<label for="item-width-960">Width</label>
								</td>
								<td>
									<input id="item-width-960" name="item-width-960" value="<?php echo $settings['item-width-960']; ?>" size="5" type="text">	
									<span class="unit">px</span>
								</td>				
							</tr>
							
							<tr class="field-row">
								<td>
									<span class="usquare-help">? <span class="usquare-tooltip">Height of items (default <?php echo $default_settings['item-height-960']; ?>px).</span></span>
									<label for="item-height-960">Height</label>
								</td>
								<td>
									<input id="item-height-960" name="item-height-960" value="<?php echo $settings['item-height-960']; ?>" size="5" type="text">	
									<span class="unit">px</span>
								</td>				
							</tr>

							<tr class="field-row">
								<td>
									<span class="usquare-help">? <span class="usquare-tooltip">Height of extended part of item (default <?php echo $default_settings['item-extended-height-960']; ?>px).</span></span>
									<label for="item-extended-height-960">Extended height</label>
								</td>
								<td>
									<input id="item-extended-height-960" name="item-extended-height-960" value="<?php echo $settings['item-extended-height-960']; ?>" size="5" type="text">	
									<span class="unit">px</span>
								</td>				
							</tr>
							<tr class="field-row">
								<td>
									<span class="usquare-help">? <span class="usquare-tooltip">Space at the bottom line of extended content (default <?php echo $default_settings['item-bottom-space-960']; ?>px).</span></span>
									<label for="item-bottom-space-960">Bottom space</label>
								</td>
								<td>
									<input id="item-bottom-space-960" name="item-bottom-space-960" value="<?php echo $settings['item-bottom-space-960']; ?>" size="5" type="text">	
									<span class="unit">px</span>
								</td>				
							</tr>
							<tr class="field-row">
								<td>
									<span class="usquare-help">? <span class="usquare-tooltip">Font size for uSquare title</span></span>
									<label for="title-font-size-960">Title size</label>
								</td>
								<td>
									<input id="title-font-size-960" name="title-font-size-960" value="<?php echo $settings['title-font-size-960']; ?>" size="5" type="text">	
									<span class="unit">px</span>
								</td>				
							</tr>
							<tr class="field-row">
								<td>
									<span class="usquare-help">? <span class="usquare-tooltip">Font size for uSquare description</span></span>
									<label for="description-font-size-960">Desc. font size</label>
								</td>
								<td>
									<input id="description-font-size-960" name="description-font-size-960" value="<?php echo $settings['description-font-size-960']; ?>" size="5" type="text">	
									<span class="unit">px</span>
								</td>				
							</tr>
							<tr class="field-row">
								<td>
									<span class="usquare-help">? <span class="usquare-tooltip">Font size for uSquare content</span></span>
									<label for="content-font-size-960">Content font size</label>
								</td>
								<td>
									<input id="content-font-size-960" name="content-font-size-960" value="<?php echo $settings['content-font-size-960']; ?>" size="5" type="text">	
									<span class="unit">px</span>
								</td>				
							</tr>
							<tr class="field-row">
								<td>
									<span class="usquare-help">? <span class="usquare-tooltip">Font size for 'info' label</span></span>
									<label for="info-font-size-960">Info font size</label>
								</td>
								<td>
									<input id="info-font-size-960" name="info-font-size-960" value="<?php echo $settings['info-font-size-960']; ?>" size="5" type="text">	
									<span class="unit">px</span>
								</td>				
							</tr>
							</table>
						</div>
					</div><!-- /ITEM STYLING OPTIONS -->





					<div id="bla5" class="postbox" >
						<div class="handlediv" title="Click to toggle"><br /></div>
						<h3 class='hndle'><span>Under 768px reolution</span></h3>
						<div class="inside">
							<table class="fields-group misc-pub-section">			
							<tr class="field-row">
								<td>
									<span class="usquare-help">? <span class="usquare-tooltip">Number of items per row (default <?php echo $default_settings['usquare-items-per-row-768']; ?>).</span></span>
									<label for="usquare-items-per-row-768" >Items per row</label>
								</td>
								<td>
									<input id="usquare-items-per-row-768" name="usquare-items-per-row-768" value="<?php echo $settings['usquare-items-per-row-768']; ?>" size="5" type="text">
								</td>				
							</tr>

							<tr class="field-row">
								<td>
									<span class="usquare-help">? <span class="usquare-tooltip">Width of items (default <?php echo $default_settings['item-width-768']; ?>px).</span></span>
									<label for="item-width-768">Width</label>
								</td>
								<td>
									<input id="item-width-768" name="item-width-768" value="<?php echo $settings['item-width-768']; ?>" size="5" type="text">	
									<span class="unit">px</span>
								</td>				
							</tr>
							
							<tr class="field-row">
								<td>
									<span class="usquare-help">? <span class="usquare-tooltip">Height of items (default <?php echo $default_settings['item-height-768']; ?>px).</span></span>
									<label for="item-height-768">Height</label>
								</td>
								<td>
									<input id="item-height-768" name="item-height-768" value="<?php echo $settings['item-height-768']; ?>" size="5" type="text">	
									<span class="unit">px</span>
								</td>				
							</tr>

							<tr class="field-row">
								<td>
									<span class="usquare-help">? <span class="usquare-tooltip">Height of extended part of item (default <?php echo $default_settings['item-extended-height-768']; ?>px).</span></span>
									<label for="item-extended-height-768">Extended height</label>
								</td>
								<td>
									<input id="item-extended-height-768" name="item-extended-height-768" value="<?php echo $settings['item-extended-height-768']; ?>" size="5" type="text">	
									<span class="unit">px</span>
								</td>				
							</tr>
							<tr class="field-row">
								<td>
									<span class="usquare-help">? <span class="usquare-tooltip">Space at the bottom line of extended content (default <?php echo $default_settings['item-bottom-space-768']; ?>px).</span></span>
									<label for="item-bottom-space-768">Bottom space</label>
								</td>
								<td>
									<input id="item-bottom-space-768" name="item-bottom-space-768" value="<?php echo $settings['item-bottom-space-768']; ?>" size="5" type="text">	
									<span class="unit">px</span>
								</td>				
							</tr>
							<tr class="field-row">
								<td>
									<span class="usquare-help">? <span class="usquare-tooltip">Font size for uSquare title</span></span>
									<label for="title-font-size-768">Title size</label>
								</td>
								<td>
									<input id="title-font-size-768" name="title-font-size-768" value="<?php echo $settings['title-font-size-768']; ?>" size="5" type="text">	
									<span class="unit">px</span>
								</td>				
							</tr>
							<tr class="field-row">
								<td>
									<span class="usquare-help">? <span class="usquare-tooltip">Font size for uSquare description</span></span>
									<label for="description-font-size-768">Desc. font size</label>
								</td>
								<td>
									<input id="description-font-size-768" name="description-font-size-768" value="<?php echo $settings['description-font-size-768']; ?>" size="5" type="text">	
									<span class="unit">px</span>
								</td>				
							</tr>
							<tr class="field-row">
								<td>
									<span class="usquare-help">? <span class="usquare-tooltip">Font size for uSquare content</span></span>
									<label for="content-font-size-768">Content font size</label>
								</td>
								<td>
									<input id="content-font-size-768" name="content-font-size-768" value="<?php echo $settings['content-font-size-768']; ?>" size="5" type="text">	
									<span class="unit">px</span>
								</td>				
							</tr>
							<tr class="field-row">
								<td>
									<span class="usquare-help">? <span class="usquare-tooltip">Font size for 'info' label</span></span>
									<label for="info-font-size-768">Info font size</label>
								</td>
								<td>
									<input id="info-font-size-768" name="info-font-size-768" value="<?php echo $settings['info-font-size-768']; ?>" size="5" type="text">	
									<span class="unit">px</span>
								</td>				
							</tr>
							</table>
						</div>
					</div><!-- /ITEM STYLING OPTIONS -->



					<div id="bla6" class="postbox" >
						<div class="handlediv" title="Click to toggle"><br /></div>
						<h3 class='hndle'><span>Under 440px reolution</span></h3>
						<div class="inside">
							<table class="fields-group misc-pub-section">			
							<tr class="field-row">
								<td>
									<span class="usquare-help">? <span class="usquare-tooltip">Number of items per row (default <?php echo $default_settings['usquare-items-per-row-440']; ?>).</span></span>
									<label for="usquare-items-per-row-440" >Items per row</label>
								</td>
								<td>
									<input id="usquare-items-per-row-440" name="usquare-items-per-row-440" value="<?php echo $settings['usquare-items-per-row-440']; ?>" size="5" type="text">
								</td>				
							</tr>

							<tr class="field-row">
								<td>
									<span class="usquare-help">? <span class="usquare-tooltip">Width of items (default <?php echo $default_settings['item-width-440']; ?>px).</span></span>
									<label for="item-width-440">Width</label>
								</td>
								<td>
									<input id="item-width-440" name="item-width-440" value="<?php echo $settings['item-width-440']; ?>" size="5" type="text">	
									<span class="unit">px</span>
								</td>				
							</tr>
							
							<tr class="field-row">
								<td>
									<span class="usquare-help">? <span class="usquare-tooltip">Height of items (default <?php echo $default_settings['item-height-440']; ?>px).</span></span>
									<label for="item-height-440">Height</label>
								</td>
								<td>
									<input id="item-height-440" name="item-height-440" value="<?php echo $settings['item-height-440']; ?>" size="5" type="text">	
									<span class="unit">px</span>
								</td>				
							</tr>

							<tr class="field-row">
								<td>
									<span class="usquare-help">? <span class="usquare-tooltip">Height of extended part of item (default <?php echo $default_settings['item-extended-height-440']; ?>px).</span></span>
									<label for="item-extended-height-440">Extended height</label>
								</td>
								<td>
									<input id="item-extended-height-440" name="item-extended-height-440" value="<?php echo $settings['item-extended-height-440']; ?>" size="5" type="text">	
									<span class="unit">px</span>
								</td>				
							</tr>
							<tr class="field-row">
								<td>
									<span class="usquare-help">? <span class="usquare-tooltip">Space at the bottom line of extended content (default <?php echo $default_settings['item-bottom-space-440']; ?>px).</span></span>
									<label for="item-bottom-space-440">Bottom space</label>
								</td>
								<td>
									<input id="item-bottom-space-440" name="item-bottom-space-440" value="<?php echo $settings['item-bottom-space-440']; ?>" size="5" type="text">	
									<span class="unit">px</span>
								</td>				
							</tr>
							<tr class="field-row">
								<td>
									<span class="usquare-help">? <span class="usquare-tooltip">Font size for uSquare title</span></span>
									<label for="title-font-size-440">Title size</label>
								</td>
								<td>
									<input id="title-font-size-440" name="title-font-size-440" value="<?php echo $settings['title-font-size-440']; ?>" size="5" type="text">	
									<span class="unit">px</span>
								</td>				
							</tr>
							<tr class="field-row">
								<td>
									<span class="usquare-help">? <span class="usquare-tooltip">Font size for uSquare description</span></span>
									<label for="description-font-size-440">Desc. font size</label>
								</td>
								<td>
									<input id="description-font-size-440" name="description-font-size-440" value="<?php echo $settings['description-font-size-440']; ?>" size="5" type="text">	
									<span class="unit">px</span>
								</td>				
							</tr>
							<tr class="field-row">
								<td>
									<span class="usquare-help">? <span class="usquare-tooltip">Font size for uSquare content</span></span>
									<label for="content-font-size-440">Content font size</label>
								</td>
								<td>
									<input id="content-font-size-440" name="content-font-size-440" value="<?php echo $settings['content-font-size-440']; ?>" size="5" type="text">	
									<span class="unit">px</span>
								</td>				
							</tr>
							<tr class="field-row">
								<td>
									<span class="usquare-help">? <span class="usquare-tooltip">Font size for 'info' label</span></span>
									<label for="info-font-size-440">Info font size</label>
								</td>
								<td>
									<input id="info-font-size-440" name="info-font-size-440" value="<?php echo $settings['info-font-size-440']; ?>" size="5" type="text">	
									<span class="unit">px</span>
								</td>				
							</tr>
							</table>
						</div>
					</div><!-- /ITEM STYLING OPTIONS -->


				</div>
			</div>
			
			<div id="postbox-container-2" class="postbox-container">
				<div id="normal-sortables" class="meta-box-sortables ui-sortable"></div>
			</div>
			
			<br class="clear"/>
			
		</div>
	
	</div>
<?php
									$post_types=get_post_types('','names'); 
									$categories = array();
									foreach ($post_types as $post_type ) {
										if (!in_array($post_type, array('page', 'attachment', 'revision', 'nav_menu_item'))) {
									  		$newCats = get_categories(array('type' => $post_type));
											foreach ($newCats as $post_cat) {
												if (!in_array($post_cat, $categories)) {
													array_push($categories, $post_cat); 
												}
											}
										}
									}  
									$catString = '';
									foreach ($categories as $category) {
										$catString .= $category->name . '||';
									}
										if($catString != '') {
										echo '<input type="hidden" id="categories-hidden" value="'.substr($catString,0,strlen($catString)-2).'" />';
									}
?>
	</form>
</div>
<script>
var usquare_upload_type=<?php echo $this->uploader_type; ?>;
(function($){
function calculate_image_size()
{
	var n=$('#usquare-items-per-line').val();
	var w=$('#item-width').val();
	var h=$('#item-height').val();
	
	$('#item-image-width').val(w/2);
	$('#item-image-height').val(h);
}

$('#usquare-items-per-line').on('change keyup', function() {calculate_image_size();});
$('#item-width').on('change keyup', function() {calculate_image_size();});
$('#item-height').on('change keyup', function() {calculate_image_size();});
$('.save_link').on('click', function() {$('#save-usquare').click();});
})(jQuery);
</script>
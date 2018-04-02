<?php
function load_usquare($id, usquareAdmin &$usquare_this) {
	global $global_usquare_this;
	if (!isset($global_usquare_this)) $global_usquare_this=$usquare_this;
	require (dirname( __FILE__ ) . '/usquare_settings.php');
	$usquare_this->check_for_upgrade();
	global $wpdb, $last_usquare_id;
	if($id) {
		$usquare = $wpdb->get_results('SELECT * FROM ' . $wpdb->base_prefix . 'usquare WHERE id='.$id);
		$usquare = $usquare[0];
	} else return FALSE;

	$last_usquare_id=$id;
	$title = $usquare->name;

	$settings = array();
	$icons = array();
	$itemsArray = array();
	foreach(explode('||',$usquare->settings) as $val) {
		$expl = explode('::',$val);
		if(substr($expl[0], 0, 8) == 'cat-name') {
		}
		else {
			$settings[$expl[0]] = $expl[1];
			$settings[$expl[0]] = str_replace('#|#|', '||', $settings[$expl[0]]);
			$settings[$expl[0]] = str_replace('#:#:', '::', $settings[$expl[0]]);
		}
	}

	if ($usquare->items != '') {
		$explode = explode('||',$usquare->items);

		$icon_link_count=array();
		$icon_image_count=array();
		foreach ($explode as $it) {
			$ex2 = explode('::', $it);
			$key = substr($ex2[0],0,strpos($ex2[0],'-'));
			$subkey = substr($ex2[0],strpos($ex2[0],'-')+1);
			$itemsArray[$key][$subkey] = $ex2[1];
			$itemsArray[$key][$subkey] = str_replace('#|#|', '||', $itemsArray[$key][$subkey]);
			$itemsArray[$key][$subkey] = str_replace('#:#:', '::', $itemsArray[$key][$subkey]);

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
	}
	return array ('settings' => $settings, 'items' => $itemsArray, 'icons' => $icons);
}

function usquare_cache_images ($settings, &$itemsArray, $weight=0, $height=0, $with_gray=1, $with_background=0) {
	global $global_usquare_this;
	$do_not_resize_images=0;
	if (isset($global_usquare_this)) {
		if ($global_usquare_this->settings['do_not_resize_images']==1) $do_not_resize_images=1;
	}
	foreach ($itemsArray as $ikey => $arr) {
		if ($do_not_resize_images==0) {
			if ($weight==0 && $height==0) {
				$w=round($settings['item-width']/2, 2); //$item_half_width_normal;
				$h=$settings['item-height'];
				if ($arr['item-image-position']==2) $w=$settings['item-width'];
			} else {
				$w=$weight;
				$h=$height;
			}
		} else {
			$weight=0;
			$height=0;
			$w=$weight;
			$h=$height;
		}
		if (!isset($itemsArray[$ikey]['item-image'])) $itemsArray[$ikey]['item-image']='';
		$url=$itemsArray[$ikey]['item-image'];
		if (!isset($itemsArray[$ikey]['item-background-image'])) $itemsArray[$ikey]['item-background-image']='';
		$bgurl=$itemsArray[$ikey]['item-background-image'];

		if ($url!='') {
			$itemsArray[$ikey]['item-image-original']=$url;
			if ($do_not_resize_images==0) {
				usquare_functions::makethumb_image_db($url, $w, $h, array(), '', '', $carr);
				//echo '<pre>'; print_r($itemsArray[$ikey]); echo '</pre>'; exit; //echo '<pre>'; print_r($carr); echo '</pre>'; exit;
				$itemsArray[$ikey]['item-image']=$carr['dest_url'];
			}
			if ($with_gray) {
				$itemsArray[$ikey]['item-image-gray-original']=$url;
				usquare_functions::makethumb_image_db($url, $w, $h, array('gray'), '', '', $carr);
				$itemsArray[$ikey]['item-image-gray']=$carr['dest_url'];
			}
		}
		if ($with_background) {
			if ($bgurl!='') {
				$itemsArray[$ikey]['item-background-image-original']=$bgurl;
				usquare_functions::makethumb_image_db($bgurl, $w, $h, array(), '', '', $carr);
				$itemsArray[$ikey]['item-background-image']=$carr['dest_url'];
			}
		}
	}
}

function usquare_cache_image ($url, $w, $h, $gray=0) {
	if ($url=='') return '';
	$opt=array();
	if ($gray) $opt=array('gray');
	usquare_functions::makethumb_image_db($url, $w, $h, $opt, '', '', $carr);
	//echo '<pre>'; print_r($itemsArray[$ikey]); echo '</pre>'; exit; //echo '<pre>'; print_r($carr); echo '</pre>'; exit;
	return $carr['dest_url'];
}

function generate_usquare(usquareAdmin &$usquare_this, $settings, &$itemsArray, $plugin_url, $id, $icons, $mode=0)
{
	global $usquare_init_number, $usquare_init;
	global $global_usquare_this;
	if (!isset($global_usquare_this)) $global_usquare_this=$usquare_this;
	if (!isset($usquare_init_number)) $usquare_init_number=0;
	$usquare_init_number++;
	
	if (!isset($usquare_init)) $usquare_init=array();

	$count=count($itemsArray);
	if ($count==0) return '';
	
	usquare_cache_images ($settings, $itemsArray);

	// normal resolution
	$items_per_row = $settings['usquare-items-per-line'];
	if ($count<$items_per_row) $items_per_row=$count;
	$item_width=$settings['item-width'];
	$item_height=$settings['item-height'];
	$item_half_width=round($settings['item-width']/2, 2);
	$item_extended_height=$settings['item-extended-height'];
	
	$item_width_scroll=$item_width-20;
	$item_width_scroll_inner=$item_width-40;

	$usquare_width=$items_per_row*$item_width;
	$close_button_left=$item_width-40;
	$item_extended_height_scroll=$item_extended_height-75-$settings['item-bottom-space'];
	$usquare_about_height=$item_extended_height-75-$settings['item-bottom-space'];
	
	$item_half_width_normal = $item_half_width;
	$item_width_normal = $item_width;
	$item_height_normal = $item_height;

	// mode 0 = print all
	// mode 1 = only css+js
	// mode 2 = only html
	
	if (!isset($usquare_init[$id]) && $mode==2) $mode=0;
	
	$usquare_init[$id]=1;
	$frontHtml='';
	
	//if (!isset($settings['skip-head-section'])) $settings['skip-head-section']=0;
	
	if ($usquare_this->settings['skip_head_section']==1) {
		if ($mode==1) return '';
		if ($mode==2) {
			$mode=0;
			if ($usquare_this->settings['use_separated_jquery']==1) {
				$frontHtml.=$usquare_this->get_separated_jquery_hack();
			}
		}
	}
	
	if ($mode==0 || $mode==1) {

		if (!isset($settings['title-font-weight']) && !isset($settings['title-font-style']) && isset($settings['title-font-variant'])) {
			$settings['title-font-weight']=get_font_weight_from_variant($settings['title-font-variant']);
			$settings['title-font-style']=get_font_style_from_variant($settings['title-font-variant']);
			//echo $settings['title-font-variant'].' = '.$settings['title-font-weight'].' - '.$settings['title-font-style'].'<br />';
		}
		if (!isset($settings['description-font-weight']) && !isset($settings['description-font-style']) && isset($settings['description-font-variant'])) {
			$settings['description-font-weight']=get_font_weight_from_variant($settings['description-font-variant']);
			$settings['description-font-style']=get_font_style_from_variant($settings['description-font-variant']);
			//echo $settings['description-font-variant'].' = '.$settings['description-font-weight'].' - '.$settings['description-font-style'].'<br />';
		}
		if (!isset($settings['content-font-weight']) && !isset($settings['content-font-style']) && isset($settings['content-font-variant'])) {
			$settings['content-font-weight']=get_font_weight_from_variant($settings['content-font-variant']);
			$settings['content-font-style']=get_font_style_from_variant($settings['content-font-variant']);
			//echo $settings['content-font-variant'].' = '.$settings['content-font-weight'].' - '.$settings['content-font-style'].'<br />';
		}
		if (!isset($settings['info-font-weight']) && !isset($settings['info-font-style']) && isset($settings['info-font-variant'])) {
			$settings['info-font-weight']=get_font_weight_from_variant($settings['info-font-variant']);
			$settings['info-font-style']=get_font_style_from_variant($settings['info-font-variant']);
			//echo $settings['info-font-variant'].' = '.$settings['info-font-weight'].' - '.$settings['info-font-style'].'<br />';
		}
		
		if ($settings['title-font-weight']!='400') $title_font_weight=$settings['title-font-weight'];
		else $title_font_weight='normal';
		$title_font_style=$settings['title-font-style'];

		if ($settings['description-font-weight']!='400') $description_font_weight=$settings['description-font-weight'];
		else $description_font_weight='normal';
		$description_font_style=$settings['description-font-style'];

		if ($settings['content-font-weight']!='400') $content_font_weight=$settings['content-font-weight'];
		else $content_font_weight='normal';
		$content_font_style=$settings['content-font-style'];

		if ($settings['info-font-weight']!='400') $info_font_weight=$settings['info-font-weight'];
		else $info_font_weight='normal';
		$info_font_style=$settings['info-font-style'];

		$fonts_to_load=array();
		$loaded_fonts=array();
		if ($settings['title-font-name']!='') {
			$v=$settings['title-font-name'];
			$add='';
			if ($settings['title-font-weight']!='400') $add.=$settings['title-font-weight'];
			if ($settings['title-font-style']!='regular') $add.=$settings['title-font-style'];
			if ($add!='') $v.=":".$add;
			if (!isset($loaded_fonts[$v])) {
				$fonts_to_load[]=$v;
				$loaded_fonts[$v]=1;
			}
		}
		if ($settings['description-font-name']!='') {
			$v=$settings['description-font-name'];
			$add='';
			if ($settings['description-font-weight']!='400') $add.=$settings['description-font-weight'];
			if ($settings['description-font-style']!='regular') $add.=$settings['description-font-style'];
			if ($add!='') $v.=":".$add;
			if (!isset($loaded_fonts[$v])) {
				$fonts_to_load[]=$v;
				$loaded_fonts[$v]=1;
			}
		}
		if ($settings['content-font-name']!='') {
			$v=$settings['content-font-name'];
			$add='';
			if ($settings['content-font-weight']!='400') $add.=$settings['content-font-weight'];
			if ($settings['content-font-style']!='regular') $add.=$settings['content-font-style'];
			if ($add!='') $v.=":".$add;
			if (!isset($loaded_fonts[$v])) {
				$fonts_to_load[]=$v;
				$loaded_fonts[$v]=1;
			}
		}
		if ($settings['info-font-name']!='') {
			$v=$settings['info-font-name'];
			$add='';
			if ($settings['info-font-weight']!='400') $add.=$settings['info-font-weight'];
			if ($settings['info-font-style']!='regular') $add.=$settings['info-font-style'];
			if ($add!='') $v.=":".$add;
			if (!isset($loaded_fonts[$v])) {
				$fonts_to_load[]=$v;
				$loaded_fonts[$v]=1;
			}
		}

		if (count($fonts_to_load)) {
			$frontHtml.='<link rel="stylesheet" type="text/css" href="http://fonts.googleapis.com/css?family=';
			$foreach_i=0;
			foreach($fonts_to_load as $font) {
				if ($foreach_i) $frontHtml.='|';
				$frontHtml.=str_replace(' ', '%20', $font);
				$foreach_i++;
			}
			$frontHtml.='">';
		}
		$frontHtml.= '
<style type="text/css">
#usquare_'.$id.' {
	width: '.$usquare_width.'px;
	position: relative;
	margin: 0 auto;
	display: block;
	clear: both;
}
#usquare_'.$id.' .usquare_module_wrapper {
	width: 100%;
	position: relative;
	display: block;
}
#usquare_'.$id.' .usquare_module_wrapper h2 {';
	if ($settings['title-font-name']!='') $frontHtml.= "	font-family: '".$settings['title-font-name']."', 'OstrichSansMedium' !important;";
	$frontHtml.= " font-weight: ".$title_font_weight." !important; font-style: ".$title_font_style." !important;";
	$frontHtml.= ' font-size: '.$settings['title-font-size'].'px !important;';
	$frontHtml.= ' line-height: '.$settings['title-font-size'].'px !important;
}
#usquare_'.$id.' .usquare_module_wrapper span {';
	if ($settings['description-font-name']!='') $frontHtml.= "	font-family: '".$settings['description-font-name']."', 'PTSansRegular' !important;";
	$frontHtml.= " font-weight: ".$description_font_weight." !important; font-style: ".$description_font_style." !important;";
	$frontHtml.= ' font-size: '.$settings['description-font-size'].'px !important;';
	$frontHtml.= ' line-height: '.$settings['description-font-size'].'px !important;
}
#usquare_'.$id.' .usquare_block_extended .usquare_about {';
	if ($settings['content-font-name']!='') $frontHtml.= "	font-family: '".$settings['content-font-name']."', 'PTSansRegular' !important;";
	$frontHtml.= " font-weight: ".$content_font_weight." !important; font-style: ".$content_font_style." !important;";
	$frontHtml.= ' font-size: '.$settings['content-font-size'].'px !important;';
	$frontHtml.= ' line-height: '.$settings['content-font-size'].'px !important;
}
#usquare_'.$id.' .usquare_module_wrapper span.bold {';
	if ($settings['info-font-name']!='') $frontHtml.= "	font-family: '".$settings['info-font-name']."', 'PTSansRegular' !important;  ";
	else $frontHtml.= '	font-family: "PTSansRegular" !important;';
	$frontHtml.= " font-weight: ".$info_font_weight." !important; font-style: ".$info_font_style." !important;";
	$frontHtml.= ' font-size: '.$settings['info-font-size'].'px !important;';
	$frontHtml.= ' line-height: '.$settings['info-font-size'].'px !important;
}
#usquare_'.$id.' .usquare_block {
	width: '.$item_width.'px;
	height: '.$item_height.'px;
}
#usquare_'.$id.' .full_usquare_square {
	width: '.$item_width.'px; 
	height: '.$item_height.'px; 
	cursor: pointer;
}
#usquare_'.$id.' .half_usquare_square {
	width: '.$item_half_width.'px; 
	height: '.$item_height.'px; 
	cursor: pointer;
}
#usquare_'.$id.' .right_gray_usquare_square {
	position: absolute;
	left:  '.$item_half_width.'px;
}
#usquare_'.$id.' .usquare_block_extended {
	width: '.$item_width.'px; 
	height: '.$item_extended_height.'px; 
	margin-top: '.$item_height.'px; 
}
#usquare_'.$id.' .usquare_block_extended .close {
	left: '.$close_button_left.'px;
}
#usquare_'.$id.' .usquare_about { width: '.$item_width_scroll.'px; clear: both; margin: 0px; padding-left: 17px; height: '.$usquare_about_height.'px; }
#usquare_'.$id.' .usquare_about .viewport { width: '.$item_width_scroll_inner.'px; height: '.$item_extended_height_scroll.'px; }
';
}

// --- 960 resolution ---
	$items_per_row = $settings['usquare-items-per-row-960'];
	if ($count<$items_per_row) $items_per_row=$count;
	$item_width=$settings['item-width-960'];
	$item_height=$settings['item-height-960'];
	$item_half_width=round($settings['item-width-960']/2, 2);
	$item_extended_height=$settings['item-extended-height-960'];

	$item_width_scroll=$item_width-20;
	$item_width_scroll_inner=$item_width-40;
	$usquare_about_height=$item_extended_height-75-$settings['item-bottom-space-960'];
	$item_extended_height_scroll=$item_extended_height-75-$settings['item-bottom-space-960'];

	$usquare_width=$items_per_row*$item_width;
	$close_button_left=$item_width-40;

if ($mode==0 || $mode==1) {
$frontHtml .= '
@media screen and (max-width:960px) {
	#usquare_'.$id.' {
		width: '.$usquare_width.'px;
		position: relative;
		margin: 0 auto;
		display: block;
		clear: both;
	}
	#usquare_'.$id.' .usquare_block {
		width: '.$item_width.'px; 
		height: '.$item_height.'px; 
	}
	#usquare_'.$id.' .full_usquare_square {
		width: '.$item_width.'px; 
		height: '.$item_height.'px; 
	}
	#usquare_'.$id.' .half_usquare_square {
		width: '.$item_half_width.'px; 
		height: '.$item_height.'px; 
	}
	#usquare_'.$id.' .right_gray_usquare_square {
		position: absolute;
		left:  '.$item_half_width.'px;
	}
	#usquare_'.$id.' .usquare_block_extended {
		width: '.$item_width.'px; 
		height: '.$item_extended_height.'px; 
		margin-top: '.$item_height.'px; 
	}
	#usquare_'.$id.' .usquare_block_extended .close {
		left: '.$close_button_left.'px;
	}
	#usquare_'.$id.' .usquare_block_extended .usquare_about {
		height: '.$usquare_about_height.'px; 
	}
	#usquare_'.$id.' .usquare_about { width: '.$item_width_scroll.'px; clear: both; margin: 0px; padding-left: 16px; height: '.$usquare_about_height.'px; }
	#usquare_'.$id.' .usquare_about .viewport { width: '.$item_width_scroll_inner.'px; height: '.$item_extended_height_scroll.'px; }
	#usquare_'.$id.' .usquare_module_wrapper h2 {
		font-size: '.$settings['title-font-size-960'].'px !important;
		line-height: '.$settings['title-font-size-960'].'px !important;
	}
	#usquare_'.$id.' .usquare_module_wrapper span {
		font-size: '.$settings['description-font-size-960'].'px !important;
		line-height: '.$settings['description-font-size-960'].'px !important;
	}
	#usquare_'.$id.' .usquare_block_extended .usquare_about {
		font-size: '.$settings['content-font-size-960'].'px !important;
		line-height: '.$settings['content-font-size-960'].'px !important;
	}
	#usquare_'.$id.' .usquare_module_wrapper span.bold {
		font-size: '.$settings['info-font-size-960'].'px !important;
		line-height: '.$settings['info-font-size-960'].'px !important;
	}
}';
}

// --- 768 resolution ---
	$items_per_row = $settings['usquare-items-per-row-768'];
	if ($count<$items_per_row) $items_per_row=$count;
	$item_width=$settings['item-width-768'];
	$item_height=$settings['item-height-768'];
	$item_half_width=round($settings['item-width-768']/2, 2);
	$item_extended_height=$settings['item-extended-height-768'];

	$item_width_scroll=$item_width-20;
	$item_width_scroll_inner=$item_width-40;
	$usquare_about_height=$item_extended_height-75-$settings['item-bottom-space-768'];
	$item_extended_height_scroll=$item_extended_height-75-$settings['item-bottom-space-768'];

	$usquare_width=$items_per_row*$item_width;
	$close_button_left=$item_width-40;

if ($mode==0 || $mode==1) {
$frontHtml .= '
@media screen and (max-width:768px) {
	#usquare_'.$id.' {
		width: '.$usquare_width.'px;
		position: relative;
		margin: 0 auto;
		display: block;
		clear: both;
	}
	#usquare_'.$id.' .usquare_block {
		width: '.$item_width.'px; 
		height: '.$item_height.'px; 
	}
	#usquare_'.$id.' .full_usquare_square {
		width: '.$item_width.'px; 
		height: '.$item_height.'px; 
	}
	#usquare_'.$id.' .half_usquare_square {
		width: '.$item_half_width.'px; 
		height: '.$item_height.'px; 
	}
	#usquare_'.$id.' .right_gray_usquare_square {
		position: absolute;
		left:  '.$item_half_width.'px;
	}
	#usquare_'.$id.' .usquare_block_extended {
		width: '.$item_width.'px; 
		height: '.$item_extended_height.'px; 
		margin-top: '.$item_height.'px; 
	}
	#usquare_'.$id.' .usquare_block_extended .close {
		left: '.$close_button_left.'px;
	}
	#usquare_'.$id.' .usquare_block_extended .usquare_about {
		height: '.$usquare_about_height.'px; 
	}
	#usquare_'.$id.' .usquare_about { width: '.$item_width_scroll.'px; clear: both; margin: 0px; padding-left: 16px; height: '.$usquare_about_height.'px; }
	#usquare_'.$id.' .usquare_about .viewport { width: '.$item_width_scroll_inner.'px; height: '.$item_extended_height_scroll.'px; }
	#usquare_'.$id.' .usquare_module_wrapper h2 {
		font-size: '.$settings['title-font-size-768'].'px !important;
		line-height: '.$settings['title-font-size-768'].'px !important;
	}
	#usquare_'.$id.' .usquare_module_wrapper span {
		font-size: '.$settings['description-font-size-768'].'px !important;
		line-height: '.$settings['description-font-size-768'].'px !important;
	}
	#usquare_'.$id.' .usquare_block_extended .usquare_about {
		font-size: '.$settings['content-font-size-768'].'px !important;
		line-height: '.$settings['content-font-size-768'].'px !important;
	}
	#usquare_'.$id.' .usquare_module_wrapper span.bold {
		font-size: '.$settings['info-font-size-768'].'px !important;
		line-height: '.$settings['info-font-size-768'].'px !important;
	}
}';
}

// --- 440 resolution ---
	$items_per_row = $settings['usquare-items-per-row-440'];
	if ($count<$items_per_row) $items_per_row=$count;
	$item_width=$settings['item-width-440'];
	$item_height=$settings['item-height-440'];
	$item_half_width=round($settings['item-width-440']/2, 2);
	$item_extended_height=$settings['item-extended-height-440'];

	$item_width_scroll=$item_width-20;
	$item_width_scroll_inner=$item_width-40;
	$usquare_about_height=$item_extended_height-75-$settings['item-bottom-space-440'];
	$item_extended_height_scroll=$item_extended_height-75-$settings['item-bottom-space-440'];

	$usquare_width=$items_per_row*$item_width;
	$close_button_left=$item_width-40;

if ($mode==0 || $mode==1) {
$frontHtml .= '
@media screen and (max-width:440px) {
	#usquare_'.$id.' {
		width: '.$usquare_width.'px;
		position: relative;
		margin: 0 auto;
		display: block;
		clear: both;
	}
	#usquare_'.$id.' .usquare_block {
		width: '.$item_width.'px; 
		height: '.$item_height.'px; 
	}
	#usquare_'.$id.' .full_usquare_square {
		width: '.$item_width.'px; 
		height: '.$item_height.'px; 
	}
	#usquare_'.$id.' .half_usquare_square {
		width: '.$item_half_width.'px; 
		height: '.$item_height.'px; 
	}
	#usquare_'.$id.' .right_gray_usquare_square {
		position: absolute;
		left:  '.$item_half_width.'px;
	}
	#usquare_'.$id.' .usquare_block_extended {
		width: '.$item_width.'px; 
		height: '.$item_extended_height.'px; 
		margin-top: '.$item_height.'px; 
	}
	#usquare_'.$id.' .usquare_block_extended .close {
		left: '.$close_button_left.'px;
	}
	#usquare_'.$id.' .usquare_block_extended .usquare_about {
		height: '.$usquare_about_height.'px; 
	}
	#usquare_'.$id.' .usquare_block_extended li {
		margin:11px 6px 0 6px;
	}
	#usquare_'.$id.' .usquare_block_extended ul {
		top: '.$item_half_width.'px;
	}
	#usquare_'.$id.' .usquare_about { width: '.$item_width_scroll.'px; clear: both; margin: 0px; padding-left: 16px; height: '.$usquare_about_height.'px; }
	#usquare_'.$id.' .usquare_about .viewport { width: '.$item_width_scroll_inner.'px; height: '.$item_extended_height_scroll.'px; }
	#usquare_'.$id.' .usquare_module_wrapper h2 {
		font-size: '.$settings['title-font-size-440'].'px !important;
		line-height: '.$settings['title-font-size-440'].'px !important;
	}
	#usquare_'.$id.' .usquare_module_wrapper span {
		font-size: '.$settings['description-font-size-440'].'px !important;
		line-height: '.$settings['description-font-size-440'].'px !important;
	}
	#usquare_'.$id.' .usquare_block_extended .usquare_about {
		font-size: '.$settings['content-font-size-440'].'px !important;
		line-height: '.$settings['content-font-size-440'].'px !important;
	}
	#usquare_'.$id.' .usquare_module_wrapper span.bold {
		font-size: '.$settings['info-font-size-440'].'px !important;
		line-height: '.$settings['info-font-size-440'].'px !important;
	}
}
</style>';
//echo $item_extended_height; exit;
}

//echo '<pre>';print_r($itemsArray);echo '</pre><hr />';

if ($mode==0 || $mode==2) {
$frontHtml .='<div id="usquare_'.$id.'"><div class="usquare_module_wrapper">
			<div class="usquare_module_shade"></div>
';

if (count($itemsArray)>0) {
	foreach ($itemsArray as $key => $arr) {
		$num = substr($key,4);
		//echo '<pre>';print_r($arr);echo '</pre>'; exit;
		
		if ($usquare_this->settings['fix_encoding']==1) {
			$arr['item-title']=mb_convert_encoding( $arr['item-title'], "HTML-ENTITIES", "UTF-8"); 
			$arr['item-description']=mb_convert_encoding( $arr['item-description'], "HTML-ENTITIES", "UTF-8"); 
			$arr['item-content']=mb_convert_encoding( $arr['item-content'], "HTML-ENTITIES", "UTF-8"); 
		}
		
		if (!isset($arr['item-background-color'])) $arr['item-background-color']='#ef4939';
		if ($arr['item-background-color']=='') $arr['item-background-color']='#ef4939';
		if (!isset($arr['item-background-image'])) $arr['item-background-image']='';
		
		$item_style='background-color: '.$arr['item-background-color'].';';
		if ($arr['item-background-image']!='') $item_style='background: url('.$arr['item-background-image'].');';
		
		if (!isset($arr['item-alt'])) $arr['item-alt']='';
		if ($arr['item-alt']!='') $alt=str_replace('"', "'", $arr['item-alt']);
		else $alt=str_replace('"', "'", $arr['item-title']);
		$alt_attr=' alt="'.$alt.'"';
		$title_attr=' title="'.$alt.'"';
		if (!isset($arr['image-position'])) $arr['image-position']=0;
		if (!isset($arr['item-link-image-url'])) $arr['item-link-image-url']='';
		if (!isset($arr['item-link-image-rel'])) $arr['item-link-image-rel']='';
		if (!isset($arr['item-link-image-target'])) $arr['item-link-image-target']='';
		if (!isset($arr['item-link-image'])) $arr['item-link-image']=0;
		if (!isset($arr['item-link-image-opened'])) $arr['item-link-image-opened']=0;

		if (!isset($arr['item-www'])) $arr['item-www']='';
		if (!isset($arr['item-email'])) $arr['item-email']='';
		if (!isset($arr['item-icons-target'])) $arr['item-icons-target']='';
		if (!isset($arr['item-title-color'])) $arr['item-title-color']='#ffffff';
		if (!isset($arr['item-description-color'])) $arr['item-description-color']='#ffffff';
		if (!isset($arr['item-content-color'])) $arr['item-content-color']='#ffffff';
		if (!isset($arr['item-info-color'])) $arr['item-info-color']='#ffffff';

		$item_dont_open='';
		if (!isset($arr['item-dont-open'])) $arr['item-dont-open']=0;
		if ($arr['item-dont-open']==1) $item_dont_open=' data-dont-open="1"';

		$item_dont_move='';
		if (!isset($arr['item-dont-move'])) $arr['item-dont-move']=0;
		if ($arr['item-dont-move']==1) $item_dont_move=' data-dont-move="1"';

		$item_link_square='';
		if (!isset($arr['item-link-square'])) $arr['item-link-square']=0;
		if ($arr['item-link-square']==1) $item_link_square=' data-link-square="1"';

		$custom_info_style='';
		$arrow_style='';
		$arrow_image=( !defined('YIT_USQUARE_URL') ) ? $plugin_url.'images/arrow.png' : YIT_USQUARE_URL.'/images/arrow.png';
		$w=$item_half_width_normal;
		$h=$item_height_normal;
		$img_class='half_usquare_square';
		$img_style='';
		if ($arr['item-image-position']==2) {$w=$item_width_normal; $img_class='full_usquare_square'; $img_style='max-width: 100%;';}
		
		if ($img_style!='') $img_style='style="'.$img_style.'"';
		
		$image='<img src="'.$arr['item-image'].'" class="usquare_square '.$img_class.'" '.$img_style.$alt_attr.' />';
		
		if ($settings['grayscale']==1) {
			$gray_image_class='';
			$gray_left='';
			if ($arr['item-image-position']==0) $gray_left=' left: 0;';
			if ($arr['item-image-position']==1) $gray_image_class=' right_gray_usquare_square';
			$image.='<img src="'.$arr['item-image-gray'].'" style="position: absolute;'.$gray_left.'" class="usquare_square '.$img_class.$gray_image_class.'" '.$img_style.' alt="" />';
			//echo '<pre>'; print_r($arr); echo '<pre>'; exit;
		}
		
		if ($arr['item-link-image']==1) {
			if ($arr['item-link-image-url']=='') $arr['item-link-image-url']=$arr['item-image-original'];
			$target='';
			$data_target='';
			if ($arr['item-link-image-target']!='') {$target='target="'.$arr['item-link-image-target'].'"'; $data_target='data-target="'.$arr['item-link-image-target'].'"';}
			if ($arr['item-link-image-opened']==0) {
				$image='<a '.$target.' href="'.$arr['item-link-image-url'].'" rel="'.$arr['item-link-image-rel'].'"'.$title_attr.'>'.$image.'</a>';
			} else {
				$image='<a '.$data_target.' data-href="'.$arr['item-link-image-url'].'" data-rel="'.$arr['item-link-image-rel'].'" data-only-open="1"'.$title_attr.'>'.$image.'</a>';
			}
		}

		if ($arr['item-image-position']==1) {
			$custom_info_style=' style="right: 0; text-align: right;"';
			$arrow_style=' style="float: right; padding: 0 0 5px;"';
			$arrow_image=( !defined('YIT_USQUARE_URL') ) ? $plugin_url.'images/arrow_r.png' : YIT_USQUARE_URL.'/images/arrow_r.png';
		}

$frontHtml .='<div class="usquare_block"'.$item_dont_move.$item_dont_open.$item_link_square.'>';
if ($arr['item-image-position']==0 || $arr['item-image-position']==2) $frontHtml .= $image;
if ($arr['item-image-position']!=2) {
$frontHtml .='				<div class="usquare_square half_usquare_square" style="'.$item_style.'">
					<div class="usquare_square_text_wrapper"'.$custom_info_style.'>
						<img src="'.$arrow_image.'" class="usquare_arrow" alt="arrow" '.$arrow_style.'/>
						<div class="clear"></div>
						<h2 style="color: '.$arr['item-title-color'].' !important;">'.$arr['item-title'].'</h2>
						<span style="color: '.$arr['item-description-color'].' !important;">'.$arr['item-description'].'</span>
						<div class="clear"></div>
					</div>
				</div>';
}
if ($arr['item-image-position']==1) $frontHtml .= $image;
$frontHtml.='
				<div class="usquare_block_extended" style="'.$item_style.'">
					<a class="close"><img src="'.$plugin_url.'images/close.png" alt="close"/></a>
					<ul class="social_background">';

		$target='';
		if ($arr['item-icons-target']!='') $target='target="'.$arr['item-icons-target'].'"';
			
		if (isset($icons[$num]))
		{
			$n=1;
			foreach ($icons[$num] as $icon_id => $icon_arr)
			{
				$link=$icon_arr['link'];
				$image=$icon_arr['image'];
				$frontHtml .= '<li><a '.$target.' href="'.$link.'"><img src="'.$image.'" alt="social" /></a></li>';
			}
		}

		if ($arr['item-www']!='') $frontHtml .= '<li><a '.$target.' href="'.$arr['item-www'].'"><img src="'.$plugin_url.'images/social-www.png" alt="www" /></a></li>';
		if ($arr['item-email']!='') $frontHtml .= '<li><a '.$target.' href="'.$arr['item-email'].'"><img src="'.$plugin_url.'images/social-email.png" alt="email" /></a></li>';
		if ($arr['item-facebook']!='') $frontHtml .= '<li><a '.$target.' href="'.$arr['item-facebook'].'"><img src="'.$plugin_url.'images/social-fb.png" alt="social" /></a></li>';
		if ($arr['item-twitter']!='') $frontHtml .= '<li><a '.$target.' href="'.$arr['item-twitter'].'"><img src="'.$plugin_url.'images/social-tw.png" alt="social" /></a></li>';
		if ($arr['item-linkedin']!='') $frontHtml .= '<li><a '.$target.' href="'.$arr['item-linkedin'].'"><img src="'.$plugin_url.'images/social-in.png" alt="social" /></a></li>';
		if ($arr['item-pinterest']!='') $frontHtml .= '<li><a '.$target.' href="'.$arr['item-pinterest'].'"><img src="'.$plugin_url.'images/social-pint.png" alt="social" /></a></li>';
		if ($arr['item-yahoo']!='') $frontHtml .= '<li><a '.$target.' href="'.$arr['item-yahoo'].'"><img src="'.$plugin_url.'images/social-yah.png" alt="social" /></a></li>';
		if ($arr['item-digg']!='') $frontHtml .= '<li><a '.$target.' href="'.$arr['item-digg'].'"><img src="'.$plugin_url.'images/social-dig.png" alt="social" /></a></li>';
$frontHtml .=					'</ul>
				
					<div class="clear"></div>
					
					<span class="bold" style="color: '.$arr['item-info-color'].' !important;">'.$settings['info-label'].'</span>
					<div class="usquare_about" style="color: '.$arr['item-content-color'].' !important;">    
					<div class="scrollbar"><div class="track"><div class="thumb"><div class="end"></div></div></div></div>
    <div class="viewport">
        <div class="overview">
            '.$arr['item-content'].'
        </div>
    </div>
	
					</div>
				</div>
			</div>
';
	}
}
$frontHtml .= '
<div class="clear"></div>
</div>
</div>
';
}

if ($mode==0 || $mode==1) {
$frontHtml .= '
<script>
';

if ($usquare_init_number==1) $frontHtml.='var usquare_plugin_url="'.$plugin_url.'";';

$jQueryAlias='jQuery';
if ($usquare_this->alternative_jquery) $jQueryAlias='usquare_jQuery';

if (!isset($settings['push_content_below'])) $settings['push_content_below']=0;
$frontHtml.='    (function($){
		$(document).ready( function() {
			if ($("#usquare_'.$id.'").length) {
				$("#usquare_'.$id.'").uSquare({
					opening_speed:		'.$settings['opening-speed'].',
					closing_speed:		'.$settings['closing-speed'].',
					easing:				"'.$settings['easing'].'",
					grayscale:			'.$settings['grayscale'].',
					push_content_below:	'.$settings['push_content_below'].'
				});
			}
		});
    })('.$jQueryAlias.');
</script>
';
}

if ($mode==0 || $mode==2) {
$frontHtml.='<div class="clear"></div>
';
}
$frontHtml=str_replace("\r", "", $frontHtml);
$frontHtml=str_replace("\n", "", $frontHtml);
return $frontHtml;
}


function usquare_check_upload_folder()
{
	$path=usquare_functions::get_root_of_uploads_dir();
	if ($path=="")
	{
		echo '<br /><div style="background-color: #decdc6; border: 1px solid #c981c0; padding: 5px; margin-bottom: 5px;"><b>Note:</b> Please make upload folder: <b>/wp-content/uploads</b></div>';
		return;
	}
	$p1=substr_count($path, "/");
	$p2=substr_count($path, "\\");
	if ($p1>$p2) $slash="/";
	else $slash="\\";
	$folder = $path.$slash;
	$file = $folder."test_file.html";
	$handle = @fopen($file, "wb");
	if ($handle==FALSE) {
		echo '<br /><div style="background-color: #decdc6; border: 1px solid #c981c0; padding: 5px; margin-bottom: 5px;"><b>Note:</b> Please make folder &nbsp;&nbsp;<b>'.$folder.'</b>&nbsp;&nbsp; writeable.<br /><a href="http://codex.wordpress.org/Changing_File_Permissions#Using_an_FTP_Client" target="_blank">Here</a> is how to do that.</div>';
		return;
	}
	@fclose($handle);
	@unlink($file);
}

function usquare_get_font_array(&$settings, $plugin_path)
{
	$google_fonts_ok=0;
	if (isset($settings['google_fonts']) && strlen($settings['google_fonts'])>1000) {
		$fonts=$settings['google_fonts'];
		$arr = json_decode($fonts, true);
		if (isset($arr['items'])) $google_fonts_ok=1;
	}
	if ($google_fonts_ok==0) {
		$fonts=@file_get_contents($plugin_path.'/fonts/fonts.txt');
		$arr = json_decode($fonts, true);
		if (isset($arr['items'])) $google_fonts_ok=1;
	}

	if ($google_fonts_ok==1) {
		$arr2 = array();
		foreach($arr['items'] as $font) {
			$arr2[$font['family']]=$font;
		}
		return array('assoc'=>$arr2, 'num'=>$arr);
	} else {
		return array('assoc'=>array(), 'num'=>array());
	}
}


function usquare_generate_font_list(&$fonts_array, $field_name='', $selected='') {
	if ($field_name!='') $fonts_select='<select name="'.$field_name.'" id="'.$field_name.'">';
	
	$fonts_select.='<option value=""';
	if ($selected=='') $fonts_select.=' selected="selected"';
	$fonts_select.='>Default</option>';

	foreach($fonts_array as $font) {
		$fonts_select.='<option value="'.$font['family'].'"';
		if ($selected==$font['family']) $fonts_select.=' selected="selected"';
		$fonts_select.='>'.$font['family'].'</option>';
	}
	$fonts_select.='</select>';
	return $fonts_select;
}

/*
function usquare_generate_font_weight_array(&$fonts_array, $font_name) {
	$arr=array();
	$arr2=array();
	if ($font_name=='') return array(0=>'100',1=>'200',2=>'300',3=>'400',4=>'500',5=>'600',6=>'700',7=>'800',9=>'900');
	foreach ($fonts_array[$font_name]['variants'] as $variant) {
		$num=0;
		$ovariant=$variant;
		$piece=substr($variant,0,3);
		if (is_numeric($piece))
			if (intval($piece)>0) $num=1;
			
		if ($num && strlen($variant)>=4) {
			$piece=substr($variant,0,4);
			if (is_numeric($piece))
				if (intval($piece)>0) $num=2;
		}
		
		if ($num==1) $variant=substr($variant,0,3);
		elseif ($num==2) $variant=substr($variant,0,4);
		else {
			if ($variant=='regular' || $variant=='italic') $variant='400';
		}
		//echo $ovariant.": num=".$num.", result=".$variant."\n";

		if (!isset($arr2[$variant])) {
			$arr[]=$variant;
			$arr2[$variant]=1;
		}
	}
	return $arr;
}

function usquare_generate_font_weight_list($weight_array, $selected='400', $field_name='') {
	if ($field_name!='') $font_weight_select='<select name="'.$field_name.'" id="'.$field_name.'">';

	foreach($weight_array as $weight) {
		$weight_text=$weight;
		if ($weight=='400') $weight_text='Normal (400)';
		$font_weight_select.='<option value="'.$weight.'"';
		if ($selected==$weight) $font_weight_select.=' selected="selected"';
		$font_weight_select.='>'.$weight_text.'</option>';
	}
	if ($field_name!='') $font_weight_select.='</select>';
	return $font_weight_select;
}

function usquare_generate_font_style_array(&$fonts_array, $font_name) {
	$arr=array();
	$arr2=array();
	if ($font_name=='') return array(0=>'regular', 1=>'italic');
	foreach ($fonts_array[$font_name]['variants'] as $variant) {
		$num=0;
		$ovariant=$variant;
		$piece=substr($variant,0,3);
		if (is_numeric($piece))
			if (intval($piece)>0) $num=1;
			
		if ($num && strlen($variant)>=4) {
			$piece=substr($variant,0,4);
			if (is_numeric($piece))
				if (intval($piece)>0) $num=2;
		}

		if ($num==1) $variant=substr($variant,3);
		elseif ($num==2) $variant=substr($variant,4);
		//echo $ovariant.": num=".$num.", result=".$variant."\n";

		if (trim($variant)!='') {
			if (!isset($arr2[$variant])) {
				$arr[]=$variant;
				$arr2[$variant]=1;
			}
		}
	}
	return $arr;
}

function usquare_generate_font_style_list($style_array, $selected='regular', $field_name='') {
	if ($field_name!='') $font_weight_select='<select name="'.$field_name.'" id="'.$field_name.'">';

	foreach($style_array as $style) {
		$style_text=strtoupper(substr($style,0,1)).substr($style,1);
		$font_weight_select.='<option value="'.$style.'"';
		if ($selected==$style) $font_weight_select.=' selected="selected"';
		$font_weight_select.='>'.$style_text.'</option>';
	}
	if ($field_name!='') $font_weight_select.='</select>';
	return $font_weight_select;
}
*/


function usquare_generate_font_variants_for_listbox_from_weight_and_style ($weight, $style) {
	if ($weight=='400') $weight='';
	if ($style=='regular') $style='';
	$r = $weight.$style;
	if ($r=='') $r='regular';
	return $r;
}
function usquare_generate_font_variants_for_header_from_weight_and_style ($weight, $style) {
	if ($weight=='400') $weight='';
	if ($style=='regular') $style='';
	return $weight.$style;
}

function usquare_generate_font_variants_array(&$fonts_array, $font_name) {
	if ($font_name=='') return array(0=>'100',1=>'100italic',2=>'200',3=>'200italic',4=>'300',5=>'300italic',6=>'400',7=>'400italic',8=>'500',9=>'500italic',10=>'600',11=>'600italic',12=>'700',13=>'700italic',14=>'800',15=>'800italic',16=>'900',17=>'900italic');
	$ok=0;
	foreach ($fonts_array[$font_name]['variants'] as $variant) if ($variant=='regular') $ok=1;
	if (!$ok) $fonts_array[$font_name]['variants'][]='regular';
	return $fonts_array[$font_name]['variants'];
}

function usquare_generate_font_variants_list($style_array, $selected='regular', $field_name='') {
	if ($field_name!='') $font_weight_select='<select name="'.$field_name.'" id="'.$field_name.'">';

	foreach($style_array as $style) {
		$style_text=strtoupper(substr($style,0,1)).substr($style,1);
		$font_weight_select.='<option value="'.$style.'"';
		if ($selected==$style) $font_weight_select.=' selected="selected"';
		$font_weight_select.='>'.$style_text.'</option>';
	}
	if ($field_name!='') $font_weight_select.='</select>';
	return $font_weight_select;
}

function get_font_weight_from_variant ($variant) {
	if ($variant=='') return '400';
	if ($variant=='regular') return '400';
	if ($variant=='italic') return '400';
	$num=0;
	if (strlen($variant)>=3) {
		$piece=substr($variant,0,3);
		if (is_numeric($piece))
			if (intval($piece)>0) $num=1;
	}

	if ($num && strlen($variant)>=4) {
		$piece=substr($variant,0,4);
		if (is_numeric($piece))
			if (intval($piece)>0) $num=2;
	}
	if ($num==1) return substr($variant,0,3);
	if ($num==2) return substr($variant,0,4);
	return '400';
}

function get_font_style_from_variant ($variant) {
	if ($variant=='') return 'regular';
	if ($variant=='regular') return 'regular';
	if ($variant=='italic') return 'italic';

	$num=0;
	if (strlen($variant)>=3) {
		$piece=substr($variant,0,3);
		if (is_numeric($piece))
			if (intval($piece)>0) $num=1;
	}

	if ($num && strlen($variant)>=4) {
		$piece=substr($variant,0,4);
		if (is_numeric($piece))
			if (intval($piece)>0) $num=2;
	}
	$r='';
	if ($num==1) $r = substr($variant,3);
	if ($num==2) $r = substr($variant,4);
	
	if ($r!='') return $r;

	return 'regular';
}

class usquare_functions {

	static public function get_filename_from_filepath($file) {
		$file_info=pathinfo($file);
		return $file_info['dirname'];
	}
	static public function get_directory_from_filepath($file) {
		$file_info=pathinfo($file);
		return $file_info['basename'];
	}
	static public function get_filename_from_url($url) {
		$pos=strrpos($url, "/");
		if ($pos!==FALSE) return substr($url, $pos+1);
		return $url;
	}
	static public function get_root_of_uploads_dir($with_slash=0) {	// return full filepath without / on end
		$upload_dir = wp_upload_dir();
		$path="";
		if (isset($upload_dir['basedir'])) $path = $upload_dir['basedir'];
		if ($path=="" && defined('ABSPATH'))
		{
			$slash="/";
			if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') $slash="\\";
			$path=ABSPATH."wp-content".$slash."uploads";
		}
		if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') $path=str_replace("/", "\\", $path);
		$slash="/";
		if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') $slash="\\";
		if ($with_slash) $path.=$slash;
		return $path;
	}
	static public function get_current_upload_dir($with_slash=0) {	// return full filepath without / on end
		$path=self::get_root_of_uploads_dir();
		$upload_dir = wp_upload_dir();
		if (isset($upload_dir['path'])) $path = $upload_dir['path'];
		if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') $path=str_replace("/", "\\", $path);
		$slash="/";
		if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') $slash="\\";
		if ($with_slash) $path.=$slash;
		return $path;
	}
	static public function get_full_urlpath_of_uploads_dir($with_slash=0) {
		$upload_dir = wp_upload_dir();
		$url=$upload_dir['baseurl'];
		if ($with_slash) $url.='/';
		return $url;	
	}
	static public function get_relative_urlpath_for_wordpress_folder() {	// return relative to webroot URL
		$wp_url=get_site_url()."/";
		if (substr($wp_url,0,7)=="http://") $wp_url=substr($wp_url,7);
		if (substr($wp_url,0,8)=="https://") $wp_url=substr($wp_url,8);
		$pos=strpos($wp_url, "/");
		$folder=substr($wp_url, $pos+1);
		if ($folder=='') $folder='/';
		return $folder;
	}
	static public function get_full_urlpath_for_wordpress_folder() {	// return relative URL
		return get_site_url()."/";
	}
	static public function get_full_urlpath_of_domain($with_slash=1) {	// return full URL with /
		$wp_url=get_site_url()."/";
		$pos=strpos($wp_url, "/", 8);
		return substr($wp_url, 0, $pos+$with_slash);
	}
	static public function get_webroot_filepath() {	// return full folderpath, ended with /
		$wp_path=ABSPATH;
		if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') $wp_path=str_replace("\\", "/", $wp_path);
		$wp_path_length=strlen($wp_path);
		$wordpress_folder=self::get_relative_urlpath_for_wordpress_folder();
		if ($wordpress_folder!='/') {
			$wordpress_folder_length=strlen($wordpress_folder);
			$ret = substr($wp_path, 0, $wp_path_length-$wordpress_folder_length);
		} else $ret = $wp_path;
		if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') $ret=str_replace("/", "\\", $ret);
		return $ret;
	}
	
	static public function get_relative_to_wordpress_urlpath_from_full_urlpath($url) {	// return relative URL without /
		if (substr($url,0,4)!='http') {
			if (substr($url,0,1)!='/') $url="/".$url;
			return $url;
		}
		$wp_path=ABSPATH;
		if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') $wp_path=str_replace("/", "\\", $wp_path);
		$wp_url=get_site_url()."/";
		$wp_url_length=strlen($wp_url);
		if (substr($url, 0, $wp_url_length)==$wp_url) {
			$piece=substr($url, $wp_url_length);
			return $piece;
		}
		return '';
	}
	static public function get_relative_to_webroot_urlpath_from_full_urlpath($url) {	// return relative URL with /
		$pos=strpos($url, '/', 8);
		return substr($url, $pos);
	}
	static public function get_full_urlpath_from_relative_urlpath($url) {	// return full URL
		if (self::is_http_link($url)) return $url;
		if (self::link_begin_with_slash($url)) {
			return self::get_full_urlpath_of_domain(0).$url;
		} else {
			return self::get_full_urlpath_for_wordpress_folder().$url;
		}
	}
	static public function get_relative_to_wordpress_urlpath_from_full_filepath($file) {	// return relative URL without /
		$wp_path=ABSPATH;
		if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') $wp_path=str_replace("/", "\\", $wp_path);
		$wp_path_length=strlen($wp_path);
		if (substr($file, 0, $wp_path_length)==$wp_path) {
			$piece=substr($file, $wp_path_length);
			if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') $piece=str_replace("\\", "/", $piece);
			return $piece;
		}
		return '';
	}
	static public function get_relative_to_webroot_urlpath_from_full_filepath($file) {	// return relative URL with	/
		$webroot_folderpath=self::get_webroot_filepath();
		//echo $webroot_folderpath; exit;
		$webroot_folderpath_length=strlen($webroot_folderpath);
		$ret = '/'.substr($file, $webroot_folderpath_length);
		if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') $ret=str_replace("\\", "/", $ret);
		return $ret;
	}
	static public function get_full_urlpath_from_full_filepath($file) {	// return full URL
		$wp_path=ABSPATH;
		$wp_url=get_site_url()."/";
		if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') $wp_path=str_replace("/", "\\", $wp_path);
		$wp_path_length=strlen($wp_path);
		if (substr($file, 0, $wp_path_length)==$wp_path) {
			$piece=substr($file, $wp_path_length);
			if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') $piece=str_replace("\\", "/", $piece);
			return $wp_url.$piece;
		}
		return '';
	}
	static public function get_full_urlpath_from_relative_to_wordpress_filepath($file) {	// return full URL
		$wp_url=get_site_url();
		if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') $file=str_replace("\\", "/", $file);
		if (substr($file,0,1)!='/') $file="/".$file;
		return $wp_url.$file;
	}
	static public function get_full_urlpath_from_relative_to_webroot_filepath($file) {	// return full URL
		$wp_url=get_site_url()."/";
		$wordpress=self::get_relative_urlpath_for_wordpress_folder();
		$wordpress_length=strlen($wordpress);
		$wp_url_length=strlen($wp_url);
		$piece=substr($wp_url, $wp_url_length-$wordpress_length);
		if ($piece==$wordpress) {
			$n=0;
			if ($wordpress=='/') $n=1;
			$root=substr($wp_url, 0, $wp_url_length-$wordpress_length+$n);
			//echo 'root='.$root;exit;
			if (substr($file,0,1)=='/' || substr($file,0,1)=='\\') $file=substr($file,1);
			if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') $file=str_replace("\\", "/", $file);
			return $root.$file;
		}
		return '';
	}
	static public function get_full_filepath_from_relative_filepath($file) {	// return full filepath
		$slash="/";
		if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') $slash="\\";
		$wp_path=ABSPATH;
		$wp_path_length=strlen($wp_path);
		if (substr($wp_path, $wp_path_length-1, 1)=='/') $wp_path=substr($wp_path, 0, $wp_path_length-1);
		if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') if (substr($wp_path, $wp_path_length-1, 1)=='\\') $wp_path=substr($wp_path, 0, $wp_path_length-1);
		if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') $file=str_replace("/", "\\", $file);
		if (substr($file,0,1)!=$slash) $file=$slash.$file;
		return $wp_path.$file;
	}
	static public function get_relative_filepath_from_full_filepath($file) {	// return relative filepath
		$wp_path=ABSPATH;
		$wp_path_length=strlen($wp_path);
		if (substr($wp_path, $wp_path_length-1, 1)=='/') $wp_path=substr($wp_path, 0, $wp_path_length-1);
		if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') $file=str_replace("/", "\\", $file);
		$wp_path_length=strlen($wp_path);
		if (substr($file, 0, $wp_path_length)==$wp_path) {
			$piece=substr($file,$wp_path_length);
			if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') $piece=str_replace("/", "\\", $piece);
			if (substr($piece,0,1)=='/' || substr($piece,0,1)=='\\') return substr($piece, 1);
			return $piece;
		}
		return '';	
	}
	static public function get_full_filepath_from_full_urlpath($url) {	// return full filepath
		$slash="/";
		if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') $slash="\\";
		$wp_path=ABSPATH;
		if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') $wp_path=str_replace("/", "\\", $wp_path);
		$wp_url=get_site_url()."/";
		$wp_url_length=strlen($wp_url);
		if (substr($url, 0, $wp_url_length)==$wp_url) {
			$piece=substr($url, $wp_url_length);
			if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') $piece=str_replace("/", "\\", $piece);
			$file=$wp_path.$piece;
			if (is_file($file)) return $file;
			//echo $piece."<br />"; //exit;
		}
		return '';
	}
	static public function get_full_filepath_from_relative_to_wordpres_urlpath ($url) {	// return full filepath
		$slash="/";
		if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') $slash="\\";
		$wp_path=ABSPATH;
		if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') $wp_path=str_replace("/", "\\", $wp_path);

		if (substr($url,0,1)=='/') $url=substr($url, 1);
		$piece=$url;
		if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') $piece=str_replace("/", "\\", $piece);
		//echo "PIECE: ".$piece."<br />"; exit;
		$file=$wp_path.$piece;
		if (is_file($file)) return $file;
		return '';
	}
	static public function get_full_filepath_from_relative_to_webroot_urlpath ($url) {// return full filepath
		if (substr($url,0,1)=='/') $url=substr($url, 1);
		$domain=self::get_full_urlpath_of_domain();
		$url=$domain.$url;
		return self::get_full_filepath_from_full_urlpath($url);
	}

/*
	static public function get_filepath_from_url_last_hope($url) {
		$slash="/";
		if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') $slash="\\";
		$wp_path=ABSPATH;
		if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') $wp_path=str_replace("/", "\\", $wp_path);
		$wp_url=get_site_url()."/";
		$wp_url_length=strlen($wp_url);
		if (substr($url, 0, $wp_url_length)==$wp_url) {
			$piece=substr($url, $wp_url_length);
			if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') $piece=str_replace("/", "\\", $piece);
			$file=$wp_path.$piece;
			if (is_file($file)) return $file;
			//echo $piece."<br />"; //exit;
		}
		if (substr($url,0,1)=='/') $url=substr($url, 1);
		$piece=$url;
		if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') $piece=str_replace("/", "\\", $piece);
		//echo "PIECE: ".$piece."<br />"; exit;
		$file=$wp_path.$piece;
		if (is_file($file)) return $file;
		
		//echo $wp_path."<br />";echo $wp_url."<br />";	exit;
		return '';
	}*/
	
	static public function get_filepath_from_url_smart($url) {	// MAIN FUNCTION FOR GETTING FILEPATH FROM URL
		//echo 'looking for: '.$url.'<br />';

		if (self::does_file_have_resolution($url)) {
			$burl=self::remove_resolution_from_file($url);
			$file=self::get_filepath_from_url($burl);
			if ($file!='' && is_file($file)) return $file;
		}

		$file=self::get_filepath_from_url($url);
		if ($file!='' && is_file($file)) return $file;


		$id=self::get_attachment_id_from_url_maybe_with_resolution ($url);
		if ($id!=NULL) {
			$file = self::get_attachment_file_from_id($id);
			if (is_file($file)) return $file;
		}

		if (self::is_http_link($url)) {
			if (self::does_file_have_resolution($url)) {
				$burl=self::remove_resolution_from_file($url);
				$file=self::get_full_filepath_from_full_urlpath($burl);
				if ($file!='' && is_file($file)) return $file;
			}
			$file=self::get_full_filepath_from_full_urlpath($url);
			if ($file!='' && is_file($file)) return $file;
		} else {
			if (self::does_file_have_resolution($url)) {
				$burl=self::remove_resolution_from_file($url);
				$file=self::get_full_filepath_from_relative_to_wordpres_urlpath ($burl);
				if ($file!='' && is_file($file)) return $file;
				$file=self::get_full_filepath_from_relative_to_webroot_urlpath ($burl);
				if ($file!='' && is_file($file)) return $file;
			}
			$file=self::get_full_filepath_from_relative_to_wordpres_urlpath ($url);
			if ($file!='' && is_file($file)) return $file;
			$file=self::get_full_filepath_from_relative_to_webroot_urlpath ($url);
			if ($file!='' && is_file($file)) return $file;
		}


		if (self::is_http_link($url)==FALSE) 
				$url=self::get_full_urlpath_from_relative_urlpath($url);
		
		if (self::does_file_have_resolution($url)) {
			$url2=self::remove_resolution_from_file($url);
			$ret = self::get_remote_and_upload($url2);
			if ($ret) return $ret;
		}
		
		$ret = self::get_remote_and_upload($url);
		if ($ret) return $ret;

		return '';
	}
	static public function get_filepath_from_url($url) {	// shortcut for getting filepath from database via guid
		$id=self::get_attachment_id_from_url ($url);
		if ($id!=NULL) return self::get_attachment_file_from_id($id);
		return '';
	}
	static public function get_attachment_id_from_url ($url) {
		global $wpdb;
		$query = "SELECT ID FROM {$wpdb->posts} WHERE guid='".$url."'";
		$id = $wpdb->get_var($query);
		return $id;
	}
	static public function get_attachment_id_from_url_without_resolution ($url) {
		global $wpdb;
		$file=self::get_relative_to_wordpress_urlpath_from_full_urlpath($url);
		$upload_dir=self::get_full_urlpath_of_uploads_dir(1);
		$upload_dir_length=strlen($upload_dir);
		if (substr($url,0, $upload_dir_length)==$upload_dir) {
			$file=substr($url, $upload_dir_length);
			$query = "SELECT post_id FROM {$wpdb->postmeta} WHERE meta_value LIKE '%".$file."%'";
			$id = $wpdb->get_var($query);
			if ($id==NULL) return NULL;
			return $id;
		}
		return NULL;
	}
	static public function get_attachment_id_from_url_maybe_with_resolution ($url) {
		if (self::does_file_have_resolution($url))
			$url=self::remove_resolution_from_file($url);
		
		return self::get_attachment_id_from_url_without_resolution ($url);	
	}
	static public function get_attachment_id_from_url_with_resolution_unsafe ($url) {
		global $wpdb;
		$file=self::get_filename_from_url($url);
		$query = "SELECT post_id FROM {$wpdb->postmeta} WHERE meta_value LIKE '%".$file."%'";
		$id = $wpdb->get_var($query);
		if ($id==NULL) return NULL;
		return $id;
	}
	static public function get_attachment_file_from_id($id) {
		$arr=wp_get_attachment_metadata($id);
		if ($arr===FALSE) return '';
		if (!is_array($arr)) return '';
		if (!isset($arr['file'])) return '';
		$file=$arr['file'];
		if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') $file=str_replace("/", "\\", $file);
		$upload_dir=self::get_root_of_uploads_dir();
		if ($upload_dir=='') return '';
		$slash="/";
		if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') $slash="\\";
		return $upload_dir.$slash.$file;
	}
	static public function remove_resolution_from_file($url) {
		$pos1=strrpos($url, "/");
		$pos11=strrpos($url, "\\");
		$pos1=max($pos1, $pos11);
		$pos2=strrpos($url, "-");
		$pos3=strrpos($url, "x");
		$pos4=strrpos($url, ".");
		$r=$pos4-$pos2;
		if ($pos1===FALSE || $pos2===FALSE || $pos3===FALSE || $pos4===FALSE) return FALSE;
		if ($pos1<$pos2 && $pos2<$pos3 && $pos3<$pos4 && $r<11) {
			$x=substr($url, $pos2+1, $pos3-$pos2-1);
			$y=substr($url, $pos3+1, $pos4-$pos3-1);
			if (is_numeric($x) && is_numeric($y)) {
				$part1=substr($url, 0, $pos2);
				$part2=substr($url, $pos4);
				return $part1.$part2;
			}
		}
		return FALSE;
	}
	static public function does_file_have_resolution($url)
	{
		//echo 'does_file_have_resolution ( '.$url.' )<br />';
		$pos1=strrpos($url, "/");
		$pos11=strrpos($url, "\\");
		$pos1=max($pos1, $pos11);
		$pos2=strrpos($url, "-");
		$pos3=strrpos($url, "x");
		$pos4=strrpos($url, ".");
		$r=$pos4-$pos2;
		if ($pos1===FALSE || $pos2===FALSE || $pos3===FALSE || $pos4===FALSE) return FALSE;
		if ($pos1<$pos2 && $pos2<$pos3 && $pos3<$pos4 && $r<11) {
			$x=substr($url, $pos2+1, $pos3-$pos2-1);
			$y=substr($url, $pos3+1, $pos4-$pos3-1);
			if (is_numeric($x) && is_numeric($y)) {
				$part1=substr($url, 0, $pos2);
				$part2=substr($url, $pos4);
				return TRUE;
			}
		}
		return FALSE;
	}
	static public function is_http_link ($url) {
		if (substr($url,0,7)=="http://") return TRUE;
		if (substr($url,0,8)=="https://") return TRUE;
		if (substr($url,0,2)=="//") return TRUE;
		return FALSE;	
	}
	static public function link_begin_with_slash($url) {
		if (substr($url,0,1)=="/") return TRUE;
		return FALSE;	
	}
	static public function save_file($file, $content) {
		$fp = fopen($file, 'w');
		if (!$fp) return FALSE;
		fwrite($fp, $content);
		fclose($fp);
		$stat = stat( dirname( $file ));
		$perms = $stat['mode'] & 0000666; //same permissions as parent folder, strip off the executable bits
		@chmod( $file, $perms );
		return TRUE;
	}
	
	static public function get_remote($url) {
		$response = wp_remote_get( $url );
		if( is_wp_error( $response ) ) {
		   //$error_message = $response->get_error_message();
		   //echo "Something went wrong: $error_message";
		   return FALSE;
		} else {
			if ($response['response']['code']=='404') return FALSE;
			return $response['body'];
		}
	}
	static public function get_remote_and_upload($url) {
		$content=self::get_remote($url);
		if ($content!==FALSE) {
			$filename=self::get_filename_from_url($url);

			$dir='';
			$tdir=self::get_current_upload_dir();
			if (is_writable($tdir)) {
				$dir=self::get_current_upload_dir(true);
			} else {
				$tdir=self::get_root_of_uploads_dir();
				if (is_writable($tdir)) {
					$dir=self::get_root_of_uploads_dir(true);
				}			
			}
			if ($dir=='') $dir=self::get_current_upload_dir(true);

			$md5=md5($url);
			$filepath=$dir.$filename;
			if (file_exists($filepath)) {
				$buf = file_get_contents($filepath);
				if ($buf==$content) return $filepath;
			}
			$filepath=$dir.$md5.'_'.$filename;
			if (file_exists($filepath)) return $filepath;
			$ret=self::save_file($filepath, $content);
			if (!$ret) return FALSE;
			return $filepath;
		}
		return FALSE;
	}
	static public function makethumb_image_db ($url, $w, $h, $opt=array(), $suffix = '', $dest_path = '', &$return_array2=NULL) {
		global $wpdb, $usquare_main_object;

		$return_array2=array(
			'orig_url' => $url,
			'orig_file' => '',
			'dest_url' => $url,
			'dest_file' => ''
		);

		$opts='';
		$variant=1;
		$opt_copy=array();
		foreach ($opt as $var => $val) {
			if ($opts!='') $opts.=',';
			if ($var==0) $variant=2;
			if ($variant==1) $opts.=$var;
			if ($variant==2) {
				$opts.=$val;
				$opt_copy[$val]=1;
			}
		}
		if ($variant==2) $opt=$opt_copy;
		//echo '<pre>'; print_r($opt); echo '</pre>'; exit;
		$ukey=$url.'?w='.$w.'&h='.$h;
		if ($opts!='') $ukey.='&opt='.$opts;
		if ($suffix!='') $ukey.='&suffix='.$suffix;
		if ($dest_path!='') $ukey.='&dest_path='.$dest_path;
		
		//echo $ukey; exit;
		$eukey=@mysql_real_escape_string($ukey);
		if ($eukey=='' || $eukey==NULL) $eukey=addslashes($ukey);

		$table = $wpdb->base_prefix . 'usquare_thmb';
		$thmb = $wpdb->get_row('SELECT * FROM ' . $table . ' WHERE ukey="'.$eukey.'"', ARRAY_A);
		if ($thmb!==NULL) {
			if (!is_file($thmb['dest_file']))
			{
				$wpdb->query('DELETE FROM ' . $table . ' WHERE ukey="'.$eukey.'"');
				$thmb=NULL;
			}
		}
		
		if ($thmb===NULL) {
			$return_array=array();
			self::makethumb_image($url, $w, $h, $opt, $suffix, $dest_path, $return_array);
			//echo '<pre>';print_r($return_array);echo '</pre>';
			if ($usquare_main_object->is_admin_panel==0 && $usquare_main_object->is_ajax==0) {
				$dest = usquare_functions::remove_resolution_from_file($return_array['dest_file']);
				if ($dest!='' && !file_exists($dest)) @copy ($return_array['dest_file'], $dest);
			}
			$data=array(
				'ukey' => $ukey,
				'orig_url' => $return_array['orig_url'],
				'orig_file' => $return_array['orig_file'],
				'dest_url' => $return_array['dest_url'],
				'dest_file' => $return_array['dest_file'],
				'width' => $w,
				'height' => $h,
				'filters' => $opts,
				'version' => 1
			);
			$return_array2=array(
				'orig_url' => $return_array['orig_url'],
				'orig_file' => $return_array['orig_file'],
				'dest_url' => $return_array['dest_url'],
				'dest_file' => $return_array['dest_file']
			);
			$wpdb->insert( $table, $data );
			return $return_array['dest_url'];
		} else {
			// check if file exists !!!!!!!!!!!!!!!
			if ($usquare_main_object->is_admin_panel==0 && $usquare_main_object->is_ajax==0) {
				$dest = usquare_functions::remove_resolution_from_file($thmb['dest_file']);
				if ($dest!='' && !file_exists($dest)) @copy ($thmb['dest_file'], $dest);
			}
			$return_array2=array(
				'orig_url' => $thmb['orig_url'],
				'orig_file' => $thmb['orig_file'],
				'dest_url' => $thmb['dest_url'],
				'dest_file' => $thmb['dest_file']
			);
			return $thmb['dest_url'];
		}
	}


	static public function makethumb_image($url, $w, $h, $opt=array(), $suffix = '', $dest_path = '', &$return_array=NULL) {
		require_once dirname( __FILE__ ) . '/usquare_image_functions.php';
		
		//if ($return_array===NULL) 

		$return_array=array(
			'orig_url' => $url,
			'orig_file' => '',
			'dest_url' => $url,
			'dest_file' => ''
		);
		
		$file=self::get_filepath_from_url_smart($url);
		if ($file=='') return $url;

		$return_array['orig_file']=$file;
		$return_array['dest_file']=$file;
		
		$md5=md5($url);
		$suffix.=$md5.'-';
		
		if ($dest_path=='') {
			$dir=self::get_filename_from_filepath($file);
			if ($dir!='' && $dir!=NULL) {
				if (!is_writable($dir)) {
					$dir2=self::get_current_upload_dir();
					if (is_writable($dir2)) {
						$dest_path=$dir2;
					} else {
						$dir2=self::get_root_of_uploads_dir();
						if (is_writable($dir2)) {
							$dest_path=$dir2;
						}
					}
				}
			}
		}
		
		$predicted_file=usquare_image_class::predict_final_file_static($file, $w, $h, $opt, $suffix, $dest_path);
		//echo 'predicted_file = '.$predicted_file.'<br />';
		if (is_file($predicted_file)) {
			$return_array['dest_file']=$predicted_file;
			$url2=self::get_full_urlpath_from_full_filepath($predicted_file);
			if ($url2) {
				$return_array['dest_url']=$url2;
				return $url2;
			}
		}

		$img = usquare_image_class::create_object($file);
		if ($img && !$img->is_error()) {
			if ($w!=0 && $h!=0) $img->resize($w,$h, true);
			if (isset($opt['gray'])) $img->gray();
			$file2 = $img->save($suffix, $dest_path);
			if ($file2) {
				$return_array['dest_file']=$file2;
				$url2=self::get_full_urlpath_from_full_filepath($file2);
				if ($url2) {
					$return_array['dest_url']=$url2;
					return $url2;
				}
			}
		}
		unset($img);

		return $url;
	}
}

?>
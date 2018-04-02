<?php 
$title = '';
require_once dirname( __FILE__ ) . '/usquare_settings.php';

foreach(explode('||',$tsettings) as $val) {
	$expl = explode('::',$val);
	$settings[$expl[0]] = $expl[1];
	$settings[$expl[0]] = str_replace('#|#|', '||', $settings[$expl[0]]);
	$settings[$expl[0]] = str_replace('#:#:', '::', $settings[$expl[0]]);
}

if ($titems != '') {
	$explode = explode('||',$titems);
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

require_once dirname( __FILE__ ) . '/usquare_functions.php';
$buf=generate_usquare($this, $settings, $itemsArray, $this->url, 0, $icons);
$usquare_width=$settings['usquare-items-per-line']*$settings['item-width'];

echo '<div style="width: '.$usquare_width.'px; position: relative; margin: 0 auto;">'.$buf.'</div>';
?>
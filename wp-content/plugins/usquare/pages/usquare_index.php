<div class="wrap">
	<h2>uSquares
			<a href="<?php echo admin_url( "admin.php?page=usquare_edit" ); ?>" class="add-new-h2">Add New</a>
	</h2>
<?php

?>

<div class="form_result" style="height: 35px;">&nbsp;</div>

<table class="wp-list-table widefat fixed">
	<thead>
		<tr>
			<th width="5%">ID</th>
			<th width="30%">Name</th>
			<th width="60%">Shortcode</th>
			<th width="20%">Actions</th>					
		</tr>
	</thead>
	
	<tfoot>
		<tr>
			<th>ID</th>
			<th>Name</th>
			<th>Shortcode</th>
			<th>Actions</th>					
		</tr>
	</tfoot>
	
	<tbody>
		<?php 
			global $wpdb;
			$prefix = $wpdb->base_prefix;

			if(isset($_GET['action']) && $_GET['action'] == 'delete') {
				$wpdb->query('DELETE FROM '. $prefix . 'usquare WHERE id = '.$_GET['id']);
			}
			$usquares = $wpdb->get_results("SELECT * FROM " . $prefix . "usquare ORDER BY id");
			if (count($usquares) == 0) {
				echo '<tr>'.
						 '<td colspan="100%">No uSquares found. Create one!</td>'.
					 '</tr>';
			} else {
				$tname;
				foreach ($usquares as $usquare) {
					$tname = $usquare->name;
					if(!$tname) {
						$tname = 'usquare #' . $usquare->id . ' (untitled)';
					}
					echo '<tr>'.
							'<td>' . $usquare->id . '</td>'.						
							'<td>' . '<a href="' . admin_url('admin.php?page=usquare_edit&id=' . $usquare->id) . '" title="Edit">'.$tname.'</a>' . '</td>'.
							'<td> [usquare id="' . $usquare->id . '"]</td>' .		
							'<td>' . '<a href="' . admin_url('admin.php?page=usquare_edit&id=' . $usquare->id) . '" title="Edit this item">Edit</a> | '.									  
								  '<a href="' . admin_url('admin.php?page=usquare&action=delete&id='  . $usquare->id) . '" title="Delete this item" >Delete</a>'.
							'</td>'.														
						'</tr>';
				}
			}
		?>
		
	</tbody>		 
</table>

<?php

$settings=$this->get_settings(1);
//echo '<pre>';print_r($settings); echo '</pre>';//exit;
$set_new_jquery=$settings['use_new_jquery'];
$use_lightbox=$settings['use_lightbox'];
$skip_head_section=$settings['skip_head_section'];
$do_not_resize_images=$settings['do_not_resize_images'];
$use_separated_jquery=$settings['use_separated_jquery'];
$new_jquery_url=$settings['new_jquery_url'];
$fix_encoding=$settings['fix_encoding'];
if ($set_new_jquery==0) {
	$new_jquery_div_style='display: none;';
	$new_jquery_checkbox_checked='';
} else {
	$new_jquery_div_style='';
	$new_jquery_checkbox_checked='checked="checked"';
}
if ($use_lightbox==0) {
	$use_lightbox_checked='';
} else {
	$use_lightbox_checked='checked="checked"';
}
if ($skip_head_section==0) {
	$skip_head_section_checked='';
} else {
	$skip_head_section_checked='checked="checked"';
}
if ($do_not_resize_images==0) {
	$do_not_resize_images_checked='';
} else {
	$do_not_resize_images_checked='checked="checked"';
}
if ($fix_encoding==0) {
	$fix_encoding_checked='';
} else {
	$fix_encoding_checked='checked="checked"';
}
if ($use_separated_jquery==0) {
	$use_separated_jquery_checked='';
} else {
	$use_separated_jquery_checked='checked="checked"';
}

$added_script_lines='';

$now = date('d-m-Y');
$now2 = date('m-Y');
if ($now!=$settings['last_update_check'])
{
	$ver=$this->plugin_version;
	$added_script_lines.='var should_check_latest_usquare_version="'.$ver.'"; ';
	$a=array();
	$a['last_update_check']=$now;
	$this->add_settings($a);
}

if ($settings['google_fonts']=='' || $now2!=$settings['google_fonts_update']) $added_script_lines.='var usquare_download_google_fonts=1; ';

if ($added_script_lines!='') {
echo '
<script>
';
echo $added_script_lines;
echo '
</script>
';
}

echo '<br /><span class="usquare-help" style="padding: 2px 0 0 0; font-size: 12px;">Troubleshooting?<span class="usquare-tooltip" style="left: 5px;">If plugin does not work - try to use separated jQuery</span></span><div class="clear"></div><label for="usquare_new_jquery_checkbox"><input type="checkbox" name="usquare_new_jquery_checkbox" id="usquare_new_jquery_checkbox" value="1" '.$new_jquery_checkbox_checked.' /> Use new jQuery version</label><br /><div id="usquare_new_jquery_div" style="'.$new_jquery_div_style.'">jQuery URL: <input type="text" name="usquare_new_jquery_url" id="usquare_new_jquery_url" value="'.$new_jquery_url.'" style="width: 400px;" /> <a href="#" id="usquare_new_jquery_check" class="button button-highlighted" style="display:inline-block; padding:0px 15px; margin:4px 10px;">Get latest</a> <a href="#" id="usquare_new_jquery_save" class="button button-highlighted" style="display:inline-block; padding:0px 15px; margin:4px 10px;">Save</a></div>
<br /><label for="usquare_use_separated_jquery_checkbox"><input type="checkbox" name="usquare_use_separated_jquery_checkbox" id="usquare_use_separated_jquery_checkbox" value="1" '.$use_separated_jquery_checked.' /> Use separated jQuery only for this plugin in order to skip possible conflicts (activate this option only if uSquares fails to open)</label><br />
<br /><label for="usquare_use_lightbox_checkbox"><input type="checkbox" name="usquare_use_lightbox_checkbox" id="usquare_use_lightbox_checkbox" value="1" '.$use_lightbox_checked.' /> Enable Lightbox</label><br />
<br /><label for="usquare_skip_head_section_checkbox"><input type="checkbox" name="usquare_skip_head_section_checkbox" id="usquare_skip_head_section_checkbox" value="1" '.$skip_head_section_checked.' /> Put CSS &amp; JS near HTML block, not in &lt;head&gt; section (this can help if your template or some other plugin screws up &lt;head&gt; section)</label><br />
<br /><label for="usquare_do_not_resize_images_checkbox"><input type="checkbox" name="usquare_do_not_resize_images_checkbox" id="usquare_do_not_resize_images_checkbox" value="1" '.$do_not_resize_images_checked.' /> Do not resize images</label><br />
<br /><label for="usquare_fix_encoding_checkbox"><input type="checkbox" name="usquare_fix_encoding_checkbox" id="usquare_fix_encoding_checkbox" value="1" '.$fix_encoding_checked.' /> Fix bad encoding</label><br />
';

require_once dirname( __FILE__ ) . '/usquare_functions.php';
usquare_check_upload_folder();
?>
<div style="margin-top:20px;">

<h2>Step by step:</h2>
<ol>
	<li><h3>Click on "Add New" button</h3></li>
	<li><h3>Setup your uSquare, save it, and come back here</h3></li>
	<li><h3>Copy "shortcode" from the table above and paste it in your post or page.<br />(for adding usquare into .php parts of template use it like this "&lt;?php echo do_shortcode('[usquare id="X"]'); ?&gt;" where X is id of your uSquare)</h3></li>
</ol>
</div>
</div>
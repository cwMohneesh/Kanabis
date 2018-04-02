<?php

class usquareAdmin {
	
	public $main, $path, $name, $url, $plugin_name, $plugin_version, $settings, $settings_status, $settings_in_db, $checked_for_upgrade, $uploader_type, $wp_version, $is_admin_panel, $is_ajax;

	function __construct($file, $name, $version) {
		global $usquare_main_object;
		$usquare_main_object=$this;
		$this->main = $file;
		$this->plugin_name = $name;
		$this->plugin_version = $version;
		$this->settings=array();
		$this->settings_status=0;
		$this->is_admin_panel=0;
		$this->is_ajax=0;
		$this->settings_in_db=array();
		$this->checked_for_upgrade=0;
		$this->alternative_jquery=0;
		$this->wp_version = get_bloginfo('version');
		$version35=$this->version_to_number('3.5');
		$current_version=$this->version_to_number($this->wp_version);
		if ($current_version>=$version35) $this->uploader_type=2;
		else $this->uploader_type=1;
		$this->init();
		/*	$this->add_settings(array(
				'version' => '1.4'
			));
			exit;*/
		return $this;
	}
	
	function init() {
		$this->path = dirname( __FILE__ );
		$this->name = basename( $this->path );
		$this->url = plugins_url( "/{$this->name}/" );
		$this->get_settings();
		if( is_admin() ) {
			$this->is_admin_panel=1;
			$this->settings['use_separated_jquery']=0;
			$this->alternative_jquery=0;
			
			register_activation_hook( $this->main , array(&$this, 'activate') );
			
			add_action('admin_menu', array(&$this, 'admin_menu')); 
			add_action( 'admin_enqueue_scripts', array(&$this, 'load_admin_scripts') );
			
			// Ajax calls
			// add_theme_support( 'post-thumbnails' );
			add_action('wp_ajax_usquare_save', array(&$this, 'ajax_save'));  
			add_action('wp_ajax_usquare_preview', array(&$this, 'ajax_preview'));
			add_action('wp_ajax_usquare_post_search', array(&$this, 'ajax_post_search'));
			add_action('wp_ajax_usquare_post_get', array(&$this, 'ajax_post_get'));
			add_action('wp_ajax_usquare_post_category_get', array(&$this, 'ajax_post_category_get'));
			add_action('wp_ajax_usquare_set_settings_2val', array(&$this, 'ajax_set_settings_2val'));
			add_action('wp_ajax_usquare_set_settings_1val', array(&$this, 'ajax_set_settings_1val'));
			add_action('wp_ajax_usquare_get_responder_answer', array(&$this, 'ajax_get_responder_answer'));
			add_action('wp_ajax_usquare_download_google_fonts', array(&$this, 'ajax_download_google_fonts'));
			add_action('wp_ajax_usquare_put_in_element', array(&$this, 'ajax_put_in_element'));
			add_action('wp_ajax_usquare_get_thumb', array(&$this, 'ajax_get_thumb'));

			add_filter( 'plugin_action_links', array(&$this, 'usquare_plugin_action_links'), 10, 2 );

			if (strpos($_SERVER['QUERY_STRING'], 'usquare')!==FALSE) add_filter('admin_footer_text', array(&$this, 'dashboard_footer'));
			
			$this->check_for_upgrade();
		}
		else {
			add_action('wp_head', array(&$this, 'header'));
			add_action('wp', array(&$this, 'frontend_includes'));
			add_shortcode('usquare', array(&$this, 'shortcode') );
			//add_filter('the_content', array(&$this, 'usquare_content_filter'), 100000);
		}
	}
	
	function load_admin_scripts() {
		if (strpos($_SERVER['QUERY_STRING'], 'usquare')!==FALSE) {
			//add_theme_support( 'post-thumbnails' );
			if ($this->uploader_type==2) wp_enqueue_media();
		}
	}

	
	function check_for_upgrade() {
		if ($this->checked_for_upgrade==1) return;
		if (count($this->settings)==0) $this->get_settings();
		if (!isset($this->settings_in_db['version'])) $version='1';
		else $version=$this->settings_in_db['version'];
		$old_version=$this->version_to_number($version);
		$current_version=$this->version_to_number($this->plugin_version);
		if ($old_version!=$current_version) $this->upgrade($old_version, $current_version);
		$this->checked_for_upgrade=1;
	}
	
	function version_to_number($version)
	{
		$version=strval($version);
		$arr=explode('.', $version);
		if (count($arr)<1) $arr[0]='0';
		if (count($arr)<2) $arr[1]='0';
		if (count($arr)<3) $arr[2]='0';
		$arr[0]=str_pad($arr[0], 3, '0', STR_PAD_LEFT);
		$arr[1]=str_pad($arr[1], 3, '0', STR_PAD_LEFT);
		$arr[2]=str_pad($arr[2], 3, '0', STR_PAD_LEFT);
		$r=$arr[0].$arr[1].$arr[2];
		return intval($r);
	}
	
	function activate() {
		global $wpdb;

		$table_name2 = $wpdb->base_prefix . 'usquare_thmb';
		if ($wpdb->get_var("SHOW TABLES LIKE '".$table_name2."'") != $table_name2) {
			$sql_string = "CREATE TABLE " . $table_name2 ." (
						`id` int(4) NOT NULL AUTO_INCREMENT,
						`ukey` varchar(512) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL,
						`orig_url` varchar(512) CHARACTER SET latin1 COLLATE latin1_general_ci DEFAULT NULL,
						`orig_file` varchar(512) CHARACTER SET latin1 COLLATE latin1_general_ci DEFAULT NULL,
						`dest_url` varchar(512) CHARACTER SET latin1 COLLATE latin1_general_ci DEFAULT NULL,
						`dest_file` varchar(512) CHARACTER SET latin1 COLLATE latin1_general_ci DEFAULT NULL,
						`width` int(4) NOT NULL DEFAULT '0',
						`height` int(4) NOT NULL DEFAULT '0',
						`filters` varchar(256) CHARACTER SET latin1 COLLATE latin1_general_ci DEFAULT NULL,
						`version` int(4) NOT NULL DEFAULT '0',
						PRIMARY KEY (`id`),
						UNIQUE KEY `ukey` (`ukey`)
					);";
			$wpdb->query($sql);
		}

		$table_name = $wpdb->base_prefix . 'usquare';
	
		if ($wpdb->get_var("SHOW TABLES LIKE '".$table_name."'") != $table_name) {
			$sql_string = "CREATE TABLE " . $table_name ." (
						  id INT(4) NOT NULL AUTO_INCREMENT,
						  name TINYTEXT NOT NULL COLLATE utf8_general_ci,
						  settings TEXT NOT NULL COLLATE utf8_general_ci,
						  items MEDIUMTEXT NOT NULL COLLATE utf8_general_ci,
						  PRIMARY KEY (id)
						);";	
	
			require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
			dbDelta($sql_string);
		}
	
	}	

	function upgrade ($old_version, $current_version) {
		global $wpdb;
		if ($old_version!=$current_version) {
			//echo $old_version.", ".$current_version; exit;
			if ($old_version<$this->version_to_number('1.4'))
			{
				$table_name = $wpdb->base_prefix . 'options';
				$v = $wpdb->get_var('SELECT autoload FROM '.$table_name.' WHERE option_name="usquare_settings"');
				if ($v=='yes') $wpdb->query('UPDATE '.$table_name.' SET autoload="no" WHERE option_name="usquare_settings"');
			}
			if ($old_version<$this->version_to_number('1.4.1'))
			{
				$table_name = $wpdb->base_prefix . 'usquare';
				$sql="ALTER TABLE ".$table_name." CHANGE items items MEDIUMTEXT CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL;";
				$wpdb->query($sql);
			}
			if ($old_version<$this->version_to_number('1.5'))
			{
				$table_name2 = $wpdb->base_prefix . 'usquare_thmb';
				if ($wpdb->get_var("SHOW TABLES LIKE '".$table_name2."'") != $table_name2) {
					$sql = "CREATE TABLE " . $table_name2 ." (
								`id` int(4) NOT NULL AUTO_INCREMENT,
								`ukey` varchar(512) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL,
								`orig_url` varchar(512) CHARACTER SET latin1 COLLATE latin1_general_ci DEFAULT NULL,
								`orig_file` varchar(512) CHARACTER SET latin1 COLLATE latin1_general_ci DEFAULT NULL,
								`dest_url` varchar(512) CHARACTER SET latin1 COLLATE latin1_general_ci DEFAULT NULL,
								`dest_file` varchar(512) CHARACTER SET latin1 COLLATE latin1_general_ci DEFAULT NULL,
								`width` int(4) NOT NULL DEFAULT '0',
								`height` int(4) NOT NULL DEFAULT '0',
								`filters` varchar(256) CHARACTER SET latin1 COLLATE latin1_general_ci DEFAULT NULL,
								`version` int(4) NOT NULL DEFAULT '0',
								PRIMARY KEY (`id`),
								UNIQUE KEY `ukey` (`ukey`)
							);";
					$wpdb->query($sql);
				}
			}

			if ($old_version==1005000 || $old_version==1005001)
			{
				$this->remove_thumbs();
			}

			$this->add_settings(array(
				'version' => $this->plugin_version
			));
		}
	}
	
	function remove_thumbs() {
		global $wpdb;
		$table = $wpdb->base_prefix . 'usquare_thmb';
		$thmb = $wpdb->get_results('SELECT * FROM ' . $table, ARRAY_A);
		foreach ($thmb as $id => $row) {
			if ($row['dest_file'] != $row['orig_file']) if (is_file($row['dest_file'])) @unlink($row['dest_file']);
		}
		$wpdb->query('DELETE FROM ' . $table);
	}

	function get_settings($skip_cache=false) {
		if ($skip_cache==false && count($this->settings)) return $this->settings;

		$this->settings=array(
			'use_new_jquery' => 0,
			'use_lightbox' => 0,
			'new_jquery_url' => 'http://ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js',
			'last_update_check' => '04-10-2014',
			'version' => $this->plugin_version,
			'google_fonts' => '',
			'google_fonts_update' => '10-2014',
			'use_separated_jquery' => 0,
			'skip_head_section' => 0,
			'do_not_resize_images' => 0,
			'fix_encoding' => 0
		);

		$this->settings_status=1;

		$a = array();
		$s=get_option('usquare_settings');
		if ($s!==FALSE) {
			if ($s!="") {
				$a=@unserialize($s);
				if (is_array($a)) $this->settings_in_db=$a;				
			}
		}
		
		//echo '<pre>get_settings: '; print_r($a); echo '</pre><hr />'; 
		if (is_array($a)) if (count($a)) $this->settings_status=2;

		if (is_array($a)) {
			foreach ($a as $var => $val) {
				$this->settings[$var] = $val;
			}
		}
		
		return $this->settings;
	}

	function add_settings($a) {
		$this->get_settings(true);
		//echo '<pre>add_settings: '; print_r($this->settings); echo '</pre><hr />'; exit;
		if (!is_array($a)) $a=array($a);
		foreach ($a as $var => $val) {
			$this->settings[$var] = $val;
		}
		$s=serialize($this->settings);
		if ($this->settings_status==2) {
			update_option('usquare_settings', $s);
		}
		else {
			$r=add_option('usquare_settings', $s, '', 'no');
			if ($r==false) update_option('usquare_settings', $s);
		}
		return TRUE;
	}
	function save_settings($a) {	// just a alias for add_settings()
		return $this->add_settings($a);
	}
	
	function get_separated_jquery_hack () {
		if ($this->alternative_jquery==1) return '';
		$jqueryurl = $this->settings['new_jquery_url'];
		$usquareurl = $this->url . 'js/frontend/jquery.usquare2.js';
		$scrollbarsurl = $this->url . 'js/frontend/jquery.tinyscrollbar2.min.js';
		$easingurl = $this->url . 'js/frontend/jquery.easing2.1.3.js';

		$this->alternative_jquery=1;

		// ---------------- ATTENTION ------------------
		// This piece of code will be used only if users template is using old jQuery.
		// This is the only way to avoid old jQuery, not removing old jQuery, but bringing 
		// totaly separated new version of jQuery and not replacing old jQuery,
		// because old jQuery must be kept in order to run old jQuery plugins which could not 
		// be run by new jQuery.
		// We are using inline .js including because this is the only way to 'copy' new 
		// jQuery immeditely after including it.
		// This piece of code is just a option - not activated by default.
		// By default - script will use wp_enqueue_script() function.

			$buffer = <<<eod
<script type='text/javascript'>
	if (typeof jQuery != 'undefined') var usquare_main_jquery1_backup = jQuery;
	if (typeof $ != 'undefined') var usquare_main_jquery2_backup = $;
</script>
<script type='text/javascript' src='$jqueryurl'></script>
<script type='text/javascript'>
	var usquare_jQuery = jQuery.noConflict();
	if (typeof usquare_main_jquery1_backup != 'undefined') jQuery = usquare_main_jquery1_backup;
	if (typeof usquare_main_jquery2_backup != 'undefined') $ = usquare_main_jquery2_backup;
</script>
<script type='text/javascript' src='$usquareurl'></script>
<script type='text/javascript' src='$scrollbarsurl'></script>
<script type='text/javascript' src='$easingurl'></script>

eod;
		return $buffer;
	}

	function header() {
		global $post;
		
		if (!isset($this->settings['skip_head_section'])) $this->settings['skip_head_section']=0;
		if ($this->settings['use_separated_jquery']==1 && $this->settings['skip_head_section']==0) {
			echo $this->get_separated_jquery_hack();
		}
		
		if (!isset($post->ID)) return;
		//print_r($post); exit;
		$mypost = get_post($post->ID);
		$content = $mypost->post_content;
		$start=0;
		$arr=array();
		while (1) {
			$pos = strpos($content, '[usquare id', $start);
			if ($pos===FALSE) break;
			$pos2 = strpos($content, '"', $pos);
			if ($pos2===FALSE) break;
			$pos3 = strpos($content, '"', $pos2+1);
			if ($pos3===FALSE) break;
			$start=$pos3;
			$arr[]=substr($content, $pos2+1, $pos3-$pos2-1);
		}
		if (count($arr)==0) return;
		require_once($this->path . '/pages/usquare_functions.php');
		
		$frontHtml='';
		foreach ($arr as $uid) {
			//echo $uid;
			$arr2=load_usquare($uid, $this);
			//echo '1: '.gettype($this).'<br />'; 
			$frontHtml.=generate_usquare($this, $arr2['settings'], $arr2['items'], $this->url, $uid, $arr2['icons'], 1);
		}
		echo $frontHtml;
		//exit;
	}
	
	function admin_menu() {
		$ctmenu = add_menu_page( 'uSquare', 'uSquare', 'manage_options', 'usquare', array(&$this, 'admin_page'));
		$submenu = add_submenu_page( 'usquare', 'uSquare', 'Add New', 'manage_options', 'usquare_edit', array(&$this, 'admin_edit_page'));
		
		add_action('load-'.$ctmenu, array(&$this, 'admin_menu_scripts')); 
		add_action('load-'.$submenu, array(&$this, 'admin_menu_scripts')); 
		add_action('load-'.$ctmenu, array(&$this, 'admin_menu_styles')); 
		add_action('load-'.$submenu, array(&$this, 'admin_menu_styles')); 
	}
	
	function admin_menu_scripts() {
		if (strpos($_SERVER['QUERY_STRING'], 'usquare')!==FALSE) {
			wp_enqueue_script('post');
			wp_enqueue_script('farbtastic');
			wp_enqueue_script('thickbox');
			wp_enqueue_script('usquare-admin-js', $this->url . 'js/usquare_admin.js' );
			wp_enqueue_script('jQuery-easing', $this->url . 'js/frontend/jquery.easing.1.3.js' );
			wp_enqueue_script('jQuery-usquare', $this->url . 'js/frontend/jquery.usquare.js' );
			wp_enqueue_script('jQuery-mousew', $this->url . 'js/frontend/jquery.mousewheel.min.js' );
			wp_enqueue_script('jQuery-tinyscrollbar', $this->url . 'js/frontend/jquery.tinyscrollbar.min.js' );
			
			$a=$this->get_settings();
			if ($a['use_lightbox']==1) {
				wp_enqueue_script('jQuery-lightbox', $this->url . 'js/frontend/lightbox.js' );
			}
			//wp_enqueue_script('jQuery-ui', $this->url . 'js/frontend/jquery-ui-1.9.1.min.js' );
			//wp_enqueue_script('jQuery-customScroll', $this->url . 'js/frontend/jquery.mCustomScrollbar.min.js' );			
		}
	}
	
	function admin_menu_styles() {
		if (strpos($_SERVER['QUERY_STRING'], 'usquare')!==FALSE) {
			wp_enqueue_script('jquery');
			wp_enqueue_style('farbtastic');	
			wp_enqueue_style('thickbox');
			wp_enqueue_style( 'usquare-admin-css', $this->url . 'css/usquare_admin.css' );
			wp_enqueue_style( 'usquare-thick-css', $this->url . 'css/thickbox.css' );
			wp_enqueue_style( 'usquare-css', $this->url . 'css/frontend/usquare_style.css' );
			//wp_enqueue_style( 'customScroll-css', $this->url . 'css/frontend/jquery.mCustomScrollbar.css' );	
			wp_enqueue_style( 'customfont1', $this->url . 'fonts/ostrich%20sans/stylesheet.css' );
			wp_enqueue_style( 'customfont2', $this->url . 'fonts/PT%20sans/stylesheet.css' );
			$a=$this->get_settings();
			if ($a['use_lightbox']==1) {
				wp_enqueue_style( 'lightbox-css', $this->url . 'js/lightbox-css/lightbox.css' );
			}
		}
	}

	function frontend_includes() {
		$a=$this->get_settings();
		if ($a['use_new_jquery']==1	&& strlen($a['new_jquery_url'])>2)
		{
			wp_deregister_script('jquery');
			wp_register_script('jquery', $a['new_jquery_url']);	
		}
		wp_enqueue_script('jquery');
		if ($this->settings['use_separated_jquery']==0) wp_enqueue_script('jQuery-easing', $this->url . 'js/frontend/jquery.easing.1.3.js' );
		if ($this->settings['use_separated_jquery']==0) wp_enqueue_script('jQuery-usquare', $this->url . 'js/frontend/jquery.usquare.js' );
		wp_enqueue_script('jQuery-mousew', $this->url . 'js/frontend/jquery.mousewheel.min.js' );
		//wp_enqueue_script('jQuery-customScroll', $this->url . 'js/frontend/jquery.mCustomScrollbar.min.js' );
		if ($this->settings['use_separated_jquery']==0) wp_enqueue_script('jQuery-tinyscrollbar', $this->url . 'js/frontend/jquery.tinyscrollbar.min.js' );

		wp_enqueue_style( 'usquare-css', $this->url . 'css/frontend/usquare_style.css' );
		//wp_enqueue_style( 'crollbar-css', $this->url . 'css/frontend/jquery.scrollbars.css' );
		//wp_enqueue_style( 'customScroll-css', $this->url . 'css/frontend/jquery.mCustomScrollbar.css' );
		wp_enqueue_style( 'customfont1', $this->url . 'fonts/ostrich%20sans/stylesheet.css' );
		wp_enqueue_style( 'customfont2', $this->url . 'fonts/PT%20sans/stylesheet.css' );
		if ($a['use_lightbox']==1) {
			wp_enqueue_script('jQuery-lightbox', $this->url . 'js/frontend/lightbox.js' );
			wp_enqueue_style( 'lightbox-css', $this->url . 'js/lightbox-css/lightbox.css' );
		}

	}

	function usquare_plugin_action_links( $links, $file ) {
		if ( $file == plugin_basename( dirname(__FILE__).'/usquare.php' ) ) {
			$links[] = '<a href="admin.php?page=usquare">'.__('Settings').'</a>';
		}

		return $links;
	}

	function dashboard_footer () {
		echo 'uSquare '.$this->plugin_version;
	}

	function ajax_save() {
		//echo $_POST['data']; exit;
		$post_array=explode('[odvoji]', $_POST['data']);
		foreach($post_array as $pval) {
			$pos=strpos($pval, '=');
			if ($pos!==FALSE) {
				$pkey=substr($pval, 0, $pos);
				$pval=substr($pval, $pos+1);
				$_POST[$pkey]=$pval;
			}
		}
		unset($_POST['data']);

		$id = false;
		$settings = '';
		$items = '';
		if (!isset($_POST['grayscale'])) $_POST['grayscale']=0;
		foreach( $_POST as $key => $value) {
			if ($key != 'action') {
				if ($key == 'usquare_id'){
					if ($value != '') {
						$id = (int)$value;
					}
				}
				else if ($key == 'usquare_title'){
					$name = stripslashes($value);
				}
				else if(strpos($key,'sort') === 0){
					$value=str_replace('||', '#|#|', $value);
					$value=str_replace('::', '#:#:', $value);
					$items .= $key . '::' . stripslashes($value) . '||';
				}
				else {
					$value=str_replace('||', '#|#|', $value);
					$value=str_replace('::', '#:#:', $value);
					$settings .= $key . '::' . stripslashes($value) . '||';
				}
			}
		}
		if ($items != '') $items = substr($items,0,-2);
		if ($settings != '') $settings = substr($settings,0,-2);
		global $wpdb;
		$table_name = $wpdb->base_prefix . 'usquare';
		if($id) {	
			$wpdb->update(
				$table_name,
				array(
					'name'=>$name,
					'settings'=>$settings,
					'items'=>$items),
				array( 'id' => $id ),
				array( 
					'%s',
					'%s',
					'%s'),
				array('%d')
			);
		}
		else {
			$wpdb->insert(
				$table_name,
				array(
					'name'=>$name,
					'settings'=>$settings,
					'items'=>$items),	
				array(
					'%s',
					'%s',
					'%s')						
				
			);
			$id = $wpdb->insert_id;
		}
		
			
		echo $id;
		die();
	}
	
	function ajax_preview() {
		//echo $_POST['data']; exit;
		$post_array=explode('[odvoji]', $_POST['data']);
		foreach($post_array as $pval) {
			$pos=strpos($pval, '=');
			if ($pos!==FALSE) {
				$pkey=substr($pval, 0, $pos);
				$pval=substr($pval, $pos+1);
				$_POST[$pkey]=$pval;
			}
		}
		unset($_POST['data']);
		
		$tid = false;
		$tsettings = '';
		$titems = '';
		if (!isset($_POST['grayscale'])) $_POST['grayscale']=0;
		foreach( $_POST as $key => $value) {
			if ($key != 'action') {
				if ($key == 'usquare_id'){
					if ($value != '') {
						$tid = (int)$value;
					}
				}
				else if ($key == 'usquare_title'){
					$tname = $value;
				}
				else if(strpos($key,'sort') === 0){
					$value=str_replace('||', '#|#|', $value);
					$value=str_replace('::', '#:#:', $value);
					$value=stripslashes($value);
					$titems .= $key . '::' . $value . '||';
				}
				else {
					$value=str_replace('||', '#|#|', $value);
					$value=str_replace('::', '#:#:', $value);
					$value=stripslashes($value);
					$tsettings .= $key . '::' . $value . '||';
				}
			}
		}
		if ($titems != '') $titems = substr($titems,0,-2);
		if ($tsettings != '') $tsettings = substr($tsettings,0,-2);
		
		include_once($this->path . '/pages/usquare_preview.php');
		
		die();
	}
	
	function ajax_post_search(){
		if(isset($_POST['query']) && !empty($_POST['query'])){
			$searchVal = strtolower($_POST['query']);
		}
		else {
			$searchVal = '';
		}
		
		$query_args = array( 'posts_per_page' => -1, 'post_type' => 'any');
		$query = new WP_Query( $query_args );
		
		foreach ( $query->posts as $match) {
			if($searchVal != ''){
				if(strpos(strtolower($match->post_name), $searchVal) !== false){
					$thumbn = wp_get_attachment_image_src( get_post_thumbnail_id($match->ID) , 'full');
					echo '<li><a href="'.$match->ID.'"><img style="margin-right:5px;" src="'.$thumbn[0].'" width="32" height="32" alt="" /><span class="usquarePostCompleteName">'.$match->post_title .'</span><span class="clear"></span></a></li>';
				}
			}
		}
		die();
	}
	
	function ajax_post_get($post_id = false){
		if (isset($_POST['post_id'])) $id = (int) $_POST['post_id'];
		if ($post_id) $id = $post_id;
		$post = get_post($id); 

		echo $post->post_title . '||';
		echo substr($post->post_date, 8, 2) . '/' . substr($post->post_date, 5, 2) . '/' . substr($post->post_date, 0, 4) . '||';
		$post_categories = get_the_category( $id );
		
		echo $post_categories[0]->name . '||';
		$excerpt = $post->post_excerpt;
		if ($excerpt == '' && $post->post_content != '') {
			//echo substr($post->post_content,0,100) . '...';
			echo $post->post_content;
		}
		
		echo $excerpt . '||';
		if ( has_post_thumbnail($id)) {
			echo wp_get_attachment_url( get_post_thumbnail_id($id , 'full'));
		}
		echo '||' . $post->post_content;

		if(!$post_id) {
			die();
		}
		
	}
	
	function ajax_post_category_get() {
		$cat_name = $_POST['cat_name'];
		$term = get_term_by('name', $cat_name, 'category');
		$cat_id = $term->term_id;
		
		$the_query = new WP_Query( array( 'cat' => $cat_id, 'post_type' => 'any', 'posts_per_page'=>-1, 'order' => 'ASC'));
		$start = true;
		while ( $the_query->have_posts() ) : $the_query->the_post();
			if ($the_query->post->post_type != 'page') {
				if (!$start) {
					echo '||';
				}
				$start = false;
				$this->ajax_post_get($the_query->post->ID);
			}
		endwhile;

		die();
	}
	
		
	function admin_page() {
		include_once($this->path . '/pages/usquare_index.php');
	}
	
	function admin_edit_page() {
		include_once($this->path . '/pages/usquare_edit.php');
	}
	
	function shortcode($atts) {
		global $last_usquare_id;
		extract(shortcode_atts(array(
			'id' => ''
		), $atts));
		
		if (!$id && isset($last_usquare_id)) {
			$id=$last_usquare_id;
		}
		$usquare_id=$id;
		require_once($this->path . '/pages/usquare_functions.php');
		
		$arr=load_usquare($usquare_id, $this);
		if ($arr===FALSE) return '';

		$frontHtml=generate_usquare($this, $arr['settings'], $arr['items'], $this->url, $usquare_id, $arr['icons'], 2);

		$frontHtml = preg_replace('/\s+/', ' ',$frontHtml);
		
		/*
		$frontHtml = str_replace('<', '[usquare_open_tag]', $frontHtml);
		$frontHtml = str_replace('>', '[usquare_close_tag]', $frontHtml); */

		return do_shortcode($frontHtml);
	}


	function ajax_set_settings_1val() {
		$var1 = $_POST['var1'];
		$val1 = $_POST['val1'];
		$a[$var1]=$val1;
		//print_r($a);
		$this->add_settings($a);
		echo 'Saved!';
		die();
	}

	function ajax_set_settings_2val() {
		$var1 = $_POST['var1'];
		$val1 = $_POST['val1'];
		$var2 = $_POST['var2'];
		$val2 = $_POST['val2'];
		$a[$var1]=$val1;
		$a[$var2]=$val2;
		$this->add_settings($a);
		echo 'Saved!';
		die();
	}

	function get_responder_answer($action, $var1='', $var2='') {
		include_once($this->path . '/pages/usquare_http_functions.php');
		$plugin_name=$this->plugin_name;
		$r=usquare_get_http ('http://www.shindiristudio.com/responder/responder.php?plugin_name='.$plugin_name.'&action='.$action.'&var1='.$var1.'&var2='.$var2, 8);
		return $r;
	}
	
	function ajax_get_responder_answer() {
		$action='';
		$var1='';
		$var2='';
		if (isset($_POST['action2'])) $action=$_POST['action2'];
		if (isset($_POST['var1'])) $var1=$_POST['var1'];
		if (isset($_POST['var2'])) $var1=$_POST['var2'];
		if ($action!='') echo $this->get_responder_answer($action, $var1, $var2);
		die();
	}
	
	function ajax_download_google_fonts() {
		include_once($this->path . '/pages/usquare_http_functions.php');
		$fonts=usquare_get_http ('http://www.shindiristudio.com/responder/fonts.txt', 8);
		$now = date('m-Y');
		if (strlen($fonts)>1000) {
			$this -> add_settings(array(
				'google_fonts' => $fonts,
				'google_fonts_update' => $now
			));
		}
		die();
	}
	
	function ajax_put_in_element() {
		require_once($this->path . '/pages/usquare_functions.php');
		if ($_POST['key']=='get_font_variants') {
			$fonts_array = usquare_get_font_array($this->settings, $this->path);
			$fonts_assoc_array = $fonts_array['assoc'];
			$variant_array = usquare_generate_font_variants_array($fonts_assoc_array, $_POST['val']);
			echo usquare_generate_font_variants_list($variant_array);
		}
		die();
	}
	
	function ajax_get_thumb() {
		$this->is_ajax=1;
		require_once($this->path . '/pages/usquare_functions.php');
		if (isset($_POST['url']) && isset($_POST['w']) && isset($_POST['h'])) {
			$url=$_POST['url'];
			$w=intval($_POST['w']);
			$h=intval($_POST['h']);
			$gray=0;
			if (isset($_POST['gray'])) $gray=intval($_POST['gray']);
			echo usquare_cache_image ($url, $w, $h, $gray);
		}
		die();
	}
}
?>
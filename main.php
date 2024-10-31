<?php
/*
  Plugin Name: Page Transitions
  Plugin URI: http://www.PageTransitions.com
  Description: The plugin adds amazing Page Transitions to your WordPress blog
  Author: Max Puhalevich
  Version: 1.4.4
  Author URI: http://wstune.com
 */

//Include config file
include_once('_config.php');

//Include all the modules
include_once(WPT_PLUGIN_PATH . '/modules/screenshot.php');
include_once(WPT_PLUGIN_PATH . '/modules/api.php');

//Add menu to the admin panel
add_action('init', 'wpt_init');
add_action('admin_menu', 'wpt_menu');
add_action('admin_head', 'wpt_head');
add_action('wp_head', 'wpt_head_frontend');
add_action('admin_notices', 'wpt_admin_notice');
add_action('admin_enqueue_scripts', 'wpt_admin_scripts');
register_activation_hook(__FILE__, 'wpt_activate');

//Get current version of the plugin
function wpt_version() {
	return '1.4.3';
}

//Init
function wpt_init() {
	wp_enqueue_script('swfobject');
	add_filter('home_template', 'wpt_home_template');
	add_filter('frontpage_template', 'wpt_home_template');
}

//Once plugin is activated
function wpt_activate() {

	//Create assets directory
	wp_mkdir_p(WP_CONTENT_DIR . '/ah_assets');

	//Concat all the page transition names into one line
	global $wpt_transitions, $wpt_default_options;
	$used_transitions = '';
	for ($i = 0; $i < count($wpt_transitions); $i++) {
		if ($i) {
			$used_transitions.= ',';
		}
		$used_transitions.= $wpt_transitions[$i][0];
	}

	//Set default option values
	$options = $wpt_default_options;
	$options['wpt_used_transitions'] = $used_transitions;

	foreach ($options as $name => $value) {
		wpt_create_option($name, $value);
	}

	//Generate random password
	update_option('wpt_pass', wpt_generate_password());
	
	//Copy theme files
	wpt_move_theme_files(true);
}

function wpt_move_theme_files($force = false){
	
	$theme_dir = STYLESHEETPATH . DIRECTORY_SEPARATOR;
	$plugin_dir = WPT_PLUGIN_PATH . DIRECTORY_SEPARATOR . 'theme-files' .DIRECTORY_SEPARATOR;
	
	//Website wrapper
	$file = $theme_dir . 'wpt-wrapper.php';
	if (!file_exists($file) || $force){
		if(!@copy($plugin_dir . 'wpt-wrapper.php', $file)){
			die('Page Transitions Plugin Error: theme dir is not writable');
		}
	}
	
	//General js
	$file = $theme_dir . 'wpt-generic.js';
	if (!file_exists($file)){
		if(!@copy($plugin_dir . 'wpt-generic.js', $file)){
			die('Page Transitions Plugin Error: theme dir is not writable');
		}
	}
}

function wpt_home_template($template) {
	
	if($_GET['wpt-wrapped'] || wpt_is_bot() || get_option('wpt_off')){		
		return $template;
	}
	wpt_move_theme_files();
	
	return STYLESHEETPATH . DIRECTORY_SEPARATOR . 'wpt-wrapper.php';
}

function wpt_admin_notice() {
	if ($_GET['page'] == 'wpt_queue_page') {
		return;
	} else if (get_option('wpt_queue')) {
		echo '<div class="updated"><p>WPT plugin requires <a href="' . get_bloginfo('url') . '/wp-admin/admin.php?page=wpt_queue_page">user interaction</a>.</p></div>';
	}
}

//Sidebar navigation
function wpt_menu() {

	//Top page
	add_menu_page('Page Transitions', 'Page Transitions', 'level_10', 'wpt_top_page', 'wpt_settings_page');

	//Settings page
	add_submenu_page('wpt_top_page', 'Settings', 'Settings', 'level_10', 'wpt_settings_page', 'wpt_settings_page');
	
	//Transition files
	add_submenu_page('wpt_top_page', 'Transition files', 'Transition files', 'level_10', 'wpt_trans_files_page', 'wpt_trans_files_page');

	//Notifications
	if (get_option('wpt_queue')) {
		$title = 'Notifications(!)';
	} else {
		$title = 'Notifications';
	}
	add_submenu_page('wpt_top_page', 'Notifications', $title, 'level_10', 'wpt_queue_page', 'wpt_queue_page');

	//Software page
	add_submenu_page('wpt_top_page', 'Software', 'Software', 'level_10', 'wpt_software_page', 'wpt_software_page');
	
	if (function_exists('remove_submenu_page')) {
		remove_submenu_page('wpt_top_page', 'wpt_top_page');
	} else {
		unset($GLOBALS['submenu']['wpt_top_page'][0]);
	}
}

//Load scripts for the settings page
function wpt_admin_scripts($hook) {
    if( 'page-transitions_page_wpt_settings_page' != $hook )
        return;
    wp_enqueue_script( 'farbtastic');
	wp_enqueue_style( 'farbtastic');
}

//Transitions page
function wpt_settings_page() {

	//Save wished page transitions
	if ($_POST['save_transitions']) {
		$str = join(',', $_POST['wpt_used_transitions']);
		update_option('wpt_used_transitions', $str);
		$success = true;

		//Turn off page transitions
	} else if ($_POST['wpt_transitions_off']) {
		update_option('wpt_off', '1');
		$success = true;

		//Turn on page transitions
	} else if ($_POST['wpt_transitions_on']) {
		update_option('wpt_off', '');
		$success = true;

		//Save settings
	} else if ($_POST['save_settings']) {
		update_option('wpt_page_w', max(0, intval($_POST['wpt_page_w'])));
		update_option('wpt_page_h', max(0, intval($_POST['wpt_page_h'])));
		update_option('wpt_quality', max(0, intval($_POST['wpt_quality'])));
		$success = true;

		//Generate new password
	} else if ($_POST['new_pass']) {
		update_option('wpt_pass', wpt_generate_password());
		$success = true;
		
		//Save styles
	} else if ($_POST['wpt_save_styles']) {
		update_option('wpt_font', stripslashes($_POST['wpt_font']));
		update_option('wpt_bold', intval($_POST['wpt_bold']));
		update_option('wpt_font_size', intval($_POST['wpt_font_size']));
		update_option('wpt_txt_color', $_POST['wpt_txt_color']);
		update_option('wpt_box_color', $_POST['wpt_box_color']);
		update_option('wpt_bg_color', $_POST['wpt_bg_color']);		
		$success = true;
	}

	//Include the template
	include(WPT_PLUGIN_PATH . '/templates/settings.php');
}

//Transition files page
function wpt_trans_files_page(){
	$wpt_error_msg = '';
	$wpt_success_msg = '';
	$alert_end = 'Please go to <a href=\''.get_bloginfo('url').'/wp-admin/admin.php?page=wpt_queue_page\'>notifications page</a> to complete this step';
	
	get_pagenum_link();
	
	/** RESET ALL **/
	if (isset($_POST['reset_transitions'])) {
		wpt_reset_transitions();
		$wpt_success_msg = 'All page transitions are added to queue list. '.$alert_end;
		
	/** REMOVE ALL FILES **/
	}else if (isset($_POST['remove_ah_files'])) {
		wpt_remove_ah_files();
		$wpt_success_msg = 'All page transitions files have been removed.';
	
	/** RESET HOME PAGE **/
	}else if(isset($_POST['wpt_reset_home'])){
		wpt_reset_home();
		$wpt_success_msg = 'Home page has been added to queue list. '.$alert_end;
		
	/** RESET ALL POSTS **/
	}else if(isset($_POST['wpt_reset_all_posts'])){
		wpt_reset_all_posts();
		$wpt_success_msg = 'All posts and pages have been added to queue list. '.$alert_end;
	
	/** RESET THE CATEGORIES **/
	}else if(isset($_POST['wpt_reset_cat'])){
		if(!$per_page = absint($_POST['wpt_posts_per_page'])){
			$wpt_error_msg = 'Wrong posts per page number';
		}else if(!$cat_id = absint($_POST['wpt_cat'])){
			wpt_reset_all_cats($per_page);
		}else {
			wpt_queue_cat($cat_id, $per_page);
			$wpt_success_msg = 'The category has been added to queue list. '.$alert_end;
		}
		
	/** RESET TAGS **/
	} else if(isset($_POST['wpt_reset_tags'])){
		if($_POST['wpt_tag'] == -1){
			wpt_reset_all_tags();
			$wpt_success_msg = 'All tags pages have been added to queue list. '.$alert_end;
		}else {
			wpt_reset_tag($_POST['wpt_tag']);
			$wpt_success_msg = 'The tag have been added to queue list. '.$alert_end;
		}
		
	/** RESET ARCHIVES **/
	} else if(isset($_POST['wpt_reset_archives'])){
		if($_POST['wpt_archives'] == -1){
			wpt_reset_all_archives();
			$wpt_success_msg = 'All archives pages have been added to queue list. '.$alert_end;
		}else {
			wpt_reset_archives($_POST['wpt_archives']);
			$wpt_success_msg = 'The archives have been added to queue list. '.$alert_end;
		}
		
	/** RESET AUTHORS **/
	} else if(isset($_POST['wpt_reset_author'])){
		if(!$_POST['wpt_author']){
			wpt_reset_all_authors();
			$wpt_success_msg = 'All authors pages have been added to queue list. '.$alert_end;
		}else {
			wpt_reset_author($_POST['wpt_author']);
			$wpt_success_msg = 'The author\'s pages have been added to queue list. '.$alert_end;
		}
		
	/** RESET PAGINATED URL **/
	}else if(isset($_POST['wpt_reset_url'])){
		$url = trim($_POST['wpt_custom_url']);
		$pages = absint($_POST['wpt_num_pages']);
		
		if(!$url){
			$wpt_error_msg = 'Enter the URL you wish to update.';
		}else if(!wpt_is_wp_url($url)){
			$wpt_error_msg = 'This URL does not belong to your blog.';
		}else {
			wpt_add_to_queue($url);
			for($i = 2; $i <= $pages; $i++){
				wpt_add_to_queue(wpt_get_pagenum_link($url, $i));
			}
			$wpt_success_msg = 'The url has been added to queue list. '.$alert_end;
		}
	}
	include(WPT_PLUGIN_PATH . '/templates/trans-files.php');
}

//Queue page
function wpt_queue_page() {
	if ($_POST['wpt_ignore']) {
		update_option('wpt_queue', '');
	}
	include(WPT_PLUGIN_PATH . '/templates/queue.php');
}

//Software page
function wpt_software_page(){
	include(WPT_PLUGIN_PATH . '/templates/software.php');
}

//Prepare the string for usage in javascript code
function wpt_script_escape($str) {
	$str = trim($str);
	return str_replace(array("\n", "\r", "\t"), array('', '', ''), $str);
}

//Get all page transitions which are used
function wpt_get_used_transitions() {
	return explode(',', get_option('wpt_used_transitions'));
}

//Check if page transition is used on the website
function wpt_is_transition_on($transition) {
	global $wpt_used_transitions;
	if (!$wpt_used_transitions) {
		$wpt_used_transitions = wpt_get_used_transitions();
	}
	return in_array($transition, $wpt_used_transitions);
}

//Check if url belongs to this wordpress
function wpt_is_wp_url($url){
	$wp_url = rtrim(get_bloginfo('url'), '/');
	if(strstr(rtrim($url, '/'), $wp_url) != 0){
		return false;
	}
	return true;
}

//Creates option if it is not exists
function wpt_create_option($name, $value) {
	if (!get_option($name)) {
		add_option($name, $value);
	}
}

//Add some javascript to the head
function wpt_head() {
	?>
	<script>
		var wpt_plugin_url = "<?php echo WPT_PLUGIN_URL ?>";
	</script>
	<?php
}

//Add some javascript to the head
function wpt_head_frontend() {
	?>
	<script src="<?php bloginfo('template_directory')?>/wpt-generic.js"></script>
	<?php
}

function wpt_generate_password($length = 8) {

	// start with a blank password
	$password = "";

	// define possible characters - any character in this string can be
	// picked for use in the password, so if you want to put vowels back in
	// or add special characters such as exclamation marks, this is where
	// you should do it
	$possible = "12346789bcdfghjkmnpqrtvwxyzBCDFGHJKLMNPQRTVWXYZ";

	// we refer to the length of $possible a few times, so let's grab it now
	$maxlength = strlen($possible);

	// check for length overflow and truncate if necessary
	if ($length > $maxlength) {
		$length = $maxlength;
	}

	// set up a counter for how many characters are in the password so far
	$i = 0;

	// add random characters to $password until $length is reached
	while ($i < $length) {

		// pick a random character from the possible ones
		$char = substr($possible, mt_rand(0, $maxlength - 1), 1);

		// have we already used this character in $password?
		if (!strstr($password, $char)) {
			// no, so it's OK to add it onto the end of whatever we've already got...
			$password .= $char;
			// ... and increase the counter by one
			$i++;
		}
	}

	// done!
	return $password;
}

/**
 * Retrieve links for page numbers.
 *
 * @since 1.5.0
 *
 * @param int $pagenum Optional. Page ID.
 * @return string
 */
function wpt_get_pagenum_link($request, $pagenum = 1) {
	global $wp_rewrite;

	$wp_url = rtrim(get_bloginfo('url'), '/');
	$request = substr($request, strlen($wp_url));
	
	$pagenum = (int) $pagenum;

	$home_root = parse_url(home_url());
	$home_root = ( isset($home_root['path']) ) ? $home_root['path'] : '';
	$home_root = preg_quote( trailingslashit( $home_root ), '|' );

	$request = preg_replace('|^'. $home_root . '|', '', $request);
	$request = preg_replace('|^/+|', '', $request);

	if ( !$wp_rewrite->using_permalinks()) {
		$base = trailingslashit( get_bloginfo( 'url' ) );

		if ( $pagenum > 1 ) {
			$result = add_query_arg( 'paged', $pagenum, $base . $request );
		} else {
			$result = $base . $request;
		}
	} else {
		$qs_regex = '|\?.*?$|';
		preg_match( $qs_regex, $request, $qs_match );

		if ( !empty( $qs_match[0] ) ) {
			$query_string = $qs_match[0];
			$request = preg_replace( $qs_regex, '', $request );
		} else {
			$query_string = '';
		}

		$request = preg_replace( "|$wp_rewrite->pagination_base/\d+/?$|", '', $request);
		$request = preg_replace( '|^index\.php|', '', $request);
		$request = ltrim($request, '/');

		$base = trailingslashit( get_bloginfo( 'url' ) );

		if ( $wp_rewrite->using_index_permalinks() && ( $pagenum > 1 || '' != $request ) )
			$base .= 'index.php/';

		if ( $pagenum > 1 ) {
			$request = ( ( !empty( $request ) ) ? trailingslashit( $request ) : $request ) . user_trailingslashit( $wp_rewrite->pagination_base . "/" . $pagenum, 'paged' );
		}

		$result = $base . $request . $query_string;
	}
	return $result;
}

function wpt_is_bot(){
	global $is_lynx, $is_gecko, $is_opera, $is_NS4, $is_safari, $is_chrome, $is_iphone, $is_IE;
	if($is_lynx)
		return false;
	if($is_gecko)
		return false;
	if($is_opera)
		return false;
	if($is_NS4)
		return false;
	if($is_safari)
		return false;
	if($is_chrome)
		return false;
	if($is_iphone)
		return false;
	if($is_IE)
		return false;
	
	/** BOT **/
	return true;
}
?>
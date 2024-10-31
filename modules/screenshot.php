<?php

add_action('save_post', 'wpt_save_postdata');
add_action('added_post_meta', 'wpt_meta_changed');
add_action('updated_post_meta', 'wpt_meta_changed');
add_action('deleted_post_meta', 'wpt_meta_changed');

//Remove ah files
function wpt_remove_ah_files(){
	$path = WP_CONTENT_DIR . '/ah_assets';
	$dir = opendir($path);
	
	while(($file = readdir($dir)) !== false){		
		if(!is_file($path.'/'.$file) || $file == '..' || $file == '.'){
			continue;
		}
		unlink($path.'/'.$file);
	}
	closedir($dir);
}

//Once post meta added / updated / deleted
function wpt_meta_changed($meta_id, $post_id = null) {

	if (!wpt_is_queue_post($post_id)) {
		return $post_id;
	}

	//Invoke event
	$post_id = apply_filters('wpt_add_post_to_queue', $post_id);
	wpt_add_to_queue(get_permalink($post_id));
}

//Save post event listener
function wpt_save_postdata($post_id) {

	//No actions for autosave
	if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
		return $post_id;
	}

	if (!wpt_is_queue_post($post_id)) {
		return $post_id;
	}

	//Invoke event
	wpt_add_post_to_queue($post_id);
}

//Check if we need to generate screenshot for the post
function wpt_is_queue_post($post_id) {

	//Validate id
	if (!$post_id = intval($post_id)) {
		return false;
	}

	//Get post  	
	$post = get_post($post_id);

	//Check post type
	global $wpt_post_types;
	if (!in_array($post->post_type, $wpt_post_types)) {
		return false;
	}

	//Check permissions
	if ($post->post_type == 'page' && !current_user_can('edit_page', $post_id)) {
		return false;
	} else if (!current_user_can('edit_post', $post_id)) {
		return false;
	}

	//Check the status
	if ($post->post_status != 'publish') {
		return false;
	}

	return true;
}

//Add post to the queue list
function wpt_add_post_to_queue($post_id) {
	if (!$post_id = intval($post_id)) {
		return false;
	}

	if ('page' == get_option('show_on_front') && get_option('page_on_front') && $post_id == get_option('page_on_front')) {
		wpt_reset_home();
	} else if ($post_id = apply_filters('wpt_add_post_to_queue', $post_id)) {
		wpt_add_to_queue(get_permalink($post_id));
		do_action('wpt_post_queued', $post_id);
	}
}

//Add category to the queue list
function wpt_queue_cat($cat_id, $per_page) {
	if (!$cat_id = intval($cat_id))
		return false;
	if (!$per_page = intval($per_page))
		return false;

	if ($cat_id = apply_filters('wpt_add_cat_to_queue', $cat_id, $per_page)) {
		$query = new WP_Query('cat=' . $cat_id . '&posts_per_page=' . $per_page);
		if (!$query->max_num_pages)
			return false;

		$base_url = get_category_link($cat_id);
		wpt_add_to_queue($base_url);

		for ($i = 2; $i <= $query->max_num_pages; $i++) {
			wpt_add_to_queue(wpt_get_pagenum_link($base_url, $i));
		}

		do_action('wpt_cat_queued', $cat_id, $per_page);
	}
}

//Add url to the queue list
function wpt_add_to_queue($url) {

	//Check if url is valid
	if (!wpt_is_valid_url($url)) {
		return false;
	}

	//Check if such url is already in queue
	$urls = get_option('wpt_queue');
	if (@in_array($url, $urls)) {
		return false;
	}

	//Add url
	$urls[] = $url;
	update_option('wpt_queue', $urls);
	return true;
}

//Check if the url is valid and belongs to this domain
function wpt_is_valid_url($url) {

	//Get WP domain
	$url_info = parse_url(get_bloginfo('url'));
	$domain = str_replace('.', '\.', $url_info['host']);

	//Validate url
	return preg_match('|^http://([a-z0-9-]+(\.[a-z0-9-]+)*\.)?' . $domain . '(:[0-9]+)?(/.*)?$|i', $url);
}

//Update all screenshots
function wpt_reset_transitions() {
	wpt_reset_home();
	wpt_reset_all_posts();
	wpt_reset_all_cats(intval(get_option('posts_per_page')));
	wpt_reset_all_archives();
	wpt_reset_all_tags();
	wpt_reset_all_authors();
	do_action('wpt_reset_transitions');
}

//Reset home page
function wpt_reset_home() {
	wpt_add_to_queue(get_bloginfo('url') . '/?wpt-wrapped=1');
	do_action('wpt_reset_home');
}

//Reset screenshots for all posts and pages
function wpt_reset_all_posts() {
	global $wpdb, $wpt_post_types;
	$sql = 'SELECT ID FROM ' . $wpdb->posts . ' 
			WHERE 
				post_type IN (';
	for ($i = 0; $i < count($wpt_post_types); $i++) {
		if ($i) {
			$sql.= ',';
		}
		$sql.='"' . $wpdb->escape($wpt_post_types[$i]) . '"';
	}
	$sql.= '	) 
				AND post_status = "publish"';
	
	if (!$items = $wpdb->get_results($sql)) {
		return;
	}
	
	for ($i = 0; $i < count($items); $i++) {
		wpt_add_post_to_queue($items[$i]->ID);
	}

	//Invoke one more event
	do_action('wpt_reset_all_posts');

	return true;
}

//Reset page transitions for all categories
function wpt_reset_all_cats($per_page) {
	$cats = get_categories(array('hide_empty' => 0, 'hierarchical' => 1));
	for ($i = 0; $i < count($cats); $i++) {
		wpt_queue_cat($cats[$i]->term_id, $per_page);
	}
	do_action('wpt_reset_all_cats');
}

//Reset one tag
function wpt_reset_tag($id) {
	if (!$id = absint($id))
		return false;
	if (!$url = get_tag_link($id))
		return false;

	$query = new WP_Query('tag_id=' . $id);
	wpt_add_to_queue($url);

	for ($i = 2; $i <= $query->max_num_pages; $i++) {
		wpt_add_to_queue(wpt_get_pagenum_link($url, $i));
	}
	do_action('wpt_reset_tag', $id);
}

//Reset all tags
function wpt_reset_all_tags() {
	$tags = get_tags();
	foreach ($tags as $tag) {
		wpt_reset_tag($tag->term_id);
	}
	do_action('wpt_reset_all_tags');
}

//Reset one user
function wpt_reset_author($id) {
	if (!$id = absint($id))
		return false;
	if (!$url = get_author_posts_url($id))
		return false;

	$query = new WP_Query('author=' . $id);
	wpt_add_to_queue($url);

	for ($i = 2; $i <= $query->max_num_pages; $i++) {
		wpt_add_to_queue(wpt_get_pagenum_link($url, $i));
	}
	do_action('wpt_reset_author', $id);
}

//Reset all users / authors
function wpt_reset_all_authors() {
	$users = get_users();	
	foreach ($users as $user) {		
		wpt_reset_author($user->ID);
	}
	do_action('wpt_reset_all_authors');
}

//Reset archives
function wpt_reset_archives($url) {

	/** Validate url * */
	$url = trim($url);
	if (!$url || !wpt_is_valid_url($url) || !wpt_is_wp_url($url))
		return false;

	/** Get year and month * */
	if (!preg_match('#(\d{4})/(\d{2})#', $url, $result) && !preg_match('#\?m=(\d{4})(\d{2})#', $url, $result))
		return false;

	do_action('wpt_reset_archives', $url);

	global $wpdb;

	/** Find how many posts it has * */
	$sql = 'SELECT COUNT(*) FROM ' . $wpdb->posts . ' WHERE post_date LIKE "' . $result[1] . '-' . $result[2] . '%" AND post_status="publish" AND post_type="post"';
	$num = $wpdb->get_var($sql);

	/** How many on one page * */
	$onpage = intval(get_option('posts_per_page'));

	/** Get total number of pages * */
	$pages = ceil($num / $onpage);

	/** Update urls * */
	wpt_add_to_queue($url);
	for ($i = 2; $i <= $pages; $i++) {
		wpt_add_to_queue(wpt_get_pagenum_link($url, $i));
	}

	return true;
}

//Reset all archives 
function wpt_reset_all_archives() {
	$str = wp_get_archives('format=option&echo=0');
	if (!preg_match_all('#value=\'([^\']*)\'#', $str, $result))
		return false;

	foreach ($result[1] as $url) {
		wpt_reset_archives($url);
	}

	do_action('wpt_reset_all_archives');
	return true;
}

?>
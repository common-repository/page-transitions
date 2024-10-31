<?php

include_once(WPT_PLUGIN_PATH . '/classes/SimpleXMLExtended.php');

add_action('wp_ajax_api_call', 'wpt_api_action');
add_action('wp_ajax_nopriv_api_call', 'wpt_api_action');

/**
 * Proceed ajax actions from the AIR applications 
 */
function wpt_api_action() {

	$xml = new SimpleXMLExtended('<response></response>');

	//Validate the password
	if ($_REQUEST['api_pass'] != get_option('wpt_pass', true)) {
		$xml->addChildCD('error', 'Wrong password');
		print $xml->asXMLFormated();
		die();
	}

	//Get list of the queued pages
	if ($_REQUEST['api_action'] == 'list') {

		//Get the settings
		$settings = $xml->addChild('settings');
		$settings->addChild('width', intval(get_option('wpt_page_w', true)));
		$settings->addChild('height', intval(get_option('wpt_page_h', true)));
		$settings->addChild('quality', intval(get_option('wpt_quality', true)));

		$items = get_option('wpt_queue');
		$node = $xml->addChild('urls');
		for ($i = 0; $i < count($items); $i++) {
			$node->addChildCD('item', $items[$i]);
		}

		//Upload the screenshot
	} else if ($_POST['wpt_action'] == 'upload') {
		$dir = WP_CONTENT_DIR . '/ah_assets';

		//Validate the url
		if(!$url = trim($_POST['url'])){
			wpt_api_error($xml, 'The application did not provide the url');
		}
		
		//Check if this url belongs to this website		
		if(!wpt_is_wp_url($url)){
			wpt_api_error($xml, 'This url does not belong to this WordPress');
		}
		$wp_url = rtrim(get_bloginfo('url'), '/');
				
		//Try to find this url in the url list. Remove it if found
		$url = strtolower($url);
		$urls = get_option('wpt_queue');
		$found = 0;
		$index = 0;
		
		for ($i = 0; $i < count($urls); $i++) {
			$list_url = $urls[$i];
			if($url == strtolower($list_url)){
				$found = true;				
				$index = $i;
				break;
			}
		}
		
		if(!$found){
			wpt_api_error($xml, 'The provided url was not found in queue');
		}
		
		//Generate file name
		$path = substr($url, strlen($wp_url) + 1);
		if(!$path || $path == '?wpt-wrapped=1')
			$path = 'index';
		$filename = preg_replace('/\W/', '_', $path);
		$filename = preg_replace('/^_*|_*$/', '', $filename);
		$filename = preg_replace('/_{2,}/', '_', $filename);
		$filename = $filename.'.pt';
		
		$file = $_FILES['Filedata'];
		if ($file['error'] !== UPLOAD_ERR_OK) {
			$xml->addChildCD('error', file_upload_error_message($file['error']));
		}else if (!move_uploaded_file($file['tmp_name'], $dir . '/' . $filename)) {
			$xml->addChildCD('error', 'Can not upload file. Please check the permissions');
		} else {
			//Remove this url from the queue
			array_splice($urls, $i, 1);
			update_option('wpt_queue', $urls);
			$xml->addChildCD('success', '1');
		}
	}

	print $xml->asXMLFormated();
	die();
}

function wpt_file_upload_error_message($error_code) {
	switch ($error_code) {
		case UPLOAD_ERR_INI_SIZE:
			return 'The uploaded file exceeds the upload_max_filesize directive in php.ini';
		case UPLOAD_ERR_FORM_SIZE:
			return 'The uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the HTML form';
		case UPLOAD_ERR_PARTIAL:
			return 'The uploaded file was only partially uploaded';
		case UPLOAD_ERR_NO_FILE:
			return 'No file was uploaded';
		case UPLOAD_ERR_NO_TMP_DIR:
			return 'Missing a temporary folder';
		case UPLOAD_ERR_CANT_WRITE:
			return 'Failed to write file to disk';
		case UPLOAD_ERR_EXTENSION:
			return 'File upload stopped by extension';
		default:
			return 'Unknown upload error';
	}
}

function wpt_api_error($xml, $error){
	$xml->addChildCD('error', $error);
	print $xml->asXMLFormated();
	die();
}
?>

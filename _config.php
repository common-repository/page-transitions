<?php
define('WPT_PLUGIN_PATH', dirname(__FILE__));
define('WPT_PLUGIN_URL', get_option('siteurl').'/wp-content/plugins/'.dirname(plugin_basename(__FILE__)));

global $wpt_transitions, $wpt_post_types, $wpt_default_options;

//List of all page transitions
$wpt_transitions = array(
	array('genie',			'Genie'),
	array('dispersion',		'Dispersion'),
	array('flip',			'Flip'),
	array('butterflies',	'Butterflies'),
	array('scanner',		'Scanner'),
	array('old-tv',			'Old TV'),
	array('bad-signal',		'Bad Signal'), 
	array('transformers',	'Transformers')
);

//List of post types which requires screenshots to be generated
$wpt_post_types = array('post', 'page');

//Default plugin options
$wpt_default_options = array(
	'wpt_page_w' 			=> 1100, 
	'wpt_page_h' 			=> 800, 
	'wpt_quality' 			=> 90
);
?>
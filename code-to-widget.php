<?php
/*
Plugin Name: Code to Widget
Plugin URI: http://seanklein.org
Description: Code to Widget Plugin uses PHP files from a specified directory, and (if the file has the proper template tags) adds a Widget.
Version: 1.2
Author: Sean Klein
Author URI: http://seanklein.org
*/

// Copyright (c) 2010 Sean Klein. All rights reserved.
//
// Released under the GPL license
// http://www.opensource.org/licenses/gpl-license.php
//
// This is an add-on for WordPress
// http://wordpress.org/
//
// **********************************************************************
// This program is distributed in the hope that it will be useful, but
// WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. 
// **********************************************************************

/**
 * Auxiliary Functionality
 */
// ini_set('display_errors', '1'); ini_set('error_reporting', E_ALL);
load_plugin_textdomain('skctw');
define('SKCW_VERSION', '1.2');
define('SKCW_DIR', plugin_dir_path(__FILE__));
//plugin_dir_url seems to be broken for including in theme files
if (file_exists(trailingslashit(get_template_directory()).'plugins/'.basename(dirname(__FILE__)))) {
	define('SKCW_DIR_URL', trailingslashit(trailingslashit(get_bloginfo('template_url')).'plugins/'.basename(dirname(__FILE__))));
}
else {
	define('SKCW_DIR_URL', trailingslashit(plugins_url(basename(dirname(__FILE__)))));	
}

/**
 * Add the menu to the Settings section of the Admin
 *
 * @return void
 */
function skcw_menu_items() {
	if (current_user_can('manage_options')) {
		add_options_page(
			__('Code To Widget','skctw'),
			__('Code To Widget','skctw'),
			10,
			'code-to-widget',
			'skcw_options'
		);
	}
}
add_action('admin_menu', 'skcw_menu_items');

/**
 * Request Handler for handling saving of settings
 *
 * @return void
 */
function skcw_request_handler() {
	if (current_user_can('manage_options') && !empty($_POST['sk_action'])) {
		switch ($_POST['sk_action']) {
			case 'update_code_widget':
				if (check_admin_referer('skcw_update_settings')) {
					update_option('skcw_template_directory', stripslashes($_POST['skcw_template_directory']));
					wp_redirect(admin_url('options-general.php?page=code-to-widget&skcw_message=updated'));
				}
				else {
					wp_redirect(admin_url('options-general.php?page=code-to-widget&skcw_message=failure'));
				}
				die();
				break;
			case 'check_directory':
				if (!empty($_POST['directory'])) {
					echo intval(skcw_check_directory(stripslashes($_POST['directory'])));
					die();
				}
				echo intval(false);
				die();
				break;
		}
	}
}
add_action('init','skcw_request_handler');

/**
 * Resources
 */

function skcw_admin_enqueue_scripts($hook = '') {
	switch ($hook) {
		case 'settings_page_code-to-widget':
			wp_enqueue_script('skcw-admin-js', admin_url('?sk_action=skcw_admin_js'), array('jquery'), SKCW_VERSION);
			wp_enqueue_style('skcw-admin-css', admin_url('?sk_action=skcw_admin_css'), array(), SKCW_VERSION, 'screen');
			break;
	}
}
add_action('admin_enqueue_scripts', 'skcw_admin_enqueue_scripts');

function skcw_resources() {
	if (!empty($_GET['sk_action'])) {
		switch ($_GET['sk_action']) {
			case 'skcw_admin_js':
				skcw_admin_js();
				die();
				break;
			case 'skcw_admin_css':
				skcw_admin_css();
				die();
				break;
		}
	}
}
add_action('init', 'skcw_resources', 1);

function skcw_admin_css() {
	header('Content-type: text/css');
	?>
	.skcw-template-directory-check-result {
		padding: 2px 8px;
		display: none;
	}
	.skcw-directory-searching {
		background: #FFFFFF url("<?php echo admin_url('images/wpspin_light.gif'); ?>") no-repeat scroll right center;
	}
	.skcw-directory-positive {
		background: #FFFFFF url("<?php echo includes_url('images/smilies/icon_cool.gif'); ?>") no-repeat scroll right center;
	}
	.skcw-directory-negative {
		background: #FFFFFF url("<?php echo includes_url('images/smilies/icon_exclaim.gif'); ?>") no-repeat scroll right center;
	}
	.skcw-directory-question {
		background: #FFFFFF url("<?php echo includes_url('images/smilies/icon_question.gif'); ?>") no-repeat scroll right center;
	}
	<?php
	die();
}

function skcw_admin_js() {
	header('Content-type: text/javascript');
	include(SKCW_DIR.'js/admin.js');
	die();
}

/**
 * Admin Functions
 */

/**
 * Function for display of Admin Options
 *
 * @return void
 */
function skcw_options() {
	$template_path = get_option('skcw_template_directory');
	$templates = get_option('skcw_templates_list');

	if ( isset($_GET['skcw_message']) ) {
		switch($_GET['skcw_message']) {
			case 'updated':
				?>
				<script type="text/javascript">
					jQuery(document).ready(function() {
						jQuery("#skcw_updated").show();
					});
				</script>
				<?php
				break;
		}
	}
	include(SKCW_DIR.'views/options-view.php');
}


/**
 * Widget Functions
 */
class SKCW_Widget extends WP_Widget {
	function SKCW_Widget() {
		$widget_ops = array( 'classname' => 'skcw-widget', 'description' => 'Widget for outputting content from the properly included files.' );
		$this->WP_Widget( 'skcw-widget', 'Code to Widget', $widget_ops );
	}

	function widget($args, $instance) {
		extract($args, EXTR_SKIP);
		$title = $instance['title'];
		$file = $instance['file'];
		$description = $instance['description'];
		
		$templates = get_option('skcw_templates_list');
		$template_path = trailingslashit(get_option('skcw_template_directory'));
		$template_file = $template_path.$templates[$file]['file'];
		
		// make sure we have a template directory
		if (!is_dir($template_path) || !is_readable($template_path)) { return false; }
		// make sure we have a file to include
		if (!is_file($template_file) || !is_readable($template_file)) { return false; }

		echo $before_widget;
		if (!empty($title)) {
			echo $before_title.$title.$after_title;
		}
		// Include the file for display
		@include($template_file);
		echo $after_widget;
	}

	function update($new_instance, $old_instance) {
		$instance = $old_instance;
		$instance['title'] = stripslashes($new_instance['title']);
		$instance['file'] = stripslashes($new_instance['file']);
		$instance['description'] = stripslashes($new_instance['description']);
		return $instance;
	}

	function form($instance) {
		$instance = wp_parse_args($instance, array('title' => '', 'file' => '', 'description' => ''));
		$title = $instance['title'];
		$file = $instance['file'];
		$description = $instance['description'];
		$templates = get_option('skcw_templates_list');
		
		if (empty($description)) {
			$description_show = ' style="display:none;"';
		}
		include(SKCW_DIR.'views/widget-form-view.php');
	}
}
add_action('widgets_init', create_function('', "register_widget('SKCW_Widget');"));

/**
 * This function adds the Widget data to the footer of the widgets.php page so we can get it
 * using JavaScript
 *
 * @return void
 */
function skcw_widget_descriptions() {
	$templates = get_option('skcw_templates_list');
	include(SKCW_DIR.'views/widget-page-view.php');
}
if (basename($_SERVER['SCRIPT_FILENAME']) == 'widgets.php') {
	add_action('admin_footer', 'skcw_widget_descriptions');
}

/**
 * This function takes the template directory, and checks to see if we can use it for the plugin
 *
 * @param string $template | Template to check
 * @return bool | Result of the check
 */
function skcw_check_directory($template) {
	if (is_dir($template) && is_readable($template)) {
		return true;
	}
	return false;
}

/**
 * Fish the login templates from the templates dir
 * Uses internal WP option caching to reduce filesystem hits
 * 
 */
function skcw_get_templates_list() {
	if (!empty($_GET['page']) && $_GET['page'] == 'code-to-widget') { return; }
	$template_path = get_option('skcw_template_directory');
	if (empty($template_path)) { return false; }

	$templates = wp_cache_get('skcw_templates', 'skcw_templates');
	if (!is_array($templates) || empty($templates)) {
		$templates = array();
	}
	else {
		return $templates;
	}
	
	$templ_dir = @opendir($template_path);
	if (!$templ_dir) { return false; }
	
	while (($file = readdir($templ_dir)) !== false) {
		if (is_file(trailingslashit($template_path).$file) && is_readable(trailingslashit($template_path).$file)) {
			$template_data = file_get_contents(trailingslashit($template_path).$file);
			if (preg_match('|Widget Name: (.*)$|mi', $template_data, $widget_name)) {
				$widget_name = $widget_name[1];
			}
			if (preg_match('|Widget Description: (.*)$|mi', $template_data, $widget_description)) {
				$widget_description = $widget_description[1];
			}
			if (preg_match('|Widget Title: (.*)$|mi', $template_data, $widget_title)) {
				$widget_title = $widget_title[1];
			}
			if (!empty($widget_name) && !empty($widget_description)) {
				$widget_classname = sanitize_title('skcw-'.$widget_name);
				$templates[$widget_classname] = array(
						'file' => $file,
						'widget_name' => $widget_name,
						'widget_classname' => $widget_classname,
						'widget_description' => $widget_description,
						'widget_title' => $widget_title
					);
			}
		}
	}
	@closedir($templ_dir);
	update_option('skcw_templates_list',$templates);
	wp_cache_set('skcw_templates', $templates, 'skcw_templates');
	return is_array($templates) ? $templates : false;
}
add_action('admin_init','skcw_get_templates_list');

?>
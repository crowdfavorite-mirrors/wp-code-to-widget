<?php
/*
Plugin Name: Code to Widget
Plugin URI: http://seanklein.org
Description: Code to Widget Plugin uses PHP files from a specified directory, and (if the file has the proper template tags) adds a Widget.
Version: 1.1.4
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
define('SKCW_VERSION', '1.1.4');

/**
 * Add the menu to the Settings section of the Admin
 *
 * @return void
 */
function skcw_menu_items() {
	if(current_user_can('manage_options')) {
		add_options_page(
			__('Code To Widget','skctw')
			, __('Code To Widget','skctw')
			, 10
			, basename(__FILE__)
			, 'skcw_options'
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
	if(current_user_can('manage_options')) {
		if (!empty($_POST['sk_action'])) {
			switch ($_POST['sk_action']) {
				case 'update_code_widget':
					check_admin_referer('skcw_update_settings');
					$template_directory = stripslashes($_POST['skcw_template_directory']);
					update_option('skcw_template_directory',$template_directory);
					wp_redirect(admin_url('options-general.php?page=code-to-widget.php&skcw_message=updated'));
					die();
					break;
				case 'check_directory':
					if (!empty($_POST['directory'])) {
						$directory = stripslashes($_POST['directory']);
						echo intval(skcw_check_directory($directory));
						die();
					}
					echo intval(false);
					die();
					break;
			}
		}
	}
}
add_action('init','skcw_request_handler');

/**
 * Resources
 */

function skcw_resources() {
	if (!empty($_GET['sk_action'])) {
		switch ($_GET['sk_action']) {
			case 'admin_js':
				skcw_admin_js();
				die();
				break;
			case 'admin_css':
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
		padding:2px 8px;
		display:none;
	}
	.skcw-directory-searching {
		background:#FFFFFF url("<?php echo admin_url('images/wpspin_light.gif'); ?>") no-repeat scroll right center;
	}
	.skcw-directory-positive {
		background:#FFFFFF url("<?php echo includes_url('images/smilies/icon_cool.gif'); ?>") no-repeat scroll right center;
	}
	.skcw-directory-negative {
		background:#FFFFFF url("<?php echo includes_url('images/smilies/icon_exclaim.gif'); ?>") no-repeat scroll right center;
	}
	.skcw-directory-question {
		background:#FFFFFF url("<?php echo includes_url('images/smilies/icon_question.gif'); ?>") no-repeat scroll right center;
	}
	<?php
	die();
}

function skcw_admin_js() {
	header('Content-type: text/javascript');
	?>
	;(function($) {
		$(function() {
			$("#skcw-template-directory-check").live('click', function(e) {
				var template_input = $("#skcw-template-directory");
				var search_result = $(".skcw-template-directory-check-result");
				var directory = template_input.val();
				
				if (directory == '') {
					alert('<?php _e('Please enter a directory in the field, then click the "Test Directory" button again.', 'skcw'); ?>');
					search_result.show().removeClass('skcw-directory-negative').removeClass('skcw-directory-positive').addClass('skcw-directory-question');
					e.preventDefault();
				}
				
				search_result.show().removeClass('skcw-directory-negative').removeClass('skcw-directory-positive').removeClass('skcw-directory-question').addClass('skcw-directory-searching');

				$.post('<?php echo admin_url(); ?>', {
					sk_action: 'check_directory',
					directory: directory
				}, function(r) {
					if (r == 1) {
						search_result.removeClass('skcw-directory-searching');
						search_result.addClass('skcw-directory-positive');
					}
					else {
						search_result.removeClass('skcw-directory-searching');
						search_result.addClass('skcw-directory-negative');
					}
				});
			});
			
			$("#skcw-show-instructions").live('click', function() {
				$("#skcw-instructions").slideToggle();
				$("#skcw-show-instructions-show").toggle();
				$("#skcw-show-instructions-hide").toggle();
			});
		});
	})(jQuery);
	<?php
	die();
}

// Enqueue the CSS and JS
if (!empty($_GET['page']) && $_GET['page'] == 'code-to-widget.php') {
	wp_enqueue_script('jquery');
	wp_enqueue_script('skcw-admin-js', admin_url('?sk_action=admin_js'), array('jquery'), SKCW_VERSION);
	wp_enqueue_style('skcw-admin-css', admin_url('?sk_action=admin_css'), array(), SKCW_VERSION, 'screen');
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
	?>
	<div id="skcw_updated" class="updated fade" style="display: none;">
		<p><?php _e('Settings Updated.', 'skctw'); ?></p>
	</div>
	<div class="wrap">
		<div class="icon32" id="icon-options-general"><br/></div>
		<h2><?php _e('Code To Widget Options','skctw'); ?></h2>
		<p>
			<?php _e('Enter the directory for the plugin to look in for widgets below.  This will need to be an absolute path to the directory.  Only files with the proper template tags will be included as widgets.  Click the "Test Directory" button to test to see if the directory is usable to gather widgets from.','skctw'); ?> <a href="#" id="skcw-show-instructions"><span id="skcw-show-instructions-show"><?php _e('Show Helpful Directory Paths', 'skctw'); ?></span><span id="skcw-show-instructions-hide" style="display:none;"><?php _e('Hide Helpful Directory Paths', 'skctw'); ?></span></a>
		</p>
		<div id="skcw-instructions" style="display:none;">
			<p><?php _e('Below are a few helpful directory paths.  Copy and paste them into the input field above.', 'skctw'); ?></p>
			<p><?php _e('Web root Path: ', 'skctw'); ?><code><?php echo trailingslashit(ABSPATH); ?></code></p>
			<p><?php _e('WP Content Path: ', 'skctw'); ?><code><?php echo trailingslashit(WP_CONTENT_DIR); ?></code></p>
			<p><?php _e('Template Path: ', 'skctw'); ?><code><?php echo trailingslashit(get_stylesheet_directory()); ?></code></p>
		</div>
		<form action="<?php admin_url(); ?>" method="post" id="skcw-form">
			<table class="form-table" width="200px">
				<tbody>
					<tr valign="top">
						<th scope="row" style="width:140px;">
							<label for="skcw-template-directory">
								<?php _e('Template Directory: ','skctw'); ?>
							</label>
						</th>
						<td>
							<input id="skcw-template-directory" name="skcw_template_directory" type="text" class="widefat" value="<?php echo esc_attr($template_path); ?>" style="width:500px;" />
							<span class="skcw-template-directory-check-result">&nbsp;</span>
							<input id="skcw-template-directory-check" name="skcw_template_directory_check" type="button" class="button" value="<?php _e('Test Directory', 'skctw'); ?>" />
						</td>
					</tr>
				</tbody>
			</table>
			<p class="submit" style="border-top: none;">
				<input name="sk_action" type="hidden" value="update_code_widget" />
				<input type="submit" name="submit" id="skcw-submit" class="button-primary" value="<?php _e('Update Settings', 'skctw'); ?>" />
				<?php wp_nonce_field('skcw_update_settings'); ?>
			</p>
		</form>
		<h3><?php _e('Currently Loaded Files','skctw'); ?></h3>
		<table class="widefat">
			<thead>
				<tr>
					<th class="manage-column" scope="col"><?php _e('File Name','skctw'); ?></th>
					<th class="manage-column" scope="col"><?php _e('Widget Name','skctw'); ?></th>
					<th class="manage-column" scope="col"><?php _e('Widget Classname','skctw'); ?></th>
					<th class="manage-column" scope="col"><?php _e('Widget Description','skctw'); ?></th>
					<th class="manage-column" scope="col"><?php _e('Widget Title','skctw'); ?></th>
				</tr>
			</thead>
			<tbody>
				<?php
				if(is_array($templates) && !empty($templates)) {
					foreach($templates as $template) {
						if (!is_array($template['file']) && !empty($template['file'])) {
							$file = esc_html($template['file']);
						}
						else {
							$file = '';
						}
						if (!is_array($template['widget_name']) && !empty($template['widget_name'])) {
							$name = esc_html($template['widget_name']);
						}
						else {
							$name = '';
						}
						if (!is_array($template['widget_classname']) && !empty($template['widget_classname'])) {
							$classname = esc_html($template['widget_classname']);
						}
						else {
							$classname = '';
						}
						if (!is_array($template['widget_description']) && !empty($template['widget_description'])) {
							$description = esc_html($template['widget_description']);
						}
						else {
							$description = '';
						}
						if (!is_array($template['widget_title']) && !empty($template['widget_title'])) {
							$title = esc_html($template['widget_title']);
						}
						else {
							$title = '';
						}
						?>
						<tr>
							<td>
								<?php echo $file; ?>
							</td>	
							<td>
								<?php echo $name; ?>
							</td>	
							<td>
								<?php echo $classname; ?>
							</td>	
							<td>
								<?php echo $description; ?>
							</td>	
							<td>
								<?php echo $title; ?>
							</td>	
						</tr>
						<?php
					}
				}
				else {
					?>
					<tr>
						<td colspan="5">
							<?php
							if (empty($template_path)) {
								_e('Please set the "Template Directory" above before trying to add widgets.', 'skctw');
							}
							else {
								_e('No widgets currently available.  Please add files to the folder referenced above with the proper template tags as referenced in the README file.', 'skctw');							}
							?>
						</td>
					</tr>
					<?php
				}
				?>
			</tbody>
		</table>
		<p>
			<?php _e('More options will come later.  Requests can be sent to: ','skctw'); ?><a href="mailto:sean@seanklein.org">Sean Klein</a>
		</p>
	</div>
	<?php
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
		if(!is_dir($template_path) || !is_readable($template_path)) { return false; }
		// make sure we have a file to include
		if(!is_file($template_file) || !is_readable($template_file)) { return false; }

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
		?>
		<p>
			<label for="skcw-widget-file">
				<?php _e('Select Widget File:','skctw'); ?>
			</label>
			<select id="<?php echo $this->get_field_id('file'); ?>" name="<?php echo $this->get_field_name('file'); ?>" class="widefat skcw-file-select">
				<option value="none"><?php _e('--None--', 'skctw')?></option>
				<?php 
				if (is_array($templates) && !empty($templates)) { 
					foreach ($templates as $key => $template) {
						?>
						<option value="<?php echo esc_attr($key); ?>"<?php selected($file, $key); ?>><?php echo esc_html($template['widget_name']); ?></option>
						<?php
					}
				} 
				?>
			</select>
		</p>
		<p>
			<label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:', 'skctw'); ?></label>
			<input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo $title; ?>" />
		</p>
		<p id="<?php echo $this->get_field_id('description_display'); ?>"<?php echo $description_show; ?>>
			<label for="<?php echo $this->get_field_id('description'); ?>">
				<?php _e('Widget Description:','skctw'); ?>
			</label>
			<div id="<?php echo $this->get_field_id('description'); ?>" class="skcw-description-display">
				<?php echo esc_html($description); ?>
			</div>
			<input type="hidden" id="<?php echo $this->get_field_id('description_hidden'); ?>" name="<?php echo $this->get_field_name('description'); ?>" value="<?php echo esc_attr($description); ?>" />
		</p>
		<?php				
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
	?>
	<script type="text/javascript">
		;(function($) {
			$(function() {
				$(".skcw-file-select").live('change', function() {
					var _this = $(this);
					var id = _this.attr('id').replace('widget-skcw-widget-', '').replace('-file', '');
					var value = _this.val();
					var description = $("#skcw-widget-description-"+value).html();
					var title = $("#skcw-widget-title-"+value).html();
					
					if (description == null) {
						$("#widget-skcw-widget-"+id+"-description_display").hide();
						$("#widget-skcw-widget-"+id+"-description_hidden").val('');
						$("#widget-skcw-widget-"+id+"-description").val('');
					}
					else {
						$("#widget-skcw-widget-"+id+"-description_display").show();
						$("#widget-skcw-widget-"+id+"-description_hidden").val(description);
						$("#widget-skcw-widget-"+id+"-description").html(description);
					}
					
					if (title == null) {
						$("#widget-skcw-widget-"+id+"-title").val('');
					}
					else {
						$("#widget-skcw-widget-"+id+"-title").val(title);
					}
				});
			});
		})(jQuery);
	</script>
	<style type="text/css">
		.skcw-description-display {
			font-weight:bold;
		}
	</style>	
	<div id="skcw-widget-descriptions" style="display:none;">
		<?php
		if (is_array($templates) && !empty($templates)) { 
			foreach ($templates as $key => $template) {
				?>
				<div id="skcw-widget-description-<?php echo esc_attr($key); ?>"><?php echo esc_html($template['widget_description']); ?></div>
				<?php 
				if (!empty($template['widget_title'])) { 
				?>
				<div id="skcw-widget-title-<?php echo esc_attr($key); ?>"><?php echo esc_html($template['widget_title']); ?></div>
				<?php
				}
			}
		}
		?>
	</div>
	<?php
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
	if(!$templ_dir) { return false; }
	
	while(($file = readdir($templ_dir)) !== false) {
		if(is_file(trailingslashit($template_path).$file) && is_readable(trailingslashit($template_path).$file)) {
			$template_data = file_get_contents(trailingslashit($template_path).$file);
			if(preg_match('|Widget Name: (.*)$|mi', $template_data, $widget_name)) {
				$widget_name = $widget_name[1];
			}
			if(preg_match('|Widget Description: (.*)$|mi', $template_data, $widget_description)) {
				$widget_description = $widget_description[1];
			}
			if(preg_match('|Widget Title: (.*)$|mi', $template_data, $widget_title)) {
				$widget_title = $widget_title[1];
			}
			if(!empty($widget_name) && !empty($widget_description)) {
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
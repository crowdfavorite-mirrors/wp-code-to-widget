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
			if (is_array($templates) && !empty($templates)) {
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
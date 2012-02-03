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
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
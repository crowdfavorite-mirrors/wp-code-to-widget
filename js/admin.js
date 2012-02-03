;(function($) {
	$(function() {
		$("#skcw-template-directory-check").live('click', function(e) {
			var template_input = $("#skcw-template-directory");
			var search_result = $(".skcw-template-directory-check-result");
			var directory = template_input.val();
			
			if (directory == '') {
				alert('Please enter a directory in the field, then click the "Test Directory" button again.');
				search_result.show().removeClass('skcw-directory-negative').removeClass('skcw-directory-positive').addClass('skcw-directory-question');
				e.preventDefault();
			}
			
			search_result.show().removeClass('skcw-directory-negative').removeClass('skcw-directory-positive').removeClass('skcw-directory-question').addClass('skcw-directory-searching');

			$.post('index.php', {
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
			
			e.preventDefault();
		});
		
		$("#skcw-show-instructions").live('click', function(e) {
			$("#skcw-instructions").slideToggle();
			$("#skcw-show-instructions-show").toggle();
			$("#skcw-show-instructions-hide").toggle();
			e.preventDefault();
		});
	});
})(jQuery);
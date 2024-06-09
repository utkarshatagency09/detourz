CKEDITOR.plugins.add('opencart', {
	init: function(editor) {
		// File manager
		editor.addCommand('OpenCart', {
			exec: function(editor) {
				$('#modal-image').remove();

				$.ajax({
					url: 'index.php?route=common/filemanager&user_token=' + getURLVar('user_token') + '&ckeditor=' + editor.name,
					dataType: 'html',
					success: function(html) {
						if(html.includes('id="modal-image"')){ // Opencart 4
							$('body').append(html);
						} else { // Opencart 3 and 2
							$('body').append('<div id="modal-image" class="modal">' + html + '</div>');

							$('#modal-image').delegate('a.thumbnail', 'click', function(e) {
									e.preventDefault();

									editor.insertHtml('<img src="' + $(this).attr('href') + '" alt="" title="" />');

									$('#modal-image').modal('hide');
							});
						}
						
						$('#modal-image').modal('show');
					}
				});
			}
		});

		editor.ui.addButton('OpenCart', {
			label: 'OpenCart',
			command: 'OpenCart',
			icon: this.path + 'images/opencart.svg'
		});

		// SVG manager
		editor.addCommand('svg', {
			exec: function(editor) {
				$('#modal-image').remove();

				$.ajax({
					url: 'index.php?route=extension/maza/common/svgmanager&user_token=' + getURLVar('user_token') + '&ckeditor=' + editor.name + '&mz_theme_code=' + getURLVar('mz_theme_code') + '&mz_skin_id=' + getURLVar('mz_skin_id'),
					dataType: 'html',
					success: function(html) {
						$('body').append('<div id="modal-image" class="modal">' + html + '</div>');

						$('#modal-image').delegate('a.thumbnail', 'click', function(e) {
								e.preventDefault();

								editor.insertHtml('<img src="' + $(this).attr('href') + '" width="100" alt="" title="" />');

								$('#modal-image').modal('hide');
						});
						
						$('#modal-image').modal('show');
					}
				});
			}
		});

		editor.ui.addButton('svg', {
			label: 'SVG',
			command: 'svg',
			icon: this.path + 'images/svg.svg'
		});
		
		// Font Awesome manager
		// editor.addCommand('fontIcon', {
		// 	exec: function(editor) {
		// 		$('#modal-image').remove();

		// 		$.ajax({
		// 			url: 'index.php?route=extension/maza/common/font_icon_manager&user_token=' + getURLVar('user_token') + '&ckeditor=' + editor.name  + '&mz_theme_code=' + getURLVar('mz_theme_code') + '&mz_skin_id=' + getURLVar('mz_skin_id'),
		// 			dataType: 'html',
		// 			success: function(html) {
		// 				$('body').append('<div id="modal-image" class="modal">' + html + '</div>');

		// 				$('#modal-image').delegate('a.icon-thumb', 'click', function(e) {
		// 						e.preventDefault();

		// 						editor.insertHtml('<i class="' + $(this).data('icon_class') + '" aria-hidden="true"></i>');
		// 						console.log('inserted');

		// 						$('#modal-image').modal('hide');
		// 				});
						
		// 				$('#modal-image').modal('show');
		// 			}
		// 		});
		// 	}
		// });

		// editor.ui.addButton('fontIcon', {
		// 	label: 'Font Awesome 5',
		// 	command: 'fontIcon',
		// 	icon: this.path + 'images/icon.png'
		// });
	}
});

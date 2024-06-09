/**
 * @package		MazaTheme
 * @author		Jay padaliya
 * @copyright   Copyright (c) 2022, TemplateMaza
 * @license		https://themeforest.net/licenses
 * @link		https://pocotheme.com/
 */

// color picker
$.fn.mzColorPicker = function(){
        // input element
        var color_input = $(this).find('input');
        
        // color indicator badge
        if($(this).data('color-badge')){
            var color_badge_target_el = $($(this).data('color-badge'));
        } else {
            var color_badge_target_el = color_input.siblings('.mz-colorpicker-badge');
        }
        color_badge_target_el.css('background-color', color_input.val());
        
        // @link http://www.eyecon.ro/colorpicker/
        color_input.ColorPicker({
                color: color_input.val(),
                onChange: function(hsb, hex, rgb){
                    color_input.val('#' + hex);
                    color_badge_target_el.css('background-color', '#' + hex);
                }
        });
        
        color_input.on('input change', function(){
                color_input.ColorPickerSetColor(this.value);
                color_badge_target_el.css('background-color', this.value);
        });
        
};

function mz_copyToClipboard(id) {
	/* Get the text field */
	var copyText = document.getElementById(id);
  
	/* Select the text field */
	copyText.select();
	copyText.setSelectionRange(0, 99999); /* For mobile devices */
  
	 /* Copy the text inside the text field */
	navigator.clipboard.writeText(copyText.value);
}

function mz_createSeoUrl(title){
	if (title) {
		return title.replaceAll(/[`~!@#$%^&*()_|+\-=?;:'",.<>\{\}\[\]\\\/]/gi, '').replaceAll(/\s+/g, ' ').replaceAll(' ', '-').toLowerCase();
	}
	return '';
}

function InitJsGlobal(html){
        var html = $(html);
        // color picker
        html.find('.mz-colorpicker').each(function(){
            $(this).mzColorPicker();
        });
        
        // list group tab
        html.find('.list-group > a[data-toggle="tab"]').on('shown.bs.tab', function(event){
            $(event.relatedTarget).removeClass('active');
            $(event.target).addClass('active');
        });
        
        // custom tab
        html.find('.mz-tab-toggle').click(function () {
            $(this).tab('show');
        });
        
        // input image widget
        html.find('.input-image-widget').each(function(){
                var meta_tab_content  =  $(this).find('.image-meta-tab-content');
                
                $(this).find('[data-toggle="tab"]').on('shown.bs.tab', function(e){
                    meta_tab_content.children('.active').removeClass('active');
                    meta_tab_content.children('.tab-' + $(this).data('meta')).addClass('active');
                });
        });
        
        // Active first tab
        html.find('.tab-dynamic').each(function(){
            $(this).children('li:first-child').children().tab('show');
        });
        
        // Fix label behavior with tab 
        // @deprecated
        html.find('label[data-toggle="tab"][for]').on('click', function(){
            $('#' + $(this).attr('for')).click();
        });
        
        // Fix input select in tab
        html.find('[data-toggle="input-tab"]').on('click', function(){
                $(this).tab('show');
        });
        
        // CKEditor
        $('[data-ckeditor]').each(function(){
                CKEDITOR.replace(this, {language: $(this).data('ckeditor')});
        });
        
        return html;
}

$(document).ready(function() {
        
        // Color scheme
        $('.color-scheme-option').on('click', function(){
            $(this).find('input[type=radio]').prop('checked', true);
            $(this).closest('.color-scheme').find('.color-scheme-option').removeClass('checked');
            $(this).addClass('checked');
        });
        
        
        
        // toggle-accordion
        $('.toggle-accordion').on('click', function(){
            var accordion_parent = $(this).data('parent');
            $(accordion_parent + ' .collapse.in').collapse('hide');
        });
        
        // Confirm
        $('.confirm').on('click', function(e){
            if(confirm($(this).data('confirm'))){
                return true;
            } else {
                e.preventDefault();
                return false;
            }
        });
        
        
        // Init Global JS
        InitJsGlobal(document);
        
});


// manager
$(function(){
	// SVG Image Manager
	$(document).on('click', 'a[data-toggle=\'svg\']', function(e) {
		var $element = $(this);
		var $popover = $element.data('bs.popover'); // element has bs popover?

		e.preventDefault();

		// destroy all image popovers
		$('a[data-toggle="svg"]').popover('destroy');

		// remove flickering (do not re-add popover when clicking for removal)
		if ($popover) {
			return;
		}

		$element.popover({
			html: true,
			placement: 'right',
			trigger: 'manual',
			content: function() {
				return '<button type="button" id="button-image" class="btn btn-primary"><i class="fa fa-pencil"></i></button> <button type="button" id="button-clear" class="btn btn-danger"><i class="fa fa-trash-o"></i></button>';
			}
		});

		$element.popover('show');

		$('#button-image').on('click', function() {
			var $button = $(this);
			var $icon   = $button.find('> i');

			$('#modal-image').remove();

			$.ajax({
				url: 'index.php?route=extension/maza/common/svgmanager&user_token=' + getURLVar('user_token') + '&mz_theme_code=' + getURLVar('mz_theme_code') + '&mz_skin_id=' + getURLVar('mz_skin_id') + '&target=' + $element.parent().find('input').attr('id') + '&thumb=' + $element.attr('id'),
				dataType: 'html',
				beforeSend: function() {
					$button.prop('disabled', true);
					if ($icon.length) {
						$icon.attr('class', 'fa fa-circle-o-notch fa-spin');
					}
				},
				complete: function() {
					$button.prop('disabled', false);

					if ($icon.length) {
						$icon.attr('class', 'fa fa-pencil');
					}
				},
				success: function(html) {
					$('body').append('<div id="modal-image" class="modal">' + html + '</div>');

					$('#modal-image').modal('show');
				}
			});

			$element.popover('destroy');
		});

		$('#button-clear').on('click', function() {
			$element.find('img').attr('src', $element.find('img').attr('data-placeholder'));

			$element.parent().find('input').val('');

			$element.popover('destroy');
		});
	});
	
	// Font icon Manager
	$(document).on('click', 'a[data-toggle=\'font\']', function(e) {
		var $element = $(this);
		var $popover = $element.data('bs.popover'); // element has bs popover?

		e.preventDefault();

		// destroy all image popovers
		$('a[data-toggle="font"]').popover('destroy');

		// remove flickering (do not re-add popover when clicking for removal)
		if ($popover) {
			return;
		}

		$element.popover({
			html: true,
			placement: 'right',
			trigger: 'manual',
			content: function() {
				return '<button type="button" id="button-image" class="btn btn-primary"><i class="fa fa-pencil"></i></button> <button type="button" id="button-clear" class="btn btn-danger"><i class="fa fa-trash-o"></i></button>';
			}
		});

		$element.popover('show');

		$('#button-image').on('click', function() {
			var $button = $(this);
			var $icon   = $button.find('> i');

			$('#modal-image').remove();

			$.ajax({
				url: 'index.php?route=extension/maza/common/font_icon_manager&user_token=' + getURLVar('user_token') + '&mz_theme_code=' + getURLVar('mz_theme_code') + '&mz_skin_id=' + getURLVar('mz_skin_id') + '&target=' + $element.parent().find('input').attr('id') + '&thumb=' + $element.attr('id'),
				dataType: 'html',
				beforeSend: function() {
					$button.prop('disabled', true);
					if ($icon.length) {
						$icon.attr('class', 'fa fa-circle-o-notch fa-spin');
					}
				},
				complete: function() {
					$button.prop('disabled', false);

					if ($icon.length) {
						$icon.attr('class', 'fa fa-pencil');
					}
				},
				success: function(html) {
					$('body').append('<div id="modal-image" class="modal">' + html + '</div>');

					$('#modal-image').modal('show');
				}
			});

			$element.popover('destroy');
		});

		$('#button-clear').on('click', function() {
			$element.find('i').attr('class', $element.find('i').attr('data-placeholder'));

			$element.parent().find('input').val('');

			$element.popover('destroy');
		});
	});

	// Multi select Image Manager
	$(document).on('click', '[data-toggle=\'mz-multi-image\']', function(e) {
		e.preventDefault();

		var $button = $(this);
		var $icon   = $button.find('> i');
		var $icon_class = $icon.attr('class');

		$('#modal-image').remove();

		$.ajax({
			url: 'index.php?route=common/filemanager&user_token=' + getURLVar('user_token') + '&mz_callback_func=' + $button.data('callback'),
			dataType: 'html',
			beforeSend: function() {
				$button.prop('disabled', true);
				if ($icon.length) {
					$icon.attr('class', 'fa fa-circle-o-notch fa-spin');
				}
			},
			complete: function() {
				$button.prop('disabled', false);

				if ($icon.length) {
					$icon.attr('class', $icon_class);
				}
			},
			success: function(html) {
				$('body').append('<div id="modal-image" class="modal">' + html + '</div>');

				$('#modal-image').modal('show');
			}
		});
	});

	// Link Manager
	$(document).on('click', '[data-toggle="linkmanager"]', function(e) {
		var target = $(this).parents('.input-group').children('input[type="hidden"]').attr('id');
		var $button = $(this);
		var $icon   = $button.find('> i');

		$('#modal-link').remove();

		$.ajax({
			url: 'index.php?route=extension/maza/common/linkmanager&user_token=' + getURLVar('user_token') + '&target=' + target + '&mz_theme_code=' + getURLVar('mz_theme_code') + '&mz_skin_id=' + getURLVar('mz_skin_id'),
			dataType: 'html',
			beforeSend: function() {
				$button.prop('disabled', true);
				if ($icon.length) {
					$icon.attr('class', 'fa fa-circle-o-notch fa-spin');
				}
			},
			complete: function() {
				$button.prop('disabled', false);

				if ($icon.length) {
					$icon.attr('class', 'fa fa-pencil');
				}
			},
			success: function(html) {
				$('body').append('<div id="modal-link" class="modal">' + html + '</div>');

				$('#modal-link').modal('show');
			}
		});
	});
	$(document).on('click', '[data-toggle="linkmanager-delete"]', function(e) {
		$group = $(this).parents('.input-group');
		$group.children('.input-group-addon').html('<i class="fa fa-link"></i>');
		$group.children('input[type="text"]').val('');
		$group.children('input[type="hidden"]').val('');
	});
});

// Layout builder Manager
$(function(){
	// widget
	$(document).on('click', '[data-toggle="mz-widget"]', function(e) {
		e.preventDefault();

		var $button = $(this);
		var $icon   = $button.find('> i');

		$.ajax({
			url: 'index.php?route=extension/maza/layout_builder/setting_widget&user_token=' + getURLVar('user_token') + '&mz_theme_code=' + getURLVar('mz_theme_code') + '&mz_skin_id=' + getURLVar('mz_skin_id') + '&target_id=' + $($button.data('target')).attr('id') + '&code=' + $button.data('code'),
			dataType: 'html',
			data: $($button.data('target')).val(),
			type: 'post',
			beforeSend: function() {
				$button.prop('disabled', true);
				if ($icon.length) {
					$icon.attr('class', 'fa fa-cog fa-spin');
				}
			},
			complete: function() {
				$button.prop('disabled', false);

				if ($icon.length) {
					$icon.attr('class', 'fa fa-cog');
				}
			},
			success: function(html) {
				$('body').append(html);
			}
		});
	});

	$(document).on('change', '.mz-widget > select', function(e) {
		var $settingToggle = $(this).parent().find('[data-toggle="mz-widget"]');

		$settingToggle.data('code', $(this).val());
		$($settingToggle.data('target')).val('');
		
		if($(this).val()){
			$settingToggle.removeClass('disabled').prop('disabled', 0);
			$settingToggle.trigger('click');
		} else {
			$settingToggle.addClass('disabled').prop('disabled', 1);
		}
	});

	// design
	$(document).on('click', '[data-toggle="mz-design"]', function(e) {
		e.preventDefault();

		var $button = $(this);
		var $icon   = $button.find('> i');

		$.ajax({
			url: 'index.php?route=extension/maza/layout_builder/setting_design&user_token=' + getURLVar('user_token') + '&mz_theme_code=' + getURLVar('mz_theme_code') + '&mz_skin_id=' + getURLVar('mz_skin_id') + '&target_id=' + $($button.data('target')).attr('id') + '&code=' + $button.data('code'),
			dataType: 'html',
			data: $($button.data('target')).val(),
			type: 'post',
			beforeSend: function() {
				$button.prop('disabled', true);
				if ($icon.length) {
					$icon.attr('class', 'fa fa-cog fa-spin');
				}
			},
			complete: function() {
				$button.prop('disabled', false);

				if ($icon.length) {
					$icon.attr('class', 'fa fa-cog');
				}
			},
			success: function(html) {
				$('body').append(html);
			}
		});
	});

	$(document).on('change', '.mz-design > select', function(e) {
		var $settingToggle = $(this).parent().find('[data-toggle="mz-design"]');

		$settingToggle.data('code', $(this).val());
		$($settingToggle.data('target')).val('');
		
		if($(this).val()){
			$settingToggle.removeClass('disabled').prop('disabled', 0);
			$settingToggle.trigger('click');
		} else {
			$settingToggle.addClass('disabled').prop('disabled', 1);
		}
	});

	// content
	$(document).on('click', '[data-toggle="mz-content"]', function(e) {
		e.preventDefault();
		
		var $button = $(this);
		var $icon   = $button.find('> i');

		$.ajax({
			url: 'index.php?route=extension/maza/layout_builder/setting_content&user_token=' + getURLVar('user_token') + '&mz_theme_code=' + getURLVar('mz_theme_code') + '&mz_skin_id=' + getURLVar('mz_skin_id') + '&target_id=' + $($button.data('target')).attr('id') + '&code=' + $button.data('code'),
			dataType: 'html',
			data: $($button.data('target')).val(),
			type: 'post',
			beforeSend: function() {
				$button.prop('disabled', true);
				if ($icon.length) {
					$icon.attr('class', 'fa fa-cog fa-spin');
				}
			},
			complete: function() {
				$button.prop('disabled', false);

				if ($icon.length) {
					$icon.attr('class', 'fa fa-cog');
				}
			},
			success: function(html) {
				$('body').append(html);
			}
		});
	});

	$(document).on('change', '.mz-content > select', function(e) {
		var $settingToggle = $(this).parent().find('[data-toggle="mz-content"]');

		$settingToggle.data('code', $(this).val());
		$($settingToggle.data('target')).val('');
		
		if($(this).val()){
			$settingToggle.removeClass('disabled').prop('disabled', 0);
			$settingToggle.trigger('click');
		} else {
			$settingToggle.addClass('disabled').prop('disabled', 1);
		}
	});
});


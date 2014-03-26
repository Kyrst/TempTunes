function ui() {};

ui.prototype =
{
	show_element_lock: false,

	dialog_class: 'kyrst-dialog',

	dialogs: [],

	validation_style: 'default', // qtip | default
	validation_error_class: 'validation-error',

	init: function()
	{
		// Notices
		if ( typeof notices !== 'undefined' )
		{
			this.show_notices(notices);
		}
	},

	init_dialog: function(type, title, body, extra, dialog_id)
	{
		var inst = this,
			dialog_index = inst.dialogs.length,
			dialog_id = (!$kyrst.is_undefined(dialog_id) ? dialog_id : 'dialog_' + dialog_index),
			selector = '#' + dialog_id;

		// Auto open
		var auto_open = false,
			modal = true,
			width = 300,
			height = 250,
			resizable = false,
			draggable = false,
			margin_top = 80;

		if ( $kyrst.is_defined(extra) )
		{
			auto_open = (typeof extra.auto_open !== 'undefined') ? extra.auto_open : false;

			// Styling
			modal = !$kyrst.is_undefined(extra.modal) ? extra.modal : true;
			width = !$kyrst.is_undefined(extra.width) ? extra.width : 300;
			height = !$kyrst.is_undefined(extra.height) ? extra.height : 250;
			resizable = !$kyrst.is_undefined(extra.resizable) ? extra.resizable : false;
			draggable = !$kyrst.is_undefined(extra.draggable) ? extra.draggable : false;

			if ( $kyrst.is_defined(extra.other) )
			{
				if ( $kyrst.is_defined(extra.other.top) )
				{
					margin_top = extra.other.top;
				}
			}

			/*if ( !$kyrst.is_undefined(extra.other.mask) )
			{
				var opacity = extra.other.mask.opacity;

				//$('.ui-widget-overlay').css('background-color', extra.other.mask.color);

				//$('.ui-widget-overlay').css('background', extra.other.mask.color).css('opacity', extra.other.mask.opacity);

				// .ui-widget-overlay {	background: ' + extra.other.mask.color + ' url(images/ui-bg_flat_0_aaaaaa_40x100.png) 50% 50% repeat-x; opacity: .' + opacity + '; filter: Alpha(Opacity=' + opacity + ') }

				//document.write('<style></style>');
			}*/
		}

		var buttons_to_disable_from_start = [];

		// Buttons
		if ( type === 'dialog' )
		{
			buttons = $kyrst.is_array(extra.buttons) ? this.init_dialog_buttons(dialog_id, extra.buttons) : {};

			for ( var button_title in buttons )
			{
				var button = buttons[button_title];

				if ( button.disabled === true )
				{
					buttons_to_disable_from_start.push(button_title);
				}
			}
		}
		else if ( type === 'confirm' )
		{
			var buttons =
			{
				'Confirm': function()
				{
					extra.confirm_callback();

					$(this).dialog('close');
				},
				'Cancel': function()
				{
					if ( !$kyrst.is_undefined(extra.cancel_callback) )
					{
						extra.cancel_callback();
					}

					$(this).dialog('close');
				}
			};
		}
		else if ( type === 'alert' )
		{
			var buttons =
			{
				'OK': function()
				{
					$(this).dialog('close');
				}
			};
		}

		var loader_height = (height - 133);

		// Create
		if ( !$kyrst.exists($(selector)) && $kyrst.is_undefined(dialog_id) )
		{
			$kyrst.$dialogs.append('<div id="' + dialog_id + '" class="' + inst.dialog_class + '" style="display:none" title="' + title + '"><div id="' + dialog_id + '_loader" style="height:' + loader_height + 'px;line-height:' + loader_height + 'px" class="loader"></div><div id="' + dialog_id + '_body">' + body + '</div></div>');
		}
		else
		{
			$('#' + dialog_id).html('<div id="' + dialog_id + '_loader" style="height:' + loader_height + 'px;line-height:' + loader_height + 'px" class="loader"></div><div id="' + dialog_id + '_body" class="body">' + $('#' + dialog_id).html() + '</div>').addClass(inst.dialog_class);
		}

		var dialog = $(selector).dialog(
		{
			autoOpen: auto_open,
			width: width,
			height: height,
			modal: modal,
			resizable: resizable,
			draggable: draggable,
			buttons: buttons,
			position: ['center', margin_top],
			create: function()
			{
				// Events
				if ( $kyrst.is_object(extra.events) )
				{
					// After open
					if ( $kyrst.is_object(extra.events.on_open) )
					{
						$(this).on('dialogopen', extra.events.on_open);
					}

					// Before close
					if ( $kyrst.is_object(extra.events.before_close) )
					{
						$(this).on('dialogbeforeclose', extra.events.before_close);
					}
				}

				// Other
				if ( $kyrst.is_object(extra.other) )
				{
					for ( var key in extra.other )
					{
						$(this).dialog('option', key, extra.other[key]);
					}
				}

				inst.dialogs[dialog_index] = $(this);
			},
			open: function()
			{
				// Disable buttons
				for ( var i = 0, num_buttons_to_disable_from_start = buttons_to_disable_from_start.length; i < num_buttons_to_disable_from_start; i++ )
				{
					var button = buttons_to_disable_from_start[i];

					$('.ui-dialog-buttonpane button:contains(' + button + ')').prop('disabled', true);
				}

				// Remove focus from buttons
				$('.ui-dialog :button').blur();

				var $this = $(this);

				// If form in dialog, enable submit from Enter key
				$($this).find('input[type="text"]').each(function(index, element)
				{
					$(element).on('keypress', function(e)
					{
						if ( e.which === 13 )
						{
							var save_button = $this.dialog('option', 'save_button');

							if ( save_button !== null )
							{
								save_button.action();
							}
						}
					});
				});
			},
			close: function()
			{
				// After close
				if ( !$kyrst.is_undefined(extra.events) && $kyrst.is_object(extra.events.after_close) )
				{
					extra.events.after_close();
				}

				// Remove all the validation
				inst.hide_validation_tooltips();

				//delete inst.dialogs[dialog_index];
			},
			beforeClose: function()
			{
				// ...
			}
		});

		dialog.set_title = function(title)
		{
			this.dialog('option', 'title', title);
		};

		dialog.set_content = function(content)
		{
			$('#' + this.attr('id') + '_body').html(content);
		};

		dialog.set_buttons = function(buttons)
		{
			this.dialog('option', 'buttons', inst.init_dialog_buttons(this.attr('id'), buttons));

			for ( var button_title in buttons )
			{
				var button = buttons[button_title];

				if ( button.save )
				{
					this.dialog('option', 'save_button', button);

					break;
				}
			}
		};

		dialog.show_loader = function()
		{
			var dialog_id = this.attr('id');

			$('#' + dialog_id + '_body').hide();
			$('#' + dialog_id + '_loader').show();
		};

		dialog.hide_loader = function()
		{
			var dialog_id = this.attr('id');

			$('#' + dialog_id + '_loader').hide();
			$('#' + dialog_id + '_body').show();
		};

		dialog.show_error = function(error)
		{
			var height = dialog.dialog('option', 'height') - 140;

			$('#' + this.attr('id') + '_body').html('<div class="kyrst-dialog-error" style="height:' + height + 'px;line-height:' + height + 'px">' + error + '</div>');
		};

		dialog.open = function()
		{
			this.dialog('open');
		};

		dialog.close = function()
		{
			this.dialog('close');
		};

		return dialog;
	},

	init_bootbox_dialog: function(type, message, extra)
	{
		var inst = this,
			dialog_index = inst.dialogs.length;

		var auto_open = false,
			modal = true,
			cancel_callback;

		if ( !$kyrst.is_undefined(extra) )
		{
			// Auto open
			if ( !$kyrst.is_undefined(extra.auto_open) )
			{
				auto_open = extra.auto_open;
			}

			// Styling
			if ( !$kyrst.is_undefined(extra.modal) )
			{
				modal = extra.modal;
			}

			// Cancel callback
			if ( !$kyrst.is_undefined(extra.cancel_callback) )
			{
				cancel_callback = extra.cancel_callback;
			}
		}

		var options =
		{
			show: auto_open,
			backdrop: modal
		};

		var buttons = [];

		// Create
		if ( type === 'confirm' )
		{
			buttons.push(
			{
				label: (!$kyrst.is_undefined(extra) && !$kyrst.is_undefined(extra.confirm_button_text) ? extra.confirm_button_text : 'Confirm'),
				className: 'btn-primary',
				callback: function()
				{
					extra.confirm_callback();
				}
			},
			{
				label: (!$kyrst.is_undefined(extra) && !$kyrst.is_undefined(extra.cancel_button_text) ? extra.cancel_button_text : 'Cancel'),
				className: 'btn-default',
				callback: function()
				{
					if ( $kyrst.is_function(cancel_callback) )
					{
						cancel_callback();
					}
				}
			});
		}
		else if ( type === 'alert' )
		{
			buttons.push(
			{
				label: 'OK',
				className: 'blue'
			});
		}

		inst.dialogs[dialog_index] = bootbox.dialog({
			message: message,
			buttons: buttons,
			options: options
		});
	},

	open_dialog: function(index)
	{
		if ( typeof this.dialogs[index] !== 'object' )
			return;

		this.dialogs[index].dialog('open');
	},

	show_confirm: function(message, confirm_callback, cancel_callback, extra)
	{
		if ( $kyrst.is_undefined(message) )
		{
			if ( DEBUG )
			{
				$kyrst.log('Missing "message" attribute.');
			}

			return;
		}

		if ( $kyrst.is_undefined(confirm_callback) )
		{
			if ( DEBUG )
			{
				$kyrst.log('Missing "confirm_callback" attribute.');
			}

			return;
		}
		else if ( !$kyrst.is_function(confirm_callback) )
		{
			if ( DEBUG )
			{
				$kyrst.log('Callback "confirm_callback" is not a function.');
			}

			return;
		}

		var extra = $.extend({},
		{
			auto_open: true,
			confirm_callback: confirm_callback,
			cancel_callback: cancel_callback
		}, extra);

		this.init_bootbox_dialog('confirm', message, extra);
	},

	show_alert: function(message)
	{
		if ( $kyrst.is_undefined(message) )
		{
			if ( DEBUG )
			{
				console.log('Missing "message" attribute.');
			}

			return;
		}

		this.init_bootbox_dialog('alert', message,
		{
			auto_open: true
		});
	},

	show_dialog: function(title, content, auto_open, width, height, modal, resizable, draggable, buttons, events, other)
	{
		return this.init_dialog('dialog', title, content,
		{
			auto_open: auto_open,
			width: width,
			height: height,
			modal: modal,
			resizable: resizable,
			draggable: draggable,
			buttons: buttons,
			events: events,
			other: other
		});
	},

	init_dialog_from_element: function(element_selector, auto_open, width, height, modal, resizable, draggable, buttons, events, other)
	{
		var $element = $(element_selector);

		return this.init_dialog('dialog', $element.attr('title'), $element.html(),
		{
			auto_open: auto_open,
			width: width,
			height: height,
			modal: modal,
			resizable: resizable,
			draggable: draggable,
			buttons: buttons,
			events: events,
			other: other
		}, element_selector.substring(1));
	},

	lock_body: function()
	{
		$('body').append('<div id="body_lock" class="lock-ie-bg" style="background:url(\'' + BASE_URL + 'assets/img/bg-white-lock.png\') repeat center center !important;top:0;position:fixed;z-index:9000;width:100%;height:100%;"><div class="lock-ie" style="background:url(\'' + BASE_URL + 'assets/img/loading.gif\') no-repeat center center !important;top:0;position:absolute;z-index:9001;width:100%;height:100%"></div></div>');
	},

	unlock_body: function()
	{
		$('#body_lock').remove();
	},

	lock_element: function($object)
	{
		// Lock body
		if ( !$kyrst.is_undefined($object.data('lock_body')) )
		{
			return this.lock_body();
		}

		if ( !$kyrst.is_object($object) )
		{
			return;
		}

		var position = $object.position();

		$object.append('<div id="element_lock" class="lock" style="' + (!$kyrst.is_undefined(position.top) ? 'top:' + position.top + 'px;' : '') + ' position:absolute;z-index:9000;width:' + $object.outerWidth() + 'px; height:' + $object.outerHeight() + 'px' + (this.show_element_lock ? ';background-color:rgba(0, 0, 0, 0.2)' : '') + '"></div>');
	},

	unlock_element: function($object)
	{
		if ( !$kyrst.is_object($object) )
		{
			return false;
		}

		var $locked_element = $object.find('#element_lock');

		if ( $locked_element.length === 0 )
			return false;

		$($locked_element).remove();
	},

	show_notices: function(notices)
	{
		for ( var type in notices )
		{
			for ( var i = 0, num_notices = notices[type].length; i < num_notices; i++ )
			{
				if ( type === 'successes' )
				{
					this.show_success(notices[type][i]);
				}
				else if ( type === 'errors' )
				{
					this.show_error(notices[type][i]);
				}
				else
				{
					this.show_info(notices[type][i]);
				}
			}
		}
	},

	show_success: function(message)
	{
		/*$.jGrowl(message,
		{
			theme: 'success'
		});*/

		this.show_alert(message);
	},

	show_error: function(message)
	{
		/*$.jGrowl(message,
		{
			sticky: true,
			theme: 'error'
		});*/

		this.show_alert(message);
	},

	show_info: function(message)
	{
		/*$.jGrowl(message,
		{
			theme: 'info'
		});*/

		this.show_alert(message);
	},

	show_validation_tooltip: function($element, $form, message, validation_error_class)
	{
		if ( this.validation_style === 'qtip' )
		{
			var corners = ['top right', 'left bottom'],
				flip_it = $element.parents('span.right').length > 0;

			if ( !$kyrst.is_undefined(validation_error_class) && validation_error_class === true )
			{
				$element.addClass(this.validation_error_class);
			}

			if ( !$kyrst.is_undefined($form.data('tooltip_position')) )
			{
				switch ( $form.data('tooltip_position') )
				{
					case 'top-left':
						corners = ['top left', 'right bottom'];

						break;
					case 'bottom-left':
						corners = ['bottom left', 'right bottom'];

						break;
					case 'top-left-bottom-right':
						corners = ['top left', 'left bottom'];

						break;
				}
			}

			$element.qtip(
			{
				overwrite: false,
				content: message,
				position:
				{
					my: corners[ flip_it ? 0 : 1 ],
					at: corners[ flip_it ? 1 : 0 ],
					viewport: $(window)
				},
				show:
				{
					event: false,
					ready: true
				},
				hide: false,
				style:
				{
					classes: 'qtip-red qtip-shadow qtip-zindex'
				}
			}).qtip('option', 'content.text', message);
		}
		else if ( this.validation_style === 'default' )
		{
			$element.parents('.form-group').addClass('has-error');
		}
	},

	hide_validation_tooltips: function()
	{
		if ( this.validation_style === 'qtip' )
		{
			$('.qtip').qtip('destroy');
		}
		else if ( this.validation_style === 'default' )
		{
			// $form.find

			$('.form-group').removeClass('has-error');
		}
	},

	hide_validation_toolip: function($element)
	{
		if ( this.validation_style === 'qtip' )
		{
			$element.qtip('destroy');
		}
		else if ( this.validation_style === 'default' )
		{
			$element.parents('.form-group.has-error').removeClass('has-error');
		}
	},

	init_dialog_buttons: function(dialog_id, buttons)
	{
		create_dialog_button_function = function(button)
		{
			return {
				text: button.title,
				class: 'btn' + (!$kyrst.is_undefined(button.class) ? ' ' + button.class : ''),
				disabled: (button.disabled === true),
				click: function()
				{
					var button_click_result;

					if ( $kyrst.is_function(button.on_click) )
					{
						button_click_result = button.on_click();
					}

					if ( button.close_on_click && button_click_result !== false )
					{
						$('#' + dialog_id).dialog('close');
					}
				}
			};
		};

		var new_buttons = {};

		for ( var i = 0, num_buttons = buttons.length; i < num_buttons; i++ )
		{
			var button = buttons[i];

			button.selector = '.ui-dialog-buttonpane button:contains(' + button.title + ')';

			button.reset = function()
			{
				$(this.selector).text(this.title).removeAttr('disabled');

				this.selector = '.ui-dialog-buttonpane button:contains(' + this.title + ')';
			};

			button.enable = function()
			{
				$(this.selector).removeAttr('disabled');
			};

			button.disable = function()
			{
				$(this.selector).prop('disabled', true);
			};

			button.show = function()
			{
				$(this.selector).css('visibility', 'visible');
			};

			button.hide = function()
			{
				$(this.selector).css('visibility', 'hidden');
			};

			button.set_title = function(title, disable)
			{
				$(this.selector).text(title);

				this.selector = '.ui-dialog-buttonpane button:contains(' + title + ')';

				if ( disable === true )
				{
					this.disable();
				}
			};

			new_buttons[button.title] = create_dialog_button_function(button);
		}

		return new_buttons;
	}
};
function ajax_request() {};

ajax_request.prototype =
{
	$form: null,

	url: '',
	data: {},
	method: 'GET',
	data_type: 'json',
	cache: false,
	async: true,

	result: null,

	before: null,
	success: null,
	error: null,
	complete: null,

	execute: function()
	{
		var inst = this;

		if ( $kyrst.is_undefined(inst.url) )
		{
			if ( DEBUG )
			{
				$kyrst.log('AJAX: Missing URL.');
			}

			return;
		}

		// Abort unfinished request to the same URL
		if ( !$kyrst.is_undefined($kyrst.ajax.requests[inst.url]) )
		{
			$kyrst.ajax.requests[inst.url].abort();
		}

		$kyrst.ajax.requests[inst.url] = $.ajax(
			{
				type: inst.method,
				url: inst.url,
				data: inst.data,
				dataType: inst.data_type,
				cache: inst.cache,
				async: inst.async,
				beforeSend: function()
				{
					if ( inst.$form !== null )
					{
						inst.hide_validation();
					}

					if ( $kyrst.is_function(inst.before) )
					{
						inst.before();
					}
				}
			}).done(function(result)
			{
				if ( $kyrst.is_empty(result) )
				{
					return false;
				}

				// Actions
				if ( !$kyrst.is_empty(result.actions) )
				{
					inst.run_actions(result.actions);
				}

				// Validation
				if ( !$kyrst.is_empty(result.validation) )
				{
					inst.show_validation(result.validation);
				}

				// Notices
				// Successes
				if ( !$kyrst.is_empty(result.successes) )
				{
					$kyrst.ui.show_notices({ successes: result.successes });
				}

				// Errors
				if ( !$kyrst.is_empty(result.errors) )
				{
					$kyrst.ui.show_notices({ errors: result.errors });
				}

				if ( $kyrst.is_function(inst.success) )
				{
					inst.success(result);
				}

				// Redirect
				if ( !$kyrst.is_undefined(result.redirect) )
				{
					return $kyrst.redirect(result.redirect);
				}

				inst.result = result;
			}).always(function(result)
			{
				if ( $kyrst.is_function(inst.complete) )
				{
					inst.complete(result);
				}

				delete $kyrst.ajax.requests[inst.url];
			}).fail(function()
			{
				if ( $kyrst.is_function(inst.error) )
				{
					inst.error();
				}
			});
	},

	run_actions: function(actions)
	{
		for ( var i = 0, num_actions = actions.length; i < num_actions; i++ )
		{
			var action = actions[i],
				$element = $(action.selector);

			// Skip if element wasn't found
			if ( $element.length === 0 )
			{
				continue;
			}

			switch ( action.method )
			{
				case 'append':
					$element.append(action.content);

					break;
				case 'prepend':
					$element.prepend(action.content);

					break;
				case 'replaceWith':
					$element.replaceWith(action.content);

					break;
				case 'html':
					$element.html(action.content);

					break;
				case 'remove':
					$element.remove();

					break;
				case 'empty':
					$element.empty();

					break;
				case 'val':
					$element.val(action.content);

					break;
			}
		}
	},

	show_validation: function(errors)
	{
		console.log(errors);

		for ( var i = 0, num_errors = errors.length; i < num_errors; i++ )
		{
			var $element = $('[id="' + errors[i].element + '"]');

			$element.addClass($kyrst.ui.validation_error_class);

			if ( !$kyrst.is_undefined($element) && !$kyrst.is_empty(errors[i].message) )
			{
				$kyrst.ui.show_validation_tooltip($element, this.$form, errors[i].message);
			}
		}
	},

	hide_validation: function()
	{
		$('.validation-error').removeClass('validation-error');

		console.log(this.$form.find('.form-group.has-error').removeClass('has-error'));

		//if ( typeof jQuery.fn.qtip === 'function' )
		//{
		//$('.qtip').qtip('destroy');
		//}
	}
};
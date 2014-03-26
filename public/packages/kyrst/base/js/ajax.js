function ajax() {};

ajax.prototype =
{
	requests: [],

	get: function(url, data, callbacks, extra)
	{
		var request = new ajax_request();
		request.method = 'GET';
		request.url = url;
		request.data = data;

		if ( $kyrst.is_undefined(callbacks) )
		{
			callbacks = {};
		}

		if ( $kyrst.is_function(callbacks.before) )
		{
			request.before = callbacks.before;
		}

		if ( $kyrst.is_function(callbacks.success) )
		{
			request.success = callbacks.success;
		}

		if ( $kyrst.is_function(callbacks.error) )
		{
			request.error = callbacks.error;
		}

		if ( $kyrst.is_function(callbacks.complete) )
		{
			request.complete = callbacks.complete;
		}

		// Extra
		if ( $kyrst.is_object(extra) )
		{
			for ( key in extra )
			{
				request[key] = extra[key];
			}
		}

		request.execute();
	},

	post: function(url, data, callbacks, extra)
	{
		var request = new ajax_request();
		request.method = 'POST';
		request.url = url;
		request.data = data;

		if ( $kyrst.is_undefined(callbacks) )
		{
			callbacks = {};
		}

		if ( $kyrst.is_function(callbacks.before) )
		{
			request.before = callbacks.before;
		}

		if ( $kyrst.is_function(callbacks.success) )
		{
			request.success = callbacks.success;
		}

		if ( $kyrst.is_function(callbacks.error) )
		{
			request.error = callbacks.error;
		}

		if ( $kyrst.is_function(callbacks.complete) )
		{
			request.complete = callbacks.complete;
		}

		// Extra
		if ( $kyrst.is_object(extra) )
		{
			for ( key in extra )
			{
				request[key] = extra[key];
			}
		}

		request.execute();
	},

	auto_submit: function($form)
	{
		var url = $form.attr('action'),
			data = $form.serialize(),
			method = $form.attr('method'),
			$submit = $form.find(':submit'),
			original_submit_button_text = $submit.html(),
			submit_button_loading_text = $form.data('submit_button_loading_text');

		method = (method !== undefined && (method === 'POST' || method === 'post')) ? 'post' : 'get';

		var request = new ajax_request();
		request.$form = $form;
		request.method = method;
		request.url = url;
		request.data = data;

		var before_function = $form.data('before');

		if ( !$kyrst.is_undefined(before_function) )
		{
			if ( $kyrst.is_function(window[before_function]) )
			{
				if ( window[before_function]() === false )
				{
					return;
				}
			}
			else
			{
				if ( DEBUG )
				{
					$kyrst.log('AJAX Auto Submit: Missing "before" function "' + before_function + '".');

					return;
				}
			}
		}

		request.before = function()
		{
			$kyrst.ui.lock_element($form);

			if ( !$kyrst.is_undefined(submit_button_loading_text) )
			{
				$submit.html(submit_button_loading_text).prop('disabled', true);
			}
		};

		var success_function = $form.data('success');

		if ( !$kyrst.is_undefined(success_function) )
		{
			if ( $kyrst.is_function(window[success_function]) )
			{
				if ( window[success_function] !== false )
				{
					request.success = window[success_function];
				}
			}
			else
			{
				if ( DEBUG )
				{
					$kyrst.log('AJAX Auto Submit: Missing "success" function "' + success_function + '".');

					return;
				}
			}
		}

		request.complete = function(result)
		{
			$kyrst.ui.unlock_element($form);

			if ( !$kyrst.is_undefined(submit_button_loading_text) && $kyrst.is_undefined(result.redirect) )
			{
				$submit.html(original_submit_button_text).removeAttr('disabled');
			}
		};

		request.execute();
	}
};
function Kyrst() {};

Kyrst.prototype =
{
	ui: null,
	ajax: null,
	helper: null,

	init: function()
	{
		this.binds();

		// Helper
		//this.helper = new helper();

		// AJAX
		this.ajax = new ajax();
	},

	after_dom_init: function()
	{
		// UI
		this.ui = new ui();
		this.ui.init();
	},

	binds: function()
	{
		// Dialogs
		//this.$dialogs = $('#dialogs');

		// Auto submit
		$('form.kyrst-auto-submit').on('submit', function(e)
		{
			e.preventDefault();

			$kyrst.ajax.auto_submit($(this));
		});
	},

	is_empty: function(object)
	{
		if ( object === null )
		{
			return true;
		}

		if ( this.is_array(object) || this.is_string(object) )
		{
			return (object.length === 0);
		}


		for ( var key in object )
		{
			if ( this.has(object, key) )
			{
				return false;
			}
		}

		return true;
	},

	is_undefined: function(object)
	{
		return object == void 0;
	},

	exists: function(object)
	{
		return object.length > 0;
	},

	is_object: function(object)
	{
		return object === Object(object);
	},

	is_array: Array.isArray || function(object)
	{
		return Object.prototype.toString.call(object) == '[object Array]';
	},

	is_function: function(object)
	{
		return (typeof object === 'function');
	},

	is_string: function(object)
	{
		return Object.prototype.toString.call(object) === '[object String]';
	},

	is_number: function(object)
	{
		return !isNaN(object);
	},

	is_integer: function(object)
	{
		return Object.prototype.toString.call(object) === '[object Number]';
	},

	has: function(object, key)
	{
		return Object.prototype.hasOwnProperty.call(object, key);
	},

	redirect: function(url)
	{
		window.location = url;
	},

	log: function(message)
	{
		window.console && console.log(message);
	}
};

$kyrst = new Kyrst();
$kyrst.init();

$(function()
{
	$kyrst.after_dom_init();
});
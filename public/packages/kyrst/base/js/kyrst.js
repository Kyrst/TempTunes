function Kyrst() {};

Kyrst.prototype =
{
	window: null,

	ui: null,
	ajax: null,
	helper: null,

	init: function()
	{
		this.window = window;

		this.binds();

		// Helper
		this.helper = new helper();
		this.helper.init();

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

	is_defined: function(object)
	{
		return !this.is_undefined(object);
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
	},

	size: function(object)
	{
		var size = 0,
			key;

		for ( key in object )
		{
			if ( object.hasOwnProperty(key) )
			{
				size++;
			}
		}

		return size;
	},

	is_email: function(str)
	{
		var regex = /^(([^<>()[\]\\.,;:\s@\"]+(\.[^<>()[\]\\.,;:\s@\"]+)*)|(\".+\"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;

		return !this.is_empty(str) && regex.test(str);
	},

	var_dump: function(array, return_val)
	{
		var output = '',
			pad_char = ' ',
			pad_val = 4,
			d = this.window.document,
			getFuncName = function(fn) {
				var name = (/\W*function\s+([\w\$]+)\s*\(/)
					.exec(fn);
				if (!name) {
					return '(Anonymous)';
				}
				return name[1];
			};
		repeat_char = function(len, pad_char) {
			var str = '';
			for (var i = 0; i < len; i++) {
				str += pad_char;
			}
			return str;
		};
		formatArray = function(obj, cur_depth, pad_val, pad_char) {
			if (cur_depth > 0) {
				cur_depth++;
			}

			var base_pad = repeat_char(pad_val * cur_depth, pad_char);
			var thick_pad = repeat_char(pad_val * (cur_depth + 1), pad_char);
			var str = '';

			if (typeof obj === 'object' && obj !== null && obj.constructor && getFuncName(obj.constructor) !==
				'PHPJS_Resource') {
				str += 'Array\n' + base_pad + '(\n';
				for (var key in obj) {
					if (Object.prototype.toString.call(obj[key]) === '[object Array]') {
						str += thick_pad + '[' + key + '] => ' + formatArray(obj[key], cur_depth + 1, pad_val, pad_char);
					} else {
						str += thick_pad + '[' + key + '] => ' + obj[key] + '\n';
					}
				}
				str += base_pad + ')\n';
			} else if (obj === null || obj === undefined) {
				str = '';
			} else { // for our "resource" class
				str = obj.toString();
			}

			return str;
		};

		output = formatArray(array, 0, pad_val, pad_char);

		if (return_val !== true) {
			if (d.body) {
				this.echo(output);
			} else {
				try {
					d = XULDocument; // We're in XUL, so appending as plain text won't work; trigger an error out of XUL
					this.echo('<pre xmlns="http://www.w3.org/1999/xhtml" style="white-space:pre;">' + output + '</pre>');
				} catch (e) {
					this.echo(output); // Outputting as plain text may work in some plain XML
				}
			}
			return true;
		}
		return output;
	},

	echo: function()
	{
		var isNode = typeof module !== 'undefined' && module.exports;

		if (isNode) {
			var args = Array.prototype.slice.call(arguments);
			return console.log(args.join(' '));
		}

		var arg = '',
			argc = arguments.length,
			argv = arguments,
			i = 0,
			holder, win = this.window,
			d = win.document,
			ns_xhtml = 'http://www.w3.org/1999/xhtml',
			ns_xul = 'http://www.mozilla.org/keymaster/gatekeeper/there.is.only.xul'; // If we're in a XUL context
		var stringToDOM = function(str, parent, ns, container) {
			var extraNSs = '';
			if (ns === ns_xul) {
				extraNSs = ' xmlns:html="' + ns_xhtml + '"';
			}
			var stringContainer = '<' + container + ' xmlns="' + ns + '"' + extraNSs + '>' + str + '</' + container + '>';
			var dils = win.DOMImplementationLS,
				dp = win.DOMParser,
				ax = win.ActiveXObject;
			if (dils && dils.createLSInput && dils.createLSParser) {
				// Follows the DOM 3 Load and Save standard, but not
				// implemented in browsers at present; HTML5 is to standardize on innerHTML, but not for XML (though
				// possibly will also standardize with DOMParser); in the meantime, to ensure fullest browser support, could
				// attach http://svn2.assembla.com/svn/brettz9/DOMToString/DOM3.js (see http://svn2.assembla.com/svn/brettz9/DOMToString/DOM3.xhtml for a simple test file)
				var lsInput = dils.createLSInput();
				// If we're in XHTML, we'll try to allow the XHTML namespace to be available by default
				lsInput.stringData = stringContainer;
				var lsParser = dils.createLSParser(1, null); // synchronous, no schema type
				return lsParser.parse(lsInput)
					.firstChild;
			} else if (dp) {
				// If we're in XHTML, we'll try to allow the XHTML namespace to be available by default
				try {
					var fc = new dp()
						.parseFromString(stringContainer, 'text/xml');
					if (fc && fc.documentElement && fc.documentElement.localName !== 'parsererror' && fc.documentElement.namespaceURI !==
						'http://www.mozilla.org/newlayout/xml/parsererror.xml') {
						return fc.documentElement.firstChild;
					}
					// If there's a parsing error, we just continue on
				} catch (e) {
					// If there's a parsing error, we just continue on
				}
			} else if (ax) { // We don't bother with a holder in Explorer as it doesn't support namespaces
				var axo = new ax('MSXML2.DOMDocument');
				axo.loadXML(str);
				return axo.documentElement;
			}
			/*else if (win.XMLHttpRequest) { // Supposed to work in older Safari
			 var req = new win.XMLHttpRequest;
			 req.open('GET', 'data:application/xml;charset=utf-8,'+encodeURIComponent(str), false);
			 if (req.overrideMimeType) {
			 req.overrideMimeType('application/xml');
			 }
			 req.send(null);
			 return req.responseXML;
			 }*/
			// Document fragment did not work with innerHTML, so we create a temporary element holder
			// If we're in XHTML, we'll try to allow the XHTML namespace to be available by default
			//if (d.createElementNS && (d.contentType && d.contentType !== 'text/html')) { // Don't create namespaced elements if we're being served as HTML (currently only Mozilla supports this detection in true XHTML-supporting browsers, but Safari and Opera should work with the above DOMParser anyways, and IE doesn't support createElementNS anyways)
			if (d.createElementNS && // Browser supports the method
				(d.documentElement.namespaceURI || // We can use if the document is using a namespace
					d.documentElement.nodeName.toLowerCase() !== 'html' || // We know it's not HTML4 or less, if the tag is not HTML (even if the root namespace is null)
					(d.contentType && d.contentType !== 'text/html') // We know it's not regular HTML4 or less if this is Mozilla (only browser supporting the attribute) and the content type is something other than text/html; other HTML5 roots (like svg) still have a namespace
					)) { // Don't create namespaced elements if we're being served as HTML (currently only Mozilla supports this detection in true XHTML-supporting browsers, but Safari and Opera should work with the above DOMParser anyways, and IE doesn't support createElementNS anyways); last test is for the sake of being in a pure XML document
				holder = d.createElementNS(ns, container);
			} else {
				holder = d.createElement(container); // Document fragment did not work with innerHTML
			}
			holder.innerHTML = str;
			while (holder.firstChild) {
				parent.appendChild(holder.firstChild);
			}
			return false;
			// throw 'Your browser does not support DOM parsing as required by echo()';
		};

		var ieFix = function(node) {
			if (node.nodeType === 1) {
				var newNode = d.createElement(node.nodeName);
				var i, len;
				if (node.attributes && node.attributes.length > 0) {
					for (i = 0, len = node.attributes.length; i < len; i++) {
						newNode.setAttribute(node.attributes[i].nodeName, node.getAttribute(node.attributes[i].nodeName));
					}
				}
				if (node.childNodes && node.childNodes.length > 0) {
					for (i = 0, len = node.childNodes.length; i < len; i++) {
						newNode.appendChild(ieFix(node.childNodes[i]));
					}
				}
				return newNode;
			} else {
				return d.createTextNode(node.nodeValue);
			}
		};

		var replacer = function(s, m1, m2) {
			// We assume for now that embedded variables do not have dollar sign; to add a dollar sign, you currently must use {$$var} (We might change this, however.)
			// Doesn't cover all cases yet: see http://php.net/manual/en/language.types.string.php#language.types.string.syntax.double
			if (m1 !== '\\') {
				return m1 + eval(m2);
			} else {
				return s;
			}
		};

		this.php_js = this.php_js || {};
		var phpjs = this.php_js,
			ini = phpjs.ini,
			obs = phpjs.obs;
		for (i = 0; i < argc; i++) {
			arg = argv[i];
			if (ini && ini['phpjs.echo_embedded_vars']) {
				arg = arg.replace(/(.?)\{?\$(\w*?\}|\w*)/g, replacer);
			}

			if (!phpjs.flushing && obs && obs.length) { // If flushing we output, but otherwise presence of a buffer means caching output
				obs[obs.length - 1].buffer += arg;
				continue;
			}

			if (d.appendChild) {
				if (d.body) {
					if (this.window.navigator.appName === 'Microsoft Internet Explorer') { // We unfortunately cannot use feature detection, since this is an IE bug with cloneNode nodes being appended
						d.body.appendChild(stringToDOM(ieFix(arg)));
					} else {
						var unappendedLeft = stringToDOM(arg, d.body, ns_xhtml, 'div')
							.cloneNode(true); // We will not actually append the div tag (just using for providing XHTML namespace by default)
						if (unappendedLeft) {
							d.body.appendChild(unappendedLeft);
						}
					}
				} else {
					d.documentElement.appendChild(stringToDOM(arg, d.documentElement, ns_xul, 'description')); // We will not actually append the description tag (just using for providing XUL namespace by default)
				}
			} else if (d.write) {
				d.write(arg);
			}
			/* else { // This could recurse if we ever add print!
			 print(arg);
			 }*/
		}
	},

	in_array: function(needle, haystack, argStrict)
	{
		var key = '',
			strict = !! argStrict;

		if (strict) {
			for (key in haystack) {
				if (haystack[key] === needle) {
					return true;
				}
			}
		} else {
			for (key in haystack) {
				if (haystack[key] == needle) {
					return true;
				}
			}
		}

		return false;
	}
};

$kyrst = new Kyrst();
$kyrst.init();

$(function()
{
	$kyrst.after_dom_init();
});
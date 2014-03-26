Base
====

A good base for new Laravel 4 projects.

## Installation

Add ``Kyrst/Base`` to your ``composer.json`` file:

```
"require": {
	"kyrst/base": "dev-master"
}
```

Change ``minimum-stability`` to ``dev`` in your ``composer.json`` file:

```
"minimum-stability": "dev"
```

Run composer update on the command line from the root of your project:
```
composer update
```

### Registering the Package

Add ``Kyrst/Base`` and ``Toddish/Verify`` service providers to your config in ``app/config/app.php``:

```php
'providers' => array
(
	'Toddish\Verify\VerifyServiceProvider',
	'Kyrst\Base\BaseServiceProvider'
)
```

### Change Auth driver

Change your Auth driver to ``verify`` in ``app/config/auth.php``:

```php
'driver' => 'verify',
```

### Publish Toddish/Verify config
php artisan config:publish toddish/verify

### Configure

Run the following commands to move assets to ``/public`` and create configuration file:

```
php artisan asset:publish kyrst/base
php artisan config:publish kyrst/base
```

The config file ``app/config/packages/kyrst/base/config.php`` will look like this by default:

```
return array
(
	'DEFAULT_PAGE_TITLE' => 'Default page title',
	'PAGE_TITLE_SEPARATOR' => '-',
	'PAGE_TITLE_APPENDIX' => 'Company Name',
	'DEFAULT_META_DESCRIPTION' => 'Default meta description.'
)
```

### Migrate database
```
php artisan migrate --package="toddish/verify"
php artisan db:seed --class=VerifyUserSeeder
```

### Change app/models/User.php

Change ``app/models/User.php`` from:

```php
<?php

use Illuminate\Auth\UserInterface;
use Illuminate\Auth\Reminders\RemindableInterface;

class User extends Eloquent implements UserInterface, RemindableInterface {
...
```

to

```php
<?php
use Kyrst\Base\Models\User as KyrstUser;

class User extends KyrstUser {
...
```

### Change app/config/auth.php

Maybe not?

Change ``'model' => 'User',`` to ``'model' => 'Kyrst\Base\Models\User',``

## Includes

```
- jQuery 2.0.3
- jQuery UI 1.10.4
- Bootstrap 3.0.3
- Bootbox 4.1.0
```

## Usage

### Auto loading of assets

``Kyrst/Base`` will automatically load the following files on every load (if they exist):

```
public/css/layouts/[layout_name].php
public/css/[controller_name]/[action_name].php
public/js/layouts/[layout_name].php
public/js/[controller_name]/[action_name].php

For example:
public/css/layouts/front.php
public/css/home/index.php
public/js/layouts/front.php
public/js/home/index.php
```

### PHP

#### How Kyrst/Base works with controllers

```php
<?php
use Kyrst\Base\Controllers\ApplicationController as ApplicationController;

class BaseController extends ApplicationController
{
	function __construct()
	{
		// Setup a new library to load later with $this->load_lib('my_lib')
		$this->libs['my_lib'] = array
		(
			'js' => 'libs/my_lib/script.js',
			'css' => array
			(
				'libs/my_lib/style1.css',
				'libs/my_lib/style2.css'
			)
		)

		// Assign var to view/template files
		$this->assign('my_var', 'value');

		/*
		$this->assign takes a 3rd (optional) argument $section that allows 3 different values: 'layout', 'content' and 'js'

		layout = Accessed from the layout view files (for example views/layouts/front.php)
		content = Accessed from the content view files (for example views/home/index.php)
		js = Accessed from JavaScript files

		Example usage:
		$this->assign('my_first_var', 'value', 'js'); // Accessed from .js files
		$this->assign('my_second_var', 'value', array('layout', 'js')); // Accessed from layout and .js files
		$this->assign('my_third_var', array(array('key1' => 'value1'), array('key2' => 'value2')), 'js'); // Accessed from .js files, automatic JSON serialization of PHP arrays
		$this->assign('my_forth_var', 'value', 'content'); // Default, accessed only from content files
		*/
	}
}
?>
```

The BaseController is not obligatory, but is a nice place to gather global stuff for use between controllers.
You could extend ApplicationController directly from HomeController instead.

HomeController.php:
```php
<?php
class HomeController extends BaseController
{
	public function index()
	{
		$this->display();

		// ... or ...

		$this->display
		(
			'view.php', // Defaults to NULL (Will automatically find the view file in `views/[layout]/[action].php`)
			'Page Title', // Defaults to NULL
			true, // Show page title appendix?
			array // Libraries to load
			(
				'my_lib'
			)
		);
	}
}
?>
```

### JavaScript

#### $kyrst

```js
$kyrst.is_empty(object)
$kyrst.is_undefined(object)
$kyrst.exists(object)
$kyrst.is_object(object)
$kyrst.is_function(object)
$kyrst.is_string(object)
$kyrst.is_number(object)
$kyrst.is_integer(object)
$kyrst.has(array, value)
$kyrst.redirect(url)
$kyrst.log(message)
```

#### $kyrst.ajax

$kyrst.ajax.get/post(url, data, callbacks, extra)

```js
$kyrst.ajax.get/post
(
	BASE_URL + 'ajax_callback',
	{
		key: value,
		key2: value
	},
	{
		success: function(result)
		{
		},
		error: function(error)
		{
		},
		complete: function(result)
		{
		}
	}
);
```

### Auto submit:

#### HTML:
```html
<form action="<?= URL::to('ajax_callback') ?>" method="post" class="kyrst-auto-submit" data-submit_button_loading_text="Posting..." data-success="form_success" data-error="form_error" data-complete="form_complete">...</form>
```

#### PHP:
```php
use Kyrst\Base\Helpers\Ajax as Ajax;

...

$post = Input::all();

if ( $this->is_ajax && $post )
{
	$error = false;

	if ( !$error )
	{
		$this->ajax->show_success('Success!'); // Opens an alert with Bootbox
		$this->ajax->output();
	}
	else
	{
		$this->ajax->output_with_error('Error!'); // Opens an alert with Bootbox

		// ... or ...

		$this->ajax->add_error('Error!');
		$this->ajax->output();
	}
}

```

### Create new user

```php
$user = User::register
(
	'info@dennisnygren.se',
	'password',
	'Dennis',
	'Nygren'
);
```

#### JavaScript:
```js
function form_success(result)
{
	if ( result.errors.length === 0 )
	{
		// All good...
	}
	else
	{
		$kyrst.ui.show_error(result.errors[0]);
	}
}

function form_error(error)
{
	// ...
}

function form_complete(result)
{
	// ...
}
```

#### $kyrst.ui

```js
- $kyrst.ui.init_dialog(type, title, body, extra, dialog_id)
- $kyrst.ui.open_dialog(dialog_id);
- $kyrst.ui.show_confirm(message, confirm_callback, cancel_callback, extra)
- $kyrst.ui.show_alert(message)
- $kyrst.ui.show_dialog(title, content, auto_open, width, height, modal, resizable, draggable, buttons, events, other)
- $kyrst.ui.init_dialog_from_element(element_selector, auto_open, width, height, modal, resizable, draggable, buttons, events, other)
- $kyrst.ui.lock_element($element)
- $kyrst.ui.unlock_element($element)
- $Kyrst.ui.show_success(message)
- $kyrst.ui.show_error(message)
- $kyrst.ui.show_info(message)
- $kyrst.ui.show_validation_tooltip($element, $form, message, validation_error_class)
- $kyrst.ui.hide_validation_tooltips()
- $kyrst.ui.hide_validation_toolip($element)
```

### Init dialog:

#### HTML

```html
<h1>Index</h1>

<div id="my_dialog" class="kyrst-dialog" title="My Dialog Title">
	Dialog content goes here.
</div>
```

#### JavaScript

```js
var dialog;

$(function()
{
	dialog = $kyrst.ui.init_dialog_from_element
	(
		'#my_dialog', // Element selector
		false, // Auto open
		500, // Width
		400, // Height
		true, // Modal
		false, // Resizable
		false, // Draggable
		// Buttons
		[
			{
				title: 'Close',
				close_on_click: true,
				on_click: function()
				{
					console.log('Clicked!')
				}
			},
			{
				title: 'OK',
				on_click: function()
				{
					// Do stuff...

					// Close dialog
					dialog.close();
				}
			}
		],
		{
			on_open: function() // When form gets opened
			{
				dialog.hide_loader();
			},
			before_close: function() // Before form gets closed
			{
			},
			after_close: function() // After form gets closed
			{
			}
		},
		{
			top: 80
		}
	);

	dialog.show_loader();
	dialog.open();
});
```

### Helpers

Use Helpers like this:

```php
<?php
use Kyrst\Base\Helpers\Time as Time;

...

$formatted_time = Time::format_seconds(30); // Outputs "00:30"
?>
```

#### Ajax

Works hand-in-hand with $kyrst.ajax.get/post for automatic validation/UI locking/callbacks.

- add_data($key, $value)
- add_error($message)
- add_success($message)
- set_validation(\Illuminate\Validation\Validator $validation)
- add_action($selector, $method, $content = '')
- redirect($url)
- output()
- output_with_error($error)

#### Email

For sending e-mails. Needs e-mail template database tables.

- send($email_template_name, $subject, $to, array $from, array $data)
- render($email_template_name, array $data)

#### File

Various file helper functions.

- format_bytes($bytes, $precision = 2)
- format_filesize_from_ini($value)
- remove_dir($dir)

#### Notice

For opening dialogs and showing success/error messages after redirect.

- add_success($message)
- add_error($message)
- add_info($message)
- bool have_notices()
- array get_notices()
- delete_session()
- save_session()
- output()

## Need to be done

- Support for XHR2 with class="kyrst-auto-submit" when enctype="multipart/form-data" is supplied
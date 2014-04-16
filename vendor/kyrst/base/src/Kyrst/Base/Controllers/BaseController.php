<?php
namespace Kyrst\Base\Controllers;

use Kyrst\Base\Helpers\Ajax as Ajax;
use Kyrst\Base\Helpers\UI as UI;

class BaseController extends \Controller
{
	public $layout = 'layouts.front';

	private $data = array
	(
		'layout' => array(),
		'content' => array(),
		'js' => array()
	);

	private $assets = array
	(
		'css' => array(),
		'js' => array()
	);

	protected $libs = array();

	private $loaded_libs = array
	(
		'pre_loaded' => array(),
		'user_loaded' => array()
	);

	protected $user;

	protected $ui;

	protected $is_ajax;
	protected $ajax;

	private $did_display = false;

	private $is_from_controller;

	protected $current_route_action = '';
	protected $current_controller;
	protected $current_action;
	protected $current_page;

	protected $root_dir;

	public function __construct()
	{
		$this->user = \Auth::user();

		// CSRF protection
		//$this->beforeFilter('csrf', array('on' => 'post'));
	}

	// Initialize
	public function setupLayout($from_no_controller = false)
	{
		global $app;

		$this->root_dir = $app['path.base'];

		$this->is_ajax = \Request::ajax();

		if ( !is_null($this->layout) )
		{
			$this->is_from_controller = !$from_no_controller;
			$current_route = \Route::getCurrentRoute();

			$this->current_route_action = $current_route !== NULL ? $current_route->getAction() : NULL;

			//App::setLocale('es');
			$this->assign('user', $this->user, array('layout', 'content'));

			// Initialize layout
			$this->layout = \View::make($this->layout);

			// Initialize page title variable
			$this->layout->page_title = '';

			// Add current route to views
			if ( $current_route !== NULL )
				$this->assign('current_route', $current_route->getPath(), array('layout', 'content'));

			// Current controller
			if ( $this->is_from_controller )
			{
				$_route = $this->current_route_action;

				$_route = strtolower(str_replace('Controller', '', $_route['controller']));
				list($controller, $action) = explode('@', $_route);

				if ( isset($controller) && $controller !== NULL )
				{
					$this->current_controller = $controller;
				}

				if ( isset($action) && $action !== NULL )
				{
					$this->current_action = $action;
				}
			}

			$this->assign('current_controller', $this->current_controller, 'layout');

			// Popup
			if ( \Session::has('popup') )
			{
				$popup = \Session::get(self::POPUP_COOKIE);

				\Session::remove(self::POPUP_COOKIE);

				$this->assign('popup', $popup, array('js'));
			}

			// UI
			$this->ui = new UI();

			// Ajax Helper
			if ( $this->is_ajax )
			{
				$this->ajax = new Ajax($this->ui);
			}

			$this->current_page = $this->get_current_page();
			$this->assign('current_page', $this->current_page, array('layout', 'content', 'js'));

			$this->assign('BASE_URL', \URL::route('home', array(), ''), 'js');

			$this->assign('DEBUG', \App::environment() !== 'live' ? true : false, array('layout', 'content', 'js'));
		}
	}

	protected function assign($key, $value, $section = 'content')
	{
		$assign = function($section, $key, $value) {
			if ( isset($this->data[$section][$key]) )
				throw new \Exception('Var "' . $key . '" already assiged.');

			$this->data[$section][$key] = $value;
		};

		if ( is_array($section) )
		{
			$types = (array)$section;

			foreach ( $types as $section )
				$assign($section, $key, $value);
		}
		else
			$assign($section, $key, $value);
	}

	public function add_css($css, $id = '')
	{
		if ( in_array($css, $this->assets['css']) )
			throw new \Exception('Stylesheet "' . $css . '" already added.');

		$this->assets['css'][] = array
		(
			'id' => $id,
			'file' => $css
		);
	}

	public function add_js($js)
	{
		if ( in_array($js, $this->assets['js']) )
			throw new \Exception('JavaScript "' . $js . '" already added.');

		$this->assets['js'][] = $js;
	}

	public function add_lib($lib_name, $css_files = NULL, $js_files = NULL, $dependencies = array())
	{
		$this->libs[$lib_name] = array
		(
			'css' => $css_files,
			'js' => $js_files
		);
	}

	public function load_lib($lib_name, $pre_loaded = true)
	{
		if ( !isset($this->libs[$lib_name]) )
			throw new \Exception('Library "' . $lib_name . '" does not exist.');

		$this->loaded_libs[$pre_loaded ? 'pre_loaded' : 'user_loaded'][] = $lib_name;

		// Dependencies
		if ( isset($this->libs[$lib_name]['dependencies']) && is_array($this->libs[$lib_name]['dependencies']) )
		{
			foreach ( $this->libs[$lib_name]['dependencies'] as $dependency_lib_name )
			{
				$this->load_lib($dependency_lib_name, true);
			}
		}
	}

	public function display($view_file = NULL, $page_title = NULL, $page_title_appendix = true, $libs_to_load = array(), $css_to_load = NULL, $js_to_load = NULL)
	{
		$this->did_display = true;

		$this->layout->page_title = ($page_title !== NULL) ? $page_title . ($page_title_appendix ? ' ' . \Config::get('base::PAGE_TITLE_SEPARATOR') . ' ' . \Config::get('base::PAGE_TITLE_APPENDIX') : '') : \Config::get('base::DEFAULT_PAGE_TITLE');

		$include_css = function($css)
		{
			if ( is_array($css) )
				foreach ( $css as $css_file )
					$this->add_css($css_file, '', false);
			else
				$this->add_css($css, '', false);
		};

		$include_js = function($js)
		{
			if ( is_array($js) )
				foreach ( $js as $js_file )
					$this->add_js($js_file, '', false);
			else
				$this->add_js($js, '', false);
		};

		foreach ( $this->loaded_libs['pre_loaded'] as $lib_name )
		{
			$lib = $this->libs[$lib_name];

			if ( isset($lib['css']) )
				$include_css($lib['css']);

			if ( isset($lib['js']) )
				$include_js($lib['js']);
		}

		foreach ( $libs_to_load as $lib_name )
		{
			$lib = $this->libs[$lib_name];

			if ( isset($lib['css']) )
				$include_css($lib['css']);

			if ( isset($lib['js']) )
				$include_js($lib['js']);
		}

		//$this->add_js('js/global.js');

		foreach ( $this->loaded_libs['user_loaded'] as $lib_name )
		{
			$lib = $this->libs[$lib_name];

			if ( isset($lib['css']) )
				$include_css($lib['css']);

			if ( isset($lib['js']) )
				$include_js($lib['js']);
		}

		$this->add_js('js/global.js');

		// Auto load layout
		// CSS
		$css_layout_filename = 'css/' . str_replace('.', '/', $this->layout->getName()) . '.css';
		$css_layout_path = public_path() . '/' . $css_layout_filename;

		if ( file_exists($css_layout_path) )
			$this->add_css($css_layout_filename, '', false);

		// JS
		$js_layout_filename = 'js/' . str_replace('.', '/', $this->layout->getName()) . '.js';
		$js_layout_path = public_path() . '/' . $js_layout_filename;

		if ( file_exists($js_layout_path) )
			$this->add_js($js_layout_filename);

		// Load display()-loaded JS and CSS
		if ( is_array($css_to_load) )
		{
			foreach ( $css_to_load as $css_filename )
			{
				$include_css($css_filename);
			}
		}
		else if ( $css_to_load !== NULL )
		{
			$include_css($css_to_load);
		}

		if ( is_array($js_to_load) )
		{
			foreach ( $js_to_load as $js_filename )
			{
				$include_js($js_filename);
			}
		}
		else if ( $js_to_load !== NULL )
		{
			$include_js($js_to_load);
		}

		// Load based of /layout
		if ( $this->is_from_controller )
		{
			$_route = $this->current_route_action;

			$_route = strtolower(str_replace('Controller', '', $_route['controller']));
			list($controller, $action) = explode('@', $_route);

			$css_short_auto_path = 'css/' . $controller . '/' . $action . '.css';
			$css_auto_path = public_path() . '/' . $css_short_auto_path;

			if ( file_exists($css_auto_path) )
				$this->add_css($css_short_auto_path, '', false);

			$js_short_auto_path = 'js/' . $controller . '/' . $action . '.js';
			$js_auto_path = public_path() . '/' . $js_short_auto_path;

			if ( file_exists($js_auto_path) )
				$this->add_js($js_short_auto_path);
		}

		$this->layout->assets = $this->assets;

		$js_vars_view = \View::make('base::layouts/partials/js_vars');
		$js_vars_view->js_vars = $this->data['js'];

		$this->layout->js_vars = $js_vars_view->render();//$this->data['js'];

		foreach ( $this->data['layout'] as $key => $value )
		{
			$this->layout->$key = $value;
		}

		$content_data = $this->get_content_data();

		// Automatically detect content view
		if ( $view_file !== NULL )
		{
			$route = $view_file;
		}
		else
		{
			$route = $controller . '/' . $action;
		}

		/*if ( $route === '' )
		{
			if ( isset($controller, $action) )
			{
				$route = $controller . '/' . $action;
			}
		}
		else
		{
			$route = str_replace('_', '/', $route);
		}*/

		return $this->layout
			->nest('content', $route, $content_data);
	}

	public function get_content_data()
	{
		return array_merge
		(
			$this->data['content'],
			array('js_vars' => $this->data['js'])
		);
	}

	private function get_current_page()
	{
		$current_page = '';

		if ( $this->is_from_controller )
		{
			$_route = $this->current_route_action;

			$_route = strtolower(str_replace('Controller', '', $_route['controller']));
			list($controller, $action) = explode('@', $_route);

			$current_page = $controller . '/' . $action;
		}

		return $current_page;
	}

	protected function get_db_queries()
	{
		return DB::getQueryLog();
	}
}
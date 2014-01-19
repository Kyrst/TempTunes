<?php defined('SYSPATH') or die('No direct script access.');

abstract class Controller_Base extends Controller_Template
{
	public $template = 'layouts/front';

	private $data = array
	(
		'layout' => array(),
		'content' => array(),
		'js' => array()
	);

	protected $ajax;
	protected $is_ajax = FALSE;

	protected $css;
	protected $js;

	protected $notice;

	protected $libs = array();

	private $loaded_libs = array
	(
		'pre_loaded' => array(),
		'user_loaded' => array()
	);

	protected $session;
	protected $cache;
	protected $constants;

	protected $user;

	protected $current_url;
	protected $current_controller;
	protected $current_action;
	protected $current_page;

	protected $redirect_data;

	private $canonical_meta;

	abstract protected function setup_libs($libs_dir);

	// Initialize
	function before()
	{
		parent::before();

		$this->session = Session::instance();

		$this->notice = new Notice($this->session);

		$this->ajax = new Ajax($this->notice);
		$this->is_ajax = $this->request->is_ajax();

		$this->cache = Cache::instance('mc');
		$this->settings = Kohana::$config->load('settings');

		$this->user = Auth::instance()->logged_in() ? Auth::instance()->get_user() : NULL;

		if ( !$this->is_ajax )
		{
			$this->setup_libs('assets/libs/');

			$this->css = new CSS();
			$this->js = new JS();

			$this->assign('BASE_URL', BASE_URL, array('js'));
			$this->assign('DEBUG', DEBUG, array('layout', 'content', 'js'));

			$this->assign('user', $this->user, array('layout', 'content'));

			// Current URL
			$this->current_url = URL::base() . Request::current()->uri() . HTML::chars(URL::query());
			$this->assign('current_url', $this->current_url);

			// Current action
			$this->current_controller = $this->request->controller();
			$this->current_action = $this->request->action();

			$this->assign('current_controller', $this->current_controller, array('layout', 'content'));
			$this->assign('current_action', $this->current_action, array('layout', 'content'));

			// Current page
			$this->current_page = strtolower($this->current_controller . '/' . $this->current_action);
			$this->assign('current_page', $this->current_page, array('layout', 'content', 'js'));

			// Redirect data
			if ( ($this->redirect_data = $this->session->get('redirect_data')) )
			{
				$this->assign('redirect_data', $this->redirect_data);

				$this->session->delete('redirect_data');
			}
		}
		else
		{
			$this->auto_render = FALSE;
		}
	}

	// Assign variable to template/JS
	protected function assign($key, $value, $section = 'content')
	{
		if ( is_array($section) )
		{
			$types = (array)$section;

			foreach ( $types as $section )
			{
				if ( isset($this->data[$section][$key]) )
					throw new Exception('Var "' . $key . '" already assiged.');

				if ( $section === 'js' )
					if ( is_bool($value) )
						$value = $value ? 'true' : 'false';

				$this->data[$section][$key] = $value;
			}
		}
		else
		{
			if ( isset($this->data[$section][$key]) )
				throw new Exception('Var "' . $key . '" already assiged.');

			if ( $section === 'js' )
				if ( is_bool($value) )
					$value = $value ? 'true' : 'false';

			$this->data[$section][$key] = $value;
		}
	}

	// Load library
	public function load_lib($lib_name, $user_loaded = FALSE)
	{
		if ( !isset($this->libs[$lib_name]) )
			throw new Exception('Library "' . $lib_name . '" does not exist.');

		$this->loaded_libs[$user_loaded ? 'user_loaded' : 'pre_loaded'][] = $lib_name;
	}

	// Display page
	public function display($template_file, $page_title = '', $page_title_appendix = TRUE, $meta_description = '')
	{
		// Include pre-loaded libs
		foreach ( $this->loaded_libs['pre_loaded'] as $lib_name )
		{
			if ( !isset($this->libs[$lib_name]) )
				throw new Exception('Library "' . $lib_name . '" has not been setup.');

			$lib = $this->libs[$lib_name];

			if ( isset($lib['css']) )
			{
				if ( is_array($lib['css']) )
					foreach ( $lib['css'] as $css_file )
						$this->css->add($css_file, '', FALSE, TRUE);
				else
					$this->css->add($lib['css'], '', FALSE, TRUE);
			}

			if ( isset($lib['external_css']) )
			{
				if ( is_array($lib['external_css']) )
					foreach ( $lib['external_css'] as $css_file )
						$this->css->add_external($css_file, TRUE);
				else
					$this->css->add_external($lib['external_css'], TRUE);
			}

			if ( isset($lib['js']) )
			{
				if ( is_array($lib['js']) )
				{
					foreach ( $lib['js'] as $js_file )
					{
						if ( is_array($js_file) )
						{
							$this->js->add_conditional($js_file['file'], $js_file['condition'], isset($js_file['downlevel_revealed']) ? $js_file['downlevel_revealed'] : TRUE, FALSE, TRUE);
						}
						else
						{
							$this->js->add($js_file, FALSE, TRUE);
						}
					}
				}
				else
					$this->js->add($lib['js'], FALSE, TRUE);
			}

			if ( isset($lib['external_js']) )
			{
				if ( is_array($lib['external_js']) )
					foreach ( $lib['external_js'] as $js_file )
						$this->js->add_external($js_file, TRUE);
				else
					$this->js->add_external($lib['external_js'], TRUE);
			}
		}

		// Include global.css
		$this->css->add('global.css', '', TRUE, TRUE);

		// Include base.js
		$this->js->add('base.js', TRUE, TRUE);

		// Include user-loaded libs
		foreach ( $this->loaded_libs['user_loaded'] as $lib_name )
		{
			$lib = $this->libs[$lib_name];

			if ( isset($lib['css']) )
			{
				if ( is_array($lib['css']) )
					foreach ( $lib['css'] as $css_file )
						$this->css->add($css_file, '', FALSE, TRUE);
				else
					$this->css->add($lib['css'], '', FALSE, TRUE);
			}

			if ( isset($lib['external_css']) )
			{
				if ( is_array($lib['external_css']) )
					foreach ( $lib['external_css'] as $css_file )
						$this->css->add_external($css_file, TRUE);
				else
					$this->css->add_external($lib['external_css'], TRUE);
			}

			if ( isset($lib['js']) )
			{
				if ( is_array($lib['js']) )
					foreach ( $lib['js'] as $js_file )
						$this->js->add($js_file, FALSE, TRUE);
				else
					$this->js->add($lib['js'], FALSE, TRUE);
			}

			if ( isset($lib['external_js']) )
			{
				if ( is_array($lib['external_js']) )
					foreach ( $lib['external_js'] as $js_file )
						$this->js->add_external($js_file, TRUE);
				else
					$this->js->add_external($lib['external_js'], TRUE);
			}
		}

		// Include CSS & JS based off template
		$template_basename = basename($this->template->getFile(), '.php');

		if ( file_exists(CSS::get_dir(TRUE) . 'layouts/' . $template_basename . '.css') )
			$this->css->add('layouts/' . $template_basename . '.css', '', TRUE, TRUE);

		if ( file_exists(JS::get_dir(TRUE) . 'layouts/' . $template_basename . '.js') )
			$this->js->add('layouts/' . $template_basename . '.js', TRUE, TRUE);

		// Include CSS & JS based off route
		$controller = strtolower($this->current_controller);

		$extra_dir = '';

		if ( $template_basename === 'admin' && $controller !== 'admin' )
		{
			$extra_dir = 'admin/';
		}

		if ( file_exists(CSS::get_dir(TRUE) . $extra_dir . $controller . '/' . $this->current_action . '.css') )
			$this->css->add($extra_dir . $controller . '/' . $this->current_action . '.css', '', TRUE);

		if ( file_exists(JS::get_dir(TRUE) . $extra_dir . $controller . '/' . $this->current_action . '.js') )
			$this->js->add($extra_dir . $controller . '/' . $this->current_action . '.js', TRUE);

		// Exclude assets
		$this->css->do_exclusion();
		$this->js->do_exclusion();

		// Attach page title
		$this->template->page_title = ($page_title !== '') ? $page_title . ($page_title_appendix ? ' ' . $this->settings->get('page_title_separator') . ' ' . $this->settings->get('page_title_appendix') : '') : $this->settings->get('default_page_title');

		// Attach META description
		$this->template->meta_description = !empty($meta_description) ? $meta_description : $this->settings->get('default_meta_description');

		// Attach assets
		$this->template->css_include = $this->css->output();
		$this->template->js_include = $this->js->output();

		// Canonical META
		$this->template->canonical_meta = $this->canonical_meta;

		// Attach notices
		if ( $this->notice->have_notices() )
			$this->assign('notices', $this->notice->output(), array('js'));

		// Attach layout variables to template
		foreach ( $this->data['layout'] as $key => $value )
			$this->template->$key = $value;

		// Attach JS variables to template
		$js_vars_view = View::factory('layouts/partials/js_vars');
		$js_vars_view->js_vars = $this->data['js'];

		$this->template->js_vars_html = $js_vars_view->render();

		// Attach content to template
		$view = View::factory($controller . '/' . $template_file);

		foreach ( $this->data['content'] as $key => $value )
			$view->$key = $value;

		$this->template->content = $view->render();
	}

	// Redirect
	public static function redirect($uri = '', $code = 302, $data = array())
	{
		if ( count($data) > 0 )
		{
			Session::instance()->set('redirect_data', $data);
		}

		return HTTP::redirect($uri, $code);
	}

	// Canonical META
	public function set_canonical_meta($url)
	{
		$this->canonical_meta = $url;
	}
}
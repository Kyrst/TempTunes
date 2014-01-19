<?php
namespace Kyrst\Base\Helpers;

class Ajax
{
	private $notice;

	private $data = array
	(
		'errors' => array(),
		'successes' => array(),
		'validation' => array(),
		'actions' => array(),
		'redirect' => NULL,
		'data' => array()
	);

	private $data_keys = array();

	function __construct(Notice $notice)
	{
		$this->data_keys = array_keys($this->data);

		$this->notice = $notice;
	}

	public function add_data($key, $value)
	{
		if ( in_array($key, $this->data_keys) )
			throw new Exception('Can\'t add data with key "' . $key . '" since it\'s a restricted keyword.');

		$this->data['data'][$key] = $value;
	}

	public function add_error($message)
	{
		$this->data['errors'][] = $message;
	}

	public function add_success($message)
	{
		$this->data['successes'][] = $message;
	}

	public function set_validation(\Illuminate\Validation\Validator $validation)
	{
		foreach ( $validation->messages()->toArray() as $element => $message )
		{
			$this->data['validation'][] = array
			(
				'element' => $element,
				'message' => $message
			);
		}
	}

	public function add_action($selector, $method, $content = '')
	{
		$this->data['actions'][] = array
		(
			'selector' => $selector,
			'method' => $method,
			'content' => $content
		);
	}

	public function redirect($url)
	{
		$this->data['redirect'] = $url;
	}

	public function output()
	{
		ob_start();

		if ( $this->data['redirect'] === NULL )
		{
			unset($this->data['redirect']);
		}
		else
		{
			// On AJAX redirect, send the success and error messages to Notice to display after page load
			foreach ( $this->data['successes'] as $success )
				$this->notice->add_success($success);

			foreach ( $this->data['errors'] as $success )
				$this->notice->add_error($success);

			$this->notice->save_session();
		}

		die(json_encode($this->data));
	}

	public function output_with_error($error)
	{
		$this->add_error($error);

		$this->output();
	}
}
<?php
namespace Kyrst\Base\Helpers;

class UI
{
	private $notices = array
	(
		'pre_loaded' => array(),
		'user_loaded' => array()
	);

	function __construct()
	{
		$empty = array
		(
			'successes' => array(),
			'errors' => array(),
			'infos' => array()
		);

		$notices_session = \Session::get('ui');

		if ( $notices_session !== NULL )
		{
			$this->notices['pre_loaded'] = $notices_session;
			$this->notices['user_loaded'] = $empty;
		}
		else
		{
			$this->notices['pre_loaded'] = $empty;
			$this->notices['user_loaded'] = $empty;
		}
	}

	function __destruct()
	{
		$this->save_session();
	}

	public function add_success($message)
	{
		$this->notices['user_loaded']['successes'][] = $message;
	}

	public function add_error($message)
	{
		$this->notices['user_loaded']['errors'][] = $message;
	}

	public function add_info($message)
	{
		$this->notices['user_loaded']['infos'][] = $message;
	}

	public function have_notices()
	{
		return ((count($this->notices['pre_loaded']['successes']) > 0) || (count($this->notices['pre_loaded']['errors']) > 0) || (count($this->notices['pre_loaded']['infos']) > 0));
	}

	public function get_notices()
	{
		return $this->notices;
	}

	public function delete_session()
	{
		\Session::forget('ui');
	}

	public function save_session()
	{
		\Session::set('ui', $this->notices['user_loaded']);
	}

	public function output()
	{
		$this->delete_session();

		return json_encode($this->notices['pre_loaded']);
	}
}
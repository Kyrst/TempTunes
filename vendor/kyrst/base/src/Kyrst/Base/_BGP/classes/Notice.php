<?php
class Notice
{
	private $session;

	private $notices = array
	(
		'pre_loaded' => array(),
		'user_loaded' => array()
	);

	function __construct($session)
	{
		$this->session = $session;

		$empty = array
		(
			'successes' => array(),
			'errors' => array(),
			'infos' => array()
		);

		$notices_session = $this->session->get('notices');

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
		$this->session->delete('notices');
	}

	public function save_session()
	{
		$this->session->set('notices', $this->notices['user_loaded']);
	}

	public function output()
	{
		$this->delete_session();

		return json_encode($this->notices['pre_loaded']);
	}
}
<?php
class CSS
{
	private $files = array
	(
		'pre_loaded' => array(),
		'user_loaded' => array()
	);

	private $scheduled_exclusions = array();

	public static function get_dir($absolute = FALSE)
	{
		return $absolute ? PUBLIC_ROOT . 'assets/css/' : 'assets/css/';
	}

	private function check_already_added($path, $throw_exception = FALSE)
	{
		foreach ( $this->files as $files )
		{
			foreach ( $files as $file )
			{
				if ( $path === $file['path'] )
				{
					if ( $throw_exception )
						throw new Exception('Stylesheet file "' . $path . '" already added.');
					else
						return true;
				}
			}
		}

		return false;
	}

	private function check_exists($path, $throw_exception = FALSE)
	{
		if ( !file_exists(PUBLIC_ROOT . $path) )
		{
			if ( $throw_exception )
				throw new Exception('Stylesheet file "' . $path . '" does not exist.');
			else
				return false;
		}

		return true;
	}

	public function add($path, $media = '', $default_dir = TRUE, $pre_loaded = FALSE)
	{
		if ( $default_dir )
			$path = self::get_dir() . $path;

		if ( !$this->check_exists($path, DEBUG) || $this->check_already_added($path, DEBUG) )
			return;

		$this->files[$pre_loaded ? 'pre_loaded' : 'user_loaded'][] = array
		(
			'path' => $path,
			'output_path' => BASE_URL . $path,
			'media' => $media
		);
	}

	public function add_external($url, $media = '', $pre_loaded = FALSE)
	{
		if ( $this->check_already_added($url, DEBUG) )
			return;

		$this->files[$pre_loaded ? 'pre_loaded' : 'user_loaded'][] = array
		(
			'path' => $url,
			'output_path' => $url,
			'media' => $media
		);
	}

	public function exclude($path)
	{
		/*if ( DEBUG )
		{
			if ( !$this->check_already_added($path) )
				throw new Exception('Stylesheet file "' . $path . '" has not been added and can therefore not be excluded.');
		}*/

		if ( in_array($path, $this->scheduled_exclusions) )
		{
			if ( DEBUG )
				throw new Exception('Stylesheet file "' . $path . '" has already been scheduled for exclusion.');
			else
				return;
		}

		$this->scheduled_exclusions[] = $path;
	}

	public function do_exclusion()
	{
		foreach ( $this->scheduled_exclusions as $exclude_file )
		{
			foreach ( $this->files as $type => $files )
			{
				foreach ( $files as $index => $file )
				{
					if ( $exclude_file === $file['path'] )
					{
						unset($this->files[$type][$index]);

						break;
					}
				}
			}
		}
	}

	public function get_loaded_files()
	{
		return $this->files;
	}

	public function output()
	{
		$files = array_merge($this->files['pre_loaded'], $this->files['user_loaded']);

		$html = '';

		foreach ( $files as $file )
		{
			$html .= '<link href="' . $file['output_path'] . '" rel="stylesheet"' . ($file['media'] !== '' ? ' media="' . $file['media'] . '"' : '') . '>' . PHP_EOL;
		}

		return $html;
	}
}
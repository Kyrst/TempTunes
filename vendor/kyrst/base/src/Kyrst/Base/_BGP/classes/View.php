<?php defined('SYSPATH') or die('No direct script access.');

class View extends Kohana_View
{
	public function getFile()
	{
		return $this->_file;
	}
}
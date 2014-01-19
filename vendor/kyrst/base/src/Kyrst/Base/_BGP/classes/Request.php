<?php
defined('SYSPATH') or die('No direct script access.');

class Request extends Kohana_Request
{
	public function query($key = NULL, $value = NULL, $validation_filter = NULL)
	{
		$result = parent::query($key, $value);

		return ($validation_filter === NULL) ? $result : ($result !== NULL ? (filter_var($result, $validation_filter) ? $result : NULL) : NULL);
	}
}
?>
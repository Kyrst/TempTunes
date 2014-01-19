<?php defined('SYSPATH') OR die('No direct script access.');

class Validation extends Kohana_Validation 
{
	public function errors($file = NULL, $translate = TRUE)
	{
		if(empty($file))
			$file = 'validation';
		return parent::errors($file, $translate);
	}
}

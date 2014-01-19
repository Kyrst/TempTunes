<?php defined('SYSPATH') or die('No direct script access.');

class Helper_Upload
{
	public static function get_php_ini_upload_max_filesize_in_bytes()
	{
		$value = trim(ini_get('upload_max_filesize'));

		switch ( strtolower($value[strlen($value) - 1]) )
		{
			case 'g':
				$value *= 1024;
			case 'm':
				$value *= 1024;
			case 'k':
				$value *= 1024;
		}

		return $value;
	}

	public static function error_code_to_message($code)
	{
		switch ($code)
		{
			case UPLOAD_ERR_INI_SIZE:
				$message = 'The uploaded file exceeds the upload_max_filesize (' . ini_get('upload_max_filesize') . ') directive in php.ini.';

				break;
			case UPLOAD_ERR_FORM_SIZE:
				$message = 'The uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the HTML form.';

				break;
			case UPLOAD_ERR_PARTIAL:
				$message = 'The uploaded file was only partially uploaded.';

				break;
			case UPLOAD_ERR_NO_FILE:
				$message = 'No file was uploaded.';

				break;
			case UPLOAD_ERR_NO_TMP_DIR:
				$message = 'Missing a temporary folder.';

				break;
			case UPLOAD_ERR_CANT_WRITE:
				$message = 'Failed to write file to disk.';

				break;
			case UPLOAD_ERR_EXTENSION:
				$message = 'File upload stopped by extension.';

				break;
			default:
				$message = 'Unknown upload error.';
				break;
		}

		return $message;
	}
}
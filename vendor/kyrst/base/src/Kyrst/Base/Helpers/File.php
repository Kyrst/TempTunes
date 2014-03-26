<?php
namespace Kyrst\Base\Helpers;

class File
{
	public static function format_bytes($bytes, $precision = 2)
	{
		$units = array('B', 'KB', 'MB', 'GB', 'TB');

		$bytes = max($bytes, 0);
		$pow = floor(($bytes ? log($bytes) : 0) / log(1024));
		$pow = min($pow, count($units) - 1);

		$bytes /= (1 << (10 * $pow));

		return round($bytes, $precision) . ' ' . $units[$pow];
	}

	public static function format_filesize_from_ini($value)
	{
		$value = trim($value);

		$last = strtolower($value[strlen($value) - 1]);

		switch($last)
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

	public static function remove_dir($dir)
	{
		if ( is_dir($dir) )
		{
			$files_and_folders = scandir($dir);

			foreach ( $files_and_folders as $file_or_folder )
			{
				if ( $file_or_folder !== '.' && $file_or_folder !== '..' )
				{
					if ( filetype($dir . '/' . $file_or_folder) == "dir" )
					{
						self::remove_dir($dir . '/' . $file_or_folder);
					}
					else
					{
						unlink($dir . '/' . $file_or_folder);
					}
				}
			}

			reset($files_and_folders);
			rmdir($dir);
		}
	}
}
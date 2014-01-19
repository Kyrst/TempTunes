<?php defined('SYSPATH') or die('No direct script access.');

class Helper_URL
{
	public static function add_protocol($url)
	{
		if ( substr($url, 0, 7) !== 'http://' && substr($url, 0, 8) !== 'https://' )
		{
			$url = 'http://' . $url;
		}

		return $url;
	}

	public static function get_domain($url)
	{
		$host = parse_url($url, PHP_URL_HOST);

		if ( $host !== NULL && preg_match('/(?P<domain>[a-z0-9][a-z0-9\-]{1,63}\.[a-z\.]{2,6})$/i', $host, $matches) )
		{
			return $matches['domain'];
		}
	}
}
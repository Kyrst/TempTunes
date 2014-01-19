<?php defined('SYSPATH') or die('No direct script access.');

class Helper_Time
{
	public static function get_relative_time($timestamp, $format = 'M jS \a\t g:i A')
	{
		if ( !filter_var($timestamp, FILTER_VALIDATE_INT) )
		{
			$timestamp = strtotime($timestamp);
		}

		$timestamp_date = date('Y-m-d', $timestamp);
		$current_date = date('Y-m-d');

		$diff = Date::span($timestamp, $_SERVER['REQUEST_TIME'], 'days, hours, minutes, seconds');

		$timestamp_year = date('Y', $timestamp);
		$does_year_diff = date('Y') !== $timestamp_year;

		if ( $timestamp > $_SERVER['REQUEST_TIME'] )
		{
			if ( $diff['days'] === 1 )
			{
				if ( $diff['hours'] > 0 )
					return 'Today at ' . date('g:i A', $timestamp);
				elseif ( $diff['minutes'] > 0 )
					return 'In ' . $diff['minutes'] . ' ' . Inflector::plural('minute', $diff['minutes']);
				elseif ( $diff['seconds'] > 0 )
					return 'In ' . $diff['seconds'] . ' ' . Inflector::plural('second', $diff['seconds']);
			}
		}
		elseif ( $timestamp_date === $current_date )
		{
			// Today
			if ( $diff['hours'] > 0 )
				return 'Today at ' . date('g:i A', $timestamp);
			elseif ( $diff['minutes'] > 0 )
				return $diff['minutes'] . ' ' . Inflector::plural('minute', $diff['minutes']) . ' ago';
			elseif ( $diff['seconds'] > 0 )
				return $diff['seconds'] . ' ' . Inflector::plural('second', $diff['seconds']) . ' ago';
			else
				return 'Just a second ago';
		}
		elseif ( $timestamp_date < $current_date )
		{
			// Another day
			$yesterday = date('Y-m-d', strtotime('-1 day'));

			if ( $timestamp_date === $yesterday ) // Yesterday
			{
				return 'Yesterday at ' . date('g:i A', $timestamp);
			}
		}

		return date($does_year_diff ? 'M jS Y, g:i A' : $format, $timestamp);
	}
}
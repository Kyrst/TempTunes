<?php defined('SYSPATH') or die('No direct script access.');

class Helper_Credit_Card
{
	const TYPE_MASTERCARD = 'mastercard';
	const TYPE_VISA = 'visa';
	const TYPE_DISCOVER = 'discover';
	const TYPE_AMERICAN_EXPRESS = 'american_express';

	public static function get_types()
	{
		$types = array
		(
			self::TYPE_MASTERCARD,
			self::TYPE_VISA,
			self::TYPE_DISCOVER,
			self::TYPE_AMERICAN_EXPRESS
		);

		$result = array();

		foreach ( $types as $type )
		{
			$result[$type] = self::format_type($type);
		}

		return $result;
	}

	public static function format_type($type)
	{
		switch ( $type )
		{
			case self::TYPE_MASTERCARD: return 'MasterCard'; break;
			case self::TYPE_VISA: return 'VISA'; break;
			case self::TYPE_DISCOVER: return 'Discover'; break;
			case self::TYPE_AMERICAN_EXPRESS: return 'American Express'; break;
		}
	}

	public static function format_stripe($type)
	{
		switch ( $type )
		{
			case 'MasterCard': return self::TYPE_MASTERCARD; break;
			case 'Visa': return self::TYPE_VISA; break;
			case 'Discover': return self::TYPE_DISCOVER; break;
			case 'American Express': return self::TYPE_AMERICAN_EXPRESS; break;
		}
	}

	public static function format_last4($last4, $type)
	{
		switch ( $type )
		{
			case self::TYPE_MASTERCARD:
			case self::TYPE_VISA:
			case self::TYPE_DISCOVER:
				return '**** **** **** ' . $last4;

				break;
			case self::TYPE_AMERICAN_EXPRESS:
				return '**** ****** *' . $last4;

				break;
		}
	}
}
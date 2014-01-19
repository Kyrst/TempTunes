<?php defined('SYSPATH') OR die('No direct script access.');

class HTML extends Kohana_HTML
{
	static function region_switcher($element, $default_country_option = '', $default_region_option = '', $selected_country = '', $selected_region = '', $countries = array(), $regions = array(), $country_classes = '', $region_classes = '', $country_label_classes = '', $region_label_classes = '')
	{
		$html = '<div class="bgp-region-switcher" ';
		$html .= 'data-id="' . $element . '" ';
		$html .= 'data-countries=\'' . HTML::chars(json_encode($countries)) . '\'" ';
		$html .= 'data-regions=\'' . HTML::chars(json_encode($regions)) . '\'"';
		$html .= ($default_country_option !== '' ? ' data-default_country_option="' . $default_country_option . '"' : '');
		$html .= ($default_region_option !== '' ? ' data-default_region_option="' . $default_region_option . '"' : '');
		$html .= ($selected_country !== '' ? ' data-selected_country="' . $selected_country . '"' : '');
		$html .= ($selected_region !== '' ? ' data-selected_region="' . $selected_region . '"' : '');
		$html .= ($country_classes !== '' ? ' data-country_classes="' . $country_classes . '"' : '');
		$html .= ($region_classes !== '' ? ' data-region_classes="' . $region_classes . '"' : '');
		$html .= ($country_label_classes !== '' ? ' data-country_label_classes="' . $country_label_classes . '"' : '');
		$html .= ($region_label_classes !== '' ? ' data-region_label_classes="' . $region_label_classes . '"' : '');
		$html .= '></div>';

		return $html;
	}

	static function credit_card_dropdown_options($selected_type = '')
	{
		$html = '';

		foreach ( Helper_Credit_Card::get_types() as $value => $name )
			$html .= '<option value="' . $value . '"' . ($value === $selected_type ? ' selected' : '') . '>' . $name . '</option>';

		return $html;
	}
}
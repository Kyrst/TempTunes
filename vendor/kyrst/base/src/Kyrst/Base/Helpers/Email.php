<?php
namespace Kyrst\Base\Helpers;

class Email
{
	const SEND = true;

	public static function send($email_template_name, $subject, $to, array $from, array $data)
	{
		$data['background_color'] = '#DEDEDE';
		$data['fore_color'] = '#111';

		if ( self::SEND === true )
		{
			$result = \Mail::send('emails.welcome_beta', $data, function($message) use ($subject, $from, $to)
			{
				$message->from($from['email'], $from['name']);

				$message->to($to)->subject($subject);
			});
		}
		else
		{
			$result = NULL;
		}

		return $result;
	}

	public static function render($email_template_name, array $data)
	{
		$layout = \View::make('layouts.email');

		$view = \View::make('emails.' . $email_template_name);
		$view->background_color = '#DEDEDE';
		$view->fore_color = '#111';

		foreach ( $data as $key => $value )
		{
			$view->$key = $value;
		}

		$layout->background_color = '#DEDEDE';
		$layout->fore_color = '#111';
		$layout->content = $view->render();

		$email_content = $layout->render();

		return $email_content;
	}
}
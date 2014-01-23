<?php
use Kyrst\Base\Models\User as User;
use Kyrst\Base\Helpers\Ajax as Ajax;

class AjaxController extends BaseController
{
	public function sign_in()
	{
		$input = Input::all();

		$email = trim($input['email']);
		$password = trim($input['password']);

		$ajax = new Ajax($this->ui);

		$user = User::login($email, $password);

		if ( $user )
		{
			$ajax->redirect(URL::route('dashboard'));
		}
		else
		{
			$ajax->add_error('INVALID_CREDENTIALS');
		}

		return $ajax->get_output();

		//$ajax->output();
	}

	public function log_out()
	{
		Auth::logout();

		return Redirect::route('home');
	}
}
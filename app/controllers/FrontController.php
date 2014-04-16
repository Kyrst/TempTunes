<?php
class FrontController extends BaseController
{
	public function index()
	{
		$this->display();
	}

	public function sign_up()
	{
		if ( $this->is_ajax && $input = Input::all() )
		{
			$username = trim($input['username']);
			$email = trim($input['email']);
			$password = trim($input['password']);
			$first_name = trim($input['first_name']);
			$last_name = trim($input['last_name']);

			$user = User::register($email, $username, $password, $first_name, $last_name);

			if ( $user !== NULL )
			{
				$user = User::login($email, $password);

				$user->num_logins++;
				$user->last_login = date('Y-m-d H:i:s');
				$user->save();

				$this->ajax->redirect($user->get_link(User::URL_PROFILE));
			}
			else
			{
				$this->ajax->add_error('Error.');
			}

			return $this->ajax->output();
		}

		$this->display();
	}

	public function sign_in()
	{
		$input = Input::all();

		if ( !$input )
		{
			return Redirect::back();
		}

		$email = trim($input['email']);
		$password = trim($input['password']);

		$this->user = User::login($email, $password);

		if ( $this->user )
		{
			$this->user->num_logins++;
			$this->user->last_login = date('Y-m-d H:i:s');
			$this->user->save();

			return Redirect::route('dashboard');
		}
		else
		{
			$this->ui->add_error('INVALID_CREDENTIALS');

			return Redirect::back();
		}
	}
}
<?php
class AdminController extends BaseController
{
	public function index()
	{
		return $this->display
		(
			NULL,
			'Administrator'
		);
	}

	public function users()
	{
		return $this->display
		(
			NULL,
			'Users - Administrator'
		);
	}

	public function get_users()
	{
		$users = User::all();

		$users_view = View::make('admin/partials/users');
		$users_view->users = $users;
		$users_view->user = $this->user;

		$users_html = $users_view->render();

		$this->ajax->add_data('users_html', $users_html);

		return $this->ajax->output();
	}

	public function user($user_id_to_edit = NULL)
	{
		$input = Input::all();

		if ( count($input) > 0 )
		{
			$email = $input['email'];
			$username = $input['username'];
			$password = $input['password'];
			$first_name = $input['first_name'];
			$last_name = $input['last_name'];

			$user = User::register($email, $username, $password, $first_name, $last_name);
		}

		$user_to_edit = NULL;

		if ( $user_id_to_edit !== NULL ) // Edit user
		{
			$heading = 'Edit ""';
		}
		else // Add new user
		{
			$heading = 'Add new user';
		}

		$this->assign('heading', $heading);
		$this->assign('user_to_edit', $user_to_edit);

		return $this->display
		(
			NULL,
			$heading . ' - Administrator'
		);
	}
}
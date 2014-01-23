<?php
namespace Kyrst\Base\Models;

use Kyrst\Base\Helpers\Time as Time;

use Toddish\Verify\Models\User as VerifyUser;
use Toddish\Verify\Models\Role as VerifyRole;

use Toddish\Verify\UserNotFoundException as UserNotFoundException;

class User extends VerifyUser
{
	public static function register($email, $password, $first_name = '', $last_name = '')
	{
		$email = trim($email);
		$password = trim($password);

		$user = new User;
		$user->email = $email;
		$user->password = $password;
		$user->first_name = trim($first_name);
		$user->last_name = trim($last_name);
		$user->verified = 1;
		$user->created_at = date(Time::ISO_DATE_FORMAT);
		$user->save();

		$role = VerifyRole::find(1);
		$user->roles()->sync(array($role->id));

		return $user;
	}

	public static function login($email, $password, $persistent = true)
	{
		try
		{
			if ( \Auth::attempt(array('email' => $email, 'password' => $password), $persistent) )
			{
				return \Auth::user();
			}
			else
			{
				return false;
			}
		}
		catch ( UserNotFoundException $e )
		{
			return false;
		}
	}

	public static function log_out()
	{
		\Auth::logout();
	}

	public function get_name()
	{
		return $this->first_name . ' ' . $this->last_name;
	}
}
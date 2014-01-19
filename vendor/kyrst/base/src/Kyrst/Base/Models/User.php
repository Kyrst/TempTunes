<?php
namespace Kyrst\Base\Models;

use Kyrst\Base\Helpers\Time as Time;

use Toddish\Verify\Models\User as VerifyUser;
use Toddish\Verify\Models\Role as VerifyRole;

class User extends VerifyUser
{
	public static function register($email, $username, $password, $first_name = '', $last_name = '', $birthdate = NULL)
	{
		$email = trim($email);
		$password = trim($password);

		$user = new User;
		$user->email = $email;
		$user->username = trim($username);
		$user->password = $password;
		$user->code = self::generate_code();
		$user->first_name = trim($first_name);
		$user->last_name = trim($last_name);
		$user->birthdate = $birthdate !== NULL ? trim(date(Time::ISO_DATE_FORMAT, strtotime($birthdate))) : NULL;
		$user->verified = 1;
		$user->created_at = date(Time::ISO_DATE_FORMAT);
		$user->save();

		$role = VerifyRole::find(1);
		$user->roles()->sync(array($role->id));

		\Auth::attempt
		(
			array
			(
				'email' => $email,
				'password' => $password
			),
			true
		);

		$user = \Auth::user();

		return $user;
	}

	public static function generate_code()
	{
		$num = 1;

		while ( $num === 1 )
		{
			$code = str_random(4);

			$num = User::where('code', '=', $code)->count();
		}

		return strtoupper($code);
	}

	public function get_name()
	{
		return $this->first_name . ' ' . $this->last_name;
	}
}
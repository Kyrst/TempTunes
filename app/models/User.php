<?php
use Kyrst\Base\Models\User as KyrstUser;

class User extends KyrstUser
{
	const URL_PROFILE = 'profile';
	const URL_SONGS = 'songs';

	public function songs()
	{
		return $this->hasMany('Song');
	}

	public function get_display_name()
	{
		return $this->username . ' (' . $this->get_name() . ')';
	}

	public function get_link($type)
	{
		if ( $type === self::URL_PROFILE )
		{
			return URL::to('users/' . $this->slug);
		}
		elseif ( $type === self::URL_SONGS )
		{
			return URL::to($this->get_link(self::URL_PROFILE) . '/songs');
		}

		return null;
	}
}
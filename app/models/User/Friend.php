<?php
class User_Friend extends Eloquent
{
	public $table = 'user_friends';

	const STATUS_PENDING = 'pending';
	const STATUS_ACCEPTED = 'accepted';
	const STATUS_DENIED = 'denied';

	public function user()
	{
		return $this->hasOne('User', 'id', 'user_id');
	}

	public function friend()
	{
		return $this->hasOne('User', 'id', 'friend_id');
	}
}
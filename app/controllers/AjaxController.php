<?php
class AjaxController extends BaseController
{
	public function log_out()
	{
		Auth::logout();

		return Redirect::route('home');
	}

	// Save song comment
	public function save_song_comment()
	{
		$input = Input::all();

		$song_version_id = $input['song_version_id'];

		try
		{
			$song_version = Song_Version::find($song_version_id)->firstOrFail();
		}
		catch ( Illuminate\Database\Eloquent\ModelNotFoundException $e )
		{
			return $this->ajax->output_with_error('SONG_VERSION_NOT_FOUND');
		}

		$song_version_comment = new Song_Version_Comment();
		$song_version_comment->song_version_id = $song_version_id;
		$song_version_comment->user_id = $this->user->id;
		$song_version_comment->comment = trim($input['comment']);
		$song_version_comment->from_seconds = $input['from_seconds'];
		$song_version_comment->to_seconds = $input['to_seconds'] ? $input['to_seconds'] : NULL;
		$song_version_comment->save();

		$this->ajax->add_data('comment_id', $song_version_comment->id);
		$this->ajax->add_data('comment', $song_version_comment->comment);
		$this->ajax->add_data('comment_hover_html', $song_version_comment->get_hover_html());
		$this->ajax->add_data('user_photo_url', $song_version_comment->user->get_photo_url(User::PHOTO_SIZE_WAVEFORM_COMMENT));

		return $this->ajax->output();
	}

	public function get_friends_autocomplete()
	{
		$input = Input::all();

		if ( !isset($input['term']) )
		{
			return $this->ajax->output_with_error('MISSING_TERM');
		}

		$term = trim($input['term']);

		$result_users = User::where('email', 'LIKE', '%' . $term . '%')
			->orWhere(DB::raw('CONCAT(first_name, " ", last_name)'), 'LIKE', '%' . $term . '%')
			->orWhere('username', 'LIKE', $term)
			->where('id', '!=', $this->user->id)
			->get();

		$users = array();

		foreach ( $result_users as $user )
		{
			$users[] = array
			(
				'id' => $user->id,
				'label' => $user->username . ' / ' . $user->get_name() . ' / ' . $user->email
			);
		}

		return Response::json($users);
	}

	public function send_friend_request()
	{
		$input = Input::all();

		if ( !isset($input['friend_user_id']) || !filter_var($input['friend_user_id'], FILTER_VALIDATE_INT) )
		{
			return $this->ajax->output_with_error('MISSING_FRIEND_USER_ID');
		}

		$friend_user_id = $input['friend_user_id'];

		$user_friend = new User_Friend();
		$user_friend->user_id = $this->user->id;
		$user_friend->friend_id = $friend_user_id;
		$user_friend->save();

		return $this->ajax->output();
	}

	public function respond_to_friend_request()
	{
		$input = Input::all();

		$id = $input['id'];
		$accept_or_deny = $input['accept_or_deny'];

		try
		{
			$friend_request = User_Friend::where('id', $id)->firstOrFail();
		}
		catch ( \Illuminate\Database\Eloquent\ModelNotFoundException $e )
		{
			return $this->ajax->output_with_error('FRIEND_REQUEST_NOT_FOUND');
		}

		$friend_request->status = ($accept_or_deny === 'accept' ? User_Friend::STATUS_ACCEPTED : User_Friend::STATUS_DENIED);
		$friend_request->save();

		return $this->ajax->output();
	}
}
<?php
class UserController extends BaseController
{
	public function profile($username)
	{
		try
		{
			$user = User::where('slug', trim($username))->firstOrFail();
		}
		catch ( Illuminate\Database\Eloquent\ModelNotFoundException $e )
		{
			$this->ui->add_error('Could not find user.');

			return Redirect::route('home');
		}

		$this->assign('profile_user', $user);

		$num_songs = $user->songs->count();
		$this->assign('num_songs', $num_songs);

		$this->display
		(
			null,
			$user->get_display_name()
		);
	}

	public function songs($username)
	{
		try
		{
			$user = User::where('slug', trim($username))->firstOrFail();
		}
		catch ( Illuminate\Database\Eloquent\ModelNotFoundException $e )
		{
			$this->ui->add_error('Could not find user.');

			return Redirect::route('home');
		}

		$this->assign('profile_user', $user);

		$this->display
		(
			null,
			'Songs by ' . $user->get_display_name()
		);
	}

	public function song($username, $song)
	{
		try
		{
			$user = User::where('slug', trim($username))->firstOrFail();
			$song = Song::where('slug', trim($song))->firstOrFail();
		}
		catch ( Illuminate\Database\Eloquent\ModelNotFoundException $e )
		{
			$this->ui->add_error('Could not find song.');

			return Redirect::route('home');
		}

		$this->assign('song', $song);

		$song_uploads = Song_Upload::where('song_id', $song->id)->orderBy('created_at', 'DESC')->get();
		$this->assign('song_uploads', $song_uploads);

		$js_song_uploads = array();

		foreach ( $song_uploads as $song_upload )
		{
			$js_song_uploads[] = array
			(
				'id' => $song_upload->id,
				'version' => $song_upload->version,
				'filename' => $song_upload->get_filename()
			);
		}

		$this->assign('js_song_uploads', $js_song_uploads, 'js');

		$this->display
		(
			null,
			$song->get_title() . ' by ' . $song->user->get_display_name()
		);
	}
}
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

		// Songs
		$songs = $user->songs->take(5);
		$this->assign('songs', $songs);

		$this->display
		(
			null,
			$user->get_display_name(),
			true
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
			'Songs by ' . $user->get_display_name(),
			true
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

		$song_versions = song_version::where('song_id', $song->id)->orderBy('created_at', 'DESC')->get();
		$this->assign('song_versions', $song_versions);

		$js_song_versions = array();

		foreach ( $song_versions as $song_version )
		{
			$js_song_versions[] = array
			(
				'id' => $song_version->id,
				'version' => $song_version->version,
				'filename' => $song_version->get_filename()
			);
		}

		$this->assign('js_song_versions', $js_song_versions, 'js');

		$this->display
		(
			null,
			$song->get_title() . ' by ' . $song->user->get_display_name()
		);
	}

	public function friends($username)
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

		$this->display();
	}
}
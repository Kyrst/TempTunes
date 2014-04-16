<?php
class Song extends Eloquent
{
	const URL_PUBLIC = 'public';
	const URL_EDIT = 'edit';
	const URL_UPLOAD_NEW_VERSION = 'upload_new_version';

	const PLAYER_SIZE_BIG = 'big';
	const PLAYER_SIZE_SONG_PAGE = 'song_page';

	const PLAYER_OPTION_SHOW_VERSIONS = 1;
	const PLAYER_OPTION_VERSIONS_MODE_DROPDOWN = 2;
	const PLAYER_OPTION_VERSIONS_MODE_TABBED = 3;

	public function user()
	{
		return $this->belongsTo('User');
	}

	public function versions()
	{
		return $this->hasMany('Song_Version');
	}

	public function uploads()
	{
		return $this->hasMany('Song_Version');
	}

	public function get_title()
	{
		return $this->title;
	}

	public function get_description()
	{
		return $this->description !== NULL ? nl2br($this->description) : '';
	}

	public function get_dir()
	{
		return $this->root_dir . '/public/uploads/' . $this->user_id . '/' . $this->id . '/';
	}

	public function get_url($type)
	{
		if ( $type === self::URL_PUBLIC )
		{
			return URL::to($this->user->get_link(User::URL_SONGS) . '/' . $this->slug);
		}
		elseif ( $type === self::URL_EDIT )
		{
			return URL::to('dashboard/edit-song/' . $this->slug);
		}
		elseif ( $type === self::URL_UPLOAD_NEW_VERSION )
		{
			return URL::to('dashboard/upload-songs/' . $this->id);
		}

		return NULL;
	}

	public function get_uploads($order = 'ASC')
	{
		$song_versions = Song_Version::where('song_id', $this->id)
			->orderBy('created_at', $order)
			->get();

		return $song_versions;
	}

	public function get_latest_version()
	{
		try
		{
			$song_version = Song_Version::where('song_id', $this->id)
				->orderBy('created_at', 'DESC')
				->firstOrFail();
		}
		catch ( Illuminate\Database\Eloquent\ModelNotFoundException $e )
		{
			return null;
		}
		finally
		{
			return $song_version;
		}
	}

	public function get_latest_versions()
	{
		return Song_Version::where('song_id', $this->id)
			->orderBy('created_at', 'DESC')
			->get();
	}

	public function print_player($size, $song_version_id = NULL, $options = array())
	{
		if ( !in_array($size, array(self::PLAYER_SIZE_BIG, self::PLAYER_SIZE_SONG_PAGE)) )
		{
			throw new Exception('Invalid size "' . $size . '".');
		}

		// If no version specified, get latest
		if ( $song_version_id === NULL )
		{
			$song_version = $this->get_latest_version();
		}
		else
		{
			try
			{
				$song_version = Song_Version::where('id', $song_version_id)->firstOrFail();
			}
			catch ( Illuminate\Database\Eloquent\ModelNotFoundException $e )
			{
				$song_version = NULL;
			}
		}

		$player_view = View::make('partials/player/' . $size);
		$player_view->song = $this;
		$player_view->song_version = $song_version;
		$player_view->identifier = $this->id . '_' . $song_version->id;
		$player_view->size = $size;
		$player_view->user_id = $song_version->song->user_id;
		$player_view->logged_in_user = Auth::user();

		return $player_view->render();
	}
}
<?php
use Kyrst\Base\Models\User as KyrstUser;

class User extends KyrstUser
{
	const URL_PROFILE = 'profile';
	const URL_SONGS = 'songs';
	const URL_FRIENDS = 'friends';

	const PHOTO_SIZE_SMALL = 'small';
	const PHOTO_SIZE_WAVEFORM_COMMENT = 'waveform_comment';

	private static $photo_sizes = array
	(
		self::PHOTO_SIZE_SMALL => array
		(
			'width' => 70,
			'height' => 100
		),
		self::PHOTO_SIZE_WAVEFORM_COMMENT => array
		(
			'width' => 20,
			'height' => 20
		)
	);

	public function songs()
	{
		return $this->hasMany('Song');
	}

	public function friends()
	{
		return $this->belongsToMany('User', 'user_friends', 'user_id', 'friend_id');
	}

	public function plan()
	{
		return $this->belongsTo('Plan');
	}

	public function get_display_name()
	{
		return $this->get_name();
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
		elseif ( $type === self::URL_FRIENDS )
		{
			return URL::to($this->get_link(self::URL_PROFILE) . '/friends');
		}

		return null;
	}

	public static function get_uploads_dir($user_id)
	{
		return base_path() . '/user_uploads/';
	}

	public static function get_photo_dir($user_id)
	{
		return self::get_uploads_dir($user_id) . $user_id . '/photo/';
	}

	public static function upload_photo($image, $user_id)
	{
		$dir = self::get_photo_dir($user_id);

		$filename = $dir . self::get_photo_filename($user_id);

		// Delete old if exists
		if ( file_exists($filename) )
		{
			unlink($filename);
		}

		// Create dir if not exists
		if ( !file_exists($dir) )
		{
			mkdir($dir, 0775, true);
		}

		$img = Image::make($image->getRealPath())
			->resize(640, 480)
			->save($filename);

		return $img;
	}

	public static function get_photo_filename($user_id)
	{
		return $user_id . '.jpg';
	}

	public function get_photo_url($size_name)
	{
		return URL::to('user-photo/' . $this->id . '/' . $size_name);
	}

	public function get_photo_html($size_name, $id = NULL)
	{
		if ( !array_key_exists($size_name, self::$photo_sizes) )
		{
			throw new Exception('Photo size "' . $size_name . '" does not exist.');
		}

		$size = self::$photo_sizes[$size_name];

		//$img_src = URL::to('profile-picture/' . $this->id . '/' . ($size['width'] !== NULL ? $size['width'] : '0') . '/' . ($size['height'] !== NULL ? $size['height'] : '0') . ($size['proportional'] === false ? '/1' : '')) . '"';
		$img_src = $this->get_photo_url($size_name);

		$img_width = isset($size['canvas']) && $size['canvas']['width'] !== NULL ? $size['canvas']['width'] : ($size['width'] !== NULL ? ' width="' . $size['width'] . '"' : '');
		$img_height = isset($size['canvas']) && $size['canvas']['height'] !== NULL ? $size['canvas']['height'] : ($size['height'] !== NULL ? ' height="' . $size['height'] . '"' : '');
		$img_id = ($id !== NULL ? ' id="' . $id . '"' : '');

		return '<img src="' . $img_src . '"' . $img_id . $img_width . $img_height . ' alt="' . $this->get_name() . '">';
	}

	public static function get_size($name)
	{
		if ( !array_key_exists($name, self::$photo_sizes) )
		{
			throw new Exception('Photo size "' . $name . '" does not exist.');
		}

		return self::$photo_sizes[$name];
	}

	public static function get_photo_cache_dir($user_id)
	{
		return self::get_photo_dir($user_id) . 'cache/';
	}

	public static function get_photo_cache_filename($user_id, $size_name)
	{
		if ( !array_key_exists($size_name, self::$photo_sizes) )
		{
			throw new Exception('Photo size "' . $size_name . '" does not exist.');
		}

		$size = self::$photo_sizes[$size_name];

		$photo_cache_filename = md5($user_id . '|' . implode('|', $size));

		return $photo_cache_filename;
	}

	public static function photo_cache_exists($user_id, $size_name)
	{
		$photo_cache_dir = self::get_photo_cache_dir($user_id);
		$photo_cache_filename = self::get_photo_cache_filename($user_id, $size_name);

		$photo_path = $photo_cache_dir . $photo_cache_filename;

		return file_exists($photo_path);
	}

	public static function get_photo_path($user_id)
	{
		return self::get_photo_dir($user_id) . self::get_photo_filename($user_id);
	}

	public function delete_photo()
	{
		Kyrst\Base\Helpers\File::remove_dir(self::get_photo_dir($this->id));
	}

	public static function get_songs_dir($user_id, $song_id, $song_version)
	{
		return self::get_uploads_dir($user_id) . 'songs/' . $song_id . '/' . ($song_version !== null ? 'v' . $song_version . '/' : '');
	}

	public function is_admin()
	{
		return $this->is('Admin');
	}

	public function get_friends($status = NULL)
	{
		$result_friends = User_Friend::where('user_id', $this->id)->orWhere('friend_id', $this->id);

		if ( $status !== NULL )
		{
			$result_friends->where('status', $status);
		}

		return $result_friends;
	}

	public function get_friend_requests()
	{
		$result_friend_requests = User_Friend::where('friend_id', $this->id)
			->where('status', User_Friend::STATUS_PENDING);

		return $result_friend_requests->get();
	}

	public function hasPlan()
	{
		return ($this->plan !== NULL);
	}

	public function get_plan_str()
	{
		if ( $this->hasPlan() )
		{
			return $this->plan->name;
		}
		else
		{
			return '/';
		}
	}

	public function getLatestUpload()
	{
		try
		{
			$song_version = Song_Version::join('songs', 'songs.id', '=', 'song_versions.song_id')
				->where('songs.user_id', $this->id)
				->orderBy('song_versions.created_at', 'DESC')
				->firstOrFail();

			return $song_version;
		}
		catch ( \Illuminate\Database\Eloquent\ModelNotFoundException $e )
		{
			return NULL;
		}
	}
}
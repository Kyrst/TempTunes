<?php
class Song extends Eloquent
{
	const URL_PUBLIC = 'public';
	const URL_EDIT = 'edit';

	public function user()
	{
		return $this->belongsTo('User');
	}

	public function uploads()
	{
		return $this->hasMany('Song_Upload');
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

		return NULL;
	}

	public function get_uploads($order = 'ASC')
	{
		$song_uploads = Song_Upload::where('song_id', $this->id)
			->orderBy('created_at', $order)
			->get();

		return $song_uploads;
	}

	public function get_latest_upload()
	{
		try
		{
			$song_upload = Song_Upload::where('song_id', $this->id)
				->orderBy('created_at', 'DESC')
				->firstOrFail();
		}
		catch ( Illuminate\Database\Eloquent\ModelNotFoundException $e )
		{
			return null;
		}
		finally
		{
			return $song_upload;
		}
	}

	public function get_latest_uploads()
	{
		return Song_Upload::where('song_id', $this->id)
			->orderBy('created_at', 'DESC')
			->get();
	}
}
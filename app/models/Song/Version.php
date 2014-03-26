<?php
class Song_Version extends Eloquent
{
	protected $table = 'song_versions';

	public function comments()
	{
		return $this->hasMany('Song_Version_Comment', 'song_version_id');
	}

	public function song()
	{
		return $this->belongsTo('Song');
	}

	public function get_filename($file_extension = 'mp3')
	{
		return User::get_songs_dir($this->song->user_id, $this->song_id, $this->version) . $this->id . '.' . $file_extension;
	}

	public static function get_filename_static($user_id, $song_id, $version, $file_extension = 'mp3')
	{
		return User::get_songs_dir($user_id, $song_id, $version) . $song_id . '.' . $file_extension;
	}

	public function get_route($file_extension = 'mp3')
	{
		return URL::to('play/' . $this->song->user_id . '/' . $this->song_id . '/v' . $this->version) . ($file_extension === 'wav' ? '/1' : '');
	}

	public function get_waveform_image($size)
	{
		return URL::to('waveform/' . $this->song->user_id . '/' . $this->song_id . '/v' . $this->version . '/' . $size);
	}
}
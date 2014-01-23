<?php
class Song_Upload extends Eloquent
{
	protected $table = 'song_uploads';

	public function song()
	{
		return $this->belongsTo('Song');
	}

	public function get_filename()
	{
		return asset('uploads/' . $this->song->user_id . '/' . $this->song_id . '/v' . $this->version . '/' . $this->song_id . '.mp3');
	}
}
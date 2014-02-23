<?php
use Kyrst\Base\Helpers\Time as Time;

class Song_Version_Comment extends Eloquent
{
	protected $table = 'song_version_comments';

	public function song_version()
	{
		return $this->belongsTo('Song_Version');
	}

	public function user()
	{
		return $this->belongsTo('User');
	}

	public function get_filename()
	{
		return asset('uploads/' . $this->song->user_id . '/' . $this->song_id . '/v' . $this->version . '/' . $this->song_id . '.mp3');
	}

	public function get_hover_html()
	{
		$comment_hover_view = View::make('partials/player/comment_hover');
		$comment_hover_view->comment = nl2br($this->comment);
		$comment_hover_view->from_seconds = Time::format_seconds($this->from_seconds);
		$comment_hover_view->to_seconds = $this->to_seconds !== NULL ? Time::format_seconds($this->to_seconds) : NULL;

		return $comment_hover_view->render();
	}
}
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
			$song_version = Song_Upload::find($song_version_id)->firstOrFail();
		}
		catch ( Illuminate\Database\Eloquent\ModelNotFoundException $e )
		{
			$this->ajax->output_with_error('SONG_VERSION_NOT_FOUND');
		}

		$song_version_comment = new Song_Upload_Comment();
		$song_version_comment->song_version_id = $song_version_id;
		$song_version_comment->user_id = $this->user->id;
		$song_version_comment->comment = trim($input['comment']);
		$song_version_comment->from_seconds = $input['from_seconds'];
		$song_version_comment->to_seconds = $input['to_seconds'] ? $input['to_seconds'] : NULL;
		$song_version_comment->save();

		$this->ajax->add_data('comment_id', $song_version_comment->id);
		$this->ajax->add_data('comment', $song_version_comment->comment);
		$this->ajax->add_data('comment_hover_html', $song_version_comment->get_hover_html());

		$this->ajax->output();
	}
}
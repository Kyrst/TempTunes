<?php
use Kyrst\Base\Helpers\Ajax as Ajax;
use Kyrst\Base\Helpers\File as File;

class DashboardController extends BaseController
{
	public $layout = 'layouts/dashboard';

	function __construct()
	{
		parent::__construct();

		$this->beforeFilter(function()
		{
			if ( $this->user === NULL )
			{
				return Redirect::route('home');
			}
		});
	}

	public function dashboard()
	{
		$this->display
		(
			null,
			'Dashboard'
		);
	}

	public function settings()
	{
		$this->display
		(
			null,
			'Settings'
		);
	}

	public function save_settings()
	{
		$input = Input::all();

		$first_name = trim($input['first_name']);
		$last_name = trim($input['last_name']);

		$this->user->first_name = $first_name;
		$this->user->last_name = $last_name;
		$this->user->save();

		$ajax = new Ajax($this->ui);
		$ajax->add_success('Settings successfully saved.');
		$ajax->output();
	}

	public function my_songs()
	{
		$songs = $this->user->songs;

		$this->assign('songs', $songs);
		$this->assign('num_songs', $this->num_songs);

		$js_songs = array();

		foreach ( $songs as $song )
		{
			$js_songs[] = array
			(
				'id' => $song->id,
				'version' => $song->version
			);
		}

		$this->assign('js_songs', $js_songs, array('js'));

		$this->display
		(
			null,
			'My Songs',
			true
		);
	}

	public function get_song_upload()
	{
		$input = Input::all();

		$song_upload_id = $input['song_upload_id'];

		$ajax = new Ajax($this->ui);

		try
		{
			$song_upload = Song_Upload::find($song_upload_id)->firstOrFail();
		}
		catch ( Illuminate\Database\Eloquent\ModelNotFoundException $e )
		{
			$ajax->output_with_error('Version not found.');
		}

		$ajax->add_data
		(
			'song_upload',
			array
			(
				'title' => $song_upload->title,
				'filename' => $song_upload->get_filename()
			)
		);

		$ajax->output();
	}

	public function upload_songs($song_id = null)
	{
		$song = null;

		if ( $song_id !== null )
		{
			try
			{
				$song = Song::find($song_id)->firstOrFail();
			}
			catch ( Illuminate\Database\Eloquent\ModelNotFoundException $e )
			{
				$this->ui->add_error('Could not find song.');

				return Redirect::route('dashboard');
			}
		}

		$this->assign('song', $song);
		$this->assign('current_song_id', $song !== null ? $song->id : 0, 'js');

		$max_upload_size = File::format_filesize_from_ini(ini_get('upload_max_filesize'));

		$this->assign('max_upload_size', $max_upload_size, array('content', 'js'));
		$this->assign('max_upload_size_formatted', File::format_bytes($max_upload_size));

		$this->display
		(
			null,
			$song !== null ? 'Upload new version of "' . $song->get_title() . '"' : 'Upload Song(s)'
		);
	}

	public function upload_song_post()
	{
		$input = Input::all();

		$extension = $input['file']->getClientOriginalExtension();

		$original_filename = $input['file']->getClientOriginalName();

		// Database
		if ( filter_var($input['song_id'], FILTER_VALIDATE_INT) && $input['song_id'] > 0 ) // Upload new version
		{
			$song = Song::find($input['song_id']);
			$song->version++;
			$song->save();
		}
		else // Create new song
		{
			$song = new Song();
			$song->user_id = $this->user->id;
			$song->original_filename = $original_filename;
			$song->title = $original_filename;
			$song->slug = Str::slug($original_filename);
			$song->version = 1;
			$song->save();
		}

		// Create Song Upload
		$song_upload = new Song_Upload();
		$song_upload->song_id = $song->id;
		$song_upload->original_filename = $original_filename;
		$song_upload->title = $original_filename;
		$song_upload->version = $song->version;
		$song_upload->save();

		$filename_without_extension = $song->id;
		$filename = $filename_without_extension . '.' . $extension;

		// Create directory if not already exists
		$uploads_dir = $this->root_dir . '/public/uploads/' . $this->user->id . '/' . $song->id . '/' . ($song->version !== null ? 'v' . $song->version . '/' : '');

		if ( !file_exists($uploads_dir) )
		{
			mkdir($uploads_dir, 0775, true);
		}

		// Upload!
		$upload_success = $input['file']->move($uploads_dir, $filename);

		// If WAV, convert to MP3
		if ( $extension === 'wav' )
		{
			// Convert to MP3
			$convert_to_mp3_cmd = Config::get('audio.LAME') . ' -q0 -b128 "' . $uploads_dir . $filename  . '" "' . $filename_without_extension . '.mp3" ' . Config::get('audio.STDOUT');

			exec($convert_to_mp3_cmd, $result);
		}
		else // If anything else than WAV, like an MP3 for example, convert to WAV
		{
			// Convert to WAV
			$convert_to_wav_cmd = Config::get('audio.SOX') . ' ' . $uploads_dir . $filename . ' -c1 -r 8000 ' . $uploads_dir . $filename_without_extension . '.wav --norm ' . Config::get('audio.STDOUT');

			exec($convert_to_wav_cmd, $result);
		}

		$result = array
		(
			'error' => ''
		);

		if ( 1 === 1 )
		{
			//return Response::json('success', 200);
		}
		else
		{
			//return Response::json('error', 400);
		}

		//die(print_r('<pre>' . print_r($input['file'], TRUE) . '</pre>'));

		return Response::json($result);
	}

	public function delete_song_post()
	{
		$input = Input::all();

		$song_id = $input['song_id'];

		$ajax = new Ajax($this->ui);

		try
		{
			$song = Song::find($song_id)->firstOrFail();
		}
		catch ( Illuminate\Database\Eloquent\ModelNotFoundException $e )
		{
			$ajax->output_with_error('Could not find song.');
		}

		Song_Upload::where('song_id', $song->id)->delete();

		$song->delete();

		// Delete files
		Kyrst\Base\Helpers\File::remove_dir($song->get_dir());

		$ajax->output();
	}

	public function edit_song($song_slug)
	{
		try
		{
			$song = Song::where('slug', $song_slug)->firstOrFail();
		}
		catch ( Illuminate\Database\Eloquent\ModelNotFoundException $e )
		{
			$this->ui->add_error('Could not find song.');

			return Redirect::route('dashboard/my-songs');
		}

		if ( $this->is_ajax )
		{
			$ajax = new Ajax($this->ui);

			$input = Input::all();

			if ( count($input) > 0 )
			{
				$song->title = trim($input['title']);
				$song->save();

				$ajax->output();
			}
			else
			{
				$ajax->output_with_error('NO_POST');
			}
		}

		$this->assign('song', $song);

		$this->display
		(
			null,
			'Edit "' . $song->get_title() . '"'
		);
	}
}
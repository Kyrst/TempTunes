<?php
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

		$this->ajax->add_success('Settings successfully saved.');
		return $this->ajax->output();
	}

	public function upload_photo()
	{
		// Upload profile picture
		User::upload_photo(Input::file('photo'), $this->user->id);

		$this->user->photo = 'yes';
		$this->user->save();

		$this->ui->add_success('Photo successfully uploaded!');

		return Redirect::route('dashboard/settings');
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

	public function get_song_version()
	{
		$input = Input::all();

		$song_version_id = $input['song_version_id'];

		try
		{
			$song_version = song_version::where('id', $song_version_id)->firstOrFail();
		}
		catch ( Illuminate\Database\Eloquent\ModelNotFoundException $e )
		{
			return $this->ajax->output_with_error('Version not found.');
		}

		$this->ajax->add_data
		(
			'song_version',
			array
			(
				'title' => $song_version->title,
				'filename' => $song_version->get_filename(),
				'route' => $song_version->get_route(),
				'waveform_image' => $song_version->get_waveform_image('big'),
				'mp3_route' => $song_version->get_route('mp3'),
				'wav_route' => $song_version->get_route('wav')
			)
		);

		$this->ajax->add_data('player_html', $song_version->song->print_player(Song::PLAYER_SIZE_BIG, $song_version->id));

		return $this->ajax->output();
	}

	public function upload_songs($song_id = null)
	{
		$song = null;

		if ( $song_id !== null )
		{
			try
			{
				$song = Song::where('id', $song_id)->firstOrFail();
			}
			catch ( Illuminate\Database\Eloquent\ModelNotFoundException $e )
			{
				$this->ui->add_error('Could not find song.');

				return Redirect::route('dashboard');
			}
		}

		$this->assign('song', $song);
		$this->assign('current_song_id', $song !== null ? $song->id : 0, 'js');

		$max_upload_size = \Symfony\Component\HttpFoundation\File\UploadedFile::getMaxFilesize();

		$this->assign('max_upload_size', $max_upload_size, array('content', 'js'));
		$this->assign('max_upload_size_formatted', Kyrst\Base\Helpers\File::format_bytes($max_upload_size));

		if ( $song !== NULL )
		{
			$this->assign('num_current_song_versions', count($song->versions));
		}

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
		$original_filename_without_extension = basename($original_filename);

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
			$song->slug = Str::slug($original_filename_without_extension);
			$song->version = 1;
			$song->save();
		}

		// Create Song Upload
		$song_version = new song_version();
		$song_version->song_id = $song->id;
		$song_version->original_filename = $original_filename;
		$song_version->title = $original_filename;
		$song_version->version = $song->version;
		$song_version->save();

		$filename_without_extension = $song->id;
		$filename = $filename_without_extension . '.' . $extension;

		// Create directory if not already exists
		$uploads_dir = User::get_songs_dir($this->user->id, $song->id, $song->version);

		if ( !file_exists($uploads_dir) )
		{
			mkdir($uploads_dir, 0775, true);
		}

		// Upload!
		$move_result = $input['file']->move($uploads_dir, $filename);

		$wav_filepath = $uploads_dir . $filename;

		// If WAV, convert to MP3
		if ( $extension === 'wav' )
		{
			// Convert to MP3
			$convert_to_mp3_cmd = Config::get('audio.LAME') . ' -q0 -b128 "' . $wav_filepath  . '" "' . $filename_without_extension . '.mp3" ' . Config::get('audio.STDOUT');

			exec($convert_to_mp3_cmd, $result);
		}
		else // If anything else than WAV, like an MP3 for example, convert to WAV
		{
			$wav_filepath = $uploads_dir . $filename_without_extension . '.wav';

			// Convert to WAV
			$convert_to_wav_cmd = Config::get('audio.SOX') . ' ' . $uploads_dir . $filename . ' -c1 -r 8000 ' . $wav_filepath . ' --norm ' . Config::get('audio.STDOUT');

			exec($convert_to_wav_cmd, $result);
		}

		// Get BPM


		// Generate waveform
		$waveform_images_dir = $uploads_dir . 'waveform_images/';

		if ( !file_exists($waveform_images_dir) )
		{
			mkdir($waveform_images_dir, 0775, true);
		}

		$sizes = Config::get('audio.player_sizes');

		foreach ( $sizes as $size_name => $size )
		{
			$generate_waveform_cmd = Config::get('audio.WAVEFORM') . ' ' . $wav_filepath . ' ' . $waveform_images_dir . $size_name . '.png -W' . $size['width'] . ' -H' . $size['height'] . ' -b#FFFFFF -ctransparent ' . Config::get('audio.STDOUT');

			exec($generate_waveform_cmd, $result);
		}

		$result = array
		(
			'error' => ''
		);

		if ( 1 === 1 )
		{
			// Email
			/*$email_data = array
			(
				'user_name' => $this->user->get_display_name(),
				'title' => $original_filename
			);

			$share_people = array();

			$friends = $this->user->friends;

			foreach ( $friends as $user )
			{
				$share_people[] = array
				(
					'email' => $user->email,
					'name' => $user->get_display_name()
				);
			}

			Mail::send('emails.song_upload', $email_data, function($email_message, $share_people)
			{
				foreach ( $share_people as $user )
				{
					error_log('to: ' . $user['email']);
					$email_message->to($user['email'], $user['name'])->subject($this->user->get_display_name() . ' uploaded a new song');
				}
			});*/

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

		try
		{
			$song = Song::find($song_id)->firstOrFail();
		}
		catch ( Illuminate\Database\Eloquent\ModelNotFoundException $e )
		{
			return $this->ajax->output_with_error('Could not find song.');
		}

		song_version::where('song_id', $song->id)->delete();

		$song->delete();

		// Delete files
		Kyrst\Base\Helpers\File::remove_dir($song->get_dir());

		return $this->ajax->output();
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
			$input = Input::all();

			if ( count($input) > 0 )
			{
				$song->title = trim($input['title']);
				$song->description = trim($input['description']);
				$song->save();

				return $this->ajax->output();
			}
			else
			{
				return $this->ajax->output_with_error('NO_POST');
			}
		}

		$this->assign('song', $song);

		$this->display
		(
			null,
			'Edit "' . $song->get_title() . '"'
		);
	}

	public function delete_user_photo()
	{
		$this->user->delete_photo();

		$this->user->photo = 'no';
		$this->user->save();

		return $this->ajax->output();
	}
}
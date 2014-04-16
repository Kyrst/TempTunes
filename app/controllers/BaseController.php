<?php
use Kyrst\Base\Controllers\ApplicationController as ApplicationController;

class BaseController extends ApplicationController
{
	protected $current_song_cookie;

	protected $num_songs;
	protected $num_friends;

	function __construct()
	{
		parent::__construct();

		$this->libs['jquery.cookie'] = array
		(
			'js' => 'libs/jquery.cookie/jquery.cookie.js'
		);

		$this->libs['underscore'] = array
		(
			'js' => 'libs/underscore/underscore.js'
		);

		$this->libs['buzz'] = array
		(
			'js' => 'libs/buzz/buzz.min.js'
		);

		$this->libs['player_manager'] = array
		(
			'js' => 'js/player_manager/player_manager.js'
		);

		/*$this->add_lib
		(
			'PxLoader',
			NULL,
			array
			(
				'libs/pxloader/PxLoader.js',
				'libs/pxloader/PxLoaderWavesurferJS.js'
			)
		);*/

		$this->load_lib('jquery.cookie');
		$this->load_lib('underscore');
		$this->load_lib('buzz');
		$this->load_lib('player_manager');
		//$this->load_lib('PxLoader');
	}

	public function setupLayout($from_no_controller = false)
	{
		parent::setupLayout($from_no_controller);

		$this->add_css('css/global.css');

		// Volume
		if ( isset($_COOKIE['volume']) )
		{
			$volume = $_COOKIE['volume'];
		}
		else
		{
			$volume = Cookie::get('audio.DEFAULT_VOLUME');
		}

		$this->assign('volume', $volume, 'js');

		// Add header view if Home or Dashboard layout
		if ( in_array($this->current_controller, array('front', 'user', 'song', 'dashboard', 'admin')) )
		{
			if ( $this->user !== NULL )
			{
				$this->num_songs = $this->user->songs !== NULL ? $this->user->songs->count() : 0;
				$this->num_friends = $this->user->friends->count();
			}

			$header_view = View::make('layouts/partials/header');
			$header_view->current_page = $this->current_page;;
			$header_view->user = $this->user;
			$header_view->num_songs = $this->num_songs;
			$header_view->num_friends = $this->num_friends;

			$current_song = NULL;

			if ( $this->current_song_cookie !== NULL )
			{
				try
				{
					$current_song = Song_Version::where('id', $this->current_song_cookie['song_version_id'])->firstOrFail();
				}
				catch ( \Illuminate\Database\Eloquent\ModelNotFoundException $e )
				{
				}
			}

			$this->assign('current_song_cookie', $this->current_song_cookie, 'js');

			$header_view->header_player_html = View::make('partials/player/header')
				->with('volume', $volume)
				->with('current_song', $current_song)
				->render();

			$this->assign('header_html', $header_view->render(), 'layout');

			$this->add_css('css/layouts/partials/header.css');
		}
	}
}
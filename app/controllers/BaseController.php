<?php
use Kyrst\Base\Controllers\ApplicationController as ApplicationController;

class BaseController extends ApplicationController
{
	protected $num_songs;

	function __construct()
	{
		parent::__construct();

		$this->libs['underscore'] = array
		(
			'js' => 'libs/underscore/underscore.js'
		);

		$this->libs['wavesurfer'] = array
		(
			'js' => array
			(
				'libs/wavesurfer/src/wavesurfer.js',
				'libs/wavesurfer/src/webaudio.js',
				'libs/wavesurfer/src/drawer.js',
				'libs/wavesurfer/src/drawer.canvas.js'
			)
		);

		$this->libs['wavesurfer.timeline'] = array
		(
			'js' => 'libs/wavesurfer/plugin/wavesurfer.timeline.js'
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

		$this->load_lib('underscore');
		$this->load_lib('buzz');
		//$this->load_lib('wavesurfer');
		//$this->load_lib('wavesurfer.timeline');
		$this->load_lib('player_manager');
		//$this->load_lib('PxLoader');
	}

	public function setupLayout($from_no_controller = false)
	{
		parent::setupLayout($from_no_controller);

		$this->add_css('css/global.css');

		// Add header view if Home or Dashboard layout
		if ( in_array($this->current_controller, array('home', 'user', 'song', 'dashboard')) )
		{
			if ( $this->user !== NULL )
			{
				$this->num_songs = $this->user->songs !== NULL ? $this->user->songs->count() : 0;
			}

			$header_view = View::make('layouts/partials/header');
			$header_view->user = $this->user;
			$header_view->num_songs = $this->num_songs;
			$header_view->header_player_html = View::make('partials/player/header')->render();

			$this->assign('header_html', $header_view->render(), 'layout');

			$this->add_css('css/layouts/partials/header.css');
		}
	}
}
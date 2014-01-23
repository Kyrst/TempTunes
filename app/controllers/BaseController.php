<?php
use Kyrst\Base\Controllers\ApplicationController as ApplicationController;

class BaseController extends ApplicationController
{
	protected $num_songs;

	function __construct()
	{
		parent::__construct();

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
			'js' => array
			(
				'libs/wavesurfer/plugin/wavesurfer.timeline.js',
			)
		);

		$this->load_lib('wavesurfer');
		$this->load_lib('wavesurfer.timeline');
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
				$this->num_songs = $this->user->songs->count();
			}

			$header_view = View::make('layouts/partials/header');
			$header_view->user = $this->user;
			$header_view->num_songs = $this->num_songs;

			$this->assign('header_html', $header_view->render(), 'layout');

			$this->add_css('css/layouts/partials/header.css');
		}
	}
}
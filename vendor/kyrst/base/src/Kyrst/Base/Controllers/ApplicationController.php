<?php
namespace Kyrst\Base\Controllers;

use Kyrst\Base\Controllers\BaseController as BaseController;

class ApplicationController extends BaseController
{
	protected $libs = array
	(
		'normalize' => array
		(
			'css' => 'packages/kyrst/base/libs/normalize/normalize.css'
		),
		'jquery' => array
		(
			'js' => 'packages/kyrst/base/libs/jquery/jquery.js'
		),
		'jquery-ui' => array
		(
			'css' => 'packages/kyrst/base/libs/jquery-ui/css/flick/jquery-ui-1.10.4.custom.min.css',
			'js' => 'packages/kyrst/base/libs/jquery-ui/js/jquery-ui-1.10.4.custom.min.js'
		),
		'bootstrap' => array
		(
			'css' => 'packages/kyrst/base/libs/bootstrap/css/bootstrap.css',
			'js' => 'packages/kyrst/base/libs/bootstrap/js/bootstrap.js'
		),
		'bootbox' => array
		(
			'js' => 'packages/kyrst/base/libs/bootbox/bootbox.js'
		),
		'kyrst' => array
		(
			'css' => array
			(
				'packages/kyrst/base/css/global.css'
			),
			'js' => array
			(
				'packages/kyrst/base/js/ajax_request.js',
				'packages/kyrst/base/js/ajax.js',
				'packages/kyrst/base/js/ui.js',
				'packages/kyrst/base/js/helper.js',
				'packages/kyrst/base/js/helpers/time.js',
				'packages/kyrst/base/js/kyrst.js'
			)
		)
	);

	function __construct($jquery = true, $bootstrap = true)
	{
		//
		parent::__construct();

		if ( $jquery === true )
		{
			$this->load_lib('jquery');
			$this->load_lib('jquery-ui');
		}

		if ( $bootstrap === true )
		{
			$this->load_lib('bootstrap');
			$this->load_lib('bootbox');
		}
		else
		{
			$this->load_lib('normalize');
		}

		$this->load_lib('kyrst');
	}

	public function index()
	{
		die('wtf this should never happen :/');

		$this->display();
	}
}
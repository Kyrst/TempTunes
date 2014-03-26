<?php
return array
(
	'LAME' => '/usr/local/bin/lame',
	'SOX' => '/usr/local/bin/sox',
	'WAVEFORM' => '/usr/bin/waveform',
	'STDOUT' => '2>&1',

	'DEFAULT_VOLUME' => 50,

	'player_sizes' => array
	(
		'big' => array
		(
			'width' => 960,
			'height' => 96
		),
		'small' => array
		(
			'width' => 320,
			'height' => 32
		)
	)
);
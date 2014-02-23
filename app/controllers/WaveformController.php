<?php
class WaveformController extends BaseController
{
	public function render($user_id, $song_id, $version, $size)
	{
		$waveform_image_path = User::get_songs_dir($user_id, $song_id, $version) . 'waveform_images/' . $size . '.png';

		if ( !file_exists($waveform_image_path) )
		{
			die('FILE_NOT_EXISTS');
		}

		$image_data = file_get_contents($waveform_image_path);

		$response = Response::make($image_data);

		$response->header('Content-Type', 'image/png');

		return $response;
	}
}
<?php
class ImageController extends Controller
{
	public function user_photo($user_id, $size_name)
	{
		$size = User::get_size($size_name);

		$cache_dir = User::get_photo_cache_dir($user_id);

		if ( !file_exists($cache_dir) )
		{
			mkdir($cache_dir, 0755, true);
		}

		$image_cache_path = User::get_photo_cache_dir($user_id) . User::get_photo_cache_filename($user_id, $size_name);

		if ( User::photo_cache_exists($user_id, $size_name) )
		{
			$image = Image::make($image_cache_path);

			$response = $image->response();
		}
		else
		{
			$image_filename = User::get_photo_path($user_id);

			if ( !file_exists($image_filename) )
			{
				return $this->render_image_not_found($size);
			}

			$image = Image::make($image_filename);
			$image->resize($size['width'], $size['height']);
			$image->save($image_cache_path);

			$response = Response::make($image->encode('jpg'));
		}

		$response->header('Content-Type', 'image/jpg');

		return $response;
	}

	public function render_image_not_found($size)
	{
		$image_filename = public_path() . '/images/avatar_silhouette.png';

		$image = Image::make($image_filename);
		$image->resize($size['width'], $size['height']);

		$response = Response::make($image->encode('png'));

		$response->header('Content-Type', 'image/jpg');

		return $response;
	}
}
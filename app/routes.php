<?php
// Home
Route::get('/', array
(
	'uses' => 'HomeController@index',
	'as' => 'home'
));

// User Songs Page
Route::get('/users/{username}/songs', array
(
	'uses' => 'UserController@songs'
))->where(array('username', '[a-z0-9_\-]+'));

// User Songs Page
Route::get('/users/{username}/friends', array
(
	'uses' => 'UserController@friends'
))->where(array('username', '[a-z0-9_\-]+'));

// Song Page
Route::get('/users/{username}/songs/{song}', array
(
	'uses' => 'UserController@song'
))->where(array('username', '[a-z0-9_\-]+'), array('song', '[a-z0-9_\-]+'));

// User Profile Page
Route::get('/users/{username}', array
(
	'uses' => 'UserController@profile'
))->where(array('username', '[a-z0-9_\-]+'));

// Sign in (POST)
Route::post('/sign-in', array
(
	'uses' => 'HomeController@sign_in',
	'as' => 'sign-in'
));

// Sign up
Route::get('/sign-up', array
(
	'uses' => 'HomeController@sign_up',
	'as' => 'sign-up'
));

// Sign up (POST)
Route::post('/sign-up', array
(
	'uses' => 'HomeController@sign_up',
	'as' => 'sign-up'
));

// Log out
Route::get('/log-out', array
(
	'uses' => 'AjaxController@log_out',
	'as' => 'log-out'
));

// Dashboard
Route::get('/dashboard', array
(
	'uses' => 'DashboardController@dashboard',
	'as' => 'dashboard'
));

// Dashboard -> Settings
Route::get('/dashboard/settings', array
(
	'uses' => 'DashboardController@settings',
	'as' => 'dashboard/settings'
));

// Dashboard -> My Songs
Route::get('/dashboard/my-songs', array
(
	'uses' => 'DashboardController@my_songs',
	'as' => 'dashboard/my-songs'
));

// Dashboard -> My Songs -> Get Song Upload
Route::get('/dashboard/my-songs/get-song-version', array
(
	'uses' => 'DashboardController@get_song_version',
	'as' => 'dashboard/my-songs/get-song-version'
));

// Dashboard -> Edit Song
Route::get('/dashboard/edit-song/{song}', array
(
	'uses' => 'DashboardController@edit_song',
));

// Dashboard -> Edit Song (POST)
Route::post('/dashboard/edit-song/{song}', array
(
	'uses' => 'DashboardController@edit_song',
));

// Dashboard -> Upload Songs
Route::get('/dashboard/upload-songs/{song_id?}', array
(
	'uses' => 'DashboardController@upload_songs',
	'as' => 'dashboard/upload-songs'
))->where(array('song_id', '\d+'));

// Dashboard -> Upload Song (POST)
Route::post('/dashboard/upload-songs', array
(
	'uses' => 'DashboardController@upload_song_post',
));

// Dashboard -> Upload Song (POST)
Route::post('/dashboard/delete-song', array
(
	'uses' => 'DashboardController@delete_song_post',
));

Route::post('/dashboard/settings/save', array
(
	'uses' => 'DashboardController@save_settings',
	'as' => 'dashboard/settings/save'
));

Route::post('/dashboard/settings/upload-photo', array
(
	'uses' => 'DashboardController@upload_photo',
	'as' => 'dashboard/settings/upload-photo'
));

/*Route::get('/dashboard/get-song-version', array
(
	'uses' => 'DashboardController@get_song_version',
	'as' => 'dashboard/get_song_version'
));*/

// Save song comment (POST)
Route::post('/ajax/save-song-comment', array
(
	'uses' => 'AjaxController@save_song_comment',
	'as' => 'ajax/save-song-comment'
));

Route::get('/user-photo/{id}/{size_name}', array
(
	'uses' => 'ImageController@user_photo'
))->where(array('id', '\d+'), array('size_name', '[a-z0-9_\-]+'));

Route::post('/dashboard/delete-user-photo', array
(
	'uses' => 'DashboardController@delete_user_photo',
	'as' => 'dashboard/delete-user-photo'
));

Route::get('/play/{user_id}/{song_id}/v{version}/{wav?}', array
(
	'uses' => 'AudioController@play'
))->where(array('user_id', '\d+'), array('song_id', '\d+'), array('version', '\d+'), array('wav', '(1|0)'));

Route::get('/waveform/{user_id}/{song_id}/v{version}/{size}', array
(
	'uses' => 'WaveformController@render'
))->where(array('user_id', '\d+'), array('song_id', '\d+'), array('version', '\d+'), array('size', '(small|big)'));

// Log database queries
if ( Config::get('database.log', false) )
{
	Event::listen('illuminate.query', function($query, $bindings, $time, $name)
	{
		$data = compact('bindings', 'time', 'name');

		// Format binding data for sql insertion
		foreach ( $bindings as $i => $binding )
		{
			if ( $binding instanceof \DateTime )
			{
				$bindings[$i] = $binding->format('\'Y-m-d H:i:s\'');
			}
			else if ( is_string($binding) )
			{
				$bindings[$i] = "'$binding'";
			}
		}

		// Insert bindings into query
		$query = str_replace(array('%', '?'), array('%%', '%s'), $query);
		$query = vsprintf($query, $bindings);

		Log::info($query, $data);
	});
}
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
	'uses' => 'AjaxController@sign_in',
	'as' => 'sign-in'
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
Route::get('/dashboard/my-songs/get-song-upload', array
(
	'uses' => 'DashboardController@get_song_upload',
	'as' => 'dashboard/my-songs/get-song-upload'
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

/*Route::get('/dashboard/get-song-upload', array
(
	'uses' => 'DashboardController@get_song_upload',
	'as' => 'dashboard/get_song_upload'
));*/
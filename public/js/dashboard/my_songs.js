var wavesurfers = [],
	timelines = [],
	num_songs = 0;

$(function()
{
	$('#songs').find('.delete-song').on('click', function()
	{
		var $this = $(this),
			song_id = $this.data('song_id'),
			song_title = $this.data('song_title');

		$kyrst.ui.show_confirm
		(
			'Are you sure you want to delete song <strong>' + song_title + '</strong>?',
			function()
			{
				$kyrst.ajax.post
				(
					BASE_URL + 'dashboard/delete-song',
					{
						song_id: song_id
					},
					{
						success: function(result)
						{
							$('#song_' + song_id).fadeOut(function()
							{
								if ( last_song )
								{

								}
							});
						},
						error: function(error)
						{
							$kyrst.ui.show_alert(error);
						},
						complete: function(result)
						{
						}
					}
				);
			}
		);
	});

	// Play
	$('#songs').find('.play-button').on('click', function()
	{
		var song_id = $(this).data('song_id'),
			$play_button = document.querySelector('#play_button_' + song_id),
			$pause_button = document.querySelector('#pause_button_' + song_id),
			$stop_button = document.querySelector('#stop_button_' + song_id);

		$play_button.style.display = 'none';
		$pause_button.style.display = 'block';
		$stop_button.removeAttribute('disabled');

		wavesurfers[song_id].play();
	});

	// Pause
	$('#songs').find('.pause-button').on('click', function()
	{
		var song_id = $(this).data('song_id'),
			$play_button = document.querySelector('#play_button_' + song_id),
			$pause_button = document.querySelector('#pause_button_' + song_id);

		$pause_button.style.display = 'none';
		$play_button.style.display = 'block';

		wavesurfers[song_id].pause();
	});

	// Stop
	$('#songs').find('.stop-button').on('click', function()
	{
		var song_id = $(this).data('song_id'),
			$play_button = document.querySelector('#play_button_' + song_id),
			$pause_button = document.querySelector('#pause_button_' + song_id),
			$stop_button = document.querySelector('#stop_button_' + song_id);

		$pause_button.style.display = 'none';
		$play_button.style.display = 'block';

		$stop_button.setAttribute('disabled');

		wavesurfers[song_id].stop();
	});

	// Change Version
	$('#songs').find('.change-version').on('click', function()
	{
		var $this = $(this),
			song_id = $this.data('song_id'),
			song_upload_id = $this.data('song_upload_id');
			$play_button = document.querySelector('#play_button_' + song_id),
			$pause_button = document.querySelector('#pause_button_' + song_id),
			$stop_button = document.querySelector('#stop_button_' + song_id);

		$kyrst.ajax.get
		(
			BASE_URL + 'dashboard/my-songs/get-song-upload',
			{
				song_upload_id: song_upload_id
			},
			{
				success: function(result)
				{
					$('#wave_timeline_' + song_id).html('');

					$play_button.style.display = 'block';
					$pause_button.style.display = 'none';
					$stop_button.style.display = 'block';
					$stop_button.setAttribute('disabled');

					wavesurfers[song_id].load(result.data.song_upload.filename);

					$('#song_versions_list_' + song_id + ' li.active').removeClass('active');
					$('#song_upload_version_' + song_upload_id).addClass('active');
					$('#song_version_downdown_button_' + song_id).html($('#song_upload_version_' + song_upload_id + ' a').html() + ' <span class="caret"></span>');
				},
				error: function(error)
				{
				},
				complete: function(result)
				{
				}
			}
		);
	});

	num_songs = js_songs.length;

	var wavesurfer_options =
	{
		height			: 96,
		waveColor		: '#428BCA',
		progressColor	: '#2D6CA2',
		loaderColor		: '#FFF',
		cursorColor		: 'navy',
		cursorWidth		: 0,
		markerWidth		: 2,
		normalize		: false
	};

	//wavesurfer_options.minPxPerSec = 100;
	//wavesurfer_options.scrollParent = true;

	for ( var i = 0; i < num_songs; i++ )
	{
		var song = js_songs[i],
			song_id = song.id,
			song_version = song.version;

		wavesurfer_options.container = document.querySelector('#waveform_' + song_id);

		wavesurfers[song_id] = Object.create(WaveSurfer);

		(function(song_id)
		{
			// Progress bar
			var $song_buttons_container = document.querySelector('#song_buttons_container_' + song_id),
				$song_error_container = document.querySelector('#song_error_container_' + song_id),
				$waveform_container = document.querySelector('#waveform_container_' + song_id),
				$play_button = document.querySelector('#play_button_' + song_id),
				$pause_button = document.querySelector('#pause_button_' + song_id),
				$stop_button = document.querySelector('#stop_button_' + song_id);

			var progressDiv = document.querySelector('#progress_bar_' + song_id);
			var progressBar = progressDiv.querySelector('.progress-bar');

			wavesurfers[song_id].on('loading', function(percent)
			{
				progressDiv.style.display = 'block';
				progressBar.style.width = percent + '%';
			});

			wavesurfers[song_id].on('ready', function()
			{
				timelines[song_id] = Object.create(WaveSurfer.Timeline);

				timelines[song_id].init(
				{
					wavesurfer: wavesurfers[song_id],
					container: "#wave_timeline_" + song_id,
					primaryColor: '#C0C0C0'
				});

				progressDiv.style.display = 'none';
				//$song_buttons_container.style.display = 'block';

				$play_button.removeAttribute('disabled');

				//$('#waveform_' + song_id + ' wave').style('height', wavesurfer_options.height + 'px', 'important');
			});

			wavesurfers[song_id].on('play', function(percent)
			{
				//$play_button.style.display = 'none';
				//$pause_button.style.display = 'block';
			});

			wavesurfers[song_id].on('finish', function(percent)
			{
				$play_button.style.display = 'block';
				$stop_button.setAttribute('disabled');
			});

			wavesurfers[song_id].on('destroy', function()
			{
				progressDiv.style.display = 'none';
			});

			wavesurfers[song_id].on('error', function(error)
			{
				$waveform_container.style.display = 'none';
				progressDiv.style.display = 'none';
				progressBar.style.display = 'none';

				$song_error_container.innerHTML = error + ' <a href="javascript:" id="retry_' + song_id + '" data-song_id="' + song_id + '" class="retry btn btn-xs btn-primary">Retry</a>';
				$song_error_container.style.display = 'block';

				// Retry
				$('#retry_' + song_id).on('click', function()
				{
					var $this = $(this),
						song_id = $this.data('song_id');

					$this.text('Retrying...').addClass('disabled').prop('disabled', true);

					wavesurfers[song_id].load(wavesurfers[song_id].filename);
				});
			});
		}(song_id));

		// Init
		wavesurfers[song_id].init(wavesurfer_options);

		// Load audio from URL
		wavesurfers[song_id].filename = BASE_URL + 'uploads/2/' + song_id + '/v' + song_version + '/' + song_id + '.mp3';
		wavesurfers[song_id].load(BASE_URL + 'uploads/2/' + song_id + '/v' + song_version + '/' + song_id + '.mp3');

		/*(function () {
			var eventHandlers = {
				'play': function () {
					wavesurfer.playPause();
				},

				'green-mark': function () {
					wavesurfer.mark({
						id: 'up',
						color: 'rgba(0, 255, 0, 0.5)'
					});
				},

				'red-mark': function () {
					wavesurfer.mark({
						id: 'down',
						color: 'rgba(255, 0, 0, 0.5)'
					});
				},

				'back': function () {
					wavesurfer.skipBackward();
				},

				'forth': function () {
					wavesurfer.skipForward();
				},

				'toggle-mute': function () {
					wavesurfer.toggleMute();
				}
			};

			document.addEventListener('keydown', function (e) {
				var map = {
					32: 'play',       // space
					38: 'green-mark', // up
					40: 'red-mark',   // down
					37: 'back',       // left
					39: 'forth'       // right
				};
				if (e.keyCode in map) {
					var handler = eventHandlers[map[e.keyCode]];
					e.preventDefault();
					handler && handler(e);
				}
			});

			document.addEventListener('click', function (e) {
				var action = e.target.dataset && e.target.dataset.action;
				if (action && action in eventHandlers) {
					eventHandlers[action](e);
				}
			});
		}());

	// Flash mark when it's played over
		wavesurfer.on('mark', function (marker) {
			if (marker.timer) { return; }

			marker.timer = setTimeout(function () {
				var origColor = marker.color;
				marker.update({ color: 'yellow' });

				setTimeout(function () {
					marker.update({ color: origColor });
					delete marker.timer;
				}, 100);
			}, 100);
		});

		wavesurfer.on('error', function (err) {
			console.error(err);
		});*/
	}
});
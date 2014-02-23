function PlayerManager() {};

PlayerManager.prototype =
{
	DEFAULT_ADD_COMMENT_SECONDS_TO_ADD: 10,

	window: null,
	buzz: null,

	players: [],
	current_player_id: null,

	init: function(buzz)
	{
		this.window = window;
		this.buzz = buzz;

		this.binds();
	},

	after_dom_init: function()
	{
	},

	binds: function()
	{
		var inst = this;

		// Look for players
		$('.player').each(function(i, $element)
		{
			var $this = $(this),
				size = $this.data('size'),
				song_id = $this.data('song_id'),
				song_version_id = $this.data('song_version_id'),
				identifier = $this.data('identifier'),
				filename = $this.data('filename'),
				mp3_route = $this.data('mp3_route'),
				wav_route = $this.data('wav_route'),
				waveform_image = $this.data('waveform_image');

			if ( DEBUG )
			{
				//console.log('~ Player #' + (i + 1) + ' ~');
			}

			/*var is_missing_attr = false;

			if ( $kyrst.is_undefined(size) )
			{
				console.log('Missing "size".');

				is_missing_attr = true;
			}

			if ( $kyrst.is_undefined(song_id) )
			{
				console.log('Missing "song_id".');

				is_missing_attr = true;
			}

			if ( $kyrst.is_undefined(song_version_id) )
			{
				console.log('Missing "song_version_id".');

				is_missing_attr = true;
			}

			if ( $kyrst.is_undefined(identifier) )
			{
				console.log('Missing "identifier".');

				is_missing_attr = true;
			}

			if ( $kyrst.is_undefined(filename) )
			{
				console.log('Missing "filename".');

				is_missing_attr = true;
			}

			if ( $kyrst.is_undefined(mp3_route) )
			{
				console.log('Missing "mp3_route".');

				is_missing_attr = true;
			}

			if ( $kyrst.is_undefined(wav_route) )
			{
				console.log('Missing "wav_route".');

				is_missing_attr = true;
			}

			if ( $kyrst.is_undefined(waveform_image) )
			{
				console.log('Missing "waveform_image".');

				is_missing_attr = true;
			}

			if ( is_missing_attr )
			{
				console.log('----------------------------------------------------------');

				return;
			}*/

			if ( DEBUG )
			{
				//console.log('Selector: #' + $element.id + '\nSize: ' + size);
				//Song ID: ' + song_id + '\nSong Version ID: ' + song_version_id + '\nFilename: ' + filename + '\nMP3 Route: ' + mp3_route + '\nWAV Route: ' + wav_route + '\n----------------------------------------------------------');
			}

			if ( $kyrst.is_defined(song_id) )
			{
				if ( !inst.buzz.isMP3Supported() && !inst.buzz.isWAVSupported() ) // Nor WAV or MP3 are supported
				{
					alert('No support!');

					return;
				}
				else if ( !inst.buzz.isMP3Supported() ) // Only WAV is supported
				{
					var sound = new inst.buzz.sound(wav_route);
				}
				else // Only MP3 is supported
				{
					//console.log(mp3_route);
					var sound = new inst.buzz.sound(mp3_route);
				}

				//sound.setVolume(0);

				// Binds
				var $waveform_container = $('#player_waveform_container_' + identifier),
					$waveform_background = $('#player_waveform_background_' + identifier),
					$waveform = $('#player_waveform_' + identifier),
					$loading_container = $('#player_loading_container_' + identifier),
					$progress_container = $('#player_progress_container_' + identifier),
					$progress_bar_time = $('#progress_bar_time_' + identifier),
					$play_button = $('#player_play_button_' + identifier),
					$pause_button = $('#player_pause_button_' + identifier),
					$stop_button = $('#player_stop_button_' + identifier),
					$open_add_comment_bubble_button = $('#player_open_add_comment_bubble_button_' + identifier);

				// Performance variables
				var waveform_container_width = $waveform_container.width();

				// Events
				(function(sound, $waveform_container, $waveform_background, $waveform, $loading_container, $progress_container, $progress_bar_time, $play_button)
				{
					var duration_in_seconds, duration_in_px;

					sound.bind('loadstart', function(e)
					{
						//document.getElementById('loading').style.display = 'block';
					}).bind('loadeddata', function(e)
					{
						//console.log(e);
						//document.getElementById('loading').style.display = 'none';
					}).bind('loadedmetadata', function(e)
					{
						duration_in_seconds = this.getDuration();
						duration_in_px = position_in_seconds_to_px(duration_in_seconds);
					}).bind('error', function(e)
					{
						alert('Error: ' + this.getErrorMessage());
					}).bind('timeupdate', function(e)
					{
						if ( !this.isPaused() ) // To avoid callback on load
						{
							var position_in_seconds = this.getTime(),
								time_in_seconds = inst.buzz.toTimer(position_in_seconds),
								percent = position_in_seconds / duration_in_seconds,
								position_in_px = (percent * waveform_container_width);

							$progress_container.width(position_in_px);

							$progress_bar_time
								.html(time_in_seconds)
								.css('left', (position_in_px + 1) + 'px');
						}
					}).bind('progress', function(e) // Loading progress
					{
						//console.log(e);
					}).bind('canplay', function(e)
					{
						//console.log(e);
					}).bind('emptied', function(e)
					{
						$waveform.show();
						$waveform_container.addClass('loaded');
						$waveform_background.delay(350).show(0, function()
						{
							$loading_container.hide();
							$play_button.removeClass('disabled');
							$open_add_comment_bubble_button.removeClass('disabled');

							$waveform_background.on('click', function(e)
							{
								var position_in_px = e.pageX - $(this).offset().left,
									position_in_seconds = position_in_px_to_seconds(position_in_px);

								sound.setTime(position_in_seconds);

								if ( sound.isPaused() )
								{
									$progress_bar_time.show();
									//$progress_bar_time... TO-DO
									$progress_container.width(position_in_px);
								}
							});
						});
					}).bind('playing', function(e)
					{
					});

					$play_button.on('click', function()
					{
						$play_button.hide();
						$pause_button.show();
						$stop_button.removeClass('disabled');
						$progress_bar_time.show();

						sound.play();
					});

					function position_in_seconds_to_px(position_in_seconds)
					{
						return (position_in_seconds / duration_in_seconds) * waveform_container_width;
					}

					function position_in_px_to_seconds(position_in_px)
					{
						var ratio = duration_in_px / duration_in_seconds;

						return (position_in_px / (duration_in_px * ratio)) * waveform_container_width;
					}
				}(sound, $waveform_container, $waveform_background, $waveform, $loading_container, $progress_container, $progress_bar_time, $play_button));

				// Load!
				sound.load();
			}
			else
			{
				var sound = null;
			}

			inst.players[identifier] =
			{
				sound: sound
			};
		});
	}
};
function PlayerManager() {};

PlayerManager.prototype =
{
	DEFAULT_ADD_COMMENT_SECONDS_TO_ADD: 10,

	window: null,
	buzz: null,

	players: [],
	current_player_id: null,

	volume: null,

	init: function(buzz)
	{
		this.window = window;
		this.buzz = buzz;

		if ( typeof volume !== 'undefined' )
		{
			$('#volume_control').val(volume);
		}

		this.volume = volume;

		this.binds();
	},

	after_dom_init: function()
	{
	},

	binds: function()
	{
		var inst = this,
			detected_player_size = null;

		// Look for players
		$('.player').each(function(i, $element)
		{
			var identifier = $(this).data('identifier'),
				song_version_id = $(this).data('song_version_id'),
				size = $(this).data('size');

			detected_player_size = size;

			// If song_page player, change tab to current player when initializing since it can't figure out the sizes otherwise
			if ( size === 'song_page' )
			{
				$('#versions_tab').find('a[href="#tab_' + song_version_id + '"]').tab('show');
			}

			init_player($(this));
		});

		// If song_player player, go back to first tab when done
		if ( detected_player_size === 'song_page' )
		{
			$('#versions_tab').find('a:first').tab('show');
		}

		function init_player($player)
		{
			var size = $player.data('size'),
				song_id = $player.data('song_id'),
				song_version_id = $player.data('song_version_id'),
				identifier = $player.data('identifier'),
				filename = $player.data('filename'),
				mp3_route = $player.data('mp3_route'),
				wav_route = $player.data('wav_route'),
				title = $player.data('title'),
				waveform_image = $player.data('waveform_image'),
				id = song_id + '_' + song_version_id,
				is_dragging_start_marker = false,
				is_dragging_end_marker = false;

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

			function load_sound(mp3_route, wav_route)
			{
				var sound = null;

				if ( !inst.buzz.isMP3Supported() && !inst.buzz.isWAVSupported() ) // Nor WAV or MP3 are supported
				{
					alert('No support!');

					return null;
				}
				else if ( !inst.buzz.isMP3Supported() ) // Only WAV is supported
				{
					sound = new inst.buzz.sound(wav_route);
				}
				else // Only MP3 is supported
				{
					//console.log(mp3_route);
					sound = new inst.buzz.sound(mp3_route);
				}

				return sound;
			}

			if ( $kyrst.is_defined(song_id) )
			{
				sound = load_sound(mp3_route, wav_route);

				sound.setVolume(inst.volume);

				// Binds
				var $waveform_container = $('#player_waveform_container_' + identifier),
					$waveform_background = $('#player_waveform_background_' + identifier),
					$waveform = $('#player_waveform_' + identifier),
					$loading_container = $('#player_loading_container_' + identifier),
					$progress_container = $('#player_progress_container_' + identifier),
					$progress_bar_time = $('#progress_bar_time_' + identifier),
					$duration_time_bar = $('#duration_time_bar_' + identifier),
					$play_button = $('#player_play_button_' + identifier),
					$pause_button = $('#player_pause_button_' + identifier),
					$stop_button = $('#player_stop_button_' + identifier),
					$open_add_comment_bubble_button = $('#player_open_add_comment_bubble_button_' + identifier),
					$add_comment_bubble = $('#add_comment_bubble_' + identifier),
					$add_comment_marker_container = $('#add_comment_marker_container_' + identifier),
					$add_comment_start_marker = $('#add_comment_start_marker_' + identifier),
					$add_comment_end_marker = $('#add_comment_end_marker_' + identifier),
					$add_comment_from_value = $('#add_comment_from_value_' + identifier),
					$add_comment_to_value = $('#add_comment_to_value_' + identifier),
					$add_comment_value = $('#add_comment_value_' + identifier),
					$add_comment_button = $('#add_comment_button_' + identifier),
					$add_comment_to_value_checkbox = $('#add_comment_to_value_checkbox_' + identifier),
					$comments_container = $('#comments_container_' + identifier);

				// Performance variables
				var waveform_container_width = $waveform_container.width();

				// Events
				(function(sound, id)
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

						$duration_time_bar.html(inst.buzz.toTimer(duration_in_seconds)).show();

						// Position comments and show them
						$comments_container.find('.comment').each(function(i, element)
						{
							var $comment = $(element),
								id = $comment.data('comment_id'),
								$comment_data = $('#comment_data_' + id),
								start_position_in_seconds = $comment.data('from_seconds'),
								end_position_in_seconds = $comment.data('to_seconds');

							var start_position_in_px = position_in_seconds_to_px(start_position_in_seconds);

							if ( $kyrst.is_defined(end_position_in_seconds) )
							{
								$('#comment_hover_' + id).css('width', Math.round(position_in_seconds_to_px(end_position_in_seconds) - start_position_in_px) + 'px');
							}

							$comment.css('left', start_position_in_px);
							$comment_data.css('left', start_position_in_px + $comment.width());

							comment_bind($comment);
						});

						$comments_container.show();
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

							set_progress_bar_time(time_in_seconds, position_in_px);
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
						//$waveform_background.delay(350).show(0, function()
						$waveform_background.show(function()
						{
							$loading_container.hide();
							$play_button.removeClass('disabled');
							$open_add_comment_bubble_button.removeClass('disabled');

							$waveform_container.on('mousemove', function(e)
							{
								mouse_move_func(e, $(this));
							}).addClass('done');

							$waveform_background.addClass('loaded');

							$(inst.window).on('mouseup', function()
							{
								is_dragging_start_marker = false;
								is_dragging_end_marker = false;
							});

							$waveform_background.on('click', function(e)
							{
								var position_in_px = e.pageX - $(this).offset().left,
									position_in_seconds = position_in_px_to_seconds(position_in_px),
									time_in_seconds = inst.buzz.toTimer(position_in_seconds);

								change_player(id);

								sound.setTime(position_in_seconds);

								if ( sound.isPaused() )
								{
									$progress_bar_time.show();
									set_progress_bar_time(time_in_seconds, position_in_px);
									$progress_container.width(position_in_px);
								}
							});
						});
					}).bind('playing', function(e)
					{
					});

					// Play
					$play_button.on('click', function()
					{
						inst.pause_current_player();

						change_player(id);

						play();
					});

					// Pause
					$pause_button.on('click', function()
					{
						inst.pause_current_player();
					});

					// Stop
					$stop_button.on('click', function()
					{
						stop();
					});

					function play()
					{
						$play_button.hide();
						$pause_button.show();
						$stop_button.removeClass('disabled');
						$progress_bar_time.show();

						var current_player = get_current_player();
						current_player.sound.play();
					}

					function stop()
					{
						$pause_button.hide();
						$play_button.show();
						$stop_button.addClass('disabled');
						$progress_bar_time.hide().html('00:00').css('left', 0);

						$progress_container.width(0);

						sound.stop();

						inst.current_player_id = null;
					}

					// Open Add Comment bubble
					$open_add_comment_bubble_button.on('click', function()
					{
						change_player(id);

						open_add_comment_bubble();
					});

					// Add comment
					$add_comment_button.on('click', function()
					{
						save_comment(song_version_id);

						close_add_comment_bubble();
					});

					// Toggle "From" input
					$add_comment_to_value_checkbox.on('click', function()
					{
						if ( $add_comment_to_value.is(':disabled') ) // "To" checkbox clicked
						{
							var current_player = get_current_player(),
								position_in_seconds = current_player.sound.getTime(),
								to_value_in_seconds = position_in_seconds + inst.DEFAULT_ADD_COMMENT_SECONDS_TO_ADD;

							current_player.marker_end_position_in_px = position_in_seconds_to_px(to_value_in_seconds);

							$add_comment_to_value.prop('disabled', false).val(inst.buzz.toTimer(to_value_in_seconds)).focus();

							$add_comment_marker_container.on('mousemove', function(e)
							{
								mouse_move_func(e, $(this));
							}).css('width', (current_player.marker_end_position_in_px - current_player.marker_start_position_in_px) + 'px');

							$add_comment_end_marker.on('mousedown', function()
							{
								is_dragging_end_marker = true;
							}).show();
						}
						else
						{
							$add_comment_to_value.prop('disabled', true).val('');
							$add_comment_end_marker.hide();
							$add_comment_marker_container.css('width', '0');
						}
					});

					$add_comment_value.on('keypress', function(e)
					{
						if ( event.keyCode === 13 && event.ctrlKey )
						{
							close_add_comment_bubble();
						}
					});

					function comment_bind($comment)
					{
						var comment_id = $comment.data('comment_id');

						$('#comment_data_' + comment_id).on('mouseover', function()
						{
						}).on('mouseout', function()
						{
						});

						// Comment bind
						$comment.on('mouseover', function()
						{
							comment_hover_bind($(this).data('comment_id'));
						}).on('mouseout', function()
						{
							comment_hover_bind($(this).data('comment_id'));
						}).on('click', function()
						{
							var position_in_px = $(this).position().left,
								position_in_seconds = position_in_px_to_seconds(position_in_px);

							change_player(id);

							var current_player = get_current_player();
							current_player.sound.setTime(position_in_seconds);

							set_progress_bar_time(inst.buzz.toTimer(current_player.sound.getTime()), position_in_px);
							$progress_container.width(position_in_px);
						});
					}

					function save_comment(id)
					{
						var current_player = get_current_player(),
							from_position_in_seconds = position_in_px_to_seconds(current_player.marker_start_position_in_px),
							to_position_in_seconds = $kyrst.is_defined(current_player.marker_end_position_in_px) ? position_in_px_to_seconds(current_player.marker_end_position_in_px) : null;

						$kyrst.ajax.post
						(
							BASE_URL + 'ajax/save-song-comment',
							{
								song_version_id: id,
								comment: $add_comment_value.val(),
								from_seconds: from_position_in_seconds,
								to_seconds: to_position_in_seconds
							},
							{
								success: function(result)
								{
									add_comment_markup(result.data.comment_id, result.data.comment_hover_html, from_position_in_seconds, to_position_in_seconds, result.data.user_photo_url);
								},
								error: function(error)
								{
								},
								complete: function()
								{
								}
							}
						);
					}

					function add_comment_markup(id, comment, from_seconds, to_seconds, user_photo_url, comment_hover_html)
					{
						var position_in_px = position_in_seconds_to_px(from_seconds);

						var $comment = document.createElement('div');
						$comment.id = 'comment_' + id;
						$comment.style.left = position_in_px + 'px';
						$comment.setAttribute('data-comment_id', id);
						$comment.setAttribute('data-from_seconds', from_seconds);
						$comment.setAttribute('data-to_seconds', to_seconds !== null ? to_seconds : '');
						$comment.style.background = '#FFF url(' + user_photo_url + ')';
						$comment.className = 'comment';

						var $comment_data = document.createElement('div');
						$comment_data.id = 'comment_data_' + id;

						var user_photo_width = 20; // Make dynamic

						$comment_data.style.left = (position_in_px + user_photo_width) + 'px';
						$comment_data.className = 'comment-data';
						$comment_data.innerHTML = comment;

						var $comment_hover = document.createElement('div');
						$comment_hover.id = 'comment_hover_' + id;
						$comment_hover.className = 'comment-hover';
						$comment_hover.style.width = (to_seconds !== null ? position_in_seconds_to_px(to_seconds) - position_in_seconds_to_px(from_seconds) : 0) + 'px';
						$comment_hover.setAttribute('is_open', 'no');

						$comment.appendChild($comment_hover);

						$comments_container.append($comment);
						$comments_container.append($comment_data);

						comment_bind($('#' + $comment.id));
					}

					function mouse_move_func(e, $parent)
					{
						if ( is_dragging_start_marker || is_dragging_end_marker )
						{
							var current_player = get_current_player(),
								parent_offset = $parent.parent().offset(),
								position_in_px = e.pageX - parent_offset.left;

							if ( is_dragging_start_marker )
							{
								current_player.marker_start_position_in_px = position_in_px;
							}
							else if ( is_dragging_end_marker )
							{
								current_player.marker_end_position_in_px = position_in_px;
							}

							update_comment_marker_container_size();
						}
					}

					function close_add_comment_bubble()
					{
						$add_comment_bubble.hide();
						$add_comment_marker_container.hide();
					}

					function position_in_seconds_to_px(position_in_seconds)
					{
						return (position_in_seconds / duration_in_seconds) * waveform_container_width;
					}

					function position_in_px_to_seconds(position_in_px)
					{
						var ratio = duration_in_px / duration_in_seconds;

						return (position_in_px / (duration_in_px * ratio)) * waveform_container_width;
					}

					function set_progress_bar_time(time_in_seconds, position_in_px)
					{
						var progress_bar_time_width = $progress_bar_time.outerWidth(),
							left_position;

						if ( position_in_px + progress_bar_time_width > $waveform_background.outerWidth() - $duration_time_bar.outerWidth() )
						{
							// Inversed
							left_position = $waveform_background.outerWidth() - $duration_time_bar.outerWidth() - progress_bar_time_width;

							// Alternative:
							// left_position = (position_in_px - progress_bar_time_width);
						}
						else
						{
							left_position = (position_in_px + 1);
						}

						$progress_bar_time.html(time_in_seconds).css('left', left_position + 'px');

						$('#header_player_time').html('(' + time_in_seconds + ')');
					}

					function change_player(id)
					{
						inst.current_player_id = id;

						$('#header_player_title').html(title);
					}

					function open_add_comment_bubble()
					{
						var current_player = get_current_player(),
							position_in_seconds = current_player.sound.getTime(),
							position_in_px = position_in_seconds_to_px(position_in_seconds);

						current_player.marker_start_position_in_px = position_in_px;

						$add_comment_bubble.show().find('.value').focus();

						update_comment_marker_container_size();

						$add_comment_from_value.val(inst.buzz.toTimer(position_in_seconds)).data('value', position_in_seconds);

						$add_comment_start_marker.on('mousedown', function()
						{
							is_dragging_start_marker = true;
						}).show();

						is_add_comment_bubble_open = true;
					}

					function update_comment_marker_container_size()
					{
						var current_player = get_current_player();

						$add_comment_marker_container.css('left', (current_player.marker_start_position_in_px - 1) + 'px').show();
						$add_comment_marker_container.css('width', (current_player.marker_end_position_in_px - current_player.marker_start_position_in_px) + 'px').show();

						var from_position_in_seconds = position_in_px_to_seconds(current_player.marker_start_position_in_px),
							to_position_in_seconds = position_in_px_to_seconds(current_player.marker_end_position_in_px);

						$add_comment_from_value.val(inst.buzz.toTimer(from_position_in_seconds)).data('value', from_position_in_seconds);
						$add_comment_to_value.val(inst.buzz.toTimer(to_position_in_seconds)).data('value', to_position_in_seconds);
					}

					function comment_hover_bind(comment_id)
					{
						var $comment_hover_element = $('#comment_hover_' + comment_id),
							is_open = ($comment_hover_element.data('is_open') === 'yes'),
							$comment_data = $('#comment_data_' + comment_id);

						if ( is_open )
						{
							$comment_hover_element.data('is_open', 'no').hide();
							$comment_data.hide();
						}
						else
						{
							$comment_hover_element.data('is_open', 'yes').show();
							$comment_data.show();
						}
					}

					function get_current_player()
					{
						return inst.players[inst.current_player_id];
					}

					// Change Version

					$player.find('.change-version').on('click', function()
					{
						var $this = $(this),
							_song_id = $this.data('song_id'),
							_song_version_id = $this.data('song_version_id'),
							new_id = _song_id + '_' + _song_version_id;

						stop();

						$waveform_background.removeClass('loaded');

						$kyrst.ajax.get
						(
							BASE_URL + 'dashboard/my-songs/get-song-version',
							{
								song_version_id: _song_version_id
							},
							{
								success: function(result)
								{
									var song_version = result.data.song_version;

									$('#player_' + identifier).replaceWith(result.data.player_html);

									init_player($('#player_' + new_id));

									/*inst.players[new_id] =
									 {
									 sound: load_sound(song_version.mp3_route, song_version.wav_route)
									 };

									 change_player(new_id);

									 $play_button.attr('data-id', new_id);

									 var waveform_image = new Image();
									 waveform_image.src = song_version.waveform_image;

									 $waveform.css('background-image', 'url(' + song_version.waveform_image + ')');

									 waveform_image.onload = function()
									 {
									 $waveform_background.addClass('loaded');
									 }*/

									 $('#song_versions_list_' + song_id + ' li.active').removeClass('active');
									 $('#song_version_version_' + _song_version_id).addClass('active');
									 $('#song_version_downdown_button_' + song_id).html($('#song_version_version_' + _song_version_id + ' a').html() + ' <span class="caret"></span>');
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
				}(sound, id));

				// Load!
				sound.load();
			}
			else
			{
				sound = null;

				// Header player
				if ( size === 'header' )
				{
				}
			}

			inst.players[identifier] =
			{
				sound: sound
			};
		}

		// Volume control
		$('#volume_control').on('change', function()
		{
			inst.volume = this.value;

			for ( var identifier in inst.players )
			{
				var player = inst.players[identifier];

				if ( player.sound === null )
				{
					continue;
				}

				player.sound.setVolume(inst.volume);
			}
		});
	},

	// Pause current player
	pause_current_player: function()
	{
		if ( this.current_player_id === null )
		{
			return;
		}

		this.players[this.current_player_id].sound.pause();

		$('#player_pause_button_' + this.current_player_id).hide();
		$('#player_play_button_' + this.current_player_id).show();
	},

	onbeforeunload: function()
	{
		// Save volume
		$.cookie('volume', this.volume, { path: '/' });
	}
};
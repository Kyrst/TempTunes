function PlayerManager() {};

PlayerManager.prototype =
{
	DEFAULT_ADD_COMMENT_SECONDS_TO_ADD: 10,

	window: null,

	bright_gray: '#5B5B5B',
	dark_gray: '#414141',

	default_wavesurfer_options:
	{
		height						: 96,
		waveColor					: '#5B5B5B',
		waveStartGradientColor		: '#565656',
		waveEndGradientColor		: '#363636',
		progressColor				: '#428BCA',
		progressStartGradientColor	: '#619FD4',
		progressEndGradientColor	: '#428BCA',
		loaderColor					: '#FFF',
		cursorColor					: 'navy',
		cursorWidth					: 0,
		markerWidth					: 2,
		normalize					: false
	},

	players: [],
	current_player_id: null,

	init: function(loader)
	{
		this.window = window;

		this.binds();
	},

	after_dom_init: function()
	{
	},

	binds: function()
	{
		var inst = this,
			wavesurfer_options = inst.default_wavesurfer_options;

		// Look for players
		$('.player').each(function(i, $element)
		{
			var $this = $(this),
				size = $this.data('size'),
				song_id = $this.data('song_id'),
				song_version_id = $this.data('song_version_id'),
				filename = $this.data('filename');

			var id = song_id + '_' + song_version_id;

			// Waveform
			wavesurfer_options.container = document.querySelector('#player_waveform_' + id);

			if ( size === 'big' )
			{
				wavesurfer_options.height = 96;
			}

			var wavesurfer = Object.create(WaveSurfer);
			wavesurfer.init(wavesurfer_options);
			wavesurfer.filename = filename;
			wavesurfer.load(filename);

			inst.players[id] =
			{
				song_id: song_id,
				song_version_id: song_version_id,
				wavesurfer: wavesurfer
			};

			(function(id, song_id, song_version_id)
			{
				// Progress bar
				var $player = $('#player_' + id),
					$player_controls_container = $('#player_controls_container_' + id),
					$player_error_container = document.querySelector('#player_error_container_' + id),
					$waveform_container = $('#player_waveform_container_' + id),
					//$player_waveform = $('#player_waveform_' + id),
					$progress_bar_time = $('#progress_bar_time_' + id),
					$play_button = $('#player_play_button_' + id),
					$pause_button = $('#player_pause_button_' + id),
					$stop_button = $('#player_stop_button_' + id),
					$open_add_comment_bubble_button = $('#player_open_add_comment_bubble_button_' + id),
					$add_comment_button = $('#add_comment_button_' + id),
					$add_comment_value = $('#add_comment_value_' + id),
					$add_comment_to_value = $('#add_comment_to_value_' + id),
					$add_comment_from_value = $('#add_comment_from_value_' + id),
					$progress = document.querySelector('#progress_bar_' + id),
					$progress_bar_container = $('#progress_bar_' + id),
					$progress_bar = $progress.querySelector('.progress-bar'),
					$progress_bar_value = document.querySelector('#progress_bar_value_' + id),
					waveform_container_width = $waveform_container.width(),
					$add_comment_bubble = $('#add_comment_bubble_' + id),
					$add_comment_marker_container = $('#add_comment_marker_container_' + id),
					$add_comment_start_marker = $('#add_comment_start_marker_' + id),
					$add_comment_end_marker = $('#add_comment_end_marker_' + id),
					duration_in_seconds = null,
					duration_in_px = null,
					current_marker_start_position_in_px = null,
					current_marker_end_position_in_px = null,
					is_dragging_start_marker = false,
					is_dragging_end_marker = false,
					is_add_comment_bubble_open = false,
					from_position_in_seconds = null,
					to_position_in_seconds = null,
					$comments_container = $('#comments_container_' + id),
					$duration_time_bar = $('#duration_time_bar_' + id);

				var wavesurfer = inst.players[id].wavesurfer;

				wavesurfer.on('loading', function(percent)
				{
					$progress.style.display = 'block';

					var percent_str = percent + '%';

					$progress_bar.style.width = percent_str;
					$progress_bar_value.innerHTML = percent_str;

					/*if ( percent > 50 )
					{
						$progress_bar_value.className = 'above-half'
					}*/

					if ( percent === 100 )
					{
						$progress_bar_value.innerHTML = 'Done';
					}
				});

				wavesurfer.on('seek', function(time)
				{
					//var percent = time * 100;

					$stop_button.removeClass('disabled');

					$progress_bar_time.show();

					if ( is_add_comment_bubble_open )
					{
						//Update "From" value
						// Get position
						/*var position_data = wavesurfer.timings(0),
							position_in_seconds = position_data[0],
							position_in_px = position_in_seconds_to_px(position_in_seconds);

						var old_marker_start_position_in_px = current_marker_end_position_in_px;

						current_marker_start_position_in_px = position_in_px;
						current_marker_end_position_in_px = old_marker_start_position_in_px;

						//psudo for att fixa om man klickar efter
						//if ( new_start_position > old_end_position )
						//{
						//	flytta end position to old_start_position + width:en
						//}

						update_comment_marker_container_size();*/
					}
				});

				function mouse_move_func(e, $parent)
				{
					if ( is_dragging_start_marker || is_dragging_end_marker )
					{
						var parent_offset = $parent.parent().offset(),
							position_in_px = e.pageX - parent_offset.left;

						if ( is_dragging_start_marker )
						{
							current_marker_start_position_in_px = position_in_px;
						}
						else if ( is_dragging_end_marker )
						{
							current_marker_end_position_in_px = position_in_px;
						}

						update_comment_marker_container_size();
					}
				}

				wavesurfer.on('ready', function()
				{
					/*inst.players[id].timeline = Object.create(WaveSurfer.Timeline);

					inst.players[id].timeline.init(
					{
						wavesurfer: inst.players[id].wavesurfer,
						container: "#player_timeline_" + id,
						primaryColor: '#C0C0C0'
					});*/

					var position_data = wavesurfer.timings(0);
					inst.players[id].duration = position_data[1];

					var position_data = wavesurfer.timings(0);
					duration_in_seconds = position_data[1];
					duration_in_px = position_in_seconds_to_px(duration_in_seconds);

					$progress.style.display = 'none';
					//$song_buttons_container.style.display = 'block';

					$play_button.removeClass('disabled');
					$open_add_comment_bubble_button.removeClass('disabled');

					// Position comments and show them
					$comments_container.find('.comment').each(function(i, element)
					{
						var $comment = $(element),
							id = $comment.data('id'),
							$comment_data = $('#comment_data_' + id),
							start_position_in_seconds = $comment.data('from_seconds'),
							end_position_in_seconds = $comment.data('to_seconds');

						var start_position_in_px = position_in_seconds_to_px(start_position_in_seconds);

						if ( end_position_in_seconds )
						{
							$('#comment_hover_' + id).css('width', Math.round(position_in_seconds_to_px(end_position_in_seconds) - start_position_in_px) + 'px');
						}

						$comment.on('mouseover', function()
						{
							comment_hover_bind(id);
						}).on('mouseout', function()
						{
							comment_hover_bind(id);
						});

						$comment.css('left', start_position_in_px);
						$comment_data.css('left', start_position_in_px + $comment.width());
					});

					$comments_container.show();

					// Show waveform
					$('#player_waveform_' + id + ' wave').show();

					$waveform_container.on('mousemove', function(e)
					{
						mouse_move_func(e, $(this));
					}).addClass('done');

					$(inst.window).on('mouseup', function()
					{
						is_dragging_start_marker = false;
						is_dragging_end_marker = false;
					});

					$duration_time_bar.show();

					//$('#waveform_' + song_version_id + ' wave').style('height', wavesurfer_options.height + 'px', 'important');
				});

				wavesurfer.on('play', function(percent)
				{
					//$play_button.style.display = 'none';
					//$pause_button.style.display = 'block';
				});

				function add_comment_markup(id, comment, from_seconds, to_seconds)
				{
					var position_in_px = position_in_seconds_to_px(from_seconds);

					var $comment = document.createElement('div');
					$comment.id = 'comment_' + id;
					$comment.style.left = position_in_px + 'px';
					$comment.setAttribute('from_seconds', from_seconds);
					$comment.setAttribute('to_seconds', to_seconds);
					$comment.className = 'comment';

					var $comment_data = document.createElement('div');
					$comment_data.id = 'comment_data_' + id;
					$comment_data.style.left = (position_in_px + parseInt($comment.style.width, 10) + 'px');
					$comment_data.className = 'comment-data';

					var $comment_hover = document.createElement('div');
					$comment_hover.id = 'comment_hover_' + id;
					$comment_hover.className = 'comment-hover';
					$comment_hover.style.width = position_in_seconds_to_px(to_seconds) - position_in_seconds_to_px(from_seconds) + 'px';
					$comment_hover.setAttribute('is_open', 'no');

					$comment.appendChild($comment_hover);

					$comment.onmouseover = function()
					{
						comment_hover_bind(id);
					};

					$comment.onmouseout = function()
					{
						comment_hover_bind(id);
					};

					$comments_container.append($comment);
					$comments_container.append($comment_data);
				}

				function comment_hover_bind(comment_id)
				{
					var $comment_hover_element = $('#comment_hover_' + comment_id),
						is_open = ($comment_hover_element.data('is_open') === 'yes')

					if ( is_open )
					{
						$comment_hover_element.data('is_open', 'no').hide();
						$('#comment_data_' + comment_id).hide();
					}
					else
					{
						$comment_hover_element.data('is_open', 'yes').show();
						$('#comment_data_' + comment_id).show();
					}
				}

				wavesurfer.on('progress', function(delta_percent)
				{
					var percent = delta_percent * 100,
						position_data = wavesurfer.timings(0),
						position_in_seconds = position_data[0];

					duration_in_seconds = position_data[1];
					duration_in_px = position_in_seconds_to_px(duration_in_seconds);

					var current_time_formatted = $kyrst.helper.time.format_seconds(position_in_seconds),
						//duration_formatted = $kyrst.helper.time.format_seconds(duration_in_seconds),
						px_position = (delta_percent * waveform_container_width) + 1;

					// Set progress bar time value and position
					$progress_bar_time.html(current_time_formatted).css('left', px_position + 'px');
				});

				wavesurfer.on('finish', function(percent)
				{
					$play_button.show();
					$pause_button.hide();
					$stop_button.addClass('disabled');

					inst.current_player_id = null;
				});

				wavesurfer.on('destroy', function()
				{
					$progress.style.display = 'none';
				});

				wavesurfer.on('error', function(error)
				{
					$waveform_container.hide();
					$progress.style.display = 'none';
					$progress_bar.style.display = 'none';

					$player_error_container.innerHTML = error + ' <a href="javascript:" id="retry_' + song_version_id + '" data-song_id="' + song_version_id + '" class="retry btn btn-xs btn-primary">Retry</a>';
					$player_error_container.style.display = 'block';

					// Retry
					$('#retry_' + id).on('click', function()
					{
						var $this = $(this),
							song_version_id = $this.data('song_version_id');

						$this.text('Retrying...').addClass('disabled').prop('disabled', true);

						wavesurfer.load(inst.players[id].wavesurfer.filename);
					});
				});

				// Control binds
				// Play
				$play_button.on('click', function()
				{
					inst.pause_current_player();

					$play_button.hide();
					$pause_button.show();
					$stop_button.removeClass('disabled');
					$progress_bar_time.show();

					wavesurfer.play();

					inst.current_player_id = id;
				});

				// Pause
				$pause_button.on('click', function()
				{
					$pause_button.hide();
					$play_button.show();

					wavesurfer.pause();
				});

				// Stop
				$stop_button.on('click', function()
				{
					$pause_button.hide();
					$play_button.show();
					$stop_button.addClass('disabled');
					$progress_bar_time.hide();

					wavesurfer.stop();

					inst.current_player_id = null;
				});

				function update_comment_marker_container_size(what)
				{
					$add_comment_marker_container.css('left', (current_marker_start_position_in_px - 1) + 'px').show();
					$add_comment_marker_container.css('width', (current_marker_end_position_in_px - current_marker_start_position_in_px) + 'px').show();

					// Update position
					from_position_in_seconds = position_in_px_to_seconds(current_marker_start_position_in_px);
					to_position_in_seconds = position_in_px_to_seconds(current_marker_end_position_in_px);

					var from_formatted = $kyrst.helper.time.format_seconds(from_position_in_seconds),
						to_formatted = $kyrst.helper.time.format_seconds(to_position_in_seconds);

					$add_comment_from_value.val(from_formatted).data('value', from_position_in_seconds);
					$add_comment_to_value.val(to_formatted).data('value', to_position_in_seconds);
				}

				function open_add_comment_bubble()
				{
					// Get position
					var position_data = wavesurfer.timings(0),
						position_in_seconds = position_data[0];

					duration_in_seconds = position_data[1];
					duration_in_px = position_in_seconds_to_px(duration_in_seconds);

					current_marker_start_position_in_px = position_in_seconds_to_px(position_in_seconds);

					$add_comment_bubble.show().find('.value').focus();

					// Show marker
					update_comment_marker_container_size();

					var from_formatted = $kyrst.helper.time.format_seconds(position_in_seconds),
						to_formatted = $kyrst.helper.time.format_seconds(duration_in_seconds);

					$add_comment_from_value.val(from_formatted).data('value', position_in_seconds);

					$add_comment_start_marker.on('mousedown', function()
					{
						is_dragging_start_marker = true;
					}).show();

					/*$('#mask').show().on('click', function()
					{
						//close_add_comment_bubble();
					});*/

					is_add_comment_bubble_open = true;
				}

				function close_add_comment_bubble()
				{
					$add_comment_bubble.hide();
					$add_comment_marker_container.hide();

					//$('#mask').hide();

					is_add_comment_bubble_open = false;
				}

				// Open Add Comment bubble
				$open_add_comment_bubble_button.on('click', function()
				{
					open_add_comment_bubble();
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

				// Toggle "From" input
				$('#add_comment_to_value_checkbox_' + id).on('click', function()
				{
					if ( $add_comment_to_value.is(':disabled') ) // "To" checkbox clicked
					{
						var position_data = wavesurfer.timings(0),
							position_in_seconds = position_data[0],
							duration_in_seconds = position_data[1],
							to_value_in_seconds = position_in_seconds + inst.DEFAULT_ADD_COMMENT_SECONDS_TO_ADD,
							to_value_formatted = $kyrst.helper.time.format_seconds(to_value_in_seconds);

						duration_in_px = position_in_seconds_to_px(duration_in_seconds);

						current_marker_end_position_in_px = position_in_seconds_to_px(to_value_in_seconds);

						$add_comment_to_value.prop('disabled', false).val(to_value_formatted).focus();

						$add_comment_marker_container.on('mousemove', function(e)
						{
							mouse_move_func(e, $(this));
						}).css('width', (current_marker_end_position_in_px - current_marker_start_position_in_px) + 'px')
							.show();

						$add_comment_end_marker.on('mousedown', function()
						{
							is_dragging_end_marker = true;
						}).show();
					}
					else
					{
						$add_comment_to_value.prop('disabled', true).val('');
						$add_comment_end_marker.hide();
						$add_comment_start_marker.hide();
					}
				});

				$add_comment_value.on('keypress', function(e)
				{
					if ( event.keyCode === 13 && event.ctrlKey )
					{
						close_add_comment_bubble();
					}
				});

				// Add comment
				$add_comment_button.on('click', function()
				{
					save_comment(song_version_id);

					close_add_comment_bubble();
				});

				function save_comment(song_version_id)
				{
					$kyrst.ajax.post
					(
						BASE_URL + 'ajax/save-song-comment',
						{
							song_version_id: song_version_id,
							comment: $add_comment_value.val(),
							from_seconds: from_position_in_seconds,
							to_seconds: to_position_in_seconds
						},
						{
							success: function(result)
							{
								add_comment_markup(result.data.comment_id, result.data.comment_hover_html, from_position_in_seconds, to_position_in_seconds);

								/*wavesurfer.mark(
								{
									//id: 'lol',
									position: $add_comment_from_value.data('value'),
									color: 'red'//,
									//width:
								});*/
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

				// Version
				$player.find('.change-version').on('click', function()
				{
					var $this = $(this),
						_song_id = $this.data('song_id'),
						_song_version_id = $this.data('song_version_id');
						/*$play_button = document.querySelector('#play_button_' + song_id),
						$pause_button = document.querySelector('#pause_button_' + song_id),
						$stop_button = document.querySelector('#stop_button_' + song_id);*/

					//var id = _song_id + '_' + _song_version_id;

					// The reason for "id" here and not _song_id + _ + _song_version_id is because
					// it's the OLD one we are deleting.
					//$('#player_timeline_' + id).html('');
					$('#player_waveform_' + id + ' wave').hide();

					$progress_bar_container.show();
					$progress_bar.style.display = 'block';
					$progress_bar_time.hide();

					$progress_bar_value.innerHTML = 'Loading...';

					// Show progress
					//$progress.style.display = 'block';
					$waveform_container.removeClass('done');

					$comments_container.hide();
					$duration_time_bar.hide();

					$kyrst.ajax.get
					(
						BASE_URL + 'dashboard/my-songs/get-song-version',
						{
							song_version_id: _song_version_id
						},
						{
							success: function(result)
							{
								$play_button.show();
								$pause_button.hide();
								$stop_button.show().addClass('disabled');

								wavesurfer.load(result.data.song_version.filename);

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
			}(id, song_id, song_version_id));
		});
	},

	pause_current_player: function()
	{
		if ( this.current_player_id === null )
		{
			return;
		}

		this.players[this.current_player_id].wavesurfer.pause();

		$('#player_pause_button_' + this.current_player_id).hide();
		$('#player_play_button_' + this.current_player_id).show();
	}
};
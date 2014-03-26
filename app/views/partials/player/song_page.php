<div id="player_<?= $identifier ?>" class="player <?= $size ?>" data-size="<?= $size ?>" data-song_id="<?= $song->id ?>" data-song_version_id="<?= $song_version->id ?>" data-identifier="<?= $identifier ?>" data-filename="<?= e($song_version->get_filename()) ?>" data-mp3_route="<?= e($song_version->get_route('mp3')) ?>" data-wav_route="<?= e($song_version->get_route('wav')) ?>" data-title="<?= e($song->title) ?>" data-waveform_image="<?= e(URL::to('waveform/' . $user_id . '/' . $song->id . '/v' . $song->version . '/' . $size)) ?>">
	<!-- Waveform -->
	<div class="waveform-and-timeline-container">
		<div id="player_waveform_container_<?= $identifier ?>" class="waveform-container">
			<div id="player_loading_container_<?= $identifier ?>" class="loading-container">
				<div id="player_loading_text_<?= $identifier ?>" class="loading-text">Loading...</div>
			</div>

			<div id="player_waveform_background_<?= $identifier ?>" class="waveform-background">
				<div id="player_progress_container_<?= $identifier ?>" class="progress-container"></div>

				<div id="player_waveform_<?= $identifier ?>" class="waveform" style="background-image:url('<?= $song_version->get_waveform_image($size) ?>')"></div>
			</div>

			<div id="progress_bar_time_<?= $identifier ?>" class="progress-bar-time">00:00</div>

			<div id="duration_time_bar_<?= $identifier ?>" class="duration-time-bar"></div>
		</div>

		<!-- Timeline -->
		<?php /*<div id="player_timeline_<?= $identifier ?>"></div>*/ ?>

		<!-- Add Comment Marker -->
		<div id="add_comment_marker_container_<?= $identifier ?>" class="add-comment-marker-container">
			<div id="add_comment_start_marker_<?= $identifier ?>" class="add-comment-start-marker marker"></div>
			<div id="add_comment_end_marker_<?= $identifier ?>" class="add-comment-end-marker marker"></div>
		</div>

		<!-- Comments -->
		<div id="comments_container_<?= $identifier ?>" class="comments-container">
			<?php foreach ( $song_version->comments as $comment ): ?>
				<div id="comment_<?= $comment->id ?>" class="comment" data-comment_id="<?= $comment->id ?>" data-from_seconds="<?= $comment->from_seconds ?>" data-to_seconds="<?= $comment->to_seconds !== NULL ? $comment->to_seconds : '' ?>" style="background:#FFF url(<?= $comment->user->get_photo_url(User::PHOTO_SIZE_WAVEFORM_COMMENT) ?>)">
					<div id="comment_hover_<?= $comment->id ?>" class="comment-hover" data-is_open="no"></div>
				</div>

				<div id="comment_data_<?= $comment->id ?>" class="comment-data">
					<?= $comment->get_hover_html() ?>
				</div>
			<?php endforeach ?>
		</div>

		<!-- Error -->
		<div id="player_error_container_<?= $identifier ?>" class="player-error-container"></div>
	</div>

	<!-- Buttons -->
	<div id="player_controls_container_<?= $identifier ?>" class="controls-container clearfix">
		<?php /*<a href="javascript:" class="btn btn-xs btn-danger pull-left disabled" style="font-size:2em;line-height:18px;height:22px">&bullet;</a>*/ ?>
		<a href="javascript:" data-song_version_id="<?= $song_version->id ?>" id="player_play_button_<?= $identifier ?>" class="play-button btn btn-primary btn-xs pull-left disabled">Play</a>
		<a href="javascript:" data-song_version_id="<?= $song_version->id ?>" id="player_pause_button_<?= $identifier ?>" class="pause-button btn btn-primary btn-xs pull-left">Pause</a>
		<a href="javascript:" data-song_version_id="<?= $song_version->id ?>" id="player_stop_button_<?= $identifier ?>" class="stop-button btn btn-primary btn-xs pull-left disabled">Stop</a>

		<a href="javascript:" data-song_version_id="<?= $song_version->id ?>" id="player_open_add_comment_bubble_button_<?= $identifier ?>" class="add-comment-button btn btn-primary btn-xs pull-left disabled last">Add Comment</a>

		<?php /*<div class="btn-group btn-group-justified pull-left" style="width:180px;margin-left:2px">
			<a href="javascript:" id="set_start_loop_marker" class="set-start-loop-button btn btn-xs btn-success disabled">Set Start Loop</a>
			<a href="javascript:" id="set_end_loop_marker" class="set-end-loop-button btn btn-xs btn-success disabled">Set End Loop</a>
		</div>*/ ?>
	</div>

	<!-- Add Comment Bubble -->
	<div id="add_comment_bubble_<?= $identifier ?>" class="comment-bubble">
		<textarea id="add_comment_value_<?= $identifier ?>" class="value"></textarea>

		<div class="controls">
			<label>From <input type="text" id="add_comment_from_value_<?= $identifier ?>" data-value="0" value="00:00" class="input-value"></label> <label for="add_comment_to_value_<?= $identifier ?>">To</label> <input type="checkbox" id="add_comment_to_value_checkbox_<?= $identifier ?>"> <input type="text" id="add_comment_to_value_<?= $identifier ?>" data-value="" class="input-value" disabled>

			<a href="javascript:" id="add_comment_button_<?= $identifier ?>" class="pull-right btn btn-xs btn-primary">Add</a>
		</div>
	</div>
</div>
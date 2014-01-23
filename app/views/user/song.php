<ol class="breadcrumb">
	<li><a href="<?= URL::route('home') ?>">Home</a></li>
	<li><a href="<?= $song->user->get_link(User::URL_PROFILE) ?>"><?= $song->user->username ?></a></li>
	<li class="active"><?= $song->get_title() ?></li>
</ol>

<h1><?= $song->get_title() ?></h1>

<p id="created_by_user_text">by <?= $song->user->username ?></p>

<ul id="versions_tab" class="nav nav-tabs">
	<?php foreach ( $song_uploads as $song_upload ): ?>
		<li class="active"><a href="#tab_<?= $song_upload->id ?>" data-toggle="tab">Version <?= $song_upload->version ?> <span class="time">(<?= $song_upload->created_at ?>)</span></a></li>
	<?php endforeach ?>
</ul>

<div id="versions_tab_content" class="tab-content">
	<?php foreach ( $song_uploads as $song_upload ): ?>
		<div id="tab_<?= $song_upload->id ?>" class="tab-pane active">

			<div id="waveform_container_<?= $song_upload->id ?>" class="waveform-container">
				<div id="waveform_<?= $song_upload->id ?>" class="waveform">
					<div id="progress_bar_<?= $song_upload->id ?>" class="progress progress-striped active">
						<div class="progress-bar progress-bar-info"></div>
					</div>
				</div>

				<div id="wave_timeline_<?= $song_upload->id ?>"></div>
			</div>

			<div id="song_buttons_container_<?= $song_upload->id ?>" class="song-buttons-container clearfix">
				<a href="javascript:" data-song_upload_id="<?= $song_upload->id ?>" id="play_button_<?= $song_upload->id ?>" class="play-button btn btn-primary btn-sm pull-left" disabled>Play</a>
				<a href="javascript:" data-song_upload_id="<?= $song_upload->id ?>" id="pause_button_<?= $song_upload->id ?>" class="pause-button btn btn-primary btn-sm pull-left">Pause</a>
				<a href="javascript:" data-song_upload_id="<?= $song_upload->id ?>" id="stop_button_<?= $song_upload->id ?>" class="stop-button btn btn-primary btn-sm pull-left" disabled>Stop</a>
			</div>

			<div id="song_error_container_<?= $song_upload->id ?>" class="song-error-container"></div>

		</div>
	<?php endforeach ?>
</div>
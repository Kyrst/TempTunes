<h1>My Songs</h1>

<p>Fixa funktion sa man kan saga te.x. "jag vill lyssna pa dessa 10 sekunder fran version 4", sa kan man ha 2 waveforms med dom sektionerna.</p>

<div class="content-separator larger">
	<a href="<?= URL::to('dashboard/upload-songs') ?>" class="btn btn-default btn-sm">Upload Song</a>
</div>

<?php if ( $num_songs > 0 ): ?>

	<div id="songs">
		<?php foreach ( $songs as $song ): ?>
			<?php $latest_song_upload = $song->get_latest_upload() ?>

			<div id="song_<?= $song->id ?>" class="song">
				<span class="header">
					<a href="<?= $song->get_url(Song::URL_PUBLIC) ?>" class="title"><?= $song->get_title() ?></a>

					<div class="version-dropdown btn-group">
						<button type="button" id="song_version_downdown_button_<?= $song->id ?>" data-selected_song_upload_id="<?= $latest_song_upload->id ?>" data-selected_version="<?= $latest_song_upload->version ?>" class="btn btn-default btn-xs dropdown-toggle<?php if ( $song->uploads->count() === 1 ): ?> disabled<?php endif ?>"" data-toggle="dropdown">
							Version <?= $song->version ?> <span class="caret"></span>
						</button>

						<ul id="song_versions_list_<?= $song->id ?>" class="dropdown-menu" role="menu">
							<?php foreach ( $song->get_uploads('desc') as $song_upload ): ?>
								<li id="song_upload_version_<?= $song_upload->id ?>"<?php if ( $song_upload->id === $latest_song_upload->id ): ?> class="active"<?php endif ?>><a href="javascript:" data-song_id="<?= $song_upload->song_id ?>" data-song_upload_id="<?= $song_upload->id ?>" class="change-version">Version <?= $song_upload->version ?></a></li>
							<?php endforeach ?>
						</ul>
					</div>

					<span class="edit-button-container"><a href="<?= $song->get_url(Song::URL_EDIT) ?>">Edit</a></span> <span class="separator">|</span>
					<span class="edit-button-container"><a href="<?= URL::to('dashboard/upload-songs/' . $song->id) ?>">Upload New Version</a></span> <span class="separator">|</span>
					<span class="edit-button-container"><a href="javascript:" data-song_id="<?= $song->id ?>" data-song_title="<?= $song->get_title() ?>" class="delete-song">Delete</a></span>
				</span>

				<p><?= $song->get_description() ?></p>

				<div class="clear"></div>

				<div id="waveform_container_<?= $song->id ?>" class="waveform-container">
					<div id="waveform_<?= $song->id ?>" class="waveform">
						<div id="progress_bar_<?= $song->id ?>" class="progress progress-striped active">
							<div class="progress-bar progress-bar-info"></div>
						</div>
					</div>

					<div id="wave_timeline_<?= $song->id ?>"></div>
				</div>

				<div id="song_buttons_container_<?= $song->id ?>" class="song-buttons-container clearfix">
					<a href="javascript:" data-song_id="<?= $song->id ?>" id="play_button_<?= $song->id ?>" class="play-button btn btn-primary btn-sm pull-left" disabled>Play</a>
					<a href="javascript:" data-song_id="<?= $song->id ?>" id="pause_button_<?= $song->id ?>" class="pause-button btn btn-primary btn-sm pull-left">Pause</a>
					<a href="javascript:" data-song_id="<?= $song->id ?>" id="stop_button_<?= $song->id ?>" class="stop-button btn btn-primary btn-sm pull-left" disabled>Stop</a>
				</div>

				<div id="song_error_container_<?= $song->id ?>" class="song-error-container"></div>
			</div>
		<?php endforeach ?>
	</div>

<?php endif ?>

<p id="no_songs_uploaded"<?php if ( $num_songs > 0 ): ?> class="hide"<?php endif ?>>No songs uploaded.</p>
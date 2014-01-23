<?php if ( $song !== null ): ?>
	<h1>Upload New Version <?php /*(v<?= $song->version + 1 ?>) */ ?>of &quot;<?= $song->get_title() ?>&quot;</h1>
<?php else: ?>
	<h1>Upload Song(s)</h1>
<?php endif ?>

<p id="max_upload_size_text">Max upload file size per file is <span id="max_upload_size"><?= $max_upload_size_formatted ?></span>.</p>

<form action="<?= URL::route('dashboard/upload-songs') ?>" method="post" enctype="multipart/form-data" id="upload_song_form">
	<input type="hidden" id="MAX_FILE_SIZE" name="MAX_FILE_SIZE" value="<?= $max_upload_size ?>">

	<div id="upload_progress_container">
		<div id="upload_progress_items"></div>
	</div>

	<input type="file" id="files_input"<?php if ( $song === null ): /* Can only select one if uploading to existing songs (remove stupid rule?) */ ?> multiple<?php endif ?>>
</form>

<div id="upload_complete_container">
	Upload completed successfully. Redirecting to <a href="<?= URL::route('dashboard/my-songs') ?>">My Songs</a>...
</div>
<h1>Edit &quot;<?= $song->get_title() ?>&quot;</h1>

<form action="<?= $song->get_url(Song::URL_EDIT) ?>" method="post" role="form" class="kyrst-auto-submit" data-submit_button_loading_text="Saving...">
	<!-- First Name -->
	<div class="form-group">
		<label for="title">Title</label>
		<input type="text" name="title" id="title" value="<?= $song->title ?>" class="form-control">
	</div>

	<!-- Description -->
	<div class="form-group">
		<label for="description">Description</label>
		<textarea name="description" id="description" class="form-control"><?= $song->get_description() ?></textarea>
	</div>

	<button type="submit" class="btn btn-primary">Save</button>
	<a href="<?= URL::route('dashboard/my-songs') ?>" class="btn btn-default">Back</a>
</form>

<!-- Versions -->

<h3>Versions</h3>

<table class="table table-striped table-bordered">
	<thead>
		<th>Title</th>
		<th>Filename</th>
		<th>Version</th>
		<th>Action</th>
	</thead>

	<tbody id="song_versions_tbody">
		<?php foreach ( $song->get_latest_versions() as $song_version ): ?>
			<tr>
				<td><?= $song_version->title ?></td>
				<td><?= $song_version->original_filename ?></td>
				<td><?= $song_version->version ?></td>
				<td>
					<a href="javascript:" data-song_version_id="<?= $song_version->id ?>" class="btn btn-default btn-xs edit-song-version">Edit</a>
					<a href="javascript:" data-song_version_id="<?= $song_version->id ?>" data-song_version_title="<?= e($song_version->title) ?>" class="btn btn-default btn-xs delete-song-version">Delete</a>
				</td>
			</tr>
		<?php endforeach ?>
	</tbody>
</table>

<!-- Edit Version Dialog -->
<div id="edit_song_version_dialog" class="kyrst-dialog">
	<form action="" method="post">
		<!-- Title -->
		<div class="form-group">
			<label for="title">Title</label>
			<input type="text" name="title" id="edit_song_version_dialog_title" class="form-control">
		</div>

		<!-- Description -->
		<div class="form-group">
			<label for="description">Description</label>
			<textarea name="description" id="description" rows="8" class="form-control"></textarea>
		</div>
	</form>
</div>
<h1>Edit &quot;<?= $song->get_title() ?>&quot;</h1>

<form action="<?= $song->get_url(Song::URL_EDIT) ?>" method="post" role="form" class="kyrst-auto-submit" data-submit_button_loading_text="Saving...">
	<!-- First Name -->
	<div class="form-group">
		<label for="title">Title</label>
		<input type="text" name="title" id="title" value="<?= $song->title ?>" class="form-control">
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

	<tbody>
		<?php foreach ( $song->get_latest_uploads() as $song_upload ): ?>
			<tr>
				<td><?= $song_upload->title ?></td>
				<td><?= $song_upload->original_filename ?></td>
				<td><?= $song_upload->version ?></td>
				<td>
					<a href="javascript:" class="btn btn-default btn-xs">Edit</a>
					<a href="javascript:" class="btn btn-default btn-xs">Delete</a>
				</td>
			</tr>
		<?php endforeach ?>
	</tbody>
</table>

<!-- Edit Version Dialog -->
<div id="edit_song_upload_dialog" class="kyrst-dialog">
	<form action="" method="post">
		<!-- Title -->
		<div class="form-group">
			<label for="title">Title</label>
			<input type="text" name="title" id="edit_song_upload_dialog_title" class="form-control">
		</div>
	</form>
</div>
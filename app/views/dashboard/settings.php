<h1>Settings</h1>

<ul id="settings_tabs" class="nav nav-tabs">
	<li class="active"><a href="#tab_general" data-toggle="tab">General</a></li>
	<li><a href="#tab_photo" data-toggle="tab">Photo</a></li>
	<li><a href="#tab_id3" data-toggle="tab">ID3 Tags</a></li>
</ul>

<div id="settings_tabs_content" class="tab-content">
	<!-- General -->
	<div id="tab_general" class="tab-pane active">
		<form action="<?= URL::route('dashboard/settings/save') ?>" method="post" role="form" class="kyrst-auto-submit" data-submit_button_loading_text="Saving...">
			<!-- First Name -->
			<div class="form-group">
				<label for="first_name">First Name</label>
				<input type="text" name="first_name" id="first_name" value="<?= $user->first_name ?>" class="form-control">
			</div>

			<!-- Last Name -->
			<div class="form-group">
				<label for="last_name">Last Name</label>
				<input type="text" name="last_name" id="last_name" value="<?= $user->last_name ?>" class="form-control">
			</div>

			<button type="submit" class="btn btn-primary">Save</button>
		</form>
	</div>

	<!-- Photo -->
	<div id="tab_photo" class="tab-pane">

		<!-- Upload Photo -->
		<div id="no_photo_container"<?php if ( $user->photo === 'no' ): ?> style="display:none"<?php endif ?>>
			<?= $user->get_photo_html(User::PHOTO_SIZE_SMALL) ?>

			<div class="clear"></div>

			<a href="javascript:" id="delete_photo" class="btn btn-primary btn-sm">Delete</a>
		</div>

		<!-- Current Photo -->
		<div id="photo_container"<?php if ( $user->photo === 'yes' ): ?> style="display:none"<?php endif ?>>
			<form action="<?= URL::route('dashboard/settings/upload-photo') ?>" method="post" enctype="multipart/form-data" role="form">
				<div class="form-group">
					<input type="file" name="photo" id="photo">
				</div>

				<div class="form-group">
					<button type="submit" class="btn btn-primary">Upload</button>
				</div>
			</form>
		</div>
	</div>

	<!-- ID3 Tags -->
	<div id="tab_id3" class="tab-pane">
		Set default ID3 tags.

		Title: [ %filename - %version (%date).mp3 ]
	</div>
</div>
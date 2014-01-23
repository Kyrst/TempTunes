<h1>Settings</h1>

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
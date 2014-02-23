<h1>Sign Up</h1>

<form action="<?= URL::route('sign-up') ?>" method="post" id="sign_up_form" class="kyrst-auto-submit" data-submit_button_loading_text="Signing Up...">
	<!-- Username -->
	<div class="form-group">
		<label for="username">Username</label>
		<input type="text" name="username" id="username" class="form-control">
	</div>

	<!-- E-mail -->
	<div class="form-group">
		<label for="email">E-mail</label>
		<input type="email" name="email" id="email" class="form-control">
	</div>

	<!-- First Name -->
	<div class="form-group">
		<label for="first_name">First Name</label>
		<input type="text" name="first_name" id="first_name" class="form-control">
	</div>

	<!-- Last Name -->
	<div class="form-group">
		<label for="last_name">Last Name</label>
		<input type="text" name="last_name" id="last_name" class="form-control">
	</div>

	<!-- Password -->
	<div class="form-group">
		<label for="password">Password</label>
		<input type="password" name="password" id="password" class="form-control">
	</div>

	<!-- Verify Password -->
	<div class="form-group">
		<label for="password_verify">Verify Password</label>
		<input type="password" name="password_verify" id="password_verify" class="form-control">
	</div>

	<div class="form-group">
		<button type="submit" class="btn btn-primary">Sign Up</button>
	</div>
</form>
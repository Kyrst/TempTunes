<h1><?= $heading ?></h1>

<form action="<?= URL::current() ?>" method="post" class="form-horizontal" role="form" novalidate>
	<!-- Username -->
	<div class="form-group">
		<label for="username" class="col-sm-2 control-label">Username</label>

		<div class="col-sm-10">
			<input type="text" id="username" name="username" class="form-control" value="<?php if ( $user_to_edit !== NULL ): ?><?= e($user_to_edit->username) ?><?php endif ?>">
		</div>
	</div>

	<!-- Email -->
	<div class="form-group">
		<label for="email" class="col-sm-2 control-label">Email</label>

		<div class="col-sm-10">
			<input type="email" id="email" name="email" class="form-control" value="<?php if ( $user_to_edit !== NULL ): ?><?= e($user_to_edit->email) ?><?php endif ?>">
		</div>
	</div>

	<!-- Password -->
	<div class="form-group">
		<label for="password" class="col-sm-2 control-label">Password</label>

		<div class="col-sm-10">
			<input type="text" id="password" name="password" class="form-control">
		</div>
	</div>

	<!-- First Name -->
	<div class="form-group">
		<label for="first_name" class="col-sm-2 control-label">First Name</label>

		<div class="col-sm-10">
			<input type="text" id="first_name" name="first_name" class="form-control" value="<?php if ( $user_to_edit !== NULL ): ?><?= e($user_to_edit->first_name) ?><?php endif ?>">
		</div>
	</div>

	<!-- Last Name -->
	<div class="form-group">
		<label for="last_name" class="col-sm-2 control-label">Last Name</label>

		<div class="col-sm-10">
			<input type="text" id="last_name" name="last_name" class="form-control" value="<?php if ( $user_to_edit !== NULL ): ?><?= e($user_to_edit->last_name) ?><?php endif ?>">
		</div>
	</div>

	<!-- Save/Add -->
	<div class="form-group">
		<div class="col-sm-offset-2 col-sm-10">
			<button type="submit" class="btn btn-primary btn-default"><?= ( $user_to_edit !== NULL ) ? 'Save' : 'Add' ?></button>
		</div>
	</div>
</form>
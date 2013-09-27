<!doctype html>
<html>
	<head>
		<meta charset="utf-8">
		<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
		<title><?= $page_title; ?></title>
		<meta name="description" content="">
		<meta name="viewport" content="width=device-width, initial-scale=1.0">

		<?php foreach ( $assets['css'] as $css ): ?>
			<link href="<?= URL::route('home', array(), false) . $css['file']; ?>" rel="stylesheet">
		<?php endforeach; ?>
	</head>
	<body>
		<!-- Header -->
		<nav id="header" class="navbar navbar-default" role="navigation">
			<div class="container">
				<a href="<?= URL::route('home'); ?>" id="logo">TempTunes</a>

				<div class="pull-right">
					<?php if ( $user ): ?>
						Inloggad
					<?php else: ?>
						<form action="<?= URL::to('login'); ?>" method="post" id="login_form" class="form-inline" role="form">
							<div class="form-group">
								<label for="login_email" class="sr-only">E-mail</label>
								<input type="email" id="exampleInputEmail2" placeholder="E-mail" class="form-control">
							</div>
							<div class="form-group">
								<label for="login_password" class="sr-only">Password</label>
								<input type="password" id="exampleInputPassword2" placeholder="Password" class="form-control">
							</div>
							<div class="checkbox">
								<label>
									<input type="checkbox"> Remember me
								</label>
							</div>
							<button type="submit" class="btn btn-default">Sign in</button>
						</form>
					<?php endif; ?>
				</div>
			</div>
		</nav>

		<div class="clear"></div>

		<!-- Content -->
		<div class="container">
			<?= $content; ?>
		</div>

		<?php if ( isset($bootbox_alert) || (count($js_vars) > 0) ): ?>
			<script>
				<?php if ( isset($bootbox_alert) ): ?>
				var bootbox_alert = '<?php echo $bootbox_alert; ?>';
				<?php endif; ?>

				<?php foreach ( $js_vars as $key => $value ): ?>
				var <?= $key; ?> = '<?= $value; ?>';
				<?php endforeach; ?>
			</script>
		<?php endif; ?>

		<?= $jquery_script; ?>

		<?php foreach ( $assets['js'] as $file ): ?>
			<script src="<?= URL::route('home', array(), false) . $file; ?>"></script>
		<?php endforeach; ?>
	</body>
</html>
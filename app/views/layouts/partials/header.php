<div id="header" class="navbar navbar-inverse" role="navigation">
	<div class="container">
		<div class="navbar-header">
			<button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
				<span class="sr-only">Toggle navigation</span>
				<span class="icon-bar"></span>
				<span class="icon-bar"></span>
				<span class="icon-bar"></span>
			</button>
			<a href="<?= URL::route('home') ?>" class="navbar-brand">TempTunes</a>
		</div>

		<div class="navbar-collapse collapse">

			<!-- Logged in -->
			<?php if ( $user !== NULL ): ?>
				<div id="header_user_nav" class="pull-right">
					<div class="btn-group">
						<button type="button" class="btn btn-default btn-sm dropdown-toggle" data-toggle="dropdown">
							<?= $user->get_name() ?> <span class="caret"></span>
						</button>

						<ul class="dropdown-menu" role="menu">
							<li><a href="<?= URL::route('home') ?>">Front Page</a></li>
							<li><a href="<?= URL::route('dashboard') ?>">Dashboard</a></li>
							<li class="divider"></li>
							<li><a href="<?= $user->get_link(User::URL_PROFILE) ?>"><?= $user->username ?></a></li>
							<li><a href="<?= URL::route('dashboard/my-songs') ?>">My Songs<?php if ( $num_songs > 0 ): ?> (<?= $num_songs ?>)<?php endif ?></a></li>
							<li><a href="<?= URL::to('dashboard/upload-songs') ?>">Upload Song</a></li>
							<li class="divider"></li>
							<li><a href="<?= URL::route('dashboard/settings') ?>">Settings</a></li>
							<li class="divider"></li>
							<li><a href="<?= URL::route('log-out') ?>">Log out</a></li>
						</ul>
					</div>
				</div>
			<!-- Not logged in -->
			<?php else: ?>
				<form action="<?= URL::route('sign-in') ?>" method="post" role="form" class="navbar-form navbar-right kyrst-auto-submit" data-submit_button_loading_text="Signing in...">
					<div class="form-group">
						<input type="text" name="email" id="header_email" placeholder="Email" class="form-control">
					</div>

					<div class="form-group">
						<input type="password" name="password" id="header_passwords" placeholder="Password" class="form-control">
					</div>

					<button type="submit" class="btn btn-success">Sign in</button>
				</form>
			<?php endif ?>

			<?php /*<div id="header_song_container">
				<div id="header_song_inner">
					<a href="/">Kyrst - Virgin</a>
				</div>
			</div>*/ ?>
		</div>
	</div>
</div>
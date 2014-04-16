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
							<li<?php if ( $current_page === 'front/index' ): ?> class="active"<?php endif ?>><a href="<?= URL::route('home') ?>">Front Page</a></li>
							<li<?php if ( $current_page === 'dashboard/dashboard' ): ?> class="active"<?php endif ?>><a href="<?= URL::route('dashboard') ?>">Dashboard</a></li>
							<li class="divider"></li>
							<li<?php if ( $current_page === 'user/profile' ): ?> class="active"<?php endif ?>><a href="<?= $user->get_link(User::URL_PROFILE) ?>"><?= $user->get_name() ?></a></li>
							<li<?php if ( $current_page === 'user/friends' ): ?> class="active"<?php endif ?>><a href="<?= $user->get_link(User::URL_FRIENDS) ?>">Your Friends<?php if ( $num_friends > 0 ): ?> <span style="color:#AAA;font-size:.8em">(<?= $num_friends ?>)</span><?php endif ?></a></li>
							<li class="divider"></li>
							<li<?php if ( $current_page === 'dashboard/my_songs' ): ?> class="active"<?php endif ?>><a href="<?= URL::route('dashboard/my-songs') ?>">Your Songs<?php if ( $num_songs > 0 ): ?> <span style="color:#AAA;font-size:.8em">(<?= $num_songs ?>)</span><?php endif ?></a></li>
							<li<?php if ( $current_page === 'dashboard/upload_songs' ): ?> class="active"<?php endif ?>><a href="<?= URL::to('dashboard/upload-songs') ?>">Upload Song</a></li>
							<li class="divider"></li>
							<li<?php if ( $current_page === 'dashboard/settings' ): ?> class="active"<?php endif ?>><a href="<?= URL::route('dashboard/settings') ?>">Settings</a></li>
							<li class="divider"></li>
							<?php if ( $user->is_admin() ): ?>
								<li<?php if ( $current_page === 'admin/index' ): ?> class="active"<?php endif ?>><a href="<?= URL::route('admin') ?>">Administrator</a></li>
								<li<?php if ( $current_page === 'admin/users' ): ?> class="active"<?php endif ?>><a href="<?= URL::route('admin/users') ?>">Users</a></li>
								<li class="divider"></li>
							<?php endif ?>
							<li><a href="<?= URL::route('log-out') ?>">Log out</a></li>
						</ul>
					</div>
				</div>
			<!-- Not logged in -->
			<?php else: ?>
				<form action="<?= URL::route('sign-in') ?>" method="post" role="form" class="navbar-form navbar-right" data-submit_button_loading_text="Signing in...">
					<div class="form-group">
						<input type="text" name="email" id="header_email" placeholder="Email" class="form-control">
					</div>

					<div class="form-group">
						<input type="password" name="password" id="header_passwords" placeholder="Password" class="form-control">
					</div>

					<button type="submit" class="btn btn-success">Sign In</button>
					<a href="<?= URL::route('sign-up') ?>" class="btn btn-primary">Sign Up</a>
				</form>
			<?php endif ?>

			<?= $header_player_html ?>
		</div>
	</div>
</div>
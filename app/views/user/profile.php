<ol class="breadcrumb">
	<li><a href="<?= URL::route('home') ?>">Home</a></li>
	<li class="active"><?= $profile_user->username ?></li>
</ol>

<h1><?= $profile_user->get_display_name() ?></h1>

<div class="content-separator larger">
	<a href="<?= $profile_user->get_link(User::URL_SONGS) ?>" class="btn btn-default btn-sm">Songs<?php if ( $num_songs > 0 ): ?> (<?= $num_songs ?>)<?php endif ?></a>
</div>
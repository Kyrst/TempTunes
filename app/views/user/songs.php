<ol class="breadcrumb">
	<li><a href="<?= URL::route('home') ?>">Home</a></li>
	<li><a href="<?= $profile_user->get_link(User::URL_PROFILE) ?>"><?= $user->username ?></a></li>
	<li class="active">Songs</li>
</ol>

<h1>Songs by <?= $profile_user->username ?></h1>
<ol class="breadcrumb">
	<li><a href="<?= URL::route('home') ?>">Home</a></li>
	<li><a href="<?= $profile_user->get_link(User::URL_PROFILE) ?>"><?= $profile_user->username ?></a></li>
	<li class="active">Friends</li>
</ol>

<h1><?= $profile_user->get_display_name() ?>'s Friends</a></h1>
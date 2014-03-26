<ol class="breadcrumb">
	<li><a href="<?= URL::route('home') ?>">Home</a></li>
	<li class="active"><?= $profile_user->username ?></li>
</ol>

<h1><?= $profile_user->get_display_name() ?></a></h1>

<!-- Action Buttons -->
<?php /*<div id="action_buttons" class="btn-group btn-group-justified">
	<a href="javascript:" id="delete_selected_button" class="btn btn-xs btn-primary disabled" role="button">Delete</a>
	<a href="javascript:" id="merge_selected_button" class="btn btn-xs btn-primary disabled" role="button">Merge</a>
</div>*/ ?>

<!-- Songs -->
<form action="<?= $profile_user->get_link(User::URL_SONGS) ?>" method="post" id="songs_form">

	<?php foreach ( $songs as $song ): ?>
		<div id="song_<?= $song->id ?>" class="song">
			<?= $song->print_player(Song::PLAYER_SIZE_BIG) ?>
		</div>
	<?php endforeach ?>

</form>

<!-- Merge Dialog -->
<div id="merge_dialog" class="kyrst-dialog">

	<h3>Merge</h3>

	How do you want to arrange the versions:
	(play) filename.mp3 [ title ] [ description ]<br>
	(play) filename2.mp3 [ title ] [ description ]

</div>
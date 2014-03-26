<ol class="breadcrumb">
	<li><a href="<?= URL::route('home') ?>">Home</a></li>
	<li><a href="<?= $song->user->get_link(User::URL_PROFILE) ?>"><?= $song->user->username ?></a></li>
	<li class="active"><?= $song->get_title() ?></li>
</ol>

<h1><?= $song->get_title() ?></h1>

<p id="created_by_user_text">by <?= $song->user->username ?></p>

<ul id="versions_tab" class="nav nav-tabs">
	<?php foreach ( $song_versions as $i => $song_version ): ?>
		<li<?php if ( $i === 0 ): ?> class="active"<?php endif ?>><a href="#tab_<?= $song_version->id ?>" data-toggle="tab">Version <?= $song_version->version ?> <span class="time">(<?= $song_version->created_at ?>)</span></a></li>
	<?php endforeach ?>
</ul>

<div id="versions_tab_content" class="tab-content">
	<?php foreach ( $song_versions as $i => $song_version ): ?>
		<div id="tab_<?= $song_version->id ?>" class="tab-pane<?php if ( $i === 0 ): ?> active<?php endif ?>">
			<?= $song->print_player(Song::PLAYER_SIZE_SONG_PAGE, $song_version->id) ?>
		</div>
	<?php endforeach ?>
</div>
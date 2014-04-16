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

			<h4>Comments</h4>
			<?php foreach ( $song_version->comments()->orderBy('created_at', 'DESC')->get() as $comment ): ?>
				<div class="comment">
					<a href="<?= $comment->user->get_link(User::URL_PROFILE) ?>"><?= $comment->user->get_name() ?></a> at <a href="javascript:"><?= Time::format_seconds($comment->from_seconds) ?><?php if ( $comment->to_seconds !== NULL ): ?> to <?= Time::format_seconds($comment->to_seconds) ?><?php endif ?></a>
					<br>
					<time><?= $comment->created_at ?></time>
					<br>
					<?= nl2br($comment->comment) ?>
				</div>
			<?php endforeach ?>

			<h4>Shared With</h4>
		</div>
	<?php endforeach ?>
</div>
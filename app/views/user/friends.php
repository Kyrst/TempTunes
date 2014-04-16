<ol class="breadcrumb">
	<li><a href="<?= URL::route('home') ?>">Home</a></li>
	<li><a href="<?= $profile_user->get_link(User::URL_PROFILE) ?>"><?= $profile_user->username ?></a></li>
	<li class="active">Friends</li>
</ol>

<h1>Your Friends</h1>

<a href="javascript:" id="add_friend_button" class="btn btn-primary">Add Friend</a>

<!-- Friend Requests -->
<?php if ( count($friend_requests) > 0 ): ?>
	<div id="friend_requests_container">
		<h4>Friend Requests</h4>

		<div id="friend_requests">
			<?php foreach ( $friend_requests as $friend_request ): ?>
				<div id="friend_request_<?= $friend_request->id ?>" class="friend-request">
					<?= $friend_request->user->get_name() ?>
					<a href="javascript:" data-id="<?= $friend_request->id ?>" data-accept_or_deny="accept" class="btn btn-xs btn-success accept-friend-request">Accept</a>
					<a href="javascript:" data-id="<?= $friend_request->id ?>" data-accept_or_deny="deny" class="btn btn-xs btn-danger deny-friend-request">Deny</a>
				</div>
			<?php endforeach ?>
		</div>
	</div>
<?php endif ?>

<!-- Friends -->
<?php if ( count($friends) > 0 ): ?>
	<div id="friends">
		<?php foreach ( $friends->get() as $friend ): ?>
			<div id="friend_<?= $friend->id ?>" class="friend">
				<a href="<?= $friend->user->get_link(User::URL_PROFILE) ?>"><?= $friend->user->get_name() ?></a>
				<br>

				<?php $latest_upload = $friend->user->getLatestUpload() ?>

				<?php if ( $latest_upload !== NULL ): ?>
					Latest upload: <a href="<?= $latest_upload->song->get_url(Song::URL_PUBLIC) ?>"><?= $latest_upload->title ?></a> / <?= $latest_upload->created_at ?>
					<br>
					<a href="/" class="btn btn-xs btn-default">Send Song</a>
					<a href="/" class="btn btn-xs btn-default">Send Message</a>
					<a href="/" class="btn btn-xs btn-default">Remove Friend</a>
				<?php endif ?>
			</div>
		<?php endforeach ?>
	</div>
<?php else: ?>
	<p>No friends.</p>
<?php endif ?>

<!-- Add Friend Dialog -->
<div id="add_friend_dialog" title="Add Friend" class="kyrst-dialog">
	<form action="<?= URL::current() ?>" method="post">
		<div clas="form-group">
			<label for="friend_identifier" class="control-label">Friend Email, Username or Real Name</label>
			<input type="text" name="friend_identifier" id="friend_identifier" class="form-control">
		</div>
	</form>
</div>
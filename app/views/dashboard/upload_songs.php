<?php if ( $song !== null ): ?>
	<h1>Upload New Version <?php /*(v<?= $song->version + 1 ?>) */ ?>of &quot;<?= $song->get_title() ?>&quot;</h1>

	<h2>Current Versions</h2>

	<?php if ( $num_current_song_versions > 0 ): ?>
		<?php foreach ( $song->versions as $song_version ): ?>
			<div class="current-version">
				<a href="<?= $song_version->getLink() ?>"><?= $song_version->title ?></a>
				<br>
				Version <?= $song_version->version ?> | Uploaded <?= $song_version->created_at ?>
			</div>
		<?php endforeach ?>
	<?php else: ?>
		<p>No other versions.</p>
	<?php endif ?>
<?php else: ?>
	<h1>Upload Song</h1>
<?php endif ?>

<?php if ( $user->hasPlan() ): ?>
	<?php
	$can_upload = TRUE;
	?>
<?php else: ?>
	<?php
	define('FREE_PLAN_MAX_VERSIONS_PER_SONG', 2);
	define('FREE_PLAN_MAX_SONGS', 3);

	$can_upload = TRUE;

	if ( $song !== NULL )
	{
		if ( $num_current_song_versions >= FREE_PLAN_MAX_VERSIONS_PER_SONG )
		{
			$can_upload = FALSE;
		}
	}
	else
	{
		$can_upload = TRUE;
	}
	?>
<?php endif ?>

<form action="<?= URL::route('dashboard/upload-songs') ?>" method="post" enctype="multipart/form-data" id="upload_song_form">
	<?php if ( $can_upload ): ?>
		<input type="hidden" id="MAX_FILE_SIZE" name="MAX_FILE_SIZE" value="<?= $max_upload_size ?>">

		<?php if ( 1 === 2 ): ?>
			<h2>Select File</h2>
		<?php endif ?>

		<?php /*<p id="max_upload_size_text">Max upload file size is <span id="max_upload_size"><?= $max_upload_size_formatted ?></span>.</p>*/ ?>

		<input type="file" id="files_input"<?php if ( !$can_upload ): ?> disabled<?php endif ?>>

		<div id="upload_progress_container">
			<div id="upload_progress_items"></div>
		</div>
	<?php else: ?>
		<style>
			.bs-callout
			{
				margin: 20px 0;
				padding: 20px;
				border-left: 3px solid #eee;
			}

			.bs-callout-info
			{
				background-color: #f4f8fa;
				border-color: #5bc0de;
			}

			.bs-callout-info h4
			{
				color: #5bc0de;
			}

			.bs-callout h4
			{
				margin-top: 0;
				margin-bottom: 5px;
			}

			.bs-callout a
			{
				color: #000;
			}
		</style>

		<div class="bs-callout bs-callout-info">
			<h4>Upgrade Your Plan</h4>
			<p>Sorry, you have uploaded <strong><?= $num_current_song_versions ?></strong> versions of <strong><?= $song->get_title() ?></strong> already, which is the maximum number of versions per song you can upload with a free account.</p>

			<p style="margin-bottom:20px">Upgrade your plan for only <strong>$5/month</strong>. <a href="<?= URL::route('dashboard/change-plan') ?>" class="btn btn-xs btn-default">More Info</a></p>

			<div class="form-group clearfix">
				<div class="col-sm-6">
					<label>Credit Card Number</label>
					<input type="text" name="credit_card[number]" id="credit_card_number" class="form-control">
				</div>

				<div class="col-sm-3">
					<label>Credit Card CVC</label>
					<input type="text" name="credit_card[cvc]" id="credit_card_cvc" class="form-control">
				</div>
			</div>

			<div class="form-group clearfix">
				<div class="col-sm-3">
					<label>Exp Year</label>
					<input type="text" name="credit_card[exp_year]" id="credit_card_exp_year" class="form-control">
				</div>

				<div class="col-sm-3">
					<label>Exp Month</label>
					<input type="text" name="credit_card[exp_month]" id="credit_card_exp_month" class="form-control">
				</div>
			</div>

			<div class="form-group clearfix">
				<div class="col-sm-12">
					<button type="submit" class="btn btn-default">Upgrade</button>
				</div>
			</div>
		</div>
	<?php endif ?>
</form>

<?php /*<div id="upload_complete_container">
	Upload completed successfully. Redirecting to <a href="<?= URL::route('dashboard/my-songs') ?>">My Songs</a>...
</div>*/ ?>

<!-- Upload Item Template -->
<div id="upload_item_template" style="display: none">
	<div id="upload_item_{{ index }}" class="upload-item">
		<span class="upload-item-title">{{ name }}</span>

		<progress id="{{ progress_bar_selector }}" class="upload-item-progress-bar"></progress>

		<span id="upload_item_progress_status_{{ index }}" class="upload-item-progress-status">0%</span>

		<div class="clear"></div>

		<ul id="upload_item_progress_buttons_{{ index }}" class="upload-item-progress-buttons">
			<li><a href="javascript:" id="upload_item_cancel_button_{{ index }}" data-index="{{ index }}">Cancel</a></li>
		</ul>

		<div id="upload_item_status_{{ index }}" class="upload-item-status"></div>

		<div id="upload_item_done_container_{{ index }}" class="upload-item-done-container">
			Share

			<select name="share" id="share_{{ index }}" class="share-select">
				<option value="">Nobody</option>
				<option value="">Everyone</option>
				<option value="">Stefan Nygren</option>
				<option value="">Jonas Stensved</option>
			</select>
		</div>
	</div>
</div>
<!doctype html>
<html>
	<head>
		<meta charset="utf-8">
		<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
		<meta name="viewport" content="width=1030, maximum-scale=1.0">

		<title><?= $page_title; ?></title>

		<meta name="description" content="">

		<link rel="shortcut icon" href="<?= URL::to('favicon.ico') ?>">
		<link rel="apple-itouch-icon" href="<?= URL::to('favicon.png') ?>">

		<?php foreach ( $assets['css'] as $css ): ?>
			<link href="<?= URL::route('home', array(), false) . $css['file'] ?>" rel="stylesheet">
		<?php endforeach; ?>
	</head>
	<body>
		<?= $header_html ?>

		<!-- Content -->
		<div id="content">
			<div class="container">
				<?= $content ?>
			</div>
		</div>

		<div id="mask"></div>

		<?= $js_vars ?>

		<?php foreach ( $assets['js'] as $file ): ?>
			<script src="<?= URL::route('home', array(), false) . $file ?>"></script>
		<?php endforeach; ?>
	</body>
</html>
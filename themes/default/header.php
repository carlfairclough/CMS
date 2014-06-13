<html>

	<head>
		<link href="<?php echo theme_url_loc(); ?>/style.css" media="screen" rel="stylesheet" type="text/css" />
	</head>

	<body class="<?php insert_body_classes(); ?>">
		<header>
		<nav>
			<p><?php echo get_settings('Header'); ?></p>
			<?php echo $get_main_nav ?>
		</nav>
		<h1><?php get_content('Template'); ?></h1>

		</header>
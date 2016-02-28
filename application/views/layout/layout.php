<?php
/**
 * Main landing page controller
 */
defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8" />
	<meta http-equiv="X-UA-Compatible" content="IE=edge" />
	<meta name="viewport" content="width=device-width, initial-scale=1.0" />
	<link rel="icon" type="image/png" href="/assets/img/favicon.png" />

	<!-- Vendor Assets -->
	<link href='//fonts.googleapis.com/css?family=Droid+Sans:400,700|Oxygen:400,300,700&subset=latin,latin-ext' rel='stylesheet' type='text/css' />
	<link rel="stylesheet" href="/assets/foundation/bower_components/foundation/css/normalize.css" />
	<link rel="stylesheet" href="/assets/foundation/stylesheets/app.css?v=1.8" />
	<link rel="stylesheet" href="/assets/foundation/stylesheets/foundation-datepicker.min.css" />
	<link rel="stylesheet" href="/assets/perfect-scrollbar/perfect-scrollbar.min.css" />
	<link rel="stylesheet" href="/assets/slick/slick.css" />
	<link rel="stylesheet" href="/assets/foundation-icons/foundation-icons.css" />

	<script src="/assets/foundation/bower_components/modernizr/modernizr.js"></script>
	<script src="/assets/foundation/bower_components/jquery/dist/jquery.min.js"></script>
	<script src="/assets/foundation/js/foundation-datepicker.min.js"></script>
	<script src="//cdn.datatables.net/1.10.7/js/jquery.dataTables.min.js"></script>
	<script src="/assets/perfect-scrollbar/perfect-scrollbar.min.js"></script>
	<script src="/assets/slick/slick.min.js"></script>

	<title><?=$title?><?php if (isset($subtitle)) : ?> | <?=$subtitle;?><?php endif; ?></title>

	<meta name="google-site-verification" content="aHIKXqj-Qz39M6qQOUdA9bvutMqyZ69dGy1xKtI5obE" />
</head>
<body>
	<nav class="top-bar" id="tnav" data-topbar role="navigation">
		<ul class="title-area">
    		<li class="name">
				<h1><a href="/"><img src="/assets/img/logo.png" alt="Stepmania Leaderboards" /></a></h1>
    		</li>
		</ul>

		<section class="top-bar-section">
			<!-- Right Nav Section -->
			<ul class="right">
				<?php if (!$logged_in) : ?>
					<li class="no-bg">
						<form action="/login" method="post">
							<input type="text" id="login_username" name="login_username" placeholder="Username" />

							<input type="password" id="login_pass" name="login_pass" placeholder="Password" />

							<input class="button" type="submit" value="Log In" />
						</form>
					</li>
					<li class="active red-cta"><a href="/register">Register</a></li>
					<li><a href="/forgot_pass">Forgot password?</a></li>
				<?php else : ?>
					<li class="has-dropdown">
						<a href="/profile/view/<?=$this->session->userdata('username');?>">Welcome, <?=$this->session->userdata('display_name');?></a>
						<ul class="dropdown">
							<li><a href="/profile/view/<?=$this->session->userdata('username');?>">View Profile</a><li>
							<li><a href="/profile/edit">Edit Profile</a><li>
							<li><a href="/logout">Log Out</a></li>
						</ul>
					</li>
					<li class="active"><a href="/scores/submit/">Submit Scores</a></li>
					<?php if ($user_level >= 2) : ?>
						<li class="has-dropdown ora-cta">
							<a href="#">Mod <span class="alert-count"><?=get_mod_alert_count();?></span></a>
							<ul class="dropdown">
								<?php /* <li><a href="#">Approve Scores <span class="alert-count">0</span></a></li>
								<li><a href="#">Verify Players</a></li>
								<li><a href="#">Ban Players</a></li> */ ?>
								<li><a href="/mod/add_pack">Add New Pack</a></li>
								<li><a href="/mod/rank_chart">Rank New Chart</a></li>
								<?php /* <li><a href="#">Add/Edit Resources</a></li>*/ ?>
								<li><a href="/mod/pending_scores">Pending Scores <span class="alert-count"><?=get_mod_alert_count_pending_scores();?></span></a></li>
								<li><a href="/mod/suggested_files">Suggested Charts <span class="alert-count"><?=get_mod_alert_count_suggested_charts();?></span></a></li>
								<li><a href="/mod/shoutboard">Mod Forum</a></li>
								<li><a href="/mod/mod_log">Mod Log</a></li>
							</ul>
						</li>
					<?php endif; ?>
					<?php if ($user_level >= 3) : ?>
						<li class="has-dropdown red-cta">
							<a href="#">Admin</a>
							<ul class="dropdown">
								<?php /* <li><a href="#">Edit Players</a></li>
								<li><a href="#">Permaban Players</a></li> */ ?>
								<li><a href="/admin/batch_recalculate">Batch Recalculate Files</a></li>
								<li><a href="/admin/batch_recalculate_scores">Batch Recalculate Scores</a></li>
								<li><a href="/admin/post_announcement">Post New Announcement</a></li>
								<li><a href="/admin/auto_type_test">Auto File Type Test</a></li>
							</ul>
						</li>
					<?php endif; ?>
				<?php endif; ?>
			</ul>

			<!-- Left Nav Section -->
			<ul class="left">
				<li class="has-dropdown">
					<a href="/leaderboards/overall">Leaderboards</a>
					<ul class="dropdown">
						<li><a href="/leaderboards/overall">Overall</a></li>
						<li><a href="/leaderboards/speed">Speed</a></li>
						<li><a href="/leaderboards/jumpstream">Jumpstream</a></li>
						<li><a href="/leaderboards/jack">Jack</a></li>
						<li><a href="/leaderboards/technical">Technical</a></li>
						<li><a href="/leaderboards/stamina">Stamina</a></li>
					</ul>
				</li>
				<li><a href="/difficulty_calculator">Difficulty Calculator</a></li>
				<li><a href="/packs">Packs</a></li>
				<li class="has-dropdown">
					<a href="/charts">Ranked Charts</a>
					<ul class="dropdown">
						<li><a href="/charts">View All</a></li>
						<?php if ($logged_in) : ?>
							<li><a href="/charts/suggest">Suggest New Charts</a></li>
						<?php endif; ?>
					</ul>
				</li>
				<li class="has-dropdown">
					<a href="#">Resources</a>
					<ul class="dropdown">
						<li><a href="/about">About</a></li>
						<li><a href="/about/faq">FAQ</a></li>
						<?php /*
						<li><a href="#">Noteskins</a></li>
						<li><a href="#">Themes</a></li>
						<li><a href="#">Clients</a></li>
						*/ ?>
					</ul>
				</li>
			</ul>
		</section>
	</nav>

	<div class="row" id="main">
		<div class="large-12 columns">
			<div id="main-content">
				<?php if (isset($subtitle)) : ?>
					<h2><?=$subtitle;?></h2>
				<?php endif; ?>
		    	<?=$yield;?>
			</div>
		</div>
	</div>
	<script>
	  (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
	  (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
	  m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
	  })(window,document,'script','//www.google-analytics.com/analytics.js','ga');

	  ga('create', 'UA-64216915-2', 'auto');
	  ga('send', 'pageview');

	</script>
	<script src="/assets/foundation/bower_components/foundation/js/foundation.min.js"></script>
	<script src="/assets/foundation/js/foundation.reveal.js"></script>
	<script src="/assets/foundation/js/confirm_with_reveal.min.js"></script>
	<script src="/assets/foundation/bower_components/foundation/js/foundation/foundation.topbar.js"></script>
	<script src="/assets/foundation/js/app.js?v=1.10"></script>
</body>
</html>

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
	    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
        <title>Ingress Inventory</title>
		<link href='http://fonts.googleapis.com/css?family=Coda:400,700' rel='stylesheet' type='text/css' />
	    <link href="css/bootstrap.css" rel='stylesheet' type='text/css'>
	    <link href="css/bootstrap-theme.css?<?php echo _VERSION_; ?>" rel='stylesheet' type='text/css'>
		<link href='js/fancybox/jquery.fancybox.css' rel="stylesheet" type='text/css' />
		<link href="js/exgrid/exgrid.css?<?php echo _VERSION_; ?>" rel="stylesheet" type="text/css" />
		
		<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.9.0/jquery.min.js"></script>
	    <script src="js/bootstrap.js" ></script>
		<script src="js/fancybox/jquery.fancybox.pack.js"></script>
		<script src="js/fancybox/helpers/jquery.fancybox-media.js"></script>
		<script src="js/highchart/highcharts.js"></script>
		<script src="js/highchart/themes/gray.js"></script>
		<script src="js/exgrid/modernizr.custom.js"></script>
		<script src="js/jquery.tinysort.js"></script>
		<script src="js/script.js?<?php echo _VERSION_; ?>"></script>
		
    </head>
    <body>
		<?php 
		
			if($_SERVER['HTTP_HOST'] != 'localhost'){
		?>




		<?php } ?>
<div class="container">
		<h1 class="hidden-xs text-center">
				<a class="title" href="index.php">Ingress Inventory </a>
		</h1>
		<div class="navbar navbar-default" role="navigation">
			<div class="navbar-header">
				<button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1">
					<span class="sr-only">Toglle navigation</span>
					<span class="icon-bar"></span>
					<span class="icon-bar"></span>
					<span class="icon-bar"></span>
				</button>
				<a class="navbar-brand visible-xs" href="index.php">Ingress Inventory</a>
			</div>
			<div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
				<ul class="nav navbar-nav">
					<li <?php echo !isSubmit('page')?  'class="active"':''; ?> ><a href="index.php">Home</a></li>
					<li <?php echo getValue('page')=='badges' ?  'class="active"':''; ?> ><a href="index.php?page=badges">Badges</a></li>
					<li <?php echo getValue('page')=='resonators' ?  'class="active"':''; ?> ><a href="index.php?page=resonators">Resonators</a></li>
					<li <?php echo getValue('page')=='weapons' ?  'class="active"':''; ?> ><a href="index.php?page=weapons">Weapons</a></li>
					<li <?php echo getValue('page')=='mods' ?  'class="active"':''; ?> ><a href="index.php?page=mods">Mods</a></li>
					<li <?php echo getValue('page')=='cubes' ?  'class="active"':''; ?> ><a href="index.php?page=cubes">Power Cubes</a></li>
					<li class="dropdown" <?php echo getValue('page')=='keys' ?  'class="active"':''; ?> >
						<a href="index.php?page=keys" class="dropdown-toggle" data-toggle="dropdown">Portal Keys <b class="caret"></b></a>
						<ul class="dropdown-menu">
							<li><a  href="index.php?page=keys&view=list">List View</a></li>
							<li><a  href="index.php?page=keys&view=map">Map View</a></li>
						</ul>

					</li>
					<li <?php echo getValue('page')=='media' ?  'class="active"':''; ?> ><a href="index.php?page=media">Media</a></li>
					<li class="dropdown <?php echo getValue('page')=='tools' ?  'active':''; ?>" >
						<a  href="#" class="dropdown-toggle" data-toggle="dropdown">Tools <b class="caret"></b></a>
						<ul class="dropdown-menu">
							<li><a href="index.php?page=tools&tool=redeem" class="">Redeem passcode</a></li>
							<li><a href="index.php?page=tools&tool=recycle" class="">Recycle items</a></li>
							<li><a href="index.php?page=tools&tool=agent" class="">Agent Info</a></li>
							<li><a href="index.php?page=tools&tool=score" class="">Regional score</a></li>
						</ul>


					</li>
				</ul>
			</div>

		</div>

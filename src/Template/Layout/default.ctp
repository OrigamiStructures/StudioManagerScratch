<?php
/**
 * 
 */

$cakeDescription = 'ClearStudio: Your Artwork\'s Lifeline';
?>
<!DOCTYPE html>
<html>
<head>
    <?= $this->Html->charset() ?>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
	<link href='https://fonts.googleapis.com/css?family=Pontano+Sans' rel='stylesheet' type='text/css'>
	<link href='https://fonts.googleapis.com/css?family=Open+Sans:400,700' rel='stylesheet' type='text/css'>
	<title>
        <?= $cakeDescription ?>:
        <?= $this->fetch('title') ?>
    </title>
    <?= $this->Html->meta('icon') ?>
    <?= $this->fetch('meta') ?>
	
	<?= $this->Html->css('artstack-response'); ?>
    <?= $this->Html->css('glyphicon.css') ?>
<!-- 	
	<?= $this->Html->script('../bower_components/jquery/dist/jquery.min.js'); ?> 
	<?= $this->Html->script('../bower_components/foundation-sites/dist/foundation.js'); ?> 
	<?= $this->Html->script('app.js'); ?>
	<?= $this->Html->script('testing'); ?>
    <?= $this->fetch('script') ?>

	<?= $this->Html->css('../bower_components/foundation-sites/dist/foundation.min.css'); ?>
    <?= $this->Html->css('app.css') ?>
    <?= $this->Html->css('prime.css') ?>
    <?= $this->fetch('css') ?>
-->
</head>
<body>
	<nav class="top-bar">
	  <!--<div class="top-bar-left">-->
		<?= $this->element('Menu/menu'); ?>
<!--	  </div>
	  <div class="top-bar-right">-->
		  <form id="search" action="/search" method="post">
			<ul class="menu">
			  <li><input placeholder="Search" name="search"></li>
			  <li><button form="search" type="submit" class="button tiny">Search</button></li>
			</ul>
		  </form>
	  <!--</div>-->
	</nav>
<!--	<nav class="breadcrumbs">
		<?= $this->Html->getCrumbList([], 'All Art'); ?>
	</nav>-->
	<?= $this->cell('StandingDisposition', [] , ['SystemState' => $SystemState]); ?>
	<?php // $this->element($this->DispositionTools->panel($standing_disposition) 
//			? 'Disposition/panel' 
//			: 'empty'); ?>
	
	<?= $this->Flash->render() ?>
    <section class="container clearfix">
        <?= $this->fetch('content') ?>
    </section>
    <footer>
		&nbsp;
    </footer>
</body>
</html>

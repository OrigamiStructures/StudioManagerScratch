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
    <title>
        <?= $cakeDescription ?>:
        <?= $this->fetch('title') ?>
    </title>
    <?= $this->Html->meta('icon') ?>
    <?= $this->fetch('meta') ?>
	
	<?= $this->Html->script('../bower_components/jquery/dist/jquery.min.js'); ?> 
	<?= $this->Html->script('../bower_components/foundation-sites/dist/foundation.min.js'); ?> 
	<?= $this->Html->script('app.js'); ?>
	<?= $this->Html->script('testing'); ?>
    <?= $this->fetch('script') ?>

	<?= $this->Html->css('../bower_components/foundation-sites/dist/foundation.min.css'); ?>
    <?= $this->Html->css('app.css') ?>
    <?= $this->Html->css('glyphicon.css') ?>
    <?= $this->Html->css('prime.css') ?>
    <?= $this->fetch('css') ?>
</head>
<body>
	<nav class="top-bar">
	  <div class="top-bar-left">
		<?= $this->element('Menu/menu'); ?>
<!--		<ul class="dropdown menu" data-dropdown-menu>
		  <li class="menu-text">ClearStudio</li>
		  <li class="has-submenu">
			<a href="#">One</a>
			<ul class="submenu menu vertical" data-submenu>
			  <li><a href="#">One</a></li>
			  <li><a href="#">Two</a></li>
			  <li><a href="#">Three</a></li>
			</ul>
		  </li>
		  <li><a href="#">Two</a></li>
		  <li><a href="#">Three</a></li>
		</ul>-->
	  </div>
	  <div class="top-bar-right">
		<ul class="menu">
		  <li><input type="search" placeholder="Search"></li>
		  <li><button type="button" class="button">Search</button></li>
		</ul>
	  </div>
	</nav>    
	<?= $this->Flash->render() ?>
    <section class="container clearfix">
        <?= $this->fetch('content') ?>
    </section>
    <footer>
    </footer>
</body>
</html>

<!DOCTYPE html>
<html>
	<head>
		<?php echo $this->Html->charset(); ?>
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<title>
			MO - Lighting design :
			<?php echo $title_for_layout; ?>
		</title>
		<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.0.0/jquery.min.js" type="text/javascript"></script>
		<?php
		echo $this->Html->meta('icon');

		echo $this->Html->script('bootstrap.min');
		echo $this->Html->css('bootstrap.min');
		echo $this->Html->css('font-awesome.min');

		echo $this->fetch('meta');
		echo $this->fetch('css');
		echo $this->fetch('script');
		?>
	</head>
	<body style="padding-top:65px;">

		<div class="navbar navbar-fixed-top navbar-inverse" role="navigation">
			<div class="container">
				<div class="navbar-header">
					<button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-ex1-collapse">
						<span class="sr-only">Toggle navigation</span>
						<span class="icon-bar"></span>
						<span class="icon-bar"></span>
						<span class="icon-bar"></span>
					</button>
					<a class="navbar-brand">superBake</a>
				</div>

				<div class="collapse navbar-collapse navbar-ex1-collapse">
					<ul class="nav navbar-nav">
						<!-- You can include menus here -->
						<!-- < ?php echo $this->element('menus/pages') ?> -->
						<!-- < ?php echo $this->element('menus/main') ?> -->
					</ul>
				</div>
			</div>
		</div>
		<div class="container">
			<div class="flashMessages">				
				<?php echo $this->Session->flash(); ?>
			</div>
		</div>
		<div class="container" id="content">
			<h1><?php echo $title_for_layout ?></h1>
			<?php echo $content_for_layout; ?>
		</div>
		<div class="container" id="footer">
			Here is a beautiful footer
		</div>
	</body>
</html>

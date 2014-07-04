<!DOCTYPE html>
<html>
	<head>
		<?php echo $this->Html->charset(); ?>
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<title>
			superBake:
			<?php echo $title_for_layout; ?>
		</title>
		<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.0.0/jquery.min.js" type="text/javascript"></script>
		<?php
		echo $this->Html->meta('icon');

		echo $this->Html->script('/sb/js/jquery-syntax/jquery.syntax.min');
		echo $this->Html->script('/sb/js/jquery.tableofcontents.min');
		echo $this->Html->script('/sb/js/bootstrap.min');

		echo $this->Html->css('/sb/css/bootstrap.min');
		echo $this->Html->css('/sb/css/font-awesome.min');
		echo $this->Html->css('/sb/css/additions');

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
						<?php
						echo $this->element('menus/main');
						?>
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
			<div class="row">
				<div class="col-lg-3">
					<div id="toc-affix"data-spy="affix-top" data-offset-top="0">
						<ol id="toc">
						</ol>
					</div>
				</div>
				<div class="col-lg-9">
					<?php
					echo $this->element('docs/doc_warning');
					?>
					<?php echo $content_for_layout; ?>
				</div>
			</div>

		</div>
		<script>
			// Enable tooltips
			$("[data-toggle='tooltip']").tooltip();

			// Syntax highlighting
			jQuery(function($) {
				$.syntax();
			});

			// TOC
			$(function() {
				$("#toc").tableOfContents(null,
								{
									startLevel: 1,
									levelClass: "toc-depth-%",
									depth: 4,
								}
				);
			});

			// Affix (TOC container)
			$('#toc-affix').affix({
				offset: {
					top: 0
					, bottom: function() {
						return (this.bottom = $('.bs-footer').outerHeight(true))
					}
				}
			})
		</script>
		<?php echo $this->element('Sb.footer');?>
	</body>
</html>

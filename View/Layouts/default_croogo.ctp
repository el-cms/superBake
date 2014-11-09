<?php
// Layout for Croogo
$this->extend('admin');
// Extra CSS/JS
echo $this->Html->script('/sb/js/jquery-syntax/jquery.syntax.min');
echo $this->Html->script('/sb/js/jquery.tableofcontents.min');
echo $this->Html->css('/sb/css/font-awesome.min');
echo $this->Html->css('/sb/css/additions');
echo $this->Html->css('/sb/css/croogo_additions');
?>

<div class="row-fluid" class="bottom_space">
	<div class="col-sm-12">
		<?php
		if (isset($routingPrefixError) && $routingPrefixError === 1):
			?>
			<div class="alert alert-error">
				<i class="icon-warning-sign icon-2x pull-left"></i>
				<?php echo __d('sb', 'The amount of routing prefixes defined in your <code>core.php</code> is damn too high ! (or too low) Please check your routing prefixes.') ?>
			</div>
			<?php
		endif;
		echo $this->fetch('content')
		?>
		<script>
			// Enable tooltips
			$("[data-toggle='tooltip']").tooltip();

			// Syntax highlighting
			jQuery(function ($) {
				$.syntax();
			});

			// TOC
			$(function () {
				$("#toc").tableOfContents(null,
								{
									startLevel: 1,
									levelClass: "toc-depth-%",
									depth: 4
								}
				);
			});

			// Affix (TOC container)
			$('#toc-affix').affix({
				offset: {
					top: 0
					, bottom: function () {
						return (this.bottom = $('.bs-footer').outerHeight(true));
					}
				}
			});
		</script>
	</div>
</div>
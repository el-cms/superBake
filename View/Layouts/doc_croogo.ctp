<?php
// Layout for Croogo
$this->extend('admin');
// Extra CSS/
echo $this->Html->script('/sb/js/jquery-syntax/jquery.syntax.min');
echo $this->Html->script('/sb/js/jquery.tableofcontents.min');
//echo $this->Html->script('/sb/js/bootstrap.min');
echo $this->Html->css('/sb/css/additions');
echo $this->Html->css('/sb/css/croogo_additions');
?>
<!-- Toolbar -->
<div class="toolbar-sb" style="margin-bottom:20px;">
	<div class="row-fluid">
		<div class="col-sm-9 col-sm-offset-3">
			<strong><?php echo __d('sb', 'Quick documentation access') ?></strong>

			<div class="btn-group">
				<a href="#" class="btn dropdown-toggle btn-small" data-toggle="dropdown"><i class="icon-folder-open"></i> <?php echo __d('sb', 'Configuration file') ?> <span class="caret"></span></a>
				<ul class="dropdown-menu">
					<li><?php echo $this->Html->link('<i class="icon-file"></i>&nbsp;' . __d('sb', 'Basics'), array('controller' => 'docs', 'action' => 'display', 'help_config.md'), array('escape' => false)); ?></li>
					<li><?php echo $this->Html->link('<i class="icon-file"></i>&nbsp;' . __d('sb', 'General section'), array('controller' => 'docs', 'action' => 'display', 'help_config_general.md'), array('escape' => false)); ?></li>
					<li><?php echo $this->Html->link('<i class="icon-file"></i>&nbsp;' . __d('sb', 'Defaults section'), array('controller' => 'docs', 'action' => 'display', 'help_config_defaults.md'), array('escape' => false)); ?></li>
					<li><?php echo $this->Html->link('<i class="icon-file"></i>&nbsp;' . __d('sb', 'Plugins section'), array('controller' => 'docs', 'action' => 'display', 'help_config_plugins.md'), array('escape' => false)); ?></li>
					<li><?php echo $this->Html->link('<i class="icon-file"></i>&nbsp;' . __d('sb', 'Plugins: models'), array('controller' => 'docs', 'action' => 'display', 'help_config_plugins_models.md'), array('escape' => false)); ?></li>
					<li><?php echo $this->Html->link('<i class="icon-file"></i>&nbsp;' . __d('sb', 'Plugins: controllers'), array('controller' => 'docs', 'action' => 'display', 'help_config_plugins_controllers.md'), array('escape' => false)); ?></li>
					<li><?php echo $this->Html->link('<i class="icon-file"></i>&nbsp;' . __d('sb', 'Plugins: views'), array('controller' => 'docs', 'action' => 'display', 'help_config_plugins_views.md'), array('escape' => false)); ?></li>
					<li><?php echo $this->Html->link('<i class="icon-file"></i>&nbsp;' . __d('sb', 'Plugins: menus'), array('controller' => 'docs', 'action' => 'display', 'help_config_plugins_menus.md'), array('escape' => false)); ?></li>
					<li><?php echo $this->Html->link('<i class="icon-file"></i>&nbsp;' . __d('sb', 'Plugins: files'), array('controller' => 'docs', 'action' => 'display', 'help_config_plugins_files.md'), array('escape' => false)); ?></li>
					<li><?php echo $this->Html->link('<i class="icon-file"></i>&nbsp;' . __d('sb', 'Theme section'), array('controller' => 'docs', 'action' => 'display', 'help_config_theme.md'), array('escape' => false)); ?></li>
				</ul>
			</div>

			<div class="btn-group">
				<a href="#" class="btn dropdown-toggle btn-small" data-toggle="dropdown"><i class="icon-folder-open"></i> <?php echo __d('sb', 'Template') ?> <span class="caret"></span></a>
				<ul class="dropdown-menu">
					<li><?php echo $this->Html->link('<i class="icon-file"></i>&nbsp;' . __d('sb', 'Basics'), array('controller' => 'docs', 'action' => 'display', 'help_templates.md'), array('escape' => false)); ?></li>
					<li class="divider"></li>
					<?php
					foreach ($templateLinks as $cat => $links) :
						?>
						<li class="dropdown-submenu">
							<a href="#"><i class="icon-folder-open"></i> <?php echo $cat ?></a>
							<ul class="dropdown-menu">
								<?php
								foreach ($links as $link):
									echo '<li>' . $this->Html->link('<i class="icon-file"></i>&nbsp;' . $link['title'], array('controller' => 'docs', 'action' => 'display', 'docs' => 'template', $link['file']), array('escape' => false)) . '</li>';
								endforeach;
								?>
							</ul>
						</li>
						<?php
					endforeach;
					?>
				</ul>
			</div>
		</div>
	</div>
</div>


<!-- Content -->
<div class="row-fluid">
	<div class="col-sm-3">
		<!-- TOC -->
		<div id="toc-affix" data-spy="affix-top" data-offset-top="0">
			<ol id="toc">
			</ol>
		</div>
	</div>

	<!-- Doc content -->
	<div class="col-sm-9">
		<?php
		echo $this->element('docs/doc_warning');
		echo $this->fetch('content');
		?>
	</div>
</div>

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

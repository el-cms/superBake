<!-- See the site -->
<li><?php echo $this->Html->link('<i class="icon-eye-open"></i>&nbsp;' . __d('sb', 'See the site'), '/', array('escape' => false)); ?></li>
<!-- superBake Home -->
<li><?php echo $this->Html->link('<i class="icon-home"></i>&nbsp;' . __d('sb', 'Home'), array('plugin' => null, 'sb' => null, 'controller' => 'sb', 'action' => 'index'), array('escape' => false)); ?></li>
<li class="dropdown">
	<a class="dropdown-toggle" data-toggle="dropdown" href="#"><i class="icon-folder-open"></i> <?php echo __d('sb', 'Tests'); ?><span class="caret"></span></a>
	<ul class="dropdown-menu">
		<li><?php echo $this->Html->link('<i class="icon-cog"></i>&nbsp;' . __d('sb', 'Check config file'), array('controller' => 'sb', 'action' => 'check'), array('escape' => false)); ?></li>
		<li><?php echo $this->Html->link('<i class="icon-cog"></i>&nbsp;' . __d('sb', 'Application summary'), array('controller' => 'sb', 'action' => 'tree'), array('escape' => false)); ?></li>
		<li role="presentation" class="divider"></li>
		<li role="presentation" class="dropdown-header"><i class="icon-cogs"></i> <?php echo __d('sb', 'Function test') ?></li>
		<li><?php echo $this->Html->link('<i class="icon-cog"></i>&nbsp;' . __d('sb', 'Sbc::updateArray()'), array('controller' => 'sb', 'action' => 'arraymerge'), array('escape' => false)); ?></li>
	</ul>
</li>
<!-- Docs -->
<li class="dropdown">
	<a class="dropdown-toggle" data-toggle="dropdown" href="#"><i class="icon-question-sign"></i> <?php echo __d('sb', 'Help'); ?><span class="caret"></span></a>
	<ul class="dropdown-menu">
		<!-- Help home -->
		<li><?php echo $this->Html->link('<i class="icon-file"></i>&nbsp;' . __d('sb', 'Help home'), array('controller' => 'docs', 'action' => 'display', 'help.md'), array('escape' => false)); ?></li>
		<li><?php echo $this->Html->link('<i class="icon-file"></i>&nbsp;' . __d('sb', 'About this plugin'), array('controller' => 'docs', 'action' => 'display', 'about.md'), array('escape' => false)); ?></li>

		<li role="presentation" class="divider"></li>

		<!-- Config -->
		<li class="dropdown-submenu">
			<a href="#"><i class="icon-folder-open"></i> <?php echo __d('sb', 'Configuration file') ?></a>
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
		</li>

		<!-- Templates -->
		<li class="dropdown-submenu">
			<a href="#"><i class="icon-folder-open"></i> <?php echo __d('sb', 'Template') ?></a>
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
								echo '<li>' . $this->Html->link('<i class="icon-file"></i>&nbsp;' . $link['title'], array('controller' => 'docs', 'action' => 'display', 'docs' =>'template', $link['file']), array('escape' => false)) . '</li>';
							endforeach;
							?>
						</ul>
					</li>
					<?php
				endforeach;
				?>
			</ul>
		</li>

		<!-- The shell -->
		<li class="dropdown-submenu">
			<a href="#"><i class="icon-folder-open"></i> <?php echo __d('sb', 'Shell') ?></a>
			<ul class="dropdown-menu">
				<li><?php echo $this->Html->link('<i class="icon-file"></i>&nbsp;' . __d('sb', 'Basics'), array('controller' => 'docs', 'action' => 'display', 'help_shell.md'), array('escape' => false)); ?></li>
				<li><?php echo $this->Html->link('<i class="icon-file"></i>&nbsp;' . __d('sb', 'Command line'), array('controller' => 'docs', 'action' => 'display', 'help_shell_commands.md'), array('escape' => false)); ?></li>
				<li><?php echo $this->Html->link('<i class="icon-file"></i>&nbsp;' . __d('sb', 'Extend the shell'), array('controller' => 'docs', 'action' => 'display', 'help_shell_extend.md'), array('escape' => false)); ?></li>
			</ul>
		</li>
	</ul>
</li>
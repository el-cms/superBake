<li><?php echo $this->Html->link('<i class="icon-eye-open"></i>&nbsp;' . __d('sb', 'See the site'), '/', array('escape' => false)); ?></li>
<li><?php echo $this->Html->link('<i class="icon-home"></i>&nbsp;' . __d('sb', 'Home'), array('plugin' => null, 'sb' => null, 'controller' => 'sb', 'action' => 'index'), array('escape' => false)); ?></li>
<li class="dropdown">
	<a class="dropdown-toggle" data-toggle="dropdown" href="#"><i class="icon-folder-open"></i> <?php echo __d('sb', 'Tests'); ?><span class="caret"></span></a>
	<ul class="dropdown-menu">
		<li><?php echo $this->Html->link('<i class="icon-folder-close"></i>&nbsp;' . __d('sb', 'Check config file'), array('controller' => 'sb', 'action' => 'check'), array('escape' => false)); ?></li>
		<li><?php echo $this->Html->link('<i class="icon-folder-close"></i>&nbsp;' . __d('sb', 'Application summary'), array('controller' => 'sb', 'action' => 'tree'), array('escape' => false)); ?></li>
		<li role="presentation" class="divider"></li>
		<li role="presentation" class="dropdown-header"><i class="icon-cog"></i> <?php echo __d('sb', 'Function test') ?></li>
		<li><?php echo $this->Html->link('<i class="icon-folder-close"></i>&nbsp;' . __d('sb', 'Sbc::updateArray()'), array('controller' => 'sb', 'action' => 'arraymerge'), array('escape' => false)); ?></li>
	</ul>
</li>
<li class="dropdown">
	<a class="dropdown-toggle" data-toggle="dropdown" href="#"><i class="icon-question-sign"></i> <?php echo __d('sb', 'Help'); ?><span class="caret"></span></a>
	<ul class="dropdown-menu">
		<!-- Help home -->
		<li><?php echo $this->Html->link('<i class="icon-folder-close"></i>&nbsp;' . __d('sb', 'Help home'), array('controller' => 'docs', 'action' => 'display', 'help'), array('escape' => false)); ?></li>
		<li><?php echo $this->Html->link('<i class="icon-folder-close"></i>&nbsp;' . __d('sb', 'About this plugin'), array('controller' => 'docs', 'action' => 'display', 'about'), array('escape' => false)); ?></li>

		<li role="presentation" class="divider"></li>

		<!-- Config -->
		<li class="dropdown-submenu">
			<a href="#"><i class="icon-cog"></i> <?php echo __d('sb', 'Configuration file') ?></a>
			<ul class="dropdown-menu">
				<li><?php echo $this->Html->link('<i class="icon-folder-close"></i>&nbsp;' . __d('sb', 'Basics'), array('controller' => 'docs', 'action' => 'display', 'help_config'), array('escape' => false)); ?></li>
				<li><?php echo $this->Html->link('<i class="icon-folder-close"></i>&nbsp;' . __d('sb', 'General section'), array('controller' => 'docs', 'action' => 'display', 'help_config_general'), array('escape' => false)); ?></li>
				<li><?php echo $this->Html->link('<i class="icon-folder-close"></i>&nbsp;' . __d('sb', 'Defaults section'), array('controller' => 'docs', 'action' => 'display', 'help_config_defaults'), array('escape' => false)); ?></li>
				<li><?php echo $this->Html->link('<i class="icon-folder-close"></i>&nbsp;' . __d('sb', 'Plugins section'), array('controller' => 'docs', 'action' => 'display', 'help_config_plugins'), array('escape' => false)); ?></li>
				<li><?php echo $this->Html->link('<i class="icon-folder-close"></i>&nbsp;' . __d('sb', 'Plugins: models'), array('controller' => 'docs', 'action' => 'display', 'help_config_plugins_models'), array('escape' => false)); ?></li>
				<li><?php echo $this->Html->link('<i class="icon-folder-close"></i>&nbsp;' . __d('sb', 'Plugins: controllers'), array('controller' => 'docs', 'action' => 'display', 'help_config_plugins_controllers'), array('escape' => false)); ?></li>
				<li><?php echo $this->Html->link('<i class="icon-folder-close"></i>&nbsp;' . __d('sb', 'Plugins: views'), array('controller' => 'docs', 'action' => 'display', 'help_config_plugins_views'), array('escape' => false)); ?></li>
				<li><?php echo $this->Html->link('<i class="icon-folder-close"></i>&nbsp;' . __d('sb', 'Plugins: menus'), array('controller' => 'docs', 'action' => 'display', 'help_config_plugins_menus'), array('escape' => false)); ?></li>
				<li><?php echo $this->Html->link('<i class="icon-folder-close"></i>&nbsp;' . __d('sb', 'Plugins: files'), array('controller' => 'docs', 'action' => 'display', 'help_config_plugins_files'), array('escape' => false)); ?></li>
			</ul>
		</li>

		<!-- Templates -->
		<li class="dropdown-submenu">
			<a href="#"><i class="icon-cog"></i> <?php echo __d('sb', 'Templates') ?></a>
			<ul class="dropdown-menu">
				<li><?php echo $this->Html->link('<i class="icon-folder-close"></i>&nbsp;' . __d('sb', 'Basics'), array('controller' => 'docs', 'action' => 'display', 'help_templates'), array('escape' => false)); ?></li>
				<li><?php echo $this->Html->link('<i class="icon-folder-close"></i>&nbsp;' . __d('sb', 'Models'), array('controller' => 'docs', 'action' => 'display', 'help_templates_models'), array('escape' => false)); ?></li>
				<li><?php echo $this->Html->link('<i class="icon-folder-close"></i>&nbsp;' . __d('sb', 'Controllers'), array('controller' => 'docs', 'action' => 'display', 'help_templates_controllers'), array('escape' => false)); ?></li>
				<li><?php echo $this->Html->link('<i class="icon-folder-close"></i>&nbsp;' . __d('sb', 'Views'), array('controller' => 'docs', 'action' => 'display', 'help_templates_views'), array('escape' => false)); ?></li>
				<li><?php echo $this->Html->link('<i class="icon-folder-close"></i>&nbsp;' . __d('sb', 'Menus'), array('controller' => 'docs', 'action' => 'display', 'help_templates_menus'), array('escape' => false)); ?></li>
				<li><?php echo $this->Html->link('<i class="icon-folder-close"></i>&nbsp;' . __d('sb', 'Files'), array('controller' => 'docs', 'action' => 'display', 'help_templates_files'), array('escape' => false)); ?></li>
			</ul>
		</li>

		<!-- The shell -->
		<li class="dropdown-submenu">
			<a href="#"><i class="icon-cog"></i> <?php echo __d('sb', 'Shell') ?></a>
			<ul class="dropdown-menu">
				<li><?php echo $this->Html->link('<i class="icon-folder-close"></i>&nbsp;' . __d('sb', 'Basics'), array('controller' => 'docs', 'action' => 'display', 'help_shell'), array('escape' => false)); ?></li>
				<li><?php echo $this->Html->link('<i class="icon-folder-close"></i>&nbsp;' . __d('sb', 'Command line'), array('controller' => 'docs', 'action' => 'display', 'help_shell_commands'), array('escape' => false)); ?></li>
				<li><?php echo $this->Html->link('<i class="icon-folder-close"></i>&nbsp;' . __d('sb', 'Extend the shell'), array('controller' => 'docs', 'action' => 'display', 'help_shell_extend'), array('escape' => false)); ?></li>
			</ul>
		</li>
	</ul>
</li>
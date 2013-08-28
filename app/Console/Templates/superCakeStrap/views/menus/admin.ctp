<?php

$pluginMenu = array();
// Grouping menu items by plugins/Controllers/prefixes
foreach ($Menu as $menuItem) {
// Checking for base controllers
	if ($menuItem['plugin'] == null) {
		$currentPlugin = $this->projectConfig['general']['appBase'];
	} else { // other plugins
		$currentPlugin = $menuItem['plugin'];
	}

	$pluginMenu[$currentPlugin][$menuItem['controller']][$menuItem['prefix']][] = $menuItem['action'];
}
// Acls menu
echo "<li class=\"dropdown\">\n";
echo "\t<a class=\"dropdown-toggle\" data-toggle=\"dropdown\" href=\"#\"><i class=\"icon-shield\"></i> <?php echo __('Acls');?> <span class=\"caret\"></span></a>";
echo "\t<ul class=\"dropdown-menu\">\n";
echo "\t\t<li class=\"dropdown-header\"><i class=\"icon-cog\"></i> <?php echo __('Actions:') ?></li>\n";
echo "\t\t<li><?php echo \$this->Html->link('<i class=\"icon-refresh\"></i> '.__d('acl', 'Sync actions ACOs'), array('plugin' => 'acl', 'admin' => 'admin', 'controller' => 'acos', 'action' => 'synchronize' ), array('escape' => false));?></li>\n";
echo "\t\t<li><?php echo \$this->Html->link('<i class=\"icon-remove\"></i> ' . __d('acl', 'Clear actions ACOs'), array('plugin' => 'acl', 'admin' => 'admin', 'controller' => 'acos', 'action' => 'empty_acos' ), array('escape' => false));?></li>\n";
echo "\t\t<li><?php echo \$this->Html->link('<i class=\"icon-wrench\"></i> ' . __d('acl', 'Build actions ACOs'), array('plugin' => 'acl', 'admin' => 'admin', 'controller' => 'acos', 'action' => 'build_acl' ), array('escape' => false));?></li>\n";
echo "\t\t<li><?php echo \$this->Html->link('<i class=\"icon-scissors\"></i> ' . __d('acl', 'Prune actions ACOs'), array('plugin' => 'acl', 'admin' => 'admin', 'controller' => 'acos', 'action' => 'prune_acos' ), array('escape' => false));?></li>\n";
echo "\t\t<li class=\"dropdown-header\"><i class=\"icon-cog\"></i> <?php echo __('Permissions:') ?></li>\n";
echo "\t\t<li><?php echo \$this->Html->link('<i class=\"icon-refresh\"></i> ' . __d('acl', 'Sync missing AROs'), array('plugin' => 'acl', 'admin' => 'admin', 'controller' => 'aros', 'action' => 'check' ), array('escape' => false));?></li>\n";
echo "\t\t<li><?php echo \$this->Html->link('<i class=\"icon-user\"></i> ' . __d('acl', 'Users roles'), array('plugin' => 'acl', 'admin' => 'admin', 'controller' => 'aros', 'action' => 'users' ), array('escape' => false));?></li>\n";
echo "\t\t<li><?php echo \$this->Html->link('<i class=\"icon-group\"></i> ' . __d('acl', 'Roles permissions'), array('plugin' => 'acl', 'admin' => 'admin', 'controller' => 'aros', 'action' => 'ajax_role_permissions' ), array('escape' => false));?></li>\n";
echo "\t\t<li><?php echo \$this->Html->link('<i class=\"icon-user\"></i> ' . __d('acl', 'Users permissions'), array('plugin' => 'acl', 'admin' => 'admin', 'controller' => 'aros', 'action' => 'user_permissions' ), array('escape' => false));?></li>\n";
echo "\t\t<li class=\"dropdown-header\"><i class=\"icon-cog\"></i> <?php echo __('Help:') ?></li>\n";
echo "\t\t<li><?php echo \$this->Html->link('<i class=\"icon-question-sign\"></i> ' . __d('acl', 'Help'), array('plugin' => 'acl', 'admin' => 'admin', 'controller' => 'acl', 'action' => 'index' ), array('escape' => false));?></li>\n";
echo "\t</ul>\n";
echo "</li>\n";
foreach ($pluginMenu as $plugin => $controllers) {
	// Opening group
	echo "<li class=\"dropdown\">\n";
	// Button
	echo "\t<a class=\"dropdown-toggle\" data-toggle=\"dropdown\" href=\"#\">";
	if ($plugin == $projectConfig['general']['appBase']) {
		echo "<i class=\"icon-cog\"></i> <?php echo __('Misc');?>";
	} else {
		switch ($plugin) {
			case 'blog':
				$icon = 'book';
				break;
			case 'gallery';
				$icon = 'picture';
				break;
			case 'licenses':
				$icon = 'tags';
				break;
			default:
				$icon = 'cog';
				break;
		}
		echo "<i class=\"icon-$icon\"></i> <?php echo " . $this->display(Inflector::humanize(Inflector::underscore($plugin))) . ";?>";
	}
	echo "<span class=\"caret\"></span></a>\n";
	// Dropdown
	echo "\t<ul class=\"dropdown-menu\">\n";
	foreach ($controllers as $controller => $prefixes) {
		echo "\t\t<li class=\"dropdown-header\"><i class=\"icon-cog\"></i> <?php echo " . $this->display(Inflector::humanize(Inflector::underscore($controller)) . ':') . " ?></li>\n";
		foreach ($prefixes as $prefix => $actions) {
			foreach ($actions as $k => $action) {
				switch ($action) {
					case 'index' :
						$title = 'List '. strtolower(Inflector::humanize(Inflector::underscore($controller)));
						$icon = 'list';
						break;
					case 'add' :
						$title = 'New ' . strtolower(Inflector::humanize(Inflector::underscore(Inflector::singularize($controller))));
						$icon = 'plus';
						break;
					default:
						$title = ucfirst("$controller - $action");
						$icon = 'cog';
						break;
				}
//				if (!empty($title)) {
//				$title.=Inflector::humanize(Inflector::underscore($controller));
//				} else {
//					$title = Inflector::humanize(Inflector::underscore(Inflector::singularize($controller) . ucfirst($action)));
//				}
				//Prefix
				if ($prefix == 'public') {
					$linkPrefix = 'null';
				} else {
					$linkPrefix = "'$prefix'";
				}
				//Plugin
				if ($plugin == $projectConfig['general']['appBase']) {
					$linkPlugin = 'null';
				} else {
					$linkPlugin = "'$plugin'";
				}
				// finally, the link
				if (!in_array(strtolower($action), array('view', 'delete', 'edit'))) {
					echo "\t\t" . '<li><?php echo $this->Html->link(\'<i class="icon-' . $icon . '"></i>&nbsp;[' . ucfirst(substr($prefix, 0, 3)) . '] \' . __(\'' . $title . '\'), array(\'plugin\' => ' . $linkPlugin . ', \'admin\' => ' . $linkPrefix . ', \'controller\' => \'' . $controller . '\', \'action\' => \'' . $action . '\'), array(\'escape\'=>false));?></li>' . "\n";
				}
			}
		}
	}
	echo "\t\t</ul>\n";
	echo "</li>\n";
}

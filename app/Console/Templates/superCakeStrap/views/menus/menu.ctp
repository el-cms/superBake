<?php

/**
 * Options:
 * - removedControllers: array of controllers to skip
 * - removedPlugins: array of plugins to skip
 * - onlyActions: array of actions to keep
 * - displayActionName: bool, default true, display or not the action name in the link title
 */
/*
 * Options
 */
// Removed controllers
if (!isset($removedControllers) || !is_array($removedControllers)) {
	$removedControllers = array();
}

// Removed plugins
if (!isset($removedPlugins) || !is_array($removedPlugins)) {
	$removedPlugins = array();
}

// Actions to keep
if (!isset($onlyActions) || !is_array($onlyActions)) {
	$onlyActions = array();
}

// Display Actions name
if (!isset($displayActionName)) {
	$displayActionName = true;
}

//
// Menu generation
//
$pluginMenu = array();
// Grouping menu items by plugins/Controllers/prefixes/actions
foreach ($Menu as $menuItem) {
// Checking for base controllers
	if ($menuItem['plugin'] == null) {
		$currentPlugin = $this->projectConfig['general']['appBase'];
	} else { // other plugins
		$currentPlugin = $menuItem['plugin'];
	}

	$pluginMenu[$currentPlugin][$menuItem['controller']][$menuItem['prefix']][] = $menuItem['action'];
}
foreach ($pluginMenu as $plugin => $controllers) {
	if (!in_array($plugin, $removedPlugins)) {
		$groupCount = 0;
		$links = '';
		// Controllers in plugin
		foreach ($controllers as $controller => $prefixes) {
			if (!in_array($controller, $removedControllers)) {
				foreach ($prefixes as $prefix => $actions) {
					foreach ($actions as $action) {
						if (in_array($action, $onlyActions)) {
							$groupCount++;
							$title = '';
							// Action name
							$title = ($displayActionName === true) ? "$action " : Inflector::humanize(Inflector::underscore($controller));
							
							// Link icon
							switch ($plugin) {
								case 'blog':
									$icon = 'book';
									// Additionnaly, you can add a title here to replace the plugin name
									// $title='Custom title';
									break;
								case 'gallery';
									$icon = 'picture';
									break;
								case 'licenses':
									$icon = 'tags';
									break;
								case 'tags':
									$icon = 'tags';
									break;
								case 'projects':
									$icon = 'folder-closed';
									break;
								case 'likes':
									$icon = 'heart';
									break;
								case 'links':
									$icon = 'link';
									break;
								default:
									$icon = 'cog';
									break;
							}
							// Creating link
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
								$links.= "\t\t" . '<li><?php echo $this->Html->link(\'<i class="icon-' . $icon . ' icon-white"></i>&nbsp;\' . __(\'' . ucfirst($title) . '\'), array(\'plugin\' => ' . $linkPlugin . ', \'admin\' => ' . $linkPrefix . ', \'controller\' => \'' . $controller . '\', \'action\' => \'' . $action . '\'), array(\'escape\'=>false));?></li>' . "\n";
							}
						}
					}
				}
			}
		}
		// Then, if we have multiple items for this plugin, we create a dropdown
		if ($groupCount > 1) {
			echo "<li class=\"dropdown\">\n\t<a class=\"dropdown-toggle\" data-toggle=\"dropdown\" href=\"#\">";
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
					case 'tags':
						$icon = 'tags';
						break;
					case 'projects':
						$icon = 'folder-open';
						break;
					case 'likes':
						$icon = 'heart';
						break;
					case 'links':
						$icon = 'link';
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
		}
		// Display the links
		echo $links;
		// We close the dropdown here
		if ($groupCount > 1) {
			echo "\t\t</ul>\n";
			echo "</li>\n";
		}
	}
}
<?php

include $themePath . "common/headers-files.ctp";

echo "\n<?php\n";
// Routes options:
if (empty($routes) || !is_array($routes)) {

	$this->speak('If you want to create routes, you should at least define one...', 'warning', 0);
} else {
	$options = null;
	foreach ($routes as $route => $path) {
		if (!isset($path['prefix'])) {
			$path['prefix'] = null;
		}
		$options = null;
		// Preparing target:
		if (is_array($path)) {
			// Options
			$i = 0;
			if (!empty($path['named'])) {
				foreach ($path['named'] as $k => $v) {
					if ($i > 0) {
						$options.=', ';
					}
					$options.="'$k'=>'$v'";
					$i++;
				}
			}
			if (!empty($path['params'])) {
				if ($i > 0) {
					$options.=', ';
				}
				$i = 0;
				foreach ($path['params'] as $param) {
					if ($i > 0) {
						$options.=', ';
					}
					$options.="'$param'";
					$i++;
				}
			}
			$target = $this->url($path['action'], $path['controller'], $path['prefix'], $options, false, $path['plugin']);
		} else {
			$target = "'$path";
		}
		echo "\n\nRouter::connect('$route',$target);\n";
	}
}

echo "\n/**\n * Load all plugin routes. See the CakePlugin documentation on\n * how to customize the loading of plugin routes.\n */\n";
echo "CakePlugin::routes();\n\n";

if ($useCakeSystem) {
	echo "/**\n * Load the CakePHP default routes. Only remove this if you do not want to use\n * the built-in default routes.\n */\n";
	echo "require CAKE . 'Config' . DS . 'routes.php';\n\n";
}
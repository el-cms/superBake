<?php

include $themePath . "common/headers-files.ctp";

// Language list for lang support
$languages = $this->Sbc->getConfig('theme.language.available');
$defaultLanguage = $this->Sbc->getConfig('theme.language.fallback');
// Remove default language from list
unset($languages[array_search($defaultLanguage, $languages)]);

echo "\n<?php\n";
// No routes ?
if (empty($routes) || !is_array($routes)) {

	$this->speak('If you want to create routes, you should at least define one...', 'warning', 0);
} else {
	foreach ($routes as $route => $path) {
		if (!isset($path['prefix'])) {
			$path['prefix'] = null;
		}
		// Resetting vars
		$options = null;


		// Passing language
		if ($this->Sbc->getConfig('theme.language.useLanguages') === true) {
			$path['regexps']['pass'][] = 'language';
		}

		// Prefixes:
		$prefixes = $this->Sbc->getPrefixesList();
		unset($prefixes[array_search('public', $prefixes)]);
		if (!empty($prefixes)) {
			$path['regexps']['prefix'] = implode('|', $prefixes);
		}

		//
		// Regexps
		$regexps = $path['regexps'];

		//
		// Preparing target:
		if (is_array($path)) {

			//
			// Route options
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
			if (empty($path['action']) && empty($path['controller']) && empty($path['prefix']) && empty($path['plugin'])) {
				$target = 'array()';
			} else {
				$target = $this->url($path['action'], $path['controller'], $path['prefix'], $options, false, $path['plugin']);
			}
		} else {
			$target = "'$path";
		}

		//
		//Language support
		if ($this->Sbc->getConfig('theme.language.useLanguages') === true) {
			$regexpsLang = $regexps;
			$regexpsLang['language'] = implode('|', $languages);
			$regexpsStringLang = ', ' . $this->displayArray($regexpsLang);
			echo "\n\nRouter::connect('/:language$route',$target$regexpsStringLang);";
		}

		$regexpsString = ', ' . $this->displayArray($regexps);

		echo "\nRouter::connect('$route',$target$regexpsString);";
	}
}

echo "\n/**\n * Load all plugin routes. See the CakePlugin documentation on\n * how to customize the loading of plugin routes.\n */\n";
echo "CakePlugin::routes();\n\n";

if ($useCakeSystem) {
	echo "/**\n * Load the CakePHP default routes. Only remove this if you do not want to use\n * the built-in default routes.\n */\n";
	echo "require CAKE . 'Config' . DS . 'routes.php';\n\n";
}
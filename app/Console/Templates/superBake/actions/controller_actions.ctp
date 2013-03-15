<?php

/**
 * Controllers actions template for EL-CMS baking
 * 
 * This file is used during controllers generation and adds basiic CRUD actions
 * to the controllers.
 * 
 * @copyright     Copyright 2012, Manuel Tancoigne (http://experimentslabs.com)
 * @author        Manuel Tancoigne <m.tancoigne@gmail.com>
 * @link          http://experimentslabs.com Experiments Labs
 * @package       EL-CMS/Console/Controllers
 * @license       GPL v3 (http://www.gnu.org/licenses/gpl.html)
 * @todo hacks/functions.php may be not included here... Must search in the console script dir.
 * 
 * ----
 * 
 *  This file is part of EL-CMS.
 *
 *  EL-CMS is free software: you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation, either version 3 of the License, or
 *  (at your option) any later version.
 * 
 *  EL-CMS is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 * 
 *
 *  You should have received a copy of the GNU General Public License
 *  along with EL-CMS. If not, see <http://www.gnu.org/licenses/> 
 */
/*
 * Defining some vars
 */

/**
 * @var string Plural controller name, CamelCased
 */
$controllerName = Inflector::camelize($pluralHumanName);

/**
 * @var string pluginName Real plugin name (without the dot)
 */
$pluginName = '';
if (!empty($plugin)) {
	$pluginName = rtrim($plugin, '.');
}

/**
 * Step by step
 */
if (empty($plugin)) {
	$pluginParams = $projectConfig['notPlugin'];
} else {
	$pluginParams = $projectConfig['plugins'][$pluginName];
}

if (empty($admin)) {
	$prefix = 'public';
} else {
	$prefix = rtrim($admin, '_');
}

/*
 * Load whitelists.
 * The project whiteLists and plugin whitelists are merged. This represents the
 * actions to bake.
 * Plugin blacklists override the whiteList
 */
$prefixWhiteList = array_merge($projectConfig['defaultWhiteList'][$prefix], $pluginParams['models'][$controllerName]['whiteList'][$prefix]);
foreach ($prefixWhiteList as $a => $path) {
	//Checking plugin's blacklist
	if (!in_array($a, $pluginParams['models'][$controllerName]['blackList'][$prefix])) {
		// Creating the snippet path. If $path is not an array, the path is created
		// as snippets/controller/action.ctp.
		if (is_null($path)) {
			$snippetFile = dirname(__FILE__) . DS . 'snippets' . DS . $controllerName . DS . $a . '.ctp';
		}
		$snippetFile = dirname(__FILE__) . DS . 'snippets' . DS . $path . '.ctp';
		if (file_exists($snippetFile)) {
			$this->out(__d('superBake', '<info>The "%s" snippet file has been added.</info>', $snippetFile), 1, Shell::VERBOSE);
			include($snippetFile);
		} else {
			$this->out(__d('superBake', '<warning>The "%s" snippet file is missing.</warning>', $snippetFile), 1, Shell::QUIET);
			include(dirname(__FILE__) . DS . 'snippets' . DS . 'missing_action.ctp');
		}
	} else {
		$this->out(__d('superBake', "Action %s not created, as specified by the blacklist", $a), 1, Shell::VERBOSE);
	}
}
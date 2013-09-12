<?php

/**
 * Controllers actions template for EL-CMS baking
 * 
 * This file is used during controllers generation and is the skeleton for an 
 * empty controller.
 * 
 * In theory, you don't need to modify this file
 * 
 * Available vars (From SuperControllerTask):
 * ================================================
 * - $controllerPath : Controller path
 * - $plugin : Plugin name, with the trailing dot
 * - $currentModelName : Model name, null if none
 * - $pluralName : Plural form of the controller name, CamelCased.
 * - $singularName : Singular form of the controller name, CamelCased.
 * - $singularHuamnName : Singular form of the controller name, human readable
 * - $pluralHumanName : Plural form of the controller name, human readable
 * - $currentPart : Part name in the config array
 * - $projectConfig : Project configuration array
 * - $admin : prefix
 * Non-tested vars :
 * - $displayField
 * - $primaryKey
 * - $wannaUseSession
 * 
 * Other vars from this file:
 * ==========================
 * - $controllerName : Controller name
 * - $pluginName : Plugin name, without the dot
 * - $prefix : Current prefix, without the underscore
 * 		
 * ---
 * This file is an updated file from cakePHP.
 * ---
 * 
 * @copyright     Copyright 2012, Manuel Tancoigne (http://experimentslabs.com)
 * @author        Manuel Tancoigne <m.tancoigne@gmail.com>
 * @link          http://experimentslabs.com Experiments Labs
 * @package       EL-CMS/Console/Controllers
 * @license       GPL v3 (http://www.gnu.org/licenses/gpl.html)
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
// ProjectConfig

/**
 * @var string Plural controller name, CamelCased
 */
//$controllerName = Inflector::camelize($pluralHumanName);
$controllerName = Inflector::camelize($controllerPath);

// Plugin name without the dot. Empty for appBase.
$pluginName = '';
if (!empty($plugin)) {
	$pluginName = rtrim($plugin, '.');
}

// Plugin name in config array (not empty for appBase)
if (empty($pluginName)) {
	$configPluginName = $projectConfig['general']['appBase'];
} else {
	$configPluginName = $pluginName;
}

//if (empty($plugin)) {
//	$pluginParams = $projectConfig['plugins'][$projectConfig['general']['appBase']];
//} else {
//	$pluginParams = $projectConfig['plugins'][$pluginName];
//}

if (empty($admin)) {
	$prefix = 'public';
} else {
	$prefix = rtrim($admin, '_');
}

/*
 * Load actions to bake.
 * The project defaultActions and plugin actions are merged. This represents the actions to bake.
 * Plugin blacklists override the actions to bake
 */
$actionsToBake = array();
//foreach ($projectConfig['plugins'][$pluginName]['parts'][$currentPart]['controller']['actions'] as $prefix => $actions) {
foreach ($projectConfig['plugins'][$configPluginName]['parts'][$currentPart]['controller']['actions'][$prefix] as $action => $actionConfig) {
//	foreach ($actions as $action => $actionConfig) {
	$actionsToBake["${action}"] = $actionConfig;
//	}
}
//$actionsToBake = $this->allowedActions($controllerName, $prefix);
//
// Baking actions, using their respective templates
//
foreach ($actionsToBake as $a => $path) {
	// The controller have options defined in config file
	//if (is_array($path)) {
	$controllerOptions = $path;
	// This is the snippet file used for this action
	//	if (!empty($controllerOptions['file'])) {
	$path = $controllerOptions['file'];
	//	} else {
	//$path = $a;
	//	}
	//}
	$this->out(__d('superBake', 'Action "%s" is being built (path to snippet: "%s")', array($a, $path)), 1, Shell::VERBOSE);

	// Creating the snippet path. If $path is not an array, the path is created
	// as snippets/controller/action.ctp.
	if (empty($path)) {
		$snippetFile = dirname(__FILE__) . DS . 'snippets' . DS . $a . '.ctp';
	} else {
		$snippetFile = dirname(__FILE__) . DS . 'snippets' . DS . str_replace('::', DS, $path) . '.ctp';
	}
	if (file_exists($snippetFile)) {
		$this->out(__d('superBake', '<info>The "%s" snippet file has been added.</info>', $snippetFile), 1, Shell::VERBOSE);
		// Options
		$tmpPrefix = str_replace('_', '', $admin);
		$tmpPrefix = (empty($tmpPrefix)) ? 'public' : $tmpPrefix;
		$options = $currentControllerConfig['actions'][$tmpPrefix][$a]['options'];
		// Including snippet file
		include($snippetFile);
	} else {
		$this->out(__d('superBake', '<warning>The "%s" snippet file is missing.</warning>', $snippetFile), 1, Shell::QUIET);
		include(dirname(__FILE__) . DS . 'snippets' . DS . 'missing_action.ctp');
	}
}
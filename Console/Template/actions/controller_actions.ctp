<?php

/**
 * Controllers actions template for EL-CMS baking
 *
 * This file is used during controllers generation and is the skeleton for an
 * empty controller.
 *
 * In theory, you don't need to modify this file.
 *
 * ---
 * This file is an updated file from cakePHP.
 * ---
 *
 * @copyright     Copyright 2012, Manuel Tancoigne (http://experimentslabs.com)
 * @author        Manuel Tancoigne <m.tancoigne@gmail.com>
 * @link          http://experimentslabs.com Experiments Labs
 * @license       GPL v3 (http://www.gnu.org/licenses/gpl.html)
 * @package       ELCMS.superBake.Templates.Default.Actions
 * @version       0.3
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
// Controller name: CamelCased Plural form
$controllerName = Inflector::camelize($controllerPath);

// Plugin name without the dot.
// Empty for appBase.
$pluginName = (!empty($plugin)) ? rtrim($plugin, '.') : '';

// Plugin name in configuration.
// Not empty for appBase
$configPluginName = $this->Sbc->pluginName($pluginName);

// Prefix
$prefix = (empty($admin)) ? 'public' : rtrim($admin, '_');

// Load actions to bake.
$actionsToBake = $this->Sbc->getActionsToBake($this->cleanPlugin($plugin), $currentPart, $prefix);

//
// Baking actions, using their respective templates
//
foreach ($actionsToBake as $a => $actionConfig) {
	$this->speak("$prefix - $a");
	// List of options to be unset after generation
	$actionOptions = array();

	// Making options available for action template
	foreach ($actionConfig as $k => $v) {
		${$k} = $v;
		$actionOptions[] = $k;
	}

	// Making the action's options available in template:
	foreach ($actionConfig['options'] as $k => $v) {
		${$k} = $v;
		$actionOptions[] = $k;
	}

	// Action template. If none is provided, will use the action name as template.
	$templateFile = (empty($template)) ? DS . str_replace($admin, '', $a) : DS . $this->cleanPath($template);
	$snippetFile = dirname(__FILE__) . DS . 'actions' . $templateFile . '.ctp';

	$this->speak("$snippetFile", 'comment', 2);

	// Checking template existence.
	if (file_exists($snippetFile)) {
		$this->speak(__d('superBake', 'The "%s" snippet file has been added.', $snippetFile), 'comment', 2);

		// Including snippet file
		include $snippetFile;
	} else {
		$this->speak(__d('superBake', "Missing snippet:\n\"%s\"", $snippetFile), 'warning', 0);
		include dirname(__FILE__) . DS . 'actions' . DS . 'missing_action.ctp';
	}
	//
	// Cleaning options
	//
	foreach ($actionOptions as $k) {
		unset($k);
	}
}
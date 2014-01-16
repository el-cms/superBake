<?php

/**
 * superController Task - Generates controllers
 *
 * @copyright     Copyright 2012, Manuel Tancoigne (http://experimentslabs.com)
 * @author        Manuel Tancoigne <m.tancoigne@gmail.com>
 * @link          http://experimentslabs.com Experiments Labs
 * @package       ELCMS.superBake.Task
 * @license       GPL v3 (http://www.gnu.org/licenses/gpl.html)
 * @version       0.3
 *
 * This file is based on the lib/Cake/Console/Command/Task/ControllerTask.php file
 * from CakePHP.
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
// SbShell from superBake
App::uses('SbShell', 'Sb.Console/Command');

// Bake from SuperBake
App::uses('BakeTask', 'Sb.Console/Command/Task');

// AppModel from Cake
App::uses('AppModel', 'Model');

/**
 * Task class for creating controller files.
 */
class SuperControllerTask extends BakeTask {

	/**
	 * Tasks to be loaded by this Task
	 *
	 * @var array
	 */
	public $tasks = array('SuperModel', 'Test', 'Sb.Template', 'DbConfig', 'Project');

	/**
	 * Path to Controller directory
	 *
	 * @var array
	 */
	public $path = null;

	/**
	 * A Sbc instance that gives all the methods to handle the config file.
	 *
	 * @var array
	 */
	public $Sbc;

	/**
	 * Current part name
	 *
	 * @var string
	 */
	public $currentPart = null;

	/**
	 * Override initialize
	 *
	 * @return void
	 */
	public function initialize() {
		$this->path = current(App::path('Controller'));
	}

	/**
	 * Execution method always used for tasks.
	 *
	 * Note: no parent::execute() is used as arguments are handled in the shell.
	 *
	 * @return void
	 */
	public function execute() {
		// Dirty inclusion of the theme class that may contain logic to create HTML elements
		// This file is located in the <template>theme.php file.
		$themeClass = $this->Template->getThemePath() . DS . 'theme.php';
		if (!file_exists($themeClass)) {
			$this->speak(__d('superBake', 'The current theme has no theme class. It is not necessary, but can help...'), 'warning', 1);
		} else {
			include_once($this->Template->getThemePath() . DS . 'theme.php');
		}

		// Getting controller name from config file
		$controller = $this->Sbc->getConfig('plugins.' . $this->Sbc->pluginName($this->plugin) . '.parts.' . $this->currentPart . '.controller.name');

		// -------------------------------------------------------------------------
		// Public actions ("public" prefix)
		// -------------------------------------------------------------------------
		$this->speak(__d('cake_console', 'Baking actions for %s', $controller), 'info', 1);

		// Getting the actions content from templates
		$actions = $this->bakeActions($controller);

		// -------------------------------------------------------------------------
		// Prefixed actions (if any)
		// -------------------------------------------------------------------------
		// Prefix list from cakePHP
		$admin = $this->getPrefix();
		// Checking if there is any prefix to use
		if (count($admin) > 0) {
			foreach ($admin as $prefix) {
				$this->speak(__d('cake_console', 'Adding %s methods', $prefix), 'info', 1);
				// Getting actions from templates, for current prefix
				$actions .= "\n" . $this->bakeActions($controller, $prefix);
			}
		}

		// Unit tests
		if ($this->bake($controller, $actions)) {
			if ($this->_checkUnitTest()) {
				$this->bakeTest($controller);
			}
		}
	}

	/**
	 * Bakes Actions from templates
	 *
	 * @param string $controllerName Controller name
	 * @param string $admin Admin route to use
	 * @param boolean $wannaUseSession Set to true to use sessions, false otherwise
	 *
	 * @return string Baked actions
	 */
	public function bakeActions($controllerName, $admin = null, $wannaUseSession = true) {
		$currentModelName = $modelImport = $this->_modelName($controllerName);
		// Definig the plugin string
		$plugin = $this->plugin;
		// Append a "." at the end of the plugin name to have the same string as
		// "cake bake" would have produced.
		if ($plugin) {
			$plugin .= '.';
		}

		// -------------------------------------------------------------------------
		// Tie the controller to its model if "haveModel" is true in the part.
		// -------------------------------------------------------------------------
		if ($this->Sbc->getConfig('plugins.' . $this->Sbc->pluginName($this->plugin) . '.parts.' . $this->currentPart . '.haveModel') === true) {
			App::uses($modelImport, $plugin . 'Model');
			// Checks if Model has been loaded correctly
			if (!class_exists($modelImport)) {
				$this->err(__d('cake_console', 'You must have a model for this class to build basic methods. Please try again.'));
				$this->_stop();
			}
			$modelObj = ClassRegistry::init($currentModelName);
			// Setting basic fields
			$displayField = $modelObj->displayField;
			$primaryKey = $modelObj->primaryKey;
		}


		// -------------------------------------------------------------------------
		// Preparing variables about the controller, for the templates.
		// Note that a lot of vars are based on the model name, because these are the
		// values used in actions to do stuff in DB
		// -------------------------------------------------------------------------
		// Path to controller
		$controllerPath = $this->_controllerPath($controllerName);
		// Plural form of the model name
		$pluralName = $this->_pluralName($currentModelName);
		// Singular form of the model name
		$singularName = Inflector::variable($currentModelName);
		// Singular, human readable form of the controller name
		$singularHumanName = $this->_singularHumanName($controllerName);
		// Plural, human readable form of the controller name
		$pluralHumanName = $this->_pluralName($controllerName);

		// -------------------------------------------------------------------------
		// Variables from config file and superBake shell
		// -------------------------------------------------------------------------
		// Name of the part in wich the model is
		$currentPart = $this->currentPart;

		// Current controller:
		$currentController = $controllerName;
		$pluralVar = $pluralName;

		// Sbc object
		$this->Template->Sbc = $this->Sbc;

		// Passing all the above variables to template task to make them available in actions templates.
		$this->Template->set(compact(
						'pluralVar', 'currentPart', 'currentController', 'plugin', 'admin', 'controllerPath', 'pluralName', 'singularName', 'singularHumanName', 'pluralHumanName', 'modelObj', 'wannaUseSession', 'currentModelName', 'displayField', 'primaryKey'
		));

		// -------------------------------------------------------------------------
		// Generating the actions :
		// file <template>/actions/controller_actions.ctp  handles the generation of
		// each action.
		// -------------------------------------------------------------------------
		$actions = $this->Template->generate('actions', 'controller_actions');
		return $actions;
	}

	/**
	 * Assembles and writes a Controller file for the $controllerName controller,
	 * with the given $actions
	 *
	 * @param string $controllerName Controller name already pluralized and correctly cased.
	 * @param string $actions Generated actions from `bakeActions()`
	 * @param array $helpers Helpers to use in controller
	 * @param array $components Components to use in controller
	 *
	 * @return string Baked controller
	 */
	public function bake($controllerName, $actions = '', $helpers = null, $components = null) {
		// Info
		$this->speak(__d('cake_console', 'Baking controller class for %s...', $controllerName), 'info', 0, 0, 1);

		// -------------------------------------------------------------------------
		// Passing vars to template task
		// -------------------------------------------------------------------------
		$this->Template->set(array(
			// Plugin name
			'plugin' => $this->plugin,
			// Plugin path
			'pluginPath' => empty($this->plugin) ? '' : $this->plugin . '.',
			// Sbc object
			'Sbc' => $this->Sbc,
			// Config from config file for the current controller
			'currentControllerConfig' => $this->Sbc->getConfig("plugins."
					. $this->Sbc->getControllerPlugin($controllerName)
					. ".parts." . $this->Sbc->getControllerPart($controllerName)
					. ".controller"),
		));
		// Making the vars available
		$this->Template->set(compact('controllerName', 'actions', 'helpers', 'components', 'isScaffold'));

		// -------------------------------------------------------------------------
		// File generation
		// -------------------------------------------------------------------------
		// Generate the file
		$contents = $this->Template->generate('classes', 'controller');

		// Try to save the baked file
		$path = $this->getControllerPath();
		$filename = $path . $controllerName . 'Controller.php';
		if ($this->createFile($filename, $contents)) {
			return $contents;
		}

		return false;
	}

	/**
	 * Assembles and writes a unit test file
	 *
	 * This method is the original one from CakePHP.
	 *
	 * @param string $className Controller class name
	 *
	 * @return string Baked test
	 */
	public function bakeTest($className) {
		$this->Test->plugin = $this->plugin;
		$this->Test->connection = $this->connection;
		$this->Test->interactive = $this->interactive;
		return $this->Test->bake('Controller', $className);
	}

	/**
	 * Gets the path for models. Checks the plugin property
	 * and returns the correct path.
	 *
	 * This is a modified function from BakeTask.php (getPath())
	 *
	 * @return string Path to output.
	 */
	public function getControllerPath() {
		$path = $this->path;
		if (isset($this->plugin)) {
			$path = $this->_pluginPath($this->plugin) . 'Controller' . DS;
		}
		return $path;
	}

	/**
	 * Checks for Configure::read('Routing.prefixes')
	 *
	 * Orig. function from Cake ProjectTask (getprefix())
	 *
	 * @return array An array of prefixes with an appended "_"
	 */
	public function getPrefix() {
		// Prepares the array
		$prefixes = array();

		// Reads prefixes from core.php
		$prefixes = Configure::read('Routing.prefixes');
		if (count($prefixes) === 0) {
			$this->speak('You have no routing prefixes enabled. Only public actions will be generated.', 'warning', 1);
			return array();
		}
		foreach ($prefixes as $k => $v) {
			$prefixes[$k].="_";
		}

		return ($prefixes);
	}

}

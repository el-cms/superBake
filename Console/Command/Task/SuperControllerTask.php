<?php

/**
 * SuperBake Shell script - superController Task - Generates controllers
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
// SbShell
App::uses('SbShell', 'Sb.Console/Command');
// Bake from Cake
App::uses('BakeTask', 'Sb.Console/Command/Task');

// AppModel from Cake
App::uses('AppModel', 'Model');

/**
 * Task class for creating and updating controller files.
 */
class SuperControllerTask extends BakeTask {

	/**
	 * Tasks to be loaded by this Task
	 *
	 * @var array
	 */
	public $tasks = array('SuperModel', 'Test', 'Sb.Template', 'DbConfig', 'Project');

	/**
	 * path to Controller directory
	 *
	 * @var array
	 */
	public $path = null;

	/**
	 * Holds the project configuration array
	 * @var array
	 */
	public $sbc;

	/**
	 * Current part name
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
	 * Execution method always used for tasks
	 *
	 * @return void
	 */
	public function execute() {
		// Disabling parent::execute as arguments are handled in the shell.
		// parent::execute();
		// Plugin
		$this->plugin = $this->plugin;
		// Controller name
		$controller = $this->sbc->getConfig('plugins.' . $this->sbc->pluginName($this->plugin) . '.parts.' . $this->currentPart . '.controller.name');
		// Public actions (public)
		$this->speak(__d('cake_console', 'Baking actions for %s', $controller), 'info', 1);
		$actions = $this->bakeActions($controller);

		// Prefixes actions (if any)
		$admin = $this->getPrefix();
		if (count($admin) > 0) {
			foreach ($admin as $prefix) {
				$this->speak(__d('cake_console', 'Adding %s methods', $prefix), 'info', 1);
				$actions .= "\n" . $this->bakeActions($controller, $prefix);
			}
		}
		// Test
		if ($this->bake($controller, $actions)) {
			if ($this->_checkUnitTest()) {
				$this->bakeTest($controller);
			}
		}
	}

	/**
	 * Bake Actions
	 *
	 * @param string $controllerName Controller name
	 * @param string $admin Admin route to use
	 * @param boolean $wannaUseSession Set to true to use sessions, false otherwise
	 * @return string Baked actions
	 */
	public function bakeActions($controllerName, $admin = null, $wannaUseSession = true) {
		$currentModelName = $modelImport = $this->_modelName($controllerName);
		$plugin = $this->plugin;
		if ($plugin) {
			$plugin .= '.';
		}

		// Tie the controller to its model if "haveModel" is true in the part.
		if ($this->sbc->getConfig('plugins.' . $this->sbc->pluginName($this->plugin) . '.parts.' . $this->currentPart . '.haveModel') == true) {
			App::uses($modelImport, $plugin . 'Model');
			if (!class_exists($modelImport)) {
				$this->err(__d('cake_console', 'You must have a model for this class to build basic methods. Please try again.'));
				$this->_stop();
			}
			$modelObj = ClassRegistry::init($currentModelName);
			$displayField = $modelObj->displayField;
			$primaryKey = $modelObj->primaryKey;
		}


		$controllerPath = $this->_controllerPath($controllerName);
		$pluralName = $this->_pluralName($currentModelName);
		$singularName = Inflector::variable($currentModelName);
		$singularHumanName = $this->_singularHumanName($controllerName);
		$pluralHumanName = $this->_pluralName($controllerName);
		$currentPart = $this->currentPart;
		$currentController = $controllerName;
		$pluralVar = $pluralName;
		$this->Template->sbc = $this->sbc;
		$this->Template->set(compact(
						'pluralVar', 'currentPart', 'currentController', 'plugin', 'admin', 'controllerPath', 'pluralName', 'singularName', 'singularHumanName', 'pluralHumanName', 'modelObj', 'wannaUseSession', 'currentModelName', 'displayField', 'primaryKey'
		));
		$actions = $this->Template->generate('actions', 'controller_actions');
		return $actions;
	}

	/**
	 * Assembles and writes a Controller file
	 *
	 * @param string $controllerName Controller name already pluralized and correctly cased.
	 * @param string $actions Actions to add, or set the whole controller to use $scaffold (set $actions to 'scaffold')
	 * @param array $helpers Helpers to use in controller
	 * @param array $components Components to use in controller
	 * @return string Baked controller
	 */
	public function bake($controllerName, $actions = '', $helpers = null, $components = null) {
		$this->speak(__d('cake_console', 'Baking controller class for %s...', $controllerName), 'info', 0, 0, 1);

		$isScaffold = ($actions === 'scaffold' ) ? true : false;

		$this->Template->set(array(
			'plugin' => $this->plugin,
			'pluginPath' => empty($this->plugin) ? '' : $this->plugin . '.',
			//Sbc
			'sbc' => $this->sbc,
			'currentControllerConfig' => $this->sbc->getConfig("plugins." . $this->sbc->getControllerPlugin($controllerName) . ".parts." . $this->sbc->getControllerPart($controllerName) . ".controller"),
		));
		$this->Template->set(compact('controllerName', 'actions', 'helpers', 'components', 'isScaffold'));
		$contents = $this->Template->generate('classes', 'controller');


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
	 * @param string $className Controller class name
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
	 * Orig. function from BakeTask.php (getPath())
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
	 * @return string Admin route to use
	 */
	public function getPrefix() {
		$admin = '';
		$prefixes = array();
		$prefixes = Configure::read('Routing.prefixes');
		if (count($prefixes) == 0) {
			$this->speak('You have no routing prefixes enabled. Only public actions will be generated.', 'warning', 1);
			return array();
		}
		foreach ($prefixes as $k => $v) {
			$prefixes[$k].="_";
		}
		return ($prefixes);
	}

}

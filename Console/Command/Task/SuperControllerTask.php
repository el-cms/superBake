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
 * Added methods/vars:
 * ==============
 * 	getControllerPath()
 * 	getPrefix()
 * -----
 * 	$Sbc
 * 	$currentPart
 *
 * Deleted methods/vars:
 * ================
 * 	_askAboutMethods()
 *  _doPropertiesChoice()
 * 	_interactive()
 * 	all()
 * 	confirmController()
 * 	doComponents()
 * 	doHelpers()
 * 	getName()
 * 	getOptionParser()
 * 	listAll()
 *
 * Modified methods:
 * =================
 * 	bake()
 * 	bakeActions()
 * 	execute()
 *
 * Original methods/vars:
 * =================
 * 	bakeTest()
 * 	initialize()
 * -----
 * 	$path
 * 	$tasks
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
		// Theme class
		App::uses('Theme', 'Sb.Console' . DS . 'Templates' . DS . $this->Sbc->getTemplateName() . DS);

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
			if ($this->Sbc->getConfig('plugins.' . $this->Sbc->pluginName($this->plugin) . '.parts.' . $this->currentPart . '.haveModel') === false) {
				$this->speak(__d('superbake', 'The controller is not related to a model, so tests will not be created.'), 'warning');
			} else {
				if ($this->_checkUnitTest()) {
					$this->bakeTest($controller);
				}
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
				$this->speak(__d('cake_console', 'You must have a model for this class to build basic methods. Please try again.', 'error', 0));
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

		// Template path
		$this->Template->templatePath = $this->getTemplatePath();

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

		$controllerConfigPath = "plugins."
						. $this->Sbc->getControllerPlugin($controllerName)
						. ".parts." . $this->Sbc->getControllerPart($controllerName)
						. ".controller";
		$this->Template->set(array(
				// Plugin name
				'plugin' => $this->plugin,
				// Plugin path
				'pluginPath' => empty($this->plugin) ? '' : $this->plugin . '.',
				// Sbc object
				'Sbc' => $this->Sbc,
				// Config from config file for the current controller
				'currentControllerConfig' => $this->Sbc->getConfig($controllerConfigPath),
				'helpers' => $this->Sbc->getConfig($controllerConfigPath . '.helpers'),
				'components' => $this->Sbc->getConfig($controllerConfigPath . '.components'),
		));
		// Making the vars available
		$this->Template->set(compact('controllerName', 'actions', 'isScaffold'));

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
			$this->speak(__d('superBake', 'You have no routing prefixes enabled. Only public actions will be generated.'), 'warning', 1);
			return array();
		}
		foreach ($prefixes as $k => $v) {
			$prefixes[$k].="_";
		}

		return ($prefixes);
	}

}

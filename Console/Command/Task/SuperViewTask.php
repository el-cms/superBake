<?php

/**
 * SuperBake Shell script - superView Task - Generates Views
 *
 * @copyright     Copyright 2012, Manuel Tancoigne (http://experimentslabs.com)
 * @author        Manuel Tancoigne <m.tancoigne@gmail.com>
 * @link          http://experimentslabs.com Experiments Labs
 * @package       ELCMS.superBake.Task
 * @license       GPL v3 (http://www.gnu.org/licenses/gpl.html)
 * @version       0.3
 *
 * This file is based on the lib/Cake/Console/Command/Task/ViewTask.php file
 * from CakePHP.
 *
 * Added methods/vars:
 * ==============
 * 	getViewPath()
 * -----
 * 	$Sbc
 * 	$currentAction
 * 	$currentPart
 * 	$currentPrefix
 * 	$currentSimpleAction
 * 	$plugin
 * 	$templateOptions
 *
 * Deleted methods/vars:
 * ================
 * 	_interactive()
 * 	_methodsToBake()
 * 	all()
 * 	bakeActions()
 * 	customAction()
 * -----
 * 	$noTemplateActions
 * 	$scaffoldActions
 *
 * Modified methods:
 * =================
 * 	_associations()
 * 	_loadController()
 * 	bake()
 * 	execute()
 * 	getContent()
 * 	getOptionParser()
 * 	getTemplate()
 * 	initialize()
 *
 * Original methods/vars:
 * =================
 * 	----
 * 	$controllerName
 * 	$path
 * 	$tasks
 * 	$template
 */

// SbShell
App::uses('SbShell', 'Sb.Console/Command');
// Bake from superBake
App::uses('BakeTask', 'Sb.Console/Command/Task');

// Controller from Cake
App::uses('Controller', 'Controller');

/**
 * Task class for creating and updating view files.
 */
class SuperViewTask extends BakeTask {

	/**
	 * Tasks to be loaded by this Task
	 *
	 * @var array
	 */
	public $tasks = array('Project', 'Controller', 'DbConfig', 'Sb.Template');

	/**
	 * Path to View directory
	 *
	 * @var array
	 */
	public $path = null;

	/**
	 * Name of the controller being used
	 *
	 * @var string
	 */
	public $controllerName = null;

	/**
	 * The template file to use
	 *
	 * @var string
	 */
	public $template = null;

	/**
	 * Current action to bake, with prefix if any.
	 *
	 * @var type string
	 */
	public $currentAction = null;

	/**
	 * Current action name, without prefix.
	 *
	 * @var type string
	 */
	public $currentSimpleAction = null;

	/**
	 * The Sbc object
	 *
	 * @var Sbc
	 */
	public $Sbc;

	/**
	 * Current part name.
	 *
	 * @var type srting
	 */
	public $currentPart;

	/**
	 * Current plugin name. Null for appBase
	 *
	 * @var type string
	 */
	public $plugin;

	/**
	 * Current prefix without the dash. Null for public.
	 *
	 * @var type string
	 */
	public $currentPrefix;

	/**
	 * List of options defined by the config files that should be accessible in view.
	 * This list is here to clear these variables after the view generation, so they are not
	 * passed through different views.
	 *
	 * @var array
	 */
	public $templateOptions = array();

	/**
	 * Override initialize
	 *
	 * Unmodified method.
	 *
	 * @return void
	 */
	public function initialize() {
		$this->path = current(App::path('View'));
	}

	/**
	 * Execution method always used for tasks
	 *
	 * Note: no parent::execute() is used as arguments are handled in the shell.
	 *
	 * @return mixed
	 */
	public function execute() {

		// Variables to be made available to a view template
		$vars = $this->_loadController();

		// Method to build an action for
		$method = $this->currentAction;

		$this->speak(__d('superBake', 'View for "%s" is being built', $method), 'info', 1);
		// Getting view content from template
		$content = $this->getContent($method, $vars);
		if ($content) {
			// Baking the file
			$this->bake($this->currentAction, $content);
		}
	}

	/**
	 * Get a list of actions that can / should have views baked for them.
	 *
	 * @return array Array of action names that should be baked
	 *
	 * @todo : filter method which must not have views.
	 */
//	protected function _getMethodsToBake() {
//		$this->out(__d('superBake', 'Searching for methods to bake for controller "' . $this->controllerName . '"'), 1, Shell::VERBOSE);
//		$methods = array_diff(
//						//Current controller
//						array_map('strtolower', get_class_methods($this->controllerName . 'Controller')),
//						//methods from appcontroller
//						array_map('strtolower', get_class_methods('AppController'))
//		);
//		// No methods
//		if (empty($methods)) {
//			$this->out(__d('superBake', '<warning>No view to bake for controller "%s". You should check your config.</warning>', $this->controllerName), 1, Shell::QUIET);
//			return array();
//		}
//
//		foreach ($methods as $i => $method) {
//			//Remove _methods and self controller named methods
//			if ($method[0] === '_' || $method === strtolower($this->controllerName . 'Controller')) {
//				unset($methods[$i]);
//			}
//		}
//		return $methods;
//	}

	/**
	 * Loads Controller and sets variables for the template
	 *
	 * @return array Returns an variables to be made available to a view template
	 */
	protected function _loadController() {
		if (!$this->controllerName) {
			$this->speak(__d('cake_console', 'Controller not found', 'error', 0, 1, 2));
		}

		$plugin = null;
		if ($this->plugin) {
			$plugin = $this->plugin . '.';
		}

		$controllerClassName = $this->controllerName . 'Controller';
		App::uses($controllerClassName, $plugin . 'Controller');
		if (!class_exists($controllerClassName)) {
			$file = $controllerClassName . '.php';
			$this->speak(__d('cake_console', "The file '%s' could not be found.\nIn order to bake a view, you'll need to first create the controller.", $file), 'error', 0, 1, 2);
			return $this->_stop();
		}
		$controllerObj = new $controllerClassName();
		$controllerObj->plugin = $this->plugin;
		$controllerObj->constructClasses();
		$modelClass = $controllerObj->modelClass;
		if (!empty($modelClass)) {
			$modelObj = $controllerObj->{$controllerObj->modelClass};

			if ($modelObj) {
				$primaryKey = $modelObj->primaryKey;
				$displayField = $modelObj->displayField;
				$singularVar = Inflector::variable($modelClass);
				$singularHumanName = $this->_singularHumanName($this->controllerName);
				$schema = $modelObj->schema(true);
				$fields = array_keys($schema);
				$associations = $this->_associations($modelObj);
			} else {
				$this->speak(__d('cake_console', "The file '%s' could not be found.\n.", $modelClass), 'error', 0, 1, 2);
				return $this->_stop();
			}
		} else {
			$this->speak(__d('superBake', 'This controller uses no model.'), 'warning');
			$primaryKey = $displayField = null;
			$singularVar = Inflector::variable(Inflector::singularize($this->controllerName));
			$singularHumanName = $this->_singularHumanName($this->controllerName);
			$fields = $schema = $associations = array();
		}
		$pluralVar = Inflector::variable($this->controllerName);
		$pluralHumanName = $this->_pluralHumanName($this->controllerName);

		return compact('modelClass', 'schema', 'primaryKey', 'displayField', 'singularVar', 'pluralVar', 'singularHumanName', 'pluralHumanName', 'fields', 'associations');
	}

	/**
	 * Assembles and writes bakes the view file.
	 *
	 * @param string $action Action to bake
	 * @param string $content Content to write
	 *
	 * @return boolean Success
	 */
	public function bake($action, $content = '') {
		if ($content === true) {
			$content = $this->getContent($action);
		}
		if (empty($content)) {
			return false;
		}
		$this->out("\n" . __d('cake_console', 'Baking "%s" view file...', $action), 1, Shell::QUIET);
		$path = $this->getViewPath();
		$filename = $path . $this->controllerName . DS . Inflector::underscore($action) . '.ctp';
		return $this->createFile($filename, $content);
	}

	/**
	 * Builds content from template and variables
	 *
	 * @param string $action name to generate content to
	 * @param array $vars passed for use in templates
	 *
	 * @return string content from template
	 */
	public function getContent($action, $vars = null) {
		if (!$vars) {
			$vars = $this->_loadController();
		}

		// Making the vars available in template
		$this->Template->set('admin', $this->currentPrefix);
		$this->Template->set('action', $action);
		$this->Template->set('plugin', $this->plugin);
		$this->Template->set('currentPart', $this->currentPart);
		$this->Template->Sbc = $this->Sbc;

		// Making view's options directly available in template.
		$currentViewConfig = $this->Sbc->getConfig('plugins.' . $this->Sbc->pluginName($this->plugin) . '.parts.' . $this->currentPart . '.controller.actions.' . ((is_null($this->currentPrefix)) ? 'public' : $this->currentPrefix) . '.' . $this->currentSimpleAction . '.view.options');
		foreach ($currentViewConfig as $k => $v) {
			$this->Template->set($k, $v);
			$this->templateOptions[] = $k;
		}
		// Vars
		$this->Template->set($vars);
		// Getting the template file to use
		$template = $this->getTemplate($action);
		$this->Template->set('template', $template);
		if ($template) {
			// Generate the content
			$generatedContent = $this->Template->generate('views', $template);
			// Clear template options
			foreach ($this->templateOptions as $k) {
				unset($this->Template->templateVars[$k]);
			}
			// Returning result.
			return $generatedContent;
		}

		return false;
	}

	/**
	 * Gets the template name based on the action name
	 *
	 * @param string $action name
	 *
	 * @return string template name
	 */
	public function getTemplate($action) {
		/*
		 * Looking for alternative template
		 */
		// Getting the plugin :
		$plugin = $this->plugin;
		// Getting current prefix
		$prefix = $this->currentPrefix;

		// Action
		$action = $this->currentAction; // Action with prefix
		$simpleAction = $this->currentSimpleAction;
		// Default: current action.
		$view = $action;
		// Alternative view
		$template = $this->Sbc->getConfig('plugins.' . $this->Sbc->pluginName($this->plugin) . '.parts.' . $this->currentPart . '.controller.actions.' . $this->Sbc->prefixName($this->currentPrefix) . '.' . $this->currentSimpleAction . '.view.template');
		if (!empty($template)) {
			$view = $template;
			$this->speak(__d('superBake', 'We want to use the "%s" template for "%s" action view', array($view, $action)), 'info', 1);
		} else {
			$view = $simpleAction;
			$this->speak(__d('superBake', 'Default template %s will be used as none is specified in the config file.', $view), 'info', 1);
		}

		//
		//First case: template view exists, everything's ok. We don't go any further
		//
		$themePath = $this->Template->getThemePath();
		$filePath = str_replace('::', DS, $view);
		if (file_exists($themePath . 'views' . DS . $filePath . '.ctp')) {
			return $filePath;
		}

		$template = $view;

		//
		// At this point, the template file has not been found in views, so we
		// try combining different things
		//

		// Removing the prefix from the views
		$prefixes = Configure::read('Routing.prefixes');
		foreach ((array) $prefixes as $prefix) {
			if (strpos($template, $prefix) !== false) {
				$template = str_replace($prefix . '_', '', $template);
			}
		}
		if (in_array($template, array('add', 'edit'))) {
			$template = 'form';
		} elseif (preg_match('@(_add|_edit)$@', $template)) {
			$template = str_replace(array('_add', '_edit'), '_form', $template);
		}
		return $template;
	}

	/**
	 * get the option parser for this task
	 *
	 * @return ConsoleOptionParser
	 */
	public function getOptionParser() {
		$parser = parent::getOptionParser();
		return $parser->description(__d('cake_console', 'Bake views for all controllers, using built-in or custom templates.'));
	}

	/**
	 * Returns associations for controllers models.
	 *
	 * Unmodified method from Cake.
	 *
	 * @param Model $model
	 *
	 * @return array $associations
	 */
	protected function _associations(Model $model) {
		$keys = array('belongsTo', 'hasOne', 'hasMany', 'hasAndBelongsToMany');
		$associations = array();

		foreach ($keys as $type) {
			foreach ($model->{$type} as $assocKey => $assocData) {
				list(, $modelClass) = pluginSplit($assocData['className']);
				$associations[$type][$assocKey]['primaryKey'] = $model->{$assocKey}->primaryKey;
				$associations[$type][$assocKey]['displayField'] = $model->{$assocKey}->displayField;
				$associations[$type][$assocKey]['foreignKey'] = $assocData['foreignKey'];
				$associations[$type][$assocKey]['controller'] = Inflector::pluralize(Inflector::underscore($modelClass));
				$associations[$type][$assocKey]['fields'] = array_keys($model->{$assocKey}->schema(true));
			}
		}
		return $associations;
	}

	/**
	 * Gets the path for views. Checks the plugin property
	 * and returns the correct path.
	 *
	 * Orig. function from BakeTask.php (getPath())
	 *
	 * @return string Path to output.
	 */
	public function getViewPath() {
		$path = $this->path;
		if (isset($this->plugin)) {
			$path = $this->_pluginPath($this->plugin) . 'View' . DS;
		}
		return $path;
	}

}

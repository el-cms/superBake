<?php

/**
 * SuperBake Shell script - superView Task - Generates Views
 * 
 * @copyright     Copyright 2012, Manuel Tancoigne (http://experimentslabs.com)
 * @author        Manuel Tancoigne <m.tancoigne@gmail.com>
 * @link          http://experimentslabs.com Experiments Labs
 * @package       EL-CMS/superBake
 * @license       GPL v3 (http://www.gnu.org/licenses/gpl.html)
 *
 * This file is based on the lib/Cake/Console/Command/Task/ViewTask.php file 
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
App::uses('AppShell', 'Console/Command');
App::uses('Controller', 'Controller');
App::uses('BakeTask', 'Console/Command/Task');

/**
 * Task class for creating and updating view files.
 *
 * @package       Cake.Console.Command.Task
 */
class SuperViewTask extends BakeTask {

	/**
	 * Tasks to be loaded by this Task
	 *
	 * @var array
	 */
	public $tasks = array('Project', 'Controller', 'DbConfig', 'Template');

	/**
	 * path to View directory
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
	 * Actions to use for scaffolding
	 *
	 * @var array
	 */
	//public $scaffoldActions = array('index', 'view', 'add', 'edit');

	/**
	 * Current view configuration. Passed from superBakeShell
	 * @var array
	 */
	public $currentViewConfig = array();

	/**
	 * An array of action names that don't require templates. These
	 * actions will not emit errors when doing bakeActions()
	 *
	 * @var array
	 */
	//public $noTemplateActions = array('delete');
	//public $missing_config_state;

	/**
	 * Project wide configuration values
	 * @var array
	 */
	public $projectConfig = array();

	/**
	 * Current controller name
	 * @var string
	 */
	public $currentController = null;

	/**
	 * Current plugin name
	 * @var string
	 */
	public $currentPlugin = null;

	/**
	 * 
	 * @var array 
	 */
	public $pluginsModels = array();

	/**
	 * Configuration values for current controller (from config file, plugin/models)
	 * 
	 * @var type array
	 */
	public $currentControllerConfig = array();

	/**
	 * Current action to bake
	 * @var type string
	 */
	public $currentAction = '';

	/**
	 * Override initialize
	 *
	 * @return void
	 */
	public function initialize() {
		$this->path = current(App::path('View'));
	}

	/**
	 * Execution method always used for tasks
	 *
	 * @return mixed
	 */
	public function execute() {
		parent::execute();
		$action = null;
		$this->controllerName = $this->currentController;
		$this->plugin = $this->currentPlugin;

		// We create an array with plugins/models 
		// $this->pluginsModels = $this->getAllPluginsControllers();
		// variables to be made available to a view template
		$vars = $this->_loadController();
		// Methods to build
		if (empty($this->currentAction)) {
			$methods = $this->_methodsToBake();
		} else {
			$methods[] = $this->currentAction;
		}
		// Generates methods views from template file
		foreach ($methods as $method) {
			$this->out(__d('superBake', 'View for "%s" is being built', $method));
			$content = $this->getContent($method, $vars);
			if ($content) {
				$this->bake($method, $content);
			}
		}
	}

	/**
	 * Get a list of actions that can / should have views baked for them.
	 *
	 * @return array Array of action names that should be baked
	 * @todo : filter method which must not have views.
	 */
	protected function _methodsToBake() {
		$this->out(__d('superBake', 'Searching for methods to bake for controller "' . $this->controllerName . '"'), 1, Shell::VERBOSE);
		$methods = array_diff(
				//Current controller
				array_map('strtolower', get_class_methods($this->controllerName . 'Controller')),
				//methods from appcontroller
				array_map('strtolower', get_class_methods('AppController'))
		);
		//$scaffoldActions = false;
		// No methods
		if (empty($methods)) {
			$this->out(__d('superBake', '<warning>No method to bake for controller "%s". You should check your config.</warning>', $this->controllerName), 1, Shell::QUIET);
			return array();
		}

		foreach ($methods as $i => $method) {
			//Remove _methods and self controller named methods
			if ($method[0] === '_' || $method == strtolower($this->controllerName . 'Controller')) {
				unset($methods[$i]);
			}
		}
		return $methods;
	}

	/**
	 * Loads Controller and sets variables for the template
	 * Available template variables
	 * 	'modelClass', 'primaryKey', 'displayField', 'singularVar', 'pluralVar',
	 * 	'singularHumanName', 'pluralHumanName', 'fields', 'foreignKeys',
	 * 	'belongsTo', 'hasOne', 'hasMany', 'hasAndBelongsToMany'
	 *
	 * @return array Returns variables to be made available to a view template
	 */
	protected function _loadController() {
		if (!$this->controllerName) {
			$this->err(__d('cake_console', '<error>Controller not found</error>'));
		}

		$plugin = null;
		if ($this->plugin) {
			$plugin = $this->plugin . '.';
		}

		$controllerClassName = $this->controllerName . 'Controller';
		App::uses($controllerClassName, $plugin . 'Controller');
		$modelExists = 0;
		if ((isset($this->currentControllerConfig['hasModel']) && $this->currentControllerConfig['hasModel'] == true) || !isset($this->currentControllerConfig['hasModel'])) {
			if (!class_exists($controllerClassName)) {
				$file = $controllerClassName . '.php';
				$this->err(__d('cake_console', "The file '%s' could not be found.\nIn order to bake a view, you'll need to first create the controller.", $file));
				$this->_stop();
			}
			$controllerObj = new $controllerClassName();
			$controllerObj->plugin = $this->plugin;
			$controllerObj->constructClasses();
			$modelClass = $controllerObj->modelClass;
			$modelObj = $controllerObj->{$controllerObj->modelClass};
			if ($modelObj) {
				$modelExists = 1;
			}
		}
		if ($modelExists) {
			$primaryKey = $modelObj->primaryKey;
			$displayField = $modelObj->displayField;
			$singularVar = Inflector::variable($modelClass);
			$singularHumanName = $this->_singularHumanName($this->controllerName);
			$schema = $modelObj->schema(true);
			$fields = array_keys($schema);
			$associations = $this->_associations($modelObj);
		} else {
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
	 * @return string content from template
	 */
	public function getContent($action, $vars = null) {
		if (!$vars) {
			$vars = $this->_loadController();
		}

		$this->Template->set('admin', null);
		$temp = explode('_', $action);
		$routing_prefix = null;
		if (count($temp) >= 2) {
			if (in_array($temp[0], Configure::read('Routing.prefixes'))) {
				$routing_prefix = $temp[0];
			}
		}
		$this->Template->set('admin', $routing_prefix);
		$this->Template->set('action', $action);
		$this->Template->set('plugin', $this->plugin);
		$this->Template->set('projectConfig', $this->projectConfig);
		$this->Template->projectConfig = $this->projectConfig;
		//Additional options from config file :
//		$viewOptions = array();
		if (is_null($routing_prefix)) {
			$routing_prefix = 'public';
		}
//		$viewOptions = $this->currentControllerConfig['views'][$routing_prefix][str_replace($routing_prefix . '_', '', $action)]['options'];
//		$viewOptions=$this->currentViewConfig['options'];
//		if (!empty($this->currentControllerConfig['views'][$routing_prefix][str_replace($routing_prefix . '_', '', $action)])) {
//			if (is_array($this->currentControllerConfig['views'][$routing_prefix][str_replace($routing_prefix . '_', '', $action)])) {
//				$viewOptions = $this->currentControllerConfig['views'][$routing_prefix][str_replace($routing_prefix . '_', '', $action)]['options'];
//			}
//		}
		foreach ($this->currentViewConfig['options'] as $k => $v) {
			$this->Template->set($k, $v);
		}
		$this->Template->set($vars);
		$template = $this->getTemplate($action);
		if ($template) {
			return $this->Template->generate('views', $template);
		}

		return false;
	}

	/**
	 * Gets the template name based on the action name
	 *
	 * @param string $action name
	 * @return string template name
	 */
	public function getTemplate($action) {
		/*
		 * Looking for alternative template
		 */
		// Getting the plugin :
//		$plugin = $this->controllerPlugin($this->controllerName);
		$plugin = $this->currentPlugin;
//		if (!is_null($plugin)) { // Searching for current model in plugins
//			$config = $this->projectConfig['plugins'][$plugin]['models'][$this->controllerName];
//		} else { // Searchning in appBase
//			$config = $this->projectConfig['general']['appBase']['models'][$this->controllerName];
//		}
		$config = $this->currentViewConfig;
		// Getting current prefix
		$prefix = $this->Template->templateVars['admin'];
		// Removing prefix from action
		$simpleAction = str_replace($prefix . '_', '', $action);
		if (empty($prefix)) {
			$prefix = 'public';
		}
		// Default: current action.
		$view = $action;
		// Alternative view
		if (isset($config['file']) && !empty($config['file'])) {
//			if (is_array($config[$prefix][$simpleAction])) {
			$view = $config['file'];
//			} else {
//				$view = $config[$prefix][$simpleAction];
//			}
			$this->speak(__d('superBake', '<info>We want to use the "%s" template for "%s" action view</info>', array($view, $action)),null,0);
		} else {
			$view = $simpleAction;
		}

		// Action has no view
		//echo $this->template;
		/* if ($config != $this->template && in_array($action, $this->noTemplateActions)) {
		  return false;
		  } */
		/*
		  if (!empty($this->template) && $action != $this->template) {
		  return $this->template;
		  } */

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
	 * @param Model $model
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
			//$path = $this->_pluginPath($this->plugin) . $this->name . DS;
			$path = $this->_pluginPath($this->plugin) . 'View' . DS;
		}
		return $path;
	}

}

<?php

/**
 * SuperBake Shell script - superMenu Task - Generates Menus
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
class SuperMenuTask extends BakeTask {

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
	 * An array of action names that don't require templates. These
	 * actions will not emit errors when doing bakeActions()
	 *
	 * @var array
	 */
	public $noTemplateActions = array('delete');
	public $projectConfig = array();
	public $currentMenu = '';
	public $currentMenuConfig = array();
	public $controllerConfig = array();

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
		$Menu = array();
		// Setting "public" prefix for empty arrays
//		if (empty($this->currentMenuConfig['prefixes'])) {
//			$this->currentMenuConfig['prefixes'] = array('public');
//		} elseif (!is_array($this->currentMenuConfig['prefixes'])) {
//			$this->currentMenuConfig['prefixes'] = array($this->currentMenuConfig['prefixes']);
//		}
		$routingPrefixes = Configure::read("Routing.prefixes");
		// Plugins
		foreach ($this->projectConfig['plugins'] as $plugin => $pluginConfig) {
			if ($plugin == $this->projectConfig['general']['appBase']) {
				$plugin = null;
			}
			$this->plugin = $plugin;
			// parts
			foreach ($pluginConfig['parts'] as $part => $partConfig) {
				$controller = $partConfig['controller']['name'];
				//foreach ($partConfig['controller']['actions'] as $action=> $actionConfig) {
//				$this->controllerName = $model;
//				$this->controllerConfig = $modelConfig;
				$this->controllerName = $controller;
				$this->controllerConfig = $partConfig['controller'];
				$vars = $this->_loadController();
				$methods = $this->_methodsToBake();
				foreach ($methods as $method) {
					$tempPrefix = explode('_', $method);
					// is it a valid prefix ?
					if (count($tempPrefix) > 0 && in_array($tempPrefix[0], $routingPrefixes)) {
						$currentPrefix = $tempPrefix[0];
					} else { // No prefix
						$currentPrefix = 'public';
					}
					// Must we keep the link ?
					if (in_array($currentPrefix, $this->currentMenuConfig['prefixes'])) {
						$Menu[] = array('plugin' => strtolower($plugin), 'controller' => $controller, 'prefix' => $currentPrefix, 'action' => str_replace($currentPrefix . '_', '', $method));
					}
					//}
					
				}
			}
		}
		$content = $this->getContent($this->currentMenu, compact('Menu'));
		if ($content) {
			$this->bake($this->currentMenu, $content);
		}
	}

	/**
	 * Get a list of actions that can / should have views baked for them.
	 *
	 * @return array Array of action names that should be baked
	 * @todo : filter method which must not have views.
	 */
	protected function _methodsToBake() {
		$this->out(__d('superBake', 'Searching for methods include in the menu "' . $this->controllerName . '"'), 1, Shell::VERBOSE);
		$methods = array_diff(
				//Current controller
				array_map('strtolower', get_class_methods($this->controllerName . 'Controller')),
				//methods from appcontroller
				array_map('strtolower', get_class_methods('AppController'))
		);
		// No methods
		if (empty($methods)) {
			$this->out(__d('superBake', '<warning>No methods in "%s". You should check your config.</warning>', $this->controllerName), 1, Shell::QUIET);
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
	 * @return array Returns an variables to be made available to a view template
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
		if ((isset($this->controllerConfig['hasModel']) && $this->controllerConfig['hasModel'] == true) || !isset($this->controllerConfig['hasModel'])) {
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
		$this->out("\n" . __d('cake_console', 'Baking "%s" menu file...', $action), 1, Shell::QUIET);
		$path = $this->getViewPath();
		$filename = $path . DS . preg_replace('@::@', DS, $this->currentMenuConfig['target'] . DS . $this->currentMenuConfig['fileName'] . '.' . $this->currentMenuConfig['ext']);
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
		$this->Template->set('admin', null);
		$temp = explode('_', $action);
		if (count($temp) >= 2) {
			if (in_array($temp[0], Configure::read('Routing.prefixes'))) {
				$this->Template->set('admin', $temp[0]);
//				$GLOBALS['admin'] = $temp[0]; // @todo check usage of this, not sure if needed anymore
			}
		}
		$this->Template->set('options', $this->currentMenuConfig['options']);
		foreach($this->currentMenuConfig['options'] as $k=>$v){
			$this->Template->set($k, $v);
		}
		$this->Template->set('action', $action);
		$this->Template->set('plugin', $this->plugin);
		$this->Template->set('projectConfig', $this->projectConfig);
		$this->Template->projectConfig = $this->projectConfig;
		$this->Template->set($vars);
		//$template = $this->getTemplate($action);
		$template = preg_replace('@::@', DS, 'menus::' . $this->currentMenuConfig['template']);
		if ($template) {
			return $this->Template->generate('views', $template);
		}

		return false;
	}

	/**
	 * get the option parser for this task
	 *
	 * @return ConsoleOptionParser
	 */
	public function getOptionParser() {
		$parser = parent::getOptionParser();
		return $parser->description(__d('cake_console', 'Bake views for a all controllers, using built-in or custom templates.'));
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
		return dirname($this->path);
	}

}

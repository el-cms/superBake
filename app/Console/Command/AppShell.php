<?php

/**
 * 
 * @copyright     Copyright 2012, Manuel Tancoigne (http://experimentslabs.com)
 * @author        Manuel Tancoigne <m.tancoigne@gmail.com>
 * @link          http://experimentslabs.com Experiments Labs
 * @package       EL-CMS/superBake
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
 *  along with Foobar. If not, see <http://www.gnu.org/licenses/> 
 */
App::uses('Shell', 'Console');

/**
 * Application Shell
 *
 * Add your application-wide methods in the class below, your shells
 * will inherit them.
 *
 * @package       app.Console.Command
 */
class AppShell extends Shell {

	/**
	 * SuperBake configuration array
	 * @var array
	 */
	public $projectConfig = array();

	/**
	 * 1 if the config is loaded. Defined by loadConfig();
	 * @var boolean
	 */
	private $initialized = 0;

	/**
	 * Array of missing models/controllers in config files.
	 * @var array
	 */
	public $missing_config = array();
	public $missing_config_state = 0;

	/**
	 * Loads the configuration files and store the values in $this->projectConfig
	 */
	public function initialize() {
		parent::initialize();
		if (!$this->loadConfig()) {
			$this->out(__d('supeBake', '<error>Some configuration files can\'t be loaded.</error>'), 1, SHELL::QUIET);
			exit();
		}
	}

	/**
	 * Loads the configuration files and make an array of them
	 * 
	 * @return boolean
	 */
	public function loadConfig() {
		$error = 0;

		$configPath = dirname(dirname(__file__));

		// Checks for main config file
		if (file_exists($configPath . DS . 'Config' . DS . 'superbake.php') === false) {
			$this->out(__d('<error>bakeShell', 'The main configuration file does not exists. Check README please.</error>'), 1, Shell::QUIET);
			$this->hr();
			exit();
		} else {
			include($configPath . DS . 'Config' . DS . 'superbake.php');
		}

		// loads all plugin-related config files
		$this->projectConfig = $projectConfig;
		foreach ($this->projectConfig['plugins'] as $plugin => $config) {

			//Try to include the file
			if (file_exists($configPath . DS . 'Config' . DS . $plugin . 'Config.php')) {
				include($configPath . DS . 'Config' . DS . $plugin . 'Config.php');
				$this->out(__d('superBake', 'The "%s" configuration file has been loaded', $plugin), 1, Shell::VERBOSE);
				$this->projectConfig['plugins'][$plugin] = $PluginConfig;
			} else {
				$this->out(__d('superBake', '<warning>The "%s" configuration file can\'t be loaded</warning>', $plugin), 1, Shell::QUIET);
				$error = 1;
			}
		}

		// Loads notPlugin : Tries to include the file
		if (file_exists($configPath . DS . 'Config' . DS . $this->projectConfig['notPlugin'] . 'Config.php')) {
			include($configPath . DS . 'Config' . DS . $this->projectConfig['notPlugin'] . 'Config.php');
			$this->out(__d('superBake', 'The "%s" configuration file has been loaded', $this->projectConfig['notPlugin'] . 'Config.php'), 1, Shell::VERBOSE);
			$this->projectConfig['notPlugin'] = $PluginConfig;
		} else {
			$this->out(__d('superBake', '<warning>The "%s" configuration file can\'t be loaded</warning>', $this->projectConfig['notPlugin'] . 'Config.php'), 1, Shell::QUIET);
			$error = 1;
		}

		if ($error === 1) {
			return false;
		} else {
			$this->out(__d('superBake', '<success>SuperBake configuration file have been successfuly loaded.</success>'), 1, Shell::VERBOSE);
			$this->initialized = 1;
			return true;
		}
	}

	/**
	 * Returns __('string') or __d('plugin', 'string').
	 * If $plugin is not set, the current plugin will be used.
	 * 
	 * @param string $string String to be displayed
	 * @param string $replacement Sring to replace in %s
	 * @param string $plugin Plugin name
	 * @return string
	 */
	public function display($string, $replacement = null, $plugin = null) {
		if (is_null($plugin)) {
			$plugin = strtolower($this->templateVars['plugin']);
		} else {
			$plugin = strtolower($plugin);
		}
		if (!is_null($replacement)) {
			$replacement = ", $replacement";
		}
		// Adding quotes if not var and not quoted string
		if (!preg_match("@^\\\$@", $string)) {
			if (!preg_match("@^'(.*)'$@", $string)) {
				$string = "'" . $string . "'";
			}
		}
		if ($plugin != '') {
			//Trim '.' ending plugin name (passed to bake on command line)
			$out = "__d('" . trim($plugin, '.') . "',$string $replacement)";
		} else {
			$out = "__($string $replacement)";
		}
		return $out;
	}

	/**
	 * This function create a link array for controllers/views, taking in account of 
	 * the admin state and if the controller is in a plugin or not (and wich).
	 * Behavior:
	 *  - if $prefix is empty, current routing prefix will be used
	 * 
	 * @param string $action	The target action
	 * @param string $controller	Target controller (MUST be given to find good plugin)
	 * @param array  $options		An array of options
	 * @return string Like "array('admin'=>'string|false', 'plugin'=>'string', 'controller'=>'controller', 'action'=>'action', 'options')"
	 */
	function url($action, $controller = null, $options = null, $prefix = null) {
		$url = 'array(';
		$prefix = $this->templateVars['admin'];
		if (!empty($prefix)) {
			$url.=" 'admin' => '$prefix',";
		} else {
			$url.=" 'admin' => false,";
		}
		$plugin = $this->controllerPlugin($controller);
		if (empty($plugin)) {
			$url.=" 'plugin' => null,";
		} else {
			$url.=" 'plugin' => '" . Inflector::underscore($plugin) . "',";
		}
		if (!is_null($controller)) {
			$controller = Inflector::underscore($controller);
			$url.=" 'controller' => '$controller',";
		}
		if (!empty($action)) {
			$url.=" 'action' => '$action'";
		} else {
			$url.=" 'action' => 'index'";
		}
		if (!empty($options)) {
			$url.=", $options";
		}
		return $url . ')';
	}

	/**
	 * Finds the plugin in which the controller must be
	 * 
	 * @param string $controller Controller name
	 * @return string|null The plugin name where the controller is or null if in
	 * none
	 */
	function controllerPlugin($controller) {
		if ($this->initialized === 0) {
			$this->loadConfig();
		}
		$controller = Inflector::camelize($controller);
		$pluginTree = $this->projectConfig['plugins'];

		foreach ($pluginTree as $plugin => $config) {
			foreach ($config['models'] as $model => $modelConfig) {
				if ($model == $controller) {
					return $plugin;
				}
			}
		}
		return null;
	}

	/**
	 * Return true if the action must be created for a given prefix
	 * 
	 * @param type $action Action to test
	 * @param string $controller Leave empty if current controller
	 * @param type $prefix The current routing prefix
	 * @return boolean
	 */
	//function actionable($action, $controller, $prefix){
	function actionable($action, $controller = null) {
		// Plugin or notPlugin ?
		if (is_null($controller)) {
			$controller = $this->templateVars['pluralVar'];
		}
		$prefix = $this->templateVars['admin'];
		if (in_array($action, $this->allowedActions($controller, $prefix))) {
			return true;
		} else {
			return false;
		}
	}

	public function inConfig($item) {
		if (array_key_exists($item, $this->missing_config)) {
			return !$this->missing_config[$item];
		}
		foreach ($this->projectConfig['plugins'] as $plugin => $config) {
			if (array_key_exists($item, $config['models'])) {
				$this->missing_config[$item] = 0;
				return true;
			}
		}
		if (array_key_exists($item, $this->projectConfig['notPlugin']['models'])) {
			$this->missing_config[$item] = 0;
			return true;
		}
		$this->missing_config[$item] = 1;
		$this->missing_config_state = 1;
		return false;
	}

	/**
	 * Returns an array of the actions to create for a given controller.
	 * @param string $controller
	 * @return array
	 */
	public function allowedActions($controller, $prefix) {
		$modelWL = array();
		$modelBL = array();
		$controller = Inflector::camelize($controller);
		$plugin = $this->controllerPlugin($controller);
		if ($this->inConfig($controller) == false) {
			return array();
		}
		if (empty($prefix)) {
			$prefix = 'public';
		}
		if (is_null($plugin)) {
			$modelWL = $this->projectConfig['notPlugin']['models'][$controller]['whiteList'][$prefix];
			$modelBL = $this->projectConfig['notPlugin']['models'][$controller]['blackList'][$prefix];
		} else {
			$modelWL = $this->projectConfig['plugins'][$plugin]['models'][$controller]['whiteList'][$prefix];
			$modelBL = $this->projectConfig['plugins'][$plugin]['models'][$controller]['blackList'][$prefix];
		}
		$modelWL = array_merge($modelWL, $this->projectConfig['defaultWhiteList'][$prefix]);
		foreach ($modelWL as $action => $config) {
			if (in_array($action, $modelBL)) {
				unset($modelWL[$action]);
			}
		}
		return $modelWL;
	}

}

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
 *  along with EL-CMS. If not, see <http://www.gnu.org/licenses/> 
 */
include_once (dirname(dirname(dirname(__file__))) . DS . 'Lib' . DS . 'Spyc.php');
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
			exit();
		}
	}

	/**
	 * Loads the configuration files and make an array of them
	 * 
	 * @return boolean
	 */
	public function loadConfig() {
		$configFile = dirname(dirname(__FILE__)) . DS . 'superBakeConfig.yml';
		if (file_exists($configFile)) {
			//$this->projectConfig=yaml_parse();
			$this->projectConfig = spyc_load_file($configFile);
			return true;
		} else {
			$this->out(__('superBake', '<error>The "%s" copnfiguration file does not exists. Please create it.</error>', $configFile));
			return false;
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

		if (!empty($replacement)) {

			if (is_array($replacement)) {
				$i = 0;
				$temp = 'array(';
				foreach ($replacement as $r) {
					if ($i == 0) {
						$temp.="'$r'";
						$i++;
					} else {
						$temp.=",'$r'";
					}
				}
				$replacement = $temp . ')';
			}

			$replacement = ", $replacement";
		}
		// Adding quotes if not var and not quoted string
		if (!preg_match("@^\\\$@", $string)) {
			if (!preg_match("@^'(.*)'$@", $string)) {
				$string = "'" . $string . "'";
			}
		}
		if ($plugin != '') {
			// Trim '.' ending plugin name (passed to bake on command line)
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
	function url($action, $controller = null, $options = null) {
		$url = 'array(';
		// Routing prefix
		$prefix = $this->templateVars['admin'];
		if (!empty($prefix)) {
			$url.=" 'admin' => '$prefix',";
		} else {
			$url.=" 'admin' => false,";
		}

		// Finding controller
		if (!is_null($controller)) { // Given controller
			$controller = Inflector::underscore($controller);
		} else { // null, so assuming current controller
			$controller = Inflector::underscore($this->templateVars['pluralVar']);
		}

		// Plugin
		$plugin = $this->controllerPlugin($controller);
		if (empty($plugin)) {
			$url.=" 'plugin' => null,";
		} else {
			$url.=" 'plugin' => '" . Inflector::underscore($plugin) . "',";
		}

		// Controller
		$url.=" 'controller' => '" . Inflector::underscore($controller) . "',";

		// Action
		if (!empty($action)) {
			$url.=" 'action' => '$action'";
		} else {
			$url.=" 'action' => 'index'";
		}

		// URL options
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
		if (is_null($controller)) {
			$controller = $this->templateVars['pluralVar'];
		}
		$prefix = $this->templateVars['admin'];
		if (array_key_exists($action, $this->allowedActions($controller, $prefix))) {
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
		if (array_key_exists($item, $this->projectConfig['appBase']['models'])) {
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
		// Plugin or not plugin ? Here's the answer.
		// Should be another way to do this
		if (is_null($plugin)) {
			if (!empty($this->projectConfig['appBase']['models'][$controller]['actions'][$prefix])) {
				$modelWL = $this->projectConfig['appBase']['models'][$controller]['actions'][$prefix];
			}
			if (!empty($this->projectConfig['appBase']['models'][$controller]['blacklist'][$prefix])) {
				$modelBL = $this->projectConfig['appBase']['models'][$controller]['blacklist'][$prefix];
			}
		} else {
			if (!empty($this->projectConfig['plugins'][$plugin]['models'][$controller]['actions'][$prefix])) {
				$modelWL = $this->projectConfig['plugins'][$plugin]['models'][$controller]['actions'][$prefix];
			}
			if (!empty($this->projectConfig['plugins'][$plugin]['models'][$controller]['blacklist'][$prefix])) {
				$modelBL = $this->projectConfig['plugins'][$plugin]['models'][$controller]['blacklist'][$prefix];
			}
		}		
		// merging : custom + default, so custom overrides default.
		$modelWL += $this->projectConfig['defaultActions'][$prefix];
		
		// Removing blacklisted items
		foreach ($modelWL as $action => $config) {
			if (in_array($action, $modelBL)) {
				unset($modelWL[$action]);
			}
		}
		return $modelWL;
	}

	/**
	 * Returns the name of the current action, without current prefix.
	 * 
	 * @param string $action If set, returns an other action, without current prefix
	 * @return string Action
	 */
	public function currentAction($action = null) {
		if (is_null($action)) {
			$action = $this->templateVars['action'];
		}
		return preg_replace('@^admin_@', '', $action);
	}

}

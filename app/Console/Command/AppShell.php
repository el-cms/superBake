<?php

/**
 * AppShell file
 *
 * PHP 5
 *
 * CakePHP(tm) : Rapid Development Framework (http://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 * @link          http://cakephp.org CakePHP(tm) Project
 * @since         CakePHP(tm) v 2.0
 * @license       http://www.opensource.org/licenses/mit-license.php MIT License
 */
App::uses('Spyc', 'Yaml');
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
	 * Method executed if the shell is executed by command line (cake superApp)
	 * 
	 * As this file contains only shared methods with SuperBake commands and tasks,
	 * this does nothing and exits.
	 */
	public function main() {
		$this->out('This is not a shell, sorry');
		$this->_stop();
	}

	/**
	 * Makes a config path value (path::to::file
	 * @param type $path If true, a trailing / will be added
	 * @return string Good path format, with trailing slash
	 */
	public function cleanPath($path, $dir = false) {
		if ($dir === true) {
			$path.=DS;
		}
		$path = str_replace('::', DS, $path);
		return str_replace('\\', '\\\\', $path);
	}

	/**
	 * Creates a pretty output
	 * 
	 * If $decorations > 0, output will have an opening HR
	 * If $decorations >= 2, output will have a closing HR
	 * 
	 * @param mixed $text String to output, or array of strings.
	 * @param string $class Class (info|warning|error|success|comment|bold)
	 * @param int $force 1 for Normal, 2 for Verbose only and 0 for Quiet shells. Use 4 for debugs (you must set $this->debug=1 somewhere)
	 * @param int $decorations If >0, text will be surrounded by hr and $decorations new lines.
	 * @param int $newLines Number of empty lines to insert before and after text.
	 */
	public function speak($text, $class = null, $force = 1, $decorations = 0, $newLines = 0) {
		switch ($class) {
			case 'info':
				//HR, beggining
				$hrB = '-[ I ]---------------------------------------------------------';
				//HR, closing
				$hrC = '---------------------------------------------------------------';
				$separator = '>';
				break;
			case 'warning':
				$hrB = '/////////////////////////////////\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\';
				$hrC = '\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\//////////////////////////////';
				$separator = '#';
				break;
			case 'error':
				$hrB = '>>>>>>>>>>>>>>>>>>>>>>>>>>>[   ERROR   ]<<<<<<<<<<<<<<<<<<<<<<<';
				$hrC = '>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>><<<<<<<<<<<<<<<<<<<<<<<<<<<<<<';
				$separator = 'o';
				break;
			case 'success':
				$hrB = '`\_/`\_/`\_/`\_/`\_/`\_/`\_/`\_/`\_/`\_/`\_/`\_/`\_/`\_/`\_/`\_';
				$hrC = '_/`\_/`\_/`\_/`\_/`\_/`\_/`\_/`\_/`\_/`\_/`\_/`\_/`\_/`\_/`\_/`';
				$separator = '*';
				break;
			case 'comment':
				$hrB = '* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *';
				$hrC = '* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *';
				$separator = '-';
				break;
			default:
				$hrB = '---------------------------------------------------------------';
				$hrC = '---------------------------------------------------------------';
				$separator = '>';
				break;
		}
		if ($decorations >= 1) {
			$this->out($hrB, 1, $force);
		}
		$finalText = '';
		if (is_array($text)) {
			foreach ($text as $string) {
				if (!is_null($class)) {
					$finalText .= "  $separator  <$class>" . str_replace(array("\n", "\r", '\n'), "\n    ", $string) . "</$class>\n";
				} else {
					$finalText = "  $separator  " . str_replace(array("\n", "\r", '\n'), "\n    ", $string) . "\n";
				}
			}
		} else {
			if (!is_null($class)) {
				$finalText = "  $separator  <$class>" . str_replace(array("\n", "\r", '\n'), "\n    ", $text) . "</$class>";
			} else {
				$finalText = "  $separator  " . str_replace(array("\n", "\r", '\n'), "\n    ", $text);
			}
		}

		$this->out('', $newLines, $force);
		$this->out($finalText, 1, $force);
		$this->out('', $newLines, $force);

		if ($decorations == 2) {
			$this->out($hrC, 1, $force);
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
		} elseif ($plugin == $this->projectConfig['general']['appBase']) {
			$plugin = null;
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
		if (empty($plugin) || $plugin == $this->projectConfig['general']['appBase']) {
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
		/* if ($this->initialized === 0) {
		  $this->loadConfig();
		  } */
		$controller = Inflector::camelize($controller);
		foreach ($this->projectConfig['plugins'] as $plugin => $pluginConfig) {
			foreach ($pluginConfig['parts'] as $part => $partConfig) {
				if (!is_null($partConfig['controller']['name'])) {
					if ($partConfig['controller']['name'] == $controller) {
						return $plugin;
					}
				}
			}
		}
		return null;
	}
	
	function getForeignModelName(&$fields,$field){
		$fields;
	}

	/**
	 * Return true if the action must be created for a given prefix
	 * 
	 * @param type $action Action to test
	 * @param string $controller Leave empty if current controller
	 * @param type $prefix The current routing prefix
	 * @return boolean
	 */
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

	/**
	 * Returns an array of the actions to create for a given controller.
	 * @param string $controller
	 * @return array
	 */
	public function allowedActions($controller, $prefix) {
//		$modelWL = array();
//		$modelBL = array();
		$controller = Inflector::camelize($controller);
		$plugin = $this->controllerPlugin($controller);
		$part = $this->getControllerPart($controller, $plugin);
//		if ($this->inConfig($controller) == false) {
//			return array();
//		}
		if (empty($prefix)) {
			$prefix = 'public';
		}
		return $this->projectConfig['plugins'][$plugin]['parts'][$part]['controller']['actions'][$prefix];
//		// Plugin or not plugin ? Here's the answer.
//		// Should be another way to do this
////		if (is_null($plugin)) {
////			if (!empty($this->projectConfig['general']['appBase']['models'][$controller]['actions'][$prefix])) {
////				$modelWL = $this->projectConfig['general']['appBase']['models'][$controller]['actions'][$prefix];
////			}
////			if (!empty($this->projectConfig['general']['appBase']['models'][$controller]['blacklist'][$prefix])) {
////				$modelBL = $this->projectConfig['general']['appBase']['models'][$controller]['blacklist'][$prefix];
////			}
////		} else {
//		if (!empty($this->projectConfig['plugins'][$plugin]['models'][$controller]['actions'][$prefix])) {
//			$modelWL = $this->projectConfig['plugins'][$plugin]['models'][$controller]['actions'][$prefix];
//		}
//		if (!empty($this->projectConfig['plugins'][$plugin]['models'][$controller]['blacklist'][$prefix])) {
//			$modelBL = $this->projectConfig['plugins'][$plugin]['models'][$controller]['blacklist'][$prefix];
//		}
////		}
//		// merging : custom + default, so custom overrides default.
//		$modelWL += $this->projectConfig['defaultActions'][$prefix];
//
//		// Removing blacklisted items
//		foreach ($modelWL as $action => $config) {
//			if (in_array($action, $modelBL)) {
//				unset($modelWL[$action]);
//			}
//		}
//		return $modelWL;
	}

	/**
	 * Returns an array of the actions list from the given plugin's controller.
	 * This list is created from the config file, not from the controller file.
	 * 
	 * @param string $plugin Plugin name
	 * @param string $controller Controller name
	 * @return array Actions list
	 */
	public function getActionList($plugin, $controller) {
		$actionList = array();
		foreach ($this->projectConfig['plugins'][$plugin]['parts'] as $part) {
			if (isset($part['controller']) && $part['controller']['name'] == $controller) {
				foreach ($part['controller']['actions'] as $prefix => $actions) {
					foreach ($actions as $action => $actionConfig) {
						if ($actionConfig['hasView'] == true) {
							if ($prefix != 'public') {
								$actionList[] = "{$prefix}_$action";
							} else {
								$actionList[] = $action;
							}
						}
					}
				}
				return $actionList;
			}
		}
		$this->speak(__d('superBake', 'No action found for controller %s, in plugin %s', array($controller, $plugin)), 'warning', 0);
		return array();
//		$prefixes = Configure::read('Routing.prefixes');
//		$prefixes[] = 'public';
//		$actions = array();
//		foreach ($prefixes as $prefix) {
//			$pActions = $this->allowedActions($controller, $prefix);
//			ksort($pActions);
//			foreach ($pActions as $pAction => $val) {
//				if ((is_array($val) && $val['hasView'] == true) || !is_array($val)) {
//					if ($prefix == 'public') {
//						$actions[] = $pAction;
//					} else {
//						$actions[] = "{$prefix}_$pAction";
//					}
//				}
//			}
//		}
//		return $actions;
	}

	/**
	 * Returns the part name of a controller.
	 * @param string $controller Controller to find
	 * @param string $plugin Plugin where the controller is
	 * @return mixed Part name or false.
	 */
	public function getControllerPart($controller, $plugin) {
		foreach ($this->projectConfig['plugins'][$plugin]['parts'] as $part => $partConfig) {
			if (!empty($partConfig['controller']) && $partConfig['controller']['name'] == $controller) {
				return $part;
			}
		}
		$this->speak(__d('superBake', 'Controller %s could not be found in plugin %s. Check your superBake config file.', array($controller, $plugin)), 'error', 1, 2, 2);
		return false;
	}

	/**
	 * Searches for a controller's plugin name. Will return the first match
	 * 
	 * @param type $controller Controller name
	 * @return false|null|string False if not found, null if appBase, plugin name either.
	 */
	public function getControllerPlugin($controller) {
		foreach ($this->projectConfig['plugins'] as $plugin => $pluginConfig) {
			foreach ($pluginConfig['parts'] as $part => $partConfig) {
				if (!empty($partConfig['controller']) && $partConfig['controller']['name'] === $controller) {
					if ($plugin === $this->projectConfig['general']['appBase']) {
						return null;
					}
					return $plugin;
				}
			}
		}
		$this->speak(__d('superBake', 'Controller %s could not be found in any plugin. Check your superBake config file.', array($controller)), 'error', 1, 2, 2);
		return false;
	}

	/**
	 * Searches for a model's plugin name. Will return the first match
	 * 
	 * @param type $model Model name
	 * @return boolean|null|string False if not found, null if appBase, plugin name either.
	 */
	public function getModelPlugin($model) {
		foreach ($this->projectConfig['plugins'] as $plugin => $pluginConfig) {
			foreach ($pluginConfig['parts'] as $part => $partConfig) {
				if (!empty($partConfig['model']) && $partConfig['model']['name'] === $model) {
					if ($plugin === $this->projectConfig['general']['appBase']) {
						return null;
					}
					return $plugin;
				}
			}
		}
		$this->speak(__d('superBake', 'Model %s could not be found in any plugin. Check your superBake config file.', array($model)), 'error', 1, 2, 2);
		return false;
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
		return preg_replace("@^admin_@", '', $action);
	}

}


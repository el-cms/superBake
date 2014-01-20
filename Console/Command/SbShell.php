<?php

/**
 * SuperBake Shell script - SbShell - Contains methods used by superBake tasks
 *
 * @copyright     Copyright 2012, Manuel Tancoigne (http://experimentslabs.com)
 * @author        Manuel Tancoigne <m.tancoigne@gmail.com>
 * @link          http://experimentslabs.com Experiments Labs
 * @package       ELCMS.superBake.Shell
 * @license       GPL v3 (http://www.gnu.org/licenses/gpl.html)
 * @version       0.3
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
// Yaml
App::uses('Spyc', 'Sb.Yaml');
// superBake lib
App::uses('Sbc', 'Sb.Superbake');

// AppShell from Cake
App::uses('AppShell', 'Console/Command');

/**
 * superBake Shell
 *
 * Add plugin-wide methods in the class below, your shells
 * will inherit them.
 */
class SbShell extends AppShell {

	/**
	 * A Sbc instance that gives all the methods to handle the config file.
	 *
	 * @var Sbc
	 */
	public $Sbc;

	/**
	 * Method executed if the shell is executed by command line (cake superApp)
	 *
	 * As this file contains only shared methods with SuperBake commands and tasks,
	 * this does nothing and exits.
	 *
	 * @return void
	 */
	public function main() {
		$this->out('This is not a shell, sorry');
		$this->_stop();
	}

	/**
	 * Returns an action's prefix.
	 *
	 * @param string $action Action to check
	 *
	 * @return mixed Action prefix, null if none
	 */
	public function getActionPrefix($action) {
		$array = explode('_', $action);
		if (count($array) > 1) {
			if (in_array($array[0], $this->Sbc->prefixesList())) {
				return $array[0];
			}
		}
		return null;
	}

	/**
	 * Returns an action name, without its prefix.
	 *
	 * @param string $action Action name
	 *
	 * @return string
	 */
	public function getActionName($action) {
		return $this->Sbc->actionRemovePrefix($action);
	}

	/**
	 * Makes a config path value (path::to::file)
	 *
	 * @param string $path Path::to::file.ext
	 * @param boolean $dir If true, a trailing / will be added
	 *
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
	 * Returns true if an $action exists for the given $prefix/$controller
	 * Use this to check links in templates
	 *
	 * @param string $action The action to check
	 * @param string $prefix The prefix. If null, current prefix will be used
	 * @param string $controller The controller, underscored_name. If null, current controller will be used.
	 *
	 * @return boolean
	 */
	public function canDo($action, $prefix = null, $controller = null) {
		$action = is_null($prefix) ? $this->templateVars['admin'] : $prefix;
		$prefix = is_null($controller) ? ucfirst(Inflector::camelize($this->templateVars['pluralVar'])) : ucfirst(Inflector::camelize($controller));

		return $this->Sbc->isActionnable($action, $prefix, $action);
	}

	/**
	 * Internationalizes String:
	 * Returns a correct __(...) or __d(...) statement. If plugin is provided, will use
	 * plugin as domain; If no plugin is provided, the current plugin will be used.
	 *
	 * @param string $string String to display.
	 * @param string $args String array of args
	 * @param string $plugin Plugin name.
	 *
	 * @return string Ready to use string.
	 */
	public function iString($string, $args = null, $plugin = null) {
		// Plugin argument
		if (is_null($plugin)) {
			$plugin = strtolower($this->templateVars['plugin']);
		} elseif ($plugin === $this->projectConfig['general']['appBase']) {
			$plugin = null;
		} else {
			$plugin = strtolower($plugin);
		}

		// Adding quotes if not var and not quoted string
		if (!preg_match("@^\\\$@", $string)) {
			if (!preg_match("@^'(.*)'$@", $string)) {
				$string = "'" . $string . "'";
			}
		}
		// Arguments
		if (!is_null($args)) {
			$args = ", $args";
		}
		// Support for internationalized strings
		if ($this->Sbc->getConfig('general.useInternationalizedStrings') === true) {// Arguments
			if ($plugin != '') {
				// Trim '.' ending plugin name (passed to bake on command line)
				$out = "__d('" . trim($plugin, '.') . "', " . __d('superBake', $string) . "$args)";
			} else {
				$out = "__(" . __d('superBake', $string) . " $args)";
			}
		} else {
			if (!is_null($args)) {
				$out = "vsprintf(" . __d('superBake', $string) . " $args)";
			} else {
				$out = __d('superBake', $string);
			}
		}

		return $out;
	}

	/**
	 * Creates a pretty output for shell messages
	 *
	 * If $decorations > 0, output will have an opening HR
	 * If $decorations >= 2, output will have a closing HR
	 *
	 * @param mixed $text String to output, or array of strings.
	 * @param string $class Class (info|warning|error|success|comment|bold)
	 * @param integer $force 1 for Normal, 2 for Verbose only and 0 for Quiet shells. Use 4 for debugs (you must set $this->debug=1 somewhere)
	 * @param integer $decorations If >0, text will be surrounded by hr and $decorations new lines.
	 * @param integer $newLines Number of empty lines to insert before and after text.
	 *
	 * @return void
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
				// Don't bother about the numerous \\, those are escaping chars.
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

		if ($decorations === 2) {
			$this->out($hrC, 1, $force);
		}
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
	 *
	 * @return string Like "array('admin'=>'string|false', 'plugin'=>'string', 'controller'=>'controller', 'action'=>'action', 'options')"
	 */
	function url($action, $controller = null, $prefix = null, $options = null) {
		// Beggining the url
		$url = 'array(';

		// Routing prefix
		if (is_null($prefix)) {
			$prefix = $this->templateVars['admin'];
		}
		$prefix = ($prefix === 'public') ? null : $prefix;
		$url .= (!is_null($prefix)) ? " '$prefix' => true," : " 'admin' => false,";

		// Finding controller
		$controller = (!is_null($controller)) ? $controller : $this->templateVars['pluralVar'];

		// Plugin
		$plugin = $this->Sbc->getControllerPlugin(ucfirst(Inflector::camelize($controller)));
		$url.=(empty($plugin) || $plugin === $this->Sbc->getAppBase()) ? " 'plugin' => null," : " 'plugin' => '" . Inflector::underscore($plugin) . "',";

		// Controller
		$url .= " 'controller' => '" . Inflector::underscore($controller) . "',";

		// Action
		$url .= (!empty($action)) ? " 'action' => '$action'" : " 'action' => 'index'";

		// URL options
		if (!empty($options)) {
			$url.=", $options";
		}
		return $url . ')';
	}

	/**
	 * Returns a string to create Flash messages with correct flash message element
	 * The presence of flash message elements is defined in the config file.
	 *
	 * @param string $content Message content
	 * @param string $class Message class: error/succes/... Must match a valid flash message element
	 *
	 * @return string
	 */
	public function setFlash($content, $class) {
		return "\$this->Session->setFlash(" . $this->iString($content) . (($this->Sbc->getConfig('theme.flashMessageElement') === true) ? ", 'flash_$class'" : '') . ");\n";
	}

	/**
	 * Cleans a plugin name: remove the dot and keep the plugin
	 *
	 * @param string $plugin Plugin to check
	 *
	 * @return string
	 */
	public function cleanPlugin($plugin) {
		return (preg_match('/(.*)\.$/', $plugin)) ? rtrim($plugin, '.') : $plugin;
	}

	/**
	 * Returns the plugin name for the given $underscored_controller_name
	 * Returns null for appBase
	 *
	 * @param string $underscored_controller_name
	 *
	 * @return mixed string or null
	 */
	public function getControllerPluginName($underscored_controller_name) {
		$controller = Inflector::camelize($underscored_controller_name);
		$plugin_name = $this->Sbc->getControllerPlugin($controller);
		$plugin = ($plugin_name === $this->Sbc->getAppBase()) ? null : $plugin_name;
		return Inflector::underscore($plugin);
	}

}

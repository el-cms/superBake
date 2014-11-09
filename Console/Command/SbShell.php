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
			if (in_array($array[0], $this->Sbc->getPrefixesList())) {
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
		$prefix = is_null($prefix) ? $this->templateVars['admin'] : $prefix;
		$controller = is_null($controller) ? ucfirst(Inflector::camelize($this->templateVars['pluralVar'])) : ucfirst(Inflector::camelize($controller));

		return $this->Sbc->isActionnable($prefix, $controller, $action);
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
		} elseif ($plugin === $this->Sbc->getAppBase()) {
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
				$out = "__d('" . trim($plugin, '.') . "', " . __d('superBakeTemplate', $string) . "$args)";
			} else {
				$out = "__(" . __d('superBakeTemplate', $string) . " $args)";
			}
		} else {
			if (!is_null($args)) {
				$out = "vsprintf(" . __d('superBakeTemplate', $string) . " $args)";
			} else {
				$out = __d('superBakeTemplate', $string);
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
	 * @param integer $force 1 for Normal, 2 for Verbose only and 0 for Quiet shells.
	 * @param integer $decorations If >0, text will be surrounded by hr and $decorations new lines.
	 * @param integer $newLines Number of empty lines to insert before and after text.
	 *
	 * @return void
	 */
	public function speak($text, $class = null, $force = 1, $decorations = 0, $newLines = 0) {

		// Defaults for each text class
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
			// New line before decorations
			$this->out('', 1, $force);
			// Decoration
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
	 * @param string $action The target action
	 * @param string $controller Target controller (MUST be given to find good plugin)
	 * @param string $prefix Optionnal target prefix. If null, current prefix will be used.
	 * @param string $options An array of options as string
	 * @param boolean $special If set to true, $action will be returned "as-is",
	 * 		to allow the use of special links in methods that use url().
	 * @param string $plugin Plugin name. If empty, plugin will be searched in config file.
	 *
	 * @return string Like "array('admin'=>'string|false', 'plugin'=>'string', 'controller'=>'controller', 'action'=>'action', 'options')"
	 * 		or $action if $special is on.
	 */
	function url($action, $controller = null, $prefix = null, $options = null, $special = false, $plugin = null) {

		// In certain cases, you need to redirect to '/' or to methods outputs or variables.
		if ($special === true) {
			return $action;
		}

		// Beggining the url
		$url = 'array(';

		// Routing prefix
		if (is_null($prefix)) {
			if (!empty($this->templateVars['admin'])) {
				$prefix = rtrim($this->templateVars['admin'], '_');
			} else {
				$prefix = null;
			}
		}

		$prefix = ($prefix === 'public' || empty($prefix)) ? null : $prefix;

		$url .= (!is_null($prefix)) ? " '$prefix' => true," : " 'admin' => false,";

		// Finding controller
		$controller = (!is_null($controller)) ? $controller : $this->templateVars['pluralVar'];

		// Plugin
		if (empty($plugin)) {
			$plugin = $this->Sbc->getControllerPlugin(ucfirst(Inflector::camelize($controller)));
		} else {
			$plugin = $this->Sbc->getPluginName($plugin);
		}
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
	 * To enable setFlash messages using sessions, 'general.useSessions' must be true
	 * in config file.
	 *
	 * @param string $content Message content
	 * @param string $class Message class: error/succes/... Must match a valid flash message element
	 * @param string $action Action to redirect the user to (in controllers, use $a
	 * 		for current action)
	 * @param array $options Array of options: controllerName, useSession, redirect, specialUrl
	 *
	 * Options:
	 * 		- controllerName (String, default NULL) Target controller name
	 * 		- redirect (Bool, default true) Redirect the user after flash or not.
	 * 		//- useSession (Bool, default false) Forces the use of setFlash or not
	 * 		- specialUrl (Bool, default false) If set to true, target action will be used as the
	 * 			target url, so it will not be passed to $this->url()
	 *    - iStringArgs string List of args for iString()
	 *
	 * @return string setFlash()+redirect, setFlash() only or flash() if session is disabled.
	 */
	public function setFlash($content, $class, $action, $options = array()) {
		// Default values for $options
		$optionsDefaults = array('controllerName' => null,
				'redirect' => true,
//			'useSession' => false,
				'specialUrl' => false,
				'iStringArgs' => null);

		// Creating the array of options with passed and default ones.
		$options = $this->Sbc->updateArray($optionsDefaults, $options);
		// Creating variables from options.
		foreach ($options as $k => $v) {
			${$k} = $v;
		}
		// Arguments for iString
		if (is_null($iStringArgs)) {

		}
		// Preparing output
		$out = null;

		// Checking for controller name. If empty, use the current controller name.
		if (is_null($controllerName)) {
			$controllerName = $this->templateVars['pluralVar'];
		}
		// Checks for the global use of sessions
		if ($this->Sbc->getConfig('general.useSessions') === true) {// || $useSession === true) {
			// Flash message and redirect
			$out = "\$this->Session->setFlash(" . $this->iString($content, $iStringArgs) . (($this->Sbc->getConfig('theme.flashMessageElement') === true) ? ", '$class'" : '') . ");\n";
			// Checks if the user must be redirected straight after the message (sometimes,
			//  in case of errors)
			if ($redirect === true) {
				$out.= "\$this->redirect(" . $this->url($action, $controllerName, null, null, $specialUrl) . ");\n";
			}
		} else {
			// Flash redirect
			if ($redirect === true) {
				$out = "\$this->flash(" . $this->iString($content, $iStringArgs) . ", " . $this->url($action, $controllerName, null, null, $specialUrl) . ");\n";
			} else {
				// @todo Find something else to handle the situation where a message must be displayed
				// but with no redirection.
				// By the way, flash will be deprecated in 3.0
				echo "echo '$content';\n";
			}
		}
		return $out;
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
	 * Return true if a given component is enabled in the <code>theme.components</code> section.
	 * @param type $component
	 * @return boolean
	 */
	public function isComponentEnabled($component) {
		return ($this->Sbc->getConfig("theme.components.{$component}.useComponent"));
//		$comp = $this->Sbc->getConfig("theme.components");
//		if (is_array($comp[$component]) && (!isset($comp[$component]['useComponent']) || $comp[$component]['useComponent'] === true)) {
//			return true;
//		} else {
//			return false;
//		}
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

	/**
	 * Replacement of var_export, output is on one line, strings are protected and
	 * vars kepts as vars.
	 *
	 * This method is recursive.
	 *
	 * @param array $array The array to display
	 * @param bool $ignoreKeys Set it to true if you want only the values. Does not work on mutidimensionnal arrays.
	 * @return string
	 */
//	public function displayArray($array, $ignoreKeys = false, $keepNumericKeys = false) {
	public function displayArray($array, $multiline = false, $ignoreKeys = false) {
		$out = var_export($array, true);

		if (!$multiline) {
			// Cleanup
			$out = str_replace(["\t", "\n", "\r"], '', $out);
			$out = preg_replace("@\s+@", ' ', $out);
			$out = preg_replace("@\( @", '(', $out);
			$out = preg_replace("@,\)@", ')', $out);
		}

		return $out;


//		$out = null;
//		$i = 0;
//		if ($ignoreKeys) {
//			foreach ($array as $v) {
//				if (!is_array($v)) {
//					if ($i > 0) {
//						$out.=", ";
//					}
//					$out.=(($v[0] === '$') ? $v : "'$v'");
//				} else {
//					$this->speak(__d('superbake', 'DisplayArray can\'t process multi-dimensionnal arrays with option "ignoreKey".'), 'error', 0);
//					return null;
//				}
//			}
//		} else {
//			foreach ($array as $k => $v) {
//				$i++;
////				if ($i > 0) {
////					$out.=", ";
////				}
//				if (is_array($v)) {
//					$out .="'$k'=>" . $this->displayArray($v) . ((count($array) != $i) ? ",\n" : "\n");
//				} else {
//					if ($keepNumericKeys === false && is_numeric($k)) {
//						$out.=(($v[0] === '$') ? $v : "'$v'") . ((count($array) != $i) ? ", " : '');
//					} else {
//						$out.="'$k'=>" . (($v[0] === '$') ? $v : "'$v'") . ((count($array) != $i) ? ", " : '');
//					}
//				}
//			}
//		}
//		return "array($out)";
	}

}

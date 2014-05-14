<?php

/**
 * Sbc class
 *
 * @copyright     Copyright 2012, Manuel Tancoigne (http://experimentslabs.com)
 * @author        Manuel Tancoigne <m.tancoigne@gmail.com>
 * @link          http://experimentslabs.com Experiments Labs
 * @license       GPL v3 (http://www.gnu.org/licenses/gpl.html)
 * @package       ELCMS.superBake.Lib
 * @version       0.3
 *
 * ----
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

/**
 * Plays with the configuration file.
 */
class Sbc {
	// --------------------------------------------------------------------------
	// Logs
	// --------------------------------------------------------------------------

	/**
	 * Number of errors logged by the log() method
	 *
	 * @var int
	 */
	protected $_errors = 0;

	/**
	 * Number of warnings logged by the log() method
	 *
	 * @var type int
	 */
	protected $_warnings = 0;

	/**
	 * The logs created by the log() method
	 *
	 * @var type array
	 */
	protected $_log = array();

	// --------------------------------------------------------------------------
	// Config
	// --------------------------------------------------------------------------

	/**
	 * THE configuration array
	 *
	 * @var array
	 */
	protected $_config = array();

	// --------------------------------------------------------------------------
	// YAML
	// --------------------------------------------------------------------------

	/**
	 * Spyc object for YAML operations
	 *
	 * @var Spyc object
	 */
	protected $_Spyc;

	// --------------------------------------------------------------------------
	// Data
	// --------------------------------------------------------------------------

	/**
	 * List of plugins to bake, saved from getPluginsToBake()
	 *
	 * @var array
	 */
	protected $_pluginsToBake;

	/**
	 * List of plugins, whatever ther 'generate' state is. Saved from getPluginsList()
	 *
	 * @var array
	 */
	protected $_pluginsList;

	/**
	 * List of models to bake, saved from getModelsToBake()
	 *
	 * @var array
	 */
	protected $_modelsToBake;

	/**
	 * List of models in their respective plugins, whatever their generate state is.
	 *
	 * @var array
	 */
	protected $_modelsList;

	/**
	 * List of controllers in their respective plugins, whatever their generate state is.
	 *
	 * @var array
	 */
	protected $_controllersList;

	/**
	 * List of controllers to bake, saved from getControllersToBake()
	 *
	 * @var array
	 */
	protected $_controllersToBake;

	/**
	 * Array of all the app's action, mainly used in menu generation.
	 *
	 * @var array
	 */
	protected $_actionsAll;

	/**
	 * List of views to bake, saved from getViewsToBake()
	 *
	 * @var array
	 */
	protected $_viewsToBake;

	/**
	 * List of menus to generate, saved from getMenusToBake()
	 *
	 * @var array
	 */
	protected $_menusToBake;

	/**
	 * List of files to generate, saved from getFilesToBake()
	 *
	 * @var array
	 */
	protected $_filesToBake;

	/**
	 * List of required file, saved from getRequiredToBake()
	 *
	 * @var array
	 */
	protected $_requiredToBake;

	/**
	 * List of prefixes in default actions list.
	 *
	 * @var array
	 */
	protected $_prefixesList;

	// --------------------------------------------------------------------------
	//
	// Plugin operations
	//
	// --------------------------------------------------------------------------

	/**
	 * returns the appBase value if $plugin is null or empty
	 *
	 * @param string $plugin Plugin name
	 *
	 * @return string Plugin name
	 */
	public function pluginName($plugin = null) {
		return ($plugin === null) ? $this->getAppBase() : $plugin;
	}

	/**
	 * Returns null if appBase
	 *
	 * @param string $plugin plugin name
	 *
	 * @return string
	 */
	public function getPluginName($plugin) {
		return ($plugin === $this->getAppBase()) ? null : $plugin;
	}

	/**
	 * Returns the array of plugins to bake
	 *
	 * @return array List of plugins to bake: array(pluginName))
	 */
	public function getPluginsToBake() {
		// Checking if this op has been done before
		if (!is_array($this->_pluginsToBake)) {
			$plugins = array();
			// Search in plugins
			foreach ($this->_config['plugins'] as $plugin => $pluginConfig) {
				// Check if plugin must be generated
				if ($pluginConfig['generate'] === true) {
					$plugins[] = $plugin;
				}
			}
			$this->_pluginsToBake = $plugins;
		}
		return $this->_pluginsToBake;
	}

	/**
	 * Returns the list of plugins whatever is their 'generate' state.
	 *
	 * @return array
	 */
	public function getPluginsList() {
		if (is_null($this->_pluginsList)) {
			$plugins = array();
			foreach ($this->_config['plugins'] as $plugin => $pluginConfig) {
				$plugins[] = $plugin;
			}
			$this->_pluginsList = $plugins;
		}
		return $this->_pluginsList;
	}

	// --------------------------------------------------------------------------
	//
	// Model operations
	//
	// --------------------------------------------------------------------------

	/**
	 * Returns the array of models to bake
	 *
	 * @return array List of models to bake: array(modelName=>array(part, plugin))
	 */
	public function getModelsToBake() {
		// Checking if this op has been done before
		if (!is_array($this->_modelsToBake)) {
			$models = array();
			// Plugin list
			foreach ($this->_config['plugins'] as $plugin => $pluginConfig) {
				if ($pluginConfig['generate'] === true) {
					// Parts
					foreach ($pluginConfig['parts'] as $part => $partConfig) {
						if ($partConfig['generate'] === true && $partConfig['haveModel'] === true) {
							// Model must be generated
							if ($partConfig['model']['generate'] === true) {
								$models[$partConfig['model']['name']] = array('part' => $part, 'plugin' => $plugin);
							}
						}
					}
				}
			}
			$this->_modelsToBake = $models;
		}
		return $this->_modelsToBake;
	}

	/**
	 * Returns a list of models that must be generated in a given plugin.
	 *
	 * @param string $plugin Plugin name
	 *
	 * @return array
	 */
	public function getModelsList($plugin = null) {
		// Searching all models to store in a variable for quicker access
		if (!is_array($this->_modelsList)) {
			$models = array();
			// Plugins
			foreach ($this->_config['plugins'] as $currentPlugin => $pluginConfig) {
				$models[$currentPlugin]=array();
				// Parts
				foreach ($pluginConfig['parts'] as $part => $partConfig) {
					if ($partConfig['haveModel'] === true && $partConfig['model']['generate'] === true && $partConfig['generate'] === true) {
						$models[$currentPlugin][] = $partConfig['model']['name'];
					}
				}
			}
			$this->_modelsList = $models;
		}
		// returns plugin models
		if (!is_null($plugin)) {
			return $this->_modelsList[$plugin];
		} else {// returns all plugins models
			return $this->_modelsList;
		}
	}

	/**
	 * This will search for a model in plugins, and RETURN THE FIRST RESULT.
	 *
	 * @param string $model Model name to search for
	 *
	 * @return string or false
	 */
	public function getModelPlugin($model) {
		foreach ($this->_config['plugins'] as $plugin => $pluginConfig) {
			foreach ($pluginConfig['parts'] as $part => $partConfig) {
				if ($partConfig['haveModel'] === true) {
					if ($partConfig['model']['name'] === $model) {
						return $this->pluginName($plugin);
					}
				}
			}
		}
		return false;
	}

	/**
	 * This will search for a model in plugins parts, and RETURN THE FIRST RESULT.
	 *
	 * @param string $model Model name to search for
	 *
	 * @return string or false
	 */
	public function getModelPart($model) {
		foreach ($this->_config['plugins'] as $plugin => $pluginConfig) {
			foreach ($pluginConfig['parts'] as $part => $partConfig) {
				if ($partConfig['haveModel'] === true) {
					if ($partConfig['model']['name'] === $model) {
						return $part;
					}
				}
			}
		}
		return false;
	}

	/**
	 * Returns the whole configuration array of a given model.
	 *
	 * @param string $model Model name
	 *
	 * @return array Configuration for given model.
	 */
	public function getModelConfig($model){
		$modelConfig=$this->getConfig('plugins.'.$this->getModelPlugin($model).'.parts.'.$this->getModelPart($model).'.model');
		return (is_null($modelConfig)?array():$modelConfig);
	}

	// --------------------------------------------------------------------------
	//
	// Controller manipulation
	//
	// --------------------------------------------------------------------------
	/**
	 * Returns the array of controllers to bake
	 *
	 * @return array List of controllers to bake: array(controllerName=>array(part, plugin))
	 */
	public function getControllersToBake() {
		// Checking if this op has been done before
		if (!is_array($this->_controllersToBake)) {
			$controllers = array();
			foreach ($this->_config['plugins'] as $plugin => $pluginConfig) {
				if ($pluginConfig['generate'] === true) {
					foreach ($pluginConfig['parts'] as $part => $partConfig) {
						if ($partConfig['generate'] === true && $partConfig['haveController'] === true) {
							if ($partConfig['controller']['generate'] === true) {
								$controllers[$partConfig['controller']['name']] = array('part' => $part, 'plugin' => $plugin);
							}
						}
					}
				}
			}
			$this->_controllersToBake = $controllers;
		}
		return $this->_controllersToBake;
	}

	/**
	 * This will search for a controller in plugins, and RETURN THE FIRST RESULT.
	 *
	 * @param string $controller Controller name to search for
	 *
	 * @return string or false
	 */
	public function getControllerPlugin($controller) {
		foreach ($this->_config['plugins'] as $plugin => $pluginConfig) {
			foreach ($pluginConfig['parts'] as $part => $partConfig) {
				if ($partConfig['haveController'] === true) {
					if ($partConfig['controller']['name'] === $controller) {
						return $this->pluginName($plugin);
					}
				}
			}
		}
		return false;
	}

	/**
	 * This will search for a controller in plugins parts, and RETURN THE FIRST RESULT.
	 *
	 * @param string $controller Controller name to search for
	 *
	 * @return string or false
	 */
	public function getControllerPart($controller) {
		foreach ($this->_config['plugins'] as $plugin => $pluginConfig) {
			foreach ($pluginConfig['parts'] as $part => $partConfig) {
				if ($partConfig['haveController'] === true) {
					if ($partConfig['controller']['name'] === $controller) {
						return $part;
					}
				}
			}
		}
		return false;
	}

	/**
	 * Returns the list of actions to bake for a given plugin/part/prefix.
	 *
	 * @param string $plugin
	 * @param string $part
	 * @param string $prefix
	 *
	 * @return array
	 */
	public function getActionsToBake($plugin, $part, $prefix) {
		return $this->getConfig("plugins." . $this->pluginName($plugin) . ".parts.$part.controller.actions.$prefix");
	}

	/**
	 * Returns the list of all actions for all controllers, whatever is the "generate" state of the controller
	 * array(plugin=>controllerName=>prefix=>action)
	 *
	 * @return array
	 */
	public function getActionsAll() {

		if (!is_array($this->_actionsAll)) {
			$actionsList = array();
			foreach ($this->getConfig('plugins') as $plugin => $pluginConfig) {
				$actionsList[$plugin] = array('displayName' => $pluginConfig['displayName'], 'controllers' => array());
				foreach ($pluginConfig['parts'] as $part => $partConfig) {
					if ($partConfig['haveController'] === true) {
						$actionsList[$plugin]['controllers'][$partConfig['controller']['name']] = array('prefixes' => array(), 'displayName' => $partConfig['controller']['displayName']);
						foreach ($partConfig['controller']['actions'] as $prefix => $actions) {
							foreach ($actions as $action => $actionConfig) {
								$actionsList[$plugin]['controllers'][$partConfig['controller']['name']]['prefixes'][$prefix][] = $action;
							}
						}
					}
				}
			}
			$this->_actionsAll = $actionsList;
		}
		return $this->_actionsAll;
	}

	/**
	 * Returns a list of controllers that must be generated in a given plugin.
	 *
	 * @param string $plugin Plugin name
	 *
	 * @return array
	 */
	public function getControllersList($plugin = null) {
		// Searching all controllers to store in a variable for quicker access
		if (!is_array($this->_controllersList)) {
			$controllers = array();
			foreach ($this->_config['plugins'] as $currentPlugin => $pluginConfig) {
				foreach ($pluginConfig['parts'] as $part => $partConfig) {
					if ($partConfig['haveController'] === true && $partConfig['controller']['generate'] === true && $partConfig['generate'] === true) {
						$controllers[$currentPlugin][] = $partConfig['controller']['name'];
					}
				}
			}
			$this->_controllersList = $controllers;
		}
		// returns plugin controllers
		if (!is_null($plugin)) {
			return $this->_controllersList[$plugin];
		} else {// returns all plugins controllers
			return $this->_controllersList;
		}
	}

	// --------------------------------------------------------------------------
	//
	// View manipulation
	//
	// --------------------------------------------------------------------------

	/**
	 * Returns the array of views to bake
	 *
	 * @param string $plugin Plugin name.
	 * @param string $controller Controller name
	 *
	 * @return array List of views to bake: array(plugin=> part=> prefix=>array(action))
	 */
	public function getViewsToBake($plugin = null, $controller = null) {
		// Checking if this op has been done before
		if (!is_array($this->_viewsToBake)) {
			$views = array();
			foreach ($this->_config['plugins'] as $tPlugin => $pluginConfig) {
				$views[$tPlugin]=array();
				if ($pluginConfig['generate'] === true) {
					foreach ($pluginConfig['parts'] as $part => $partConfig) {
						if ($partConfig['generate'] === true && $partConfig['haveController'] === true) {
							if ($partConfig['controller']['generateViews'] === true) {
								foreach ($partConfig['controller']['actions'] as $prefix => $actions) {
									foreach ($actions as $action => $actionConfig) {
										if ($actionConfig['haveView'] === true && (isset($actionConfig['view']['generate']) && $actionConfig['view']['generate'] === true)) {
											$views[$tPlugin][$part][$prefix][] = $action;
										}
									}
								}
							}
						}
					}
				}
			}
			$this->_viewsToBake = $views;
		}
		if (!is_null($plugin)) {
			if (!is_null($controller)) {
				return $this->_viewsToBake[$plugin][$this->getControllerPart($controller)];
			} else {
				return $this->_viewsToBake[$plugin];
			}
		}
		return $this->_viewsToBake;
	}

	// --------------------------------------------------------------------------
	//
	// Menus related methods
	//
	// --------------------------------------------------------------------------

	/**
	 * Returns the list of menus with "generate" set to true.
	 *
	 * @return array
	 */
	public function getMenusToBake() {
		// Checking if this op has been done before
		if (!is_array($this->_menusToBake)) {
			$menus = array();
			foreach ($this->_config['plugins'] as $plugin => $pluginConfig) {
				if ($pluginConfig['generate'] === true) {
					foreach ($pluginConfig['menus'] as $menu => $menuConfig) {
						if ($menuConfig['generate'] === true) {
							$menus[$plugin][] = $menu;
						}
					}
				}
			}
			$this->_menusToBake = $menus;
		}
		return $this->_menusToBake;
	}

	// --------------------------------------------------------------------------
	//
	// Files related methods
	//
	// --------------------------------------------------------------------------

	/**
	 * Returns the list of files to bake with "generate" set to true.
	 *
	 * @return array
	 */
	public function getFilesToBake() {
		// Checking if this op has been done before
		if (!is_array($this->_filesToBake)) {
			$files = array();
			foreach ($this->_config['plugins'] as $plugin => $pluginConfig) {
				if ($pluginConfig['generate'] === true) {
					foreach ($pluginConfig['files'] as $file => $fileConfig) {
						if ($fileConfig['generate'] === true) {
							$files[$plugin][] = $file;
						}
					}
				}
			}
			$this->_filesToBake = $files;
		}
		return $this->_filesToBake;
	}

	/**
	 * Returns the required files/dirs to copy
	 *
	 * @return array
	 */
	public function getRequiredToBake() {
		// Checking if this op has been done before
		if (!is_array($this->_requiredToBake)) {
			$required = array();
			foreach ($this->_config['plugins'] as $plugin => $pluginConfig) {
				if ($pluginConfig['generate'] === true) {
					foreach ($pluginConfig['required'] as $file => $fileConfig) {
						if ($fileConfig['generate'] === true) {
							$required[$plugin][] = $file;
						}
					}
				}
			}
			$this->_requiredToBake = $required;
		}
		return $this->_requiredToBake;
	}

	// --------------------------------------------------------------------------
	//
	// Misc methods
	//
	// --------------------------------------------------------------------------

	/**
	 * Returns true if the controller/prefix/action exists in config (that means
	 * the current prefix have access to this action).
	 *
	 * @param string $prefix Prefix to check
	 * @param string $controller Controller to check
	 * @param string $action Action to check
	 *
	 * @return boolean
	 */
	public function isActionnable($prefix, $controller, $action) {
		$tmp = $this->getConfig("plugins." . $this->getControllerPlugin($controller) . ".parts." . $this->getControllerPart($controller) . ".controller.actions." . ((is_null($prefix)) ? 'public' : $prefix) . ".$action");
		if (is_array($tmp) && $tmp['blackListed'] === false) {
			return true;
		} else {
			return false;
		}
	}

	// --------------------------------------------------------------------------
	//
	// Config file manipulation
	//
	// --------------------------------------------------------------------------

	/**
	 * Returns public if $prefix is null, or $prefix.
	 * @param string $prefix Prefix to test
	 *
	 * @return string
	 */
	public function prefixName($prefix) {
		return (is_null($prefix)) ? 'public' : $prefix;
	}

	/**
	 * Returns the list of prefixes in default actions list.
	 *
	 * @return array
	 */
	public function getPrefixesList() {
		if (!is_array($this->_prefixesList)) {
			foreach ($this->getConfig('defaults.actions') as $prefix => $actions) {
				$list[] = $prefix;
			}
			$this->_prefixesList = $list;
		}
		return $this->_prefixesList;
	}

	/**
	 * Creates a prefix_action string, and returns only action if prefix is public.
	 *
	 * @param string $action Action name
	 * @param string $prefix Prefix
	 *
	 * @return string
	 */
	public function actionAddPrefix($action, $prefix = null) {
		return (($prefix === 'public' || is_null($prefix)) ? '' : $prefix . '_') . $action;
	}

	/**
	 * Removes the prefix from an action name.
	 *
	 * @param string $action prefixed_action
	 *
	 * @return string action
	 */
	public function actionRemovePrefix($action) {
		$actionArray = explode('_', $action);
		$prefixes = $this->getPrefixesList();
		// There's a prefix
		if (count($actionArray) > 1 && in_array($actionArray[0], $prefixes)) {
			unset($actionArray[0]);
			return implode('_', $actionArray);
		} else {
			return $action;
		}
	}

	/**
	 * Loads the configuration file and populates the array
	 *
	 * @param string $file Configuration file name
	 *
	 * @return void
	 */
	public function loadFile($file) {
		$file = $this->getConfigPath() . $file;
		if (!file_exists($file)) {
			die("\n\nThe configuration file was not found in $file\n\n\n");
		}
		$this->log("Loading configuration file:"
						. "<br/><small>\"$file\"...</small>", 'info', 1);
		$this->_Spyc = new Spyc();
		$this->_config = $this->_Spyc->YAMLLoad($file);
		$this->log('Configuration file loaded.', 'success');
		$this->populate();
	}

	/**
	 * Returns the appBase value
	 *
	 * @return string
	 */
	public function getAppBase() {
		return $this->getConfig('general.appBase');
	}

	/**
	 * Returns the path to Console/Configurations/
	 *
	 * @return string Path to the configuration file
	 */
	public function getConfigPath() {
		$path = dirname(dirname(dirname(__FILE__))) . DS . 'Console' . DS . 'Template' . DS;
		return $path;
	}

	/**
	 * Searches for the value of the given key in the config array.
	 * Key must be in the format of "key.subKey.subSubKey", as for Configure::read()
	 *
	 * @param string $key
	 *
	 * @return mixed Key's value
	 */
	public function getConfig($key = null) {
		if ($key === null) {
			return $this->_config;
		} else {
			return Hash::get($this->_config, $key);
		}
	}

	/**
	 * Populates the configuration array with defaults values.
	 *
	 * @return void
	 */
	public function populate() {

		// @todo must run checks to check defaults
		// @todo must run checks to check general
		//
		// Prefixes
		//
		foreach ($this->_config['defaults']['actions'] as $prefix => $actions) {
			$routingPrefixes = (is_array(Configure::read('Routing.prefixes'))) ? Configure::read('Routing.prefixes') : array();
			if ($prefix != 'public' && !in_array($prefix, $routingPrefixes)) {
				$this->log("Prefix <strong>$prefix</strong> is not enabled in core.php but is used in your config file.", 'error', 1);
			}
		}
		//
		// Plugins
		//
		$this->log('Populating plugins.', 'part', 1);
		foreach ($this->_config['plugins'] as $plugin => $pluginConfig) {
			$this->log("Populating plugin \"<strong>$plugin</strong>\".", 'part', 2);

			//
			// Plugin configuration
			//
			if (is_array($pluginConfig) && isset($pluginConfig['parts']) && !is_null($pluginConfig['parts']) && is_array($pluginConfig['parts'])) {
				// Plugin has no displayName
				if (!isset($pluginConfig['displayName'])) {
					$pluginConfig['displayName'] = Inflector::humanize(Inflector::underscore($plugin));
					$this->log("Plugin has no displayName.<br>"
									. "   =>I'll use \"<strong>${pluginConfig['displayName']}</strong>\" instead.", 'warning', 3);
				}
				// Merging with default plugin
				$pluginConfig = $this->updateArray($this->_config['defaults']['plugin'], $pluginConfig, true);

				//
				// Parts
				//
				$this->log("Populating parts...", 'part', 3);
				foreach ($pluginConfig['parts'] as $part => $partConfig) {
					$this->log("Populating \"<strong>$part</strong>\"", 'part', 4);
					// Merge part with defaults
					$partConfig = $this->updateArray($this->_config['defaults']['part'], $partConfig, true);

					//
					// Model
					//
					// Must have a model ?
					if ($partConfig['haveModel'] === true) {
						$this->log("Model configuration", 'part', 5);
						// String definition
						if (!is_array($partConfig['model'])) {
							if (empty($partConfig['model'])) {
								$this->log("The model should be defined as an array.<br>"
												. "   => I'll base the name on the part name (\"<strong>" . Inflector::singularize($part) . "</strong>\")", 'warning', 6);
								$partConfig['model'] = array('name' => Inflector::singularize($part));
							} else {
								$this->log("The model should be defined as an array.<br>"
												. "   => I'll use \"<strong>${partConfig['model']}</strong>\" as &lt;model&gt;.name", 'warning', 6);
								$partConfig['model'] = array('name' => $partConfig['model']);
							}
						}
						// Array definition
						else {
							// Empty 'name' attribute
							if (!isset($partConfig['model']['name']) || empty($partConfig['model']['name'])) {
								$this->log("The model definition should contain a name attribute.<br>"
												. "   => I'll base the name on the part name (\"<strong>" . Inflector::singularize($part) . "</strong>\")", 'warning', 6);
								$partConfig['model']['name'] = Inflector::singularize($part);
							}
						}

						// Model population
						$partConfig['model'] = $this->updateArray($this->_config['defaults']['model'], $partConfig['model']);
						// Merging part config with model options
						$partConfig['model']['options'] = $this->updateArray($partConfig['options'], $partConfig['model']['options'], true);
						// Snippets
						foreach ($partConfig['model']['snippets'] as $snippet => $snippetConfig) {
							// Merging snippets with defaults
							$partConfig['model']['snippets'][$snippet] = $this->updateArray($this->getConfig('defaults.snippet'), $snippetConfig);
							//Merging snippet options with part options
							$partConfig['model']['snippets'][$snippet]['options'] = $this->updateArray($partConfig['options'], $partConfig['model']['snippets'][$snippet]['options']);
						}
						$this->log("Model \"<strong>" . $partConfig['model']['name'] . "</strong>\" populated.", 'success', 5);
					} else {
						$this->log("This part should not have model", 'info', 5);
						unset($partConfig['model']);
					}

					//
					// Controller
					//
					// Must have a controller ?
					if ($partConfig['haveController'] === true) {
						$this->log("Controller configuration", 'part', 5);
						// String definition
						if (!is_array($partConfig['controller'])) {
							if (empty($partConfig['controller'])) {
								if ($partConfig['haveModel'] === false) {
									$this->log("The controller should be defined as an array.<br>"
													. "   => I'll use \"<strong>" . $part . "</strong>\" as name (part name)", 'warning', 6);
									$partConfig['controller'] = array('name' => $part);
								} else {
									$this->log("The controller should be defined as an array.<br>"
													. "   => I'll use \"<strong>" . Inflector::pluralize($partConfig['model']['name']) . "</strong>\", based on the model name", 'warning', 6);
									$partConfig['controller'] = array('name' => Inflector::pluralize($partConfig['model']['name']));
								}
							} else {
								$this->log("The controller should be defined as an array.<br>"
												. "   => I'll use \"<strong>${partConfig['controller']}</strong>\" as &lt;controller&gt;.name", 'warning', 6);
								$partConfig['controller'] = array('name' => $partConfig['controller']);
							}
						}
						// Array definition
						else {
							// Empty 'name' attribute
							if (!isset($partConfig['controller']['name']) || empty($partConfig['controller']['name'])) {
								if ($partConfig['haveModel'] === false) {
									$this->log("The controller definition should have a name.<br>"
													. "   => I'll use \"<strong>" . $part . "</strong>\" as name (part name)", 'warning', 6);
									$partConfig['controller']['name'] = $part;
								} else {
									$this->log("The controller definition should have a name.<br>"
													. "   => I'll use \"<strong>" . Inflector::pluralize($partConfig['model']['name']) . "</strong>\", based on the model name", 'warning', 6);
									$partConfig['controller']['name'] = Inflector::pluralize($partConfig['model']['name']);
								}
							}
						}
						// Controller population
						$partConfig['controller'] = $this->updateArray($this->_config['defaults']['controller'], $partConfig['controller'], true);
						// Display name check
						if (empty($partConfig['controller']['displayName'])) {
							$this->log("The controller should have display name.<br>"
											. "   => I'll base one on the controller name : \"<strong>" . ucfirst(strtolower(Inflector::humanize(Inflector::underscore($partConfig['controller']['name'])))) . "</strong>\"", 'warning', 6);
							$partConfig['controller']['displayName'] = ucfirst(strtolower(Inflector::humanize(Inflector::underscore($partConfig['controller']['name']))));
						}
						//
						// Actions
						//
						$partConfig['controller']['actions'] = $this->updateArray($this->_config['defaults']['actions'], $partConfig['controller']['actions'], true);
						// Merge each action with the defaults for it, and fill the view array
						foreach ($partConfig['controller']['actions'] as $prefix => $actions) {
							foreach ($actions as $action => $actionConfig) {
								// Action
								$partConfig['controller']['actions'][$prefix][$action] = $this->updateArray($this->_config['defaults']['action'], $actionConfig, true);
								// Options from part
								$partConfig['controller']['actions'][$prefix][$action]['options'] = $this->updateArray($partConfig['options'], $partConfig['controller']['actions'][$prefix][$action]['options'], true);

								//
								// View
								//
								if ($partConfig['controller']['actions'][$prefix][$action]['haveView'] === true) {
									if (empty($actionConfig['view'])) {
										$actionConfig['view'] = array();
									}
									$partConfig['controller']['actions'][$prefix][$action]['view'] = $this->updateArray($this->_config['defaults']['view'], $actionConfig['view'], true);
									// Options from part
									$partConfig['controller']['actions'][$prefix][$action]['view']['options'] = $this->updateArray($partConfig['options'], $partConfig['controller']['actions'][$prefix][$action]['view']['options'], true);
									// Options from action
									$partConfig['controller']['actions'][$prefix][$action]['view']['options'] = $this->updateArray($partConfig['controller']['actions'][$prefix][$action]['options'], $partConfig['controller']['actions'][$prefix][$action]['view']['options'], true);
								}
							}
						}
						// Now, searching for actions to remove
						foreach ($partConfig['controller']['actions'] as $prefix => $actions) {
							foreach ($actions as $action => $actionConfig) {
								if ($actionConfig['blackListed'] === true) {
									$this->log("Removing blacklisted action \"<strong>$prefix.$action</strong>\"", 'info', 6);
									unset($partConfig['controller']['actions'][$prefix][$action]);
								}
							}
						}
						$this->log("Controller \"<strong>" . $partConfig['controller']['name'] . "</strong>\" populated.", 'success', 5);
					} else {
						$this->log("This part should not have controller", 'info', 5);
						unset($partConfig['controller']);
					}
					// No more things to do for this part. Setting it in the plugin config
					$pluginConfig['parts'][$part] = $partConfig;
					$this->log("Part \"$part\" populated.", 'success', 4);
				}
				$this->log("Parts population is over.", 'success', 3);


				//
				// Menus
				//
				$this->log("Populating menus...", 'part', 3);
				foreach ($pluginConfig['menus'] as $menu => $menuConfig) {
					$pluginConfig['menus'][$menu] = $this->updateArray($this->_config['defaults']['menu'], $menuConfig, true);
					if (empty($pluginConfig['menus'][$menu]['template'])) {
						$this->log("No template set, using \"" . $menu . "\" as template name", 'warning', 5);
						$pluginConfig['menus'][$menu]['fileName'] = $menu;
					}
					if (empty($pluginConfig['menus'][$menu]['targetFileName'])) {
						$this->log("No target file set, using \"" . $menu . "\" as target file", 'warning', 5);
						$pluginConfig['menus'][$menu]['targetFileName'] = $menu;
					}
					$this->log("Added $menu", 'success', 4);
				}
				$this->log("Menus population is over.", 'success', 3);


				//
				// Files
				//
				$this->log("Populating files...", 'part', 3);
				foreach ($pluginConfig['files'] as $file => $fileConfig) {
					$pluginConfig['files'][$file] = $this->updateArray($this->_config['defaults']['file'], $fileConfig, true);
					if (empty($pluginConfig['files'][$file]['template'])) {
						$this->log("No fileName set, using \"" . $file . "\" as template name", 'warning', 5);
						$pluginConfig['files'][$file]['fileName'] = $file;
					}
					if (empty($pluginConfig['files'][$file]['targetFileName'])) {
						$this->log("No target file set, using \"" . $file . "\" as target file", 'warning', 5);
						$pluginConfig['files'][$file]['targetFileName'] = $file;
					}
					$this->log("Added $file", 'success', 4);
				}
				$this->log("Files population is over.", 'success', 3);

				//
				// Required files
				//
				$this->log("Populating required files...", 'part', 3);
				foreach ($pluginConfig['required'] as $required => $requiredConfig) {
					$pluginConfig['required'][$required] = $this->updateArray($this->_config['defaults']['required'], $requiredConfig, true);
					if (empty($pluginConfig['required'][$required]['type'])) {
						$this->log("No file type set, removing $required from configuration.", 'error', 5);
						unset($pluginConfig['required'][$required]);
						$error = 1;
					} elseif (empty($pluginConfig['required'][$required]['target'])) {
						$this->log("No target set, removing $required from configuration.", 'error', 5);
						unset($pluginConfig['required'][$required]);
						$error = 1;
					} elseif (empty($pluginConfig['required'][$required]['source'])) {
						$this->log("No source set, removing $required from configuration.", 'error', 5);
						unset($pluginConfig['required'][$required]);
						$error = 1;
					} else {
						$error = 0;
						$this->log("Added $required", 'success', 4);
					}
				}
				$this->log("Files population is over.", 'success', 3);

				// @todo maybe check the templates existence.
				//
				$this->_config['plugins'][$plugin] = $pluginConfig;
				$this->log("Plugin \"$plugin\" populated.", 'success', 2);
				//
			} else {
				$this->log("Plugin <strong>\"$plugin\"</strong> is empty or have no parts.<br>"
								. "   => It will now be removed from configuration.", 'error', 3);
				unset($this->_config['plugins'][$plugin]);
			}
		}
	}

	/**
	 * Complete one array of default values with an array of defined values.
	 * Default values are overwriten if in the defined array.
	 * Keys from the defined array that are absent from the default array are added.
	 *
	 * @param array $default An array of default values
	 * @param array $defined An array of defined values
	 * @param boolean $keep If set to true, keeps the values defined in $defined array and absent from $default array.
	 *
	 * @return array Default array updated with defined array
	 */
	public function updateArray($default = array(), $defined = array(), $keep = false) {
		// Walking throug the default array
		$finalArray = array();
		if (!empty($default) && !empty($defined)) {
			foreach ($default as $k => $v) {
				// Check in defined
				if (key_exists($k, $defined)) {
					if (is_array($v)) {
						$finalArray[$k] = $this->updateArray($v, $defined[$k], $keep);
					} else {
						$finalArray[$k] = $defined[$k];
					}
					if ($keep === true) {
						unset($defined[$k]);
					}
				} else {
					$finalArray[$k] = $v;
				}
				// Alone defined values. What must we do with it ?
				if ($keep === true) {
					foreach ($defined as $k => $v) {
						$finalArray[$k] = $v;
					}
				}
			}
			return $finalArray;
		} else {
			if (empty($default) && !empty($defined)) {
				return $defined;
			} elseif (!empty($default) && empty($defined)) {
				return $default;
			} else {
				return array();
			}
		}
	}

	// --------------------------------------------------------------------------
	//
	// Logs manipulation
	//
	// --------------------------------------------------------------------------

	/**
	 * Logs a message in an array of messages.
	 *
	 * @param string $message The message
	 * @param string $type Message type: info|warning|success|error|part
	 * @param integer $level Level of the message. The lower the message is, the less it is important.
	 *
	 * @return void
	 */
	public function log($message, $type = 'info', $level = 0) {
		$this->_log[] = array('level' => $level, 'type' => $type, 'message' => $message);
		if ($type === 'error') {
			$this->_errors++;
		}
		if ($type === 'warning') {
			$this->_warnings++;
		}
	}

	/**
	 * Returns the log array
	 *
	 * @return array
	 */
	public function displayLog() {
		return $this->_log;
	}

	/**
	 * Returns the number of errors generated by the log() function.
	 *
	 * @return int
	 */
	public function getErrors() {
		return $this->_errors;
	}

	/**
	 * Returns the number of warnings generated by the log() function.
	 *
	 * @return int
	 */
	public function getWarnings() {
		return $this->_warnings;
	}

}

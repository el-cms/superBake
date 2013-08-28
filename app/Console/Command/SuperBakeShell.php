<?php

/**
 * SuperBake Shell script
 * 
 * This is the SuperBake shell.
 * Its goal is to allow quick generation of the app, based on a configuration file.
 * It will build all plugin dirs, all models/controllers/views, IN wanted plugins
 * and with ALL routing prefixes.
 * 
 * For controllers, it will bake ALL actions : public actions AND prefixed actions
 * for ALL prefixes.
 * 
 * @copyright     Copyright 2012, Manuel Tancoigne (http://experimentslabs.com)
 * @author        Manuel Tancoigne <m.tancoigne@gmail.com>
 * @link          http://experimentslabs.com Experiments Labs
 * @package       EL-CMS/superBake
 * @license       GPL v3 (http://www.gnu.org/licenses/gpl.html)
 * @version 0.1
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
App::uses('Model', 'Model');

/**
 * Command-line code generation utility to automate programmer chores.
 *
 * SuperBake is EL's code generation script, which can help you kickstart
 * application development by writing fully functional skeleton controllers,
 * models, and views. Going further, SuperBake can also write Unit Tests for you.
 *
 */
class SuperBakeShell extends AppShell {

	/**
	 * Contains tasks to load and instantiate
	 *
	 * @var array
	 */
	public $tasks = array('SuperModel', 'SuperController', 'SuperView', 'SuperPlugin', 'SuperMenu');

	/**
	 * The connection being used.
	 *
	 * @var string
	 */
	public $connection = 'default';

	/**
	 * Project configuration. Filled by loadConfig()
	 * 
	 * @var array
	 */
	public $projectConfig = array();

	/**
	 * Determines if the current generation is interactive or not
	 * @var Boolean
	 */
	public $interactive = true;

	/**
	 * Indicates if there is a prefix configuration error (in superBakeConfig or core.php)
	 * @var bool
	 */
	public $prefixError = 0;

	/**
	 * 1 if the config is loaded. Defined by loadConfig();
	 * @var boolean
	 */
	private $initialized = 0;

	/**
	 * Indicates the plugin we're working with.
	 * @var string
	 */
	//public $currentPlugin = '';

	/**
	 * Assign $this->connection to the active task if a connection param is set.
	 *
	 * @return void
	 */
	public function startup() {
		parent::startup();
		Configure::write('debug', 2);
		Configure::write('Cache.disable', 1);

		$task = Inflector::classify($this->command);
		// Checking for alternative "connection" param
		if (isset($this->{$task})) {
			if (isset($this->params['connection'])) {
				$this->{$task}->connection = $this->params['connection'];
			}
		}
		$this->_loadConfig();
	}

	/**
	 * Main "screen"
	 *
	 * @return mixed
	 */
	public function main() {
		// This adds a style for console formatting. It's used in the menu to better recognize
		// the actions' letters.
		$this->stdout->styles('bold', array('bold' => true, 'underline' => true));
		// Menu
		$this->out('', 1, 0);
		$this->out('+--[ SuperBake ]------------------------------------------------+');
		$this->out('|                                           |   ___             |');
		$this->out('|                                           |   | | <success>Experiments</success> |');
		$this->out('|  This script generates plugins, models,   |  /   \    <success>Labs</success>    |');
		$this->out('|  views and controllers for them.          | (____\')           |');
		$this->out('|                                           |                   |');
		$this->out('+---------------------------------------------------------------+', 1, 0);
		$this->out('|                                                               |', 1, 0);
		$this->out('|  <info>Welcome to the SuperBake Shell.</info>                              |', 1, 0);
		$this->out('|  <info>This Shell can be quieter if launched with the -q option.</info>    |');
		$this->out('|                                                               |', 1, 0);
		$this->out('|  <warning>Read the doc before things turns bad</warning>                         |', 1, 0);
		$this->out('|                                                               |', 1, 0);
		$this->out('+---------------------------------------------------------------+', 1, 0);
		$this->out('|                                                               ', 1, 0);
		if ($this->prefixError == 1) {
			$this->out('|  <error>' . __d('superBake', 'Something is wrong with your prefixes. Check messages above.<') . '</error>', 1, 0);
			$this->out('|                                                               |', 1, 0);
		}
		$this->out('+--[ <error>' . __d('superBake', 'All Plugins') . '</error> ]', 1, 0);
		$this->out('|  ' . __d('superBake', '    [<bold>P</bold>]lugins (Creates all plugins structures)'), 1, 0);
		$this->out('|  ' . __d('superBake', '    [<bold>M</bold>]odels (Generates all models)'), 1, 0);
		$this->out('|  ' . __d('superBake', '    [<bold>C</bold>]ontrollers (Generates all controllers)'), 1, 0);
		$this->out('|  ' . __d('superBake', '    [<bold>V</bold>]iews (Generates all views)'), 1, 0);
		$this->out('|  ' . __d('superBake', '    [<bold>A</bold>]ll (Models, Controllers and Views)'), 1, 0);
		$this->out('|  ' . __d('superBake', '    [<bold>S</bold>]pecific entire plugin (M/C/V)'), 1, 0);
		$this->out('|                                                               ', 1, 0);
		$this->out('+--[ <error>' . __d('superBake', 'Model generation') . '</error> ]', 1, 0);
		$this->out('|  ' . __d('superBake', '    One plugin mo[<bold>D</bold>]els (All models for a specific plugin)'), 1, 0);
		$this->out('|  ' . __d('superBake', '    [<bold>B</bold>]ake one model'), 1, 0);
		$this->out('|                                                               ', 1, 0);
		$this->out('+--[ <error>' . __d('superBake', 'Controller generation') . '</error> ]', 1, 0);
		$this->out('|  ' . __d('superBake', '    [<bold>O</bold>]ne plugin controllers (All controllers for a specific plugin)'), 1, 0);
		$this->out('|  ' . __d('superBake', '    Bake one contro[<bold>L</bold>]ler'), 1, 0);
		$this->out('|                                                               ', 1, 0);
		$this->out('+--[ <error>' . __d('superBake', 'View generation') . '</error> ]', 1, 0);
		$this->out('|  ' . __d('superBake', '<info>View generation is based on the configuration file, and not</info>'));
		$this->out('|  ' . __d('superBake', '<info>from the existing controller. That means that if you have modified your</info>'));
		$this->out('|  ' . __d('superBake', '<info>controllers, the new actions will not be available.</info>'));
		$this->out('|  ' . __d('superBake', '    O[<bold>N</bold>]e plugin views (All views for a specific plugin)'), 1, 0);
		$this->out('|  ' . __d('superBake', '    Views using one [<bold>T</bold>]emplate (All the views using it)'), 1, 0);
		$this->out('|  ' . __d('superBake', '    V[<bold>I</bold>]ews for a given action name (All views for all actions with this name)'), 1, 0);
		$this->out('|  ' . __d('superBake', '    Bake a view for one [<bold>G</bold>]iven action (plugin/controller specific)'), 1, 0);
		$this->out('|  ' . __d('superBake', '    Views fo[<bold>R</bold>] one given controller'), 1, 0);
		$this->out('|                                                               ', 1, 0);
		$this->out('+--[ <error>' . __d('superBake', 'Misc') . '</error> ]', 1, 0);
		$this->out('|  ' . __d('superBake', '    M[<bold>E</bold>]nus (Generates menus)'), 1, 0);
		//$this->out('|  ' . __d('superBake', '    Con[<bold>F</bold>]ig (Interactive config file creation)'), 1, 0);
		$this->out('|  ' . __d('superBake', '    Config [<bold>J</bold>]anitor (Cleans and fills your config. Outputs the result)'), 1, 0);
		$this->out('|  ' . __d('superBake', '    Deb[<bold>U</bold>]g Config file (Helps you find errors)'), 1, 0);
		$this->out('|  ' . __d('superBake', '    [<bold>Q</bold>]uit'), 1, 0);
		$this->out('|                                                               ', 1, 0);
		// Used letters (just for info):
		// A B C D E F G H I J K L M N O P Q R S T U V W X Y Z
		// = = = = = = =   = =   = = = = = = = = = = =        

		$classToGenerate = strtoupper($this->in('+--> ' . __d('superBake', 'What would you like to generate ?'), array('A', 'B', 'C', 'D', 'E', /* 'F', */ 'G', 'I', 'J', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V')));
		switch ($classToGenerate) {
			// Plugins: -------------------------------------------------------
			case 'P': // Plugin structures
				$this->Plugins();
				break;
			case 'M': // Models
				$this->Models();
				break;
			case 'V': // Views
				$this->Views();
				break;
			case 'C': // Controllers
				$this->Controllers();
				break;
			case 'A': // MVC
				$this->MVC();
				break;
			case 'S': // MVC of a given plugin
				$this->pluginMVC();
				break;
			// Models: --------------------------------------------------------
			case 'D': // one plugin Models
				$this->pluginModels();
				break;
			case 'B': // one specific Model
				$this->Model();
				break;
			// Controllers: ---------------------------------------------------
			case 'O': // one plugin controllers
				$this->pluginControllers();
				break;
			case 'L': // one specific controller
				$this->Controller();
				break;
			// Views: ---------------------------------------------------------
			case 'N': // one plugin views
				$this->pluginViews();
				break;
			case 'T': // Views using one given templatePath
				$this->templateViews();
				break;
			case 'I': // Views of a given ActionName
				$this->actionViews();
				break;
			case 'G': // View for a given PluginName.ControllerName.ActionName
				$this->View();
				break;
			case 'R': // View for a give PluginName.ControllerName
				$this->controllerViews();
				break;
			// Misc:
			case 'E': //Menus
				$this->Menus();
				break;
//			case 'F': //Config
//				$this->out(__d('superBake', '<warning>This action is not ready yet. Sorry...</warning>'), 1, 0);
//				$this->Config();
//				break;
			case 'J': // Outputs a complete config.
				$this->Janitor();
				break;
			case 'U': // Helps in config file debugging
				$this->Debug();
				break;
			case 'Q': //Quit
				$this->_stop();
				break;
			default:
				$this->out(__d('superBake', '<warning>You have made an invalid selection. Please choose something to do.</warning>'), 1, 0);
		}
		$this->hr();
		//Loop
		$this->main();
	}

	/**
	 * Generates Models, Controllers and Views for every plugins.
	 * 
	 * Command line access:
	 *  $ cake superBake MVC
	 */
	public function MVC() {
		$this->speak(__d('superBake', 'Generating Models, Controllers and Views for ALL plugins.'), 'info', 0, 2, 2);
		$this->Models();
		$this->Controllers();
		$this->Views();
		$this->speak(__d('superBake', 'MVC generation complete.'), 'success', 0, 2, 2);
	}

	/**
	 * Generates Models, Controllers and Views for a given plugin
	 * 
	 * Command line access:
	 *  $ cake superBake pluginMVC
	 *  $ cake superBake pluginMVC PluginName
	 */
	public function pluginMVC() {

		$this->speak(__d('superBake', 'Building ALL models, controllers and views for a plugin'), 'info', 0, 2);

		$args = $this->_checkArgs(1, array('p'));

		//Manually set the args value
		$this->args[0] = $args['plugin'];

		$this->pluginModels();
		$this->pluginControllers();
		$this->pluginViews();
		unset($this->args[0]);
	}

	/**
	 * Generates all plugins structures
	 * 
	 * Command line access:
	 *  $ cake superBake plugins
	 */
	public function Plugins() {
		$this->speak(__d('superBake', 'Building all plugins structure'), 'info', 0, 2);
		$updateBootstrap = strtoupper($this->in(__d('superBake', 'Do we have to update the app\'s bootstrap file ?'), array('y', 'n'), 'n'));
		$this->out();
		$this->hr();
		$this->out(__d('superBake', "<info>Plugins generation summary:</info>"));
		$this->hr();

		//Passing project configuration to superPlugin Task
		//$this->SuperPlugin->projectConfig = $this->projectConfig;
		$this->SuperPlugin->updateBootstrap = $updateBootstrap;

		// Get plugin list
		$plugins = $this->_getPluginList();
		// Executes generation
		foreach ($plugins as $plugin) {
			if ($plugin != $this->projectConfig['general']['appBase']) {
				//Giving the name of the current plugin to superPlugin
				$this->SuperPlugin->currentPlugin = $plugin;
				$this->SuperPlugin->pluginConfig = $this->projectConfig['plugins'][$plugin];
				$this->SuperPlugin->execute();
			}
		}
		if ($updateBootstrap == 'Y') {
			$this->hr();
			$this->out(__d('superBake', "<info>The bootstrap file has been updated. Please re-launch SuperBake\n" .
							"to reload the new configuration (if you want to use it more)</info>"));
			$this->hr();
			$this->_stop();
		}
	}

	/**
	 * Returns an array of plugin names
	 * 
	 * @return array List of plugins
	 */
	private function _getPluginList() {
		$plugins = array();

		foreach ($this->projectConfig['plugins'] as $plugin => $vals) {
			$plugins[] = $plugin;
		}
		return $plugins;
	}

	/**
	 * Asks the user for a plugin name 
	 * 
	 * @return string Plugin name
	 */
	private function _getPluginName() {
		$plugins = $this->_getPluginList();

		$count = count($plugins);

		$this->out(__d('cake_console', 'Possible Plugins based on your current config file:'));
		$len = strlen($count + 1);
		for ($i = 0; $i < $count; $i++) {
			$this->out(sprintf("%${len}d. %s", $i + 1, $plugins[$i]));
		}
		$enteredPlugin = '';

		while (!$enteredPlugin) {
			$enteredPlugin = $this->in(__d('cake_console', "Enter a number from the list above, or 'q' to exit"), null, 'q');

			if ($enteredPlugin === 'q') {
				$this->out(__d('cake_console', 'Exit'));
				$this->_stop();
			}
			$intEnteredPlugin = intval($enteredPlugin);
			if (empty($intEnteredPlugin) || $intEnteredPlugin > $count) {
				$this->err(__d('cake_console', "The plugin name you supplied was empty,\n" .
								"or the number you selected was not an option. Please try again."));
				$enteredPlugin = '';
			}
		}
		return $plugins[$enteredPlugin - 1];
	}

	/**
	 * Generates a given model
	 * 
	 * Command line access:
	 *  $ cake superBake model
	 *  $ cake superBake model PluginName.ModelName
	 */
	public function Model() {
		$this->speak(__d('superBake', 'Building unique model'), 'info', 0, 2);
		$args = $this->_checkArgs(2, array('p', 'm'));
		$this->_model($args['plugin'], $args['model']);
		$this->speak(__d('superBake', '"%s" model has been generated in plugin "%s"', array($args['model'], $args['plugin'])), 'success', 0, 2, 2);
	}

	/**
	 * Generates all models in all plugins
	 * 
	 * Command line access:
	 *  $ cake superBake models
	 */
	public function Models() {
		$this->speak(__d('superBake', 'Building ALL models for ALL plugins'), 'info', 0, 2);
		$plugins = $this->_getPluginList();
		foreach ($plugins as $plugin) {
			$this->speak(__d('superBake', 'Generating models for plugin %s...', $plugin), 'info', 0, 0, 1);
			$models = $this->_getModelList($plugin);
			foreach ($models as $model) {
				$this->_model($plugin, $model);
			}
		}
		$this->out('', 1, 0);
		$this->speak(__d('superBake', 'Models generation complete'), 'success', 0, 2, 2);
	}

	/**
	 * Generates all models in a given plugin
	 * 
	 * Command line access:
	 *  $ cake superBake pluginModels
	 *  $ cake superBake pluginModels PluginName
	 */
	public function pluginModels() {
		$this->speak(__d('superBake', 'Building ALL models for a plugin'), 'info', 0, 2);
		$args = $this->_checkArgs(1, array('p'));
		$models = $this->_getModelList($args['plugin']);
		foreach ($models as $model) {
			$this->_model($args['plugin'], $model);
		}
	}

	/**
	 * Generates the model in the plugin
	 * 
	 * @param string $plugin
	 * @param string $model
	 */
	private function _model($plugin, $model) {
		// @todo move this elsewhere to speed up generation
		// --------------------------------------------------------------------
		// Template to use
		if ($this->projectConfig['general']['askForTemplate'] == false) {
			$this->SuperModel->params['theme'] = $this->projectConfig['general']['defaultTemplate'];
		}
		$this->SuperModel->projectConfig = $this->projectConfig;
		//---------------------------------------------------------------------
		if ($plugin == $this->projectConfig['general']['appBase']) {
			$this->SuperModel->currentPlugin = null;
		} else {
			$this->SuperModel->currentPlugin = $plugin;
		}
		$this->SuperModel->currentModel = $model;
		$this->SuperModel->currentPart = $this->_getModelPart($model, $plugin);
		$this->SuperModel->currentModelConfig = $this->projectConfig['plugins'][$plugin]['parts'][$this->SuperModel->currentPart]['model'];
		$this->SuperModel->execute();
	}

	/**
	 * Returns the part name of a model.
	 * @param string $model Model to find
	 * @param string $plugin Plugin where the model is
	 * @return mixed Part name or false.
	 */
	private function _getModelPart($model, $plugin) {
		foreach ($this->projectConfig['plugins'][$plugin]['parts'] as $part => $partConfig) {
			if (!empty($partConfig['model']) && $partConfig['model']['name'] == $model) {
				return $part;
			}
		}
		$this->speak(__d('superBake', 'Model %s could not be found in plugin %s. Check your superBake config file.', array($model, $plugin)), 'error', 1, 2, 2);
		return false;
	}

	/**
	 * Returns an array of model names in the given plugin
	 * 
	 * @param string $plugin Plugin name
	 * @return array List of models
	 */
	private function _getModelList($plugin) {
		$models = array();
		// Looking for parts
		foreach ($this->projectConfig['plugins'][$plugin]['parts'] as $part) {
			if (!empty($part['model'])) {
				$models[] = $part['model']['name'];
			}
		}
		return $models;
	}

	/**
	 * Asks the user to choose a model name in a plugin's model list.
	 * 
	 * @param string $plugin Plugin name
	 * @return string Choosen model name
	 */
	private function _getModelName($plugin) {
		$models = $this->_getModelList($plugin);

		$count = count($models);

		$this->out(__d('cake_console', 'Possible Models for the "%s" plugin, based on your current config file:', $plugin));
		$len = strlen($count + 1);
		for ($i = 0; $i < $count; $i++) {
			$this->out(sprintf("%${len}d. %s", $i + 1, $models[$i]));
		}
		$enteredModel = '';

		while (!$enteredModel) {
			$enteredModel = $this->in(__d('cake_console', "Enter a number from the list above, or 'q' to exit"), null, 'q');

			if ($enteredModel === 'q') {
				$this->out(__d('cake_console', 'Exit'));
				$this->_stop();
			}
			$intEnteredModel = intval($enteredModel);
			if (empty($intEnteredModel) || $intEnteredModel > $count) {
				$this->err(__d('cake_console', "The Model name you supplied was empty,\n" .
								"or the number you selected was not an option. Please try again."));
				$enteredModel = '';
			}
		}
		return $models[$enteredModel - 1];
	}

	/**
	 * Generates a controller from a plugin
	 * 
	 * Command line access :
	 *  $ cake superBake controller
	 *  $ cake superBake controller PluginName.ControllerName
	 * 
	 */
	public function Controller() {
		$this->speak(__d('superBake', 'Building unique controller'), 'info', 0, 2);

		$args = $this->_checkArgs(2, array('p', 'c'));

		$this->_controller($args['plugin'], $args['controller']);

		$this->out('', 1, 0);
		$this->speak(__d('superBake', '"%s" controller has been generated in plugin "%s"', array($args['controller'], $args['plugin'])), 'success', 0, 2, 2);
	}

	/**
	 * Generates all models in all plugins
	 * 
	 * Command line access:
	 *  $ cake superBake controllers
	 */
	public function Controllers() {

		$this->speak(__d('superBake', 'Building ALL controllers, for ALL plugins'), 'info', 0, 2);

		$plugins = $this->_getPluginList();
		foreach ($plugins as $plugin) {
			$this->speak(__d('superBake', 'Generating controllers for plugin %s...', $plugin), 'info', 0, 0, 1);
			$controllers = $this->_getControllerList($plugin);
			foreach ($controllers as $controller) {
				$this->_controller($plugin, $controller);
			}
		}
		$this->out('', 1, 0);
		$this->speak(__d('superBake', 'Controller generation complete'), 'success', 0, 2, 2);
	}

	/**
	 * Generates all controllers in a given plugin
	 * 
	 * Command line access:
	 *  $ cake superBake pluginControllers
	 *  $ cake superBake pluginControllers PluginName
	 */
	public function pluginControllers() {

		$this->speak(__d('superBake', 'Building ALL controllers for a plugin'), 'info', 0, 2);

		$args = $this->_checkArgs(1, array('p'));

		$controllers = $this->_getModelList($args['plugin']);

		foreach ($controllers as $controller) {
			$this->_controller($args['plugin'], $controller);
		}
	}

	/**
	 * Returns an array of controllers names for the given plugin
	 * 
	 * @param string $plugin Plugin name
	 * @return array Controller names
	 */
	private function _getControllerList($plugin) {
		$controllers = array();
		foreach ($this->projectConfig['plugins'][$plugin]['parts'] as $part) {
			if (!empty($part['controller'])) {
				$controllers[] = $part['controller']['name'];
			}
		}
//		foreach ($this->projectConfig['plugins'][$plugin]['models'] as $controller => $config) {
//			if ($this->_isController($controller, $plugin)) {
//				$controllers[] = $controller;
//			}
//		}
		return $controllers;
	}

	/**
	 * Returns true if a given controller is really a controller, or false if it's a standalone
	 * model
	 * 
	 * @param string $controller Controller name
	 * @param string $plugin Plugin name
	 * @return booleanModel
	 */
//	private function _isController($controller, $plugin) {
//		if (isset($this->projectConfig['plugins'][$plugin]['models'][$controller]['hasController']) && $this->projectConfig['plugins'][$plugin]['models'][$controller]['hasController'] == false) {
//			return false;
//		} else {
//			return true;
//		}
//	}

	/**
	 * Asks the user to choose a controller name in a plugin's model list.
	 * 
	 * @param string $plugin Plugin name
	 * @return string Choosen controller name
	 */
	private function _getControllerName($plugin) {
		$controllers = $this->_getControllerList($plugin);

		$count = count($controllers);

		$this->out(__d('cake_console', 'Possible Controllers for the "%s" plugin, based on your current config file:', $plugin));
		$len = strlen($count + 1);
		for ($i = 0; $i < $count; $i++) {
			$this->out(sprintf("%${len}d. %s", $i + 1, $controllers[$i]));
		}
		$enteredController = '';

		while (!$enteredController) {
			$enteredController = $this->in(__d('cake_console', "Enter a number from the list above, or 'q' to exit"), null, 'q');

			if ($enteredController === 'q') {
				$this->out(__d('cake_console', 'Exit'));
				$this->_stop();
			}
			$intEnteredController = intval($enteredController);
			if (empty($intEnteredController) || $intEnteredController > $count) {
				$this->err(__d('cake_console', "The Controller name you supplied was empty,\n" .
								"or the number you selected was not an option. Please try again."));
				$enteredController = '';
			}
		}
		return $controllers[$enteredController - 1];
	}

	function Janitor() {
		$this->out('##');
		$this->out('## ' . __d('superBake', 'Delete me, and everything above if you want to have a valid file.'));
		$this->out('## ' . __d('superBake', 'Tip : you can run the shell with the quiet option ($ cake superBake Janitor -q)'));
		$this->out('## ' . __d('superBake', 'to get rid of all the text above and keep only what you wanted : the config file.'));
		$this->out('##');
		$this->out('## ' . __d('superBake', 'This file will not be valid until you remove the above lines.'));
		$this->out('##');
		$this->out();
		$this->out('##', 1, 0);
		$this->out('## ' . __d('superBake', 'File generated with superBake on %s', date('l Y-m-j h:i:s A')), 1, 0);
		$this->out('##', 1, 0);
		$this->out('## ' . __d('superBake', 'What\'s next ?'), 1, 0);
		$this->out('## ' . __d('superBake', 'Next, you should copy/paste parts that are important to you, and replace them in your configuration file. And customize them :)'), 1, 0);
		$this->out('##', 1, 0);
		$this->out(Spyc::YAMLDump($this->projectConfig), 1, 0);
		$this->_stop();
	}

	/**
	 * This method don't do anything special, except explaining the user how to
	 * access the file config "debug mode" which is not really a mode and can be annoying
	 * for people.
	 */
	function Debug() {
		$this->speak('In order to debug your config file, launch superBake in verbose mode :', 'info', 0);
		$this->speak('./Console/cake superBake debug -v', 'info', 0, 2, 1);
		$this->speak("Note that you'll have this 'debug' feature on whatever you launch with the -v option", 'info', 0);
		$this->_stop();
	}

	/**
	 * Bakes a controller
	 * 
	 * @param string $plugin Plugin name
	 * @param string $controller Controller name (can be a model name)
	 */
	private function _controller($plugin, $controller) {
		// Project Config
		// @todo move this elsewhere to speed up generation
		// --------------------------------------------------------------------
		$part = $this->getControllerPart($controller, $plugin);
		// Template to use
		if ($this->projectConfig['general']['askForTemplate'] == false) {
			$this->SuperController->params['theme'] = $this->projectConfig['general']['defaultTemplate'];
		}
		$this->SuperController->projectConfig = $this->projectConfig;
		//---------------------------------------------------------------------
		// Plugin
		if ($plugin == $this->projectConfig['general']['appBase']) {
			$this->SuperController->currentPlugin = null;
		} else {
			$this->SuperController->currentPlugin = $plugin;
		}

		// Part
		$this->SuperController->currentPart = $part;
		// Controller
		$this->SuperController->currentController = $controller;
		// Controller config
		$this->SuperController->currentControllerConfig = $this->projectConfig['plugins'][$plugin]['parts'][$part]['controller'];
		// Model
		if ($this->projectConfig['plugins'][$plugin]['parts'][$part]['controller']['hasModel'] == true) {
			$this->SuperController->currentModel = $this->projectConfig['plugins'][$plugin]['parts'][$part]['model']['name'];
		}
		// Model config
		$this->SuperController->currentModelConfig = $this->projectConfig['plugins'][$plugin]['parts'][$part]['controller'];

		$this->SuperController->execute();
	}

	/**
	 * Generates a view for a given plugin/controller/action
	 * 
	 * Command line access:
	 *  $ cake superBake view
	 *  $ cake superBake view PluginName.ControllerName.ActionName
	 */
	public function View() {

		$this->speak(__d('superBake', 'Building unique view'), 'info', 0, 2);

		$args = $this->_checkArgs(3, array('p', 'c', 'v'));

		$this->_view($args['plugin'], $args['controller'], $args['action']);

		$this->out('', 1, 0);
		$this->speak(__d('superBake', '"%s" view has been generated for controller "%s" in plugin "%s"', array($args['action'], $args['controller'], $args['plugin'])), 'success', 0, 2, 2);
	}

	/**
	 * Generates all plugin's views
	 * 
	 * Command line access:
	 *  $ cake superBake views
	 */
	public function Views() {

		$this->speak(__d('superBake', 'Building ALL views for ALL controllers'), 'info', 0, 2);

		$plugins = $this->_getPluginList();
		foreach ($plugins as $plugin) {
			$this->speak(__d('superBake', 'Generating views for plugin %s...', $plugin), 'info', 0, 1, 1);
			$controllers = $this->_getControllerList($plugin);
			foreach ($controllers as $controller) {
				$this->speak(__d('superBake', 'Generating views for controller %s...', $controller), 'info', 0, 0, 1);
				$actions = $this->getActionList($plugin, $controller);
				foreach ($actions as $action) {
					$this->_view($plugin, $controller, $action);
				}
			}
		}
		$this->speak(__d('superBake', 'View generation complete'), 'success', 0, 2, 2);
	}

	/**
	 * Generates all views of a given plugin
	 * 
	 * Command line access:
	 *  $ cake superBake pluginViews
	 *  $ cake superBake pluginViews PluginName
	 */
	public function pluginViews() {

		$this->speak(__d('superBake', 'Building ALL views for a plugin'), 'info', 0, 2);

		$args = $this->_checkArgs(1, array('p'));

		$plugin = $args['plugin'];

		$controllers = $this->_getModelList($plugin);
		foreach ($controllers as $controller) {
			$actions = $this->getActionList($plugin, $controller);
			foreach ($actions as $action) {
				$this->_view($plugin, $controller, $action);
			}
		}
	}

	/**
	 * Generates all views of a given plugin/controller
	 * 
	 * Command line access:
	 *  $ cake superBake controllerViews
	 *  $ cake superBake controllerViews PluginName.ControllerName
	 */
	public function controllerViews() {

		$args = $this->_checkArgs(1, array('p', 'c'));

		$plugin = $args['plugin'];
		$controller = $args['controller'];

		$this->speak(__d('superBake', 'Building views for controller "%s.%s"', array($plugin, $controller)), 'info', 0, 2);
		$actions = $this->getActionList($plugin, $controller);

		foreach ($actions as $action) {
			$this->_view($plugin, $controller, $action);
		}
		$this->speak(__d('superBake', 'Views for "%s.%s" have been generated', array($plugin, $controller)), 'success', 0, 2, 2);
	}

	/**
	 * Generates a view for a given plugin/controller/action
	 * 
	 * @param string $plugin Plugin name
	 * @param string $part Part name
	 * @param string $controller Controller name
	 * @param string $action Action name
	 */
	private function _view($plugin, $controller, $action) {
		// @todo move this elsewhere to speed up generation
		// --------------------------------------------------------------------
		// Template to use
		if ($this->projectConfig['general']['askForTemplate'] == false) {
			$this->SuperView->params['theme'] = $this->projectConfig['general']['defaultTemplate'];
		}
		$this->SuperView->projectConfig = $this->projectConfig;
		//---------------------------------------------------------------------
		if ($plugin == $this->projectConfig['general']['appBase']) {
			$this->SuperView->currentPlugin = null;
		} else {
			$this->SuperView->currentPlugin = $plugin;
		}
		$this->SuperView->currentController = $controller;
		$part = $this->getControllerPart($controller, $plugin);
		$this->SuperView->currentControllerConfig = $this->projectConfig['plugins'][$plugin]['parts'][$part]['controller'];
		$complete_action = explode('_', $action);
		if (in_array($complete_action[0], Configure::read('Routing.prefixes'))) {
			$currentPrefix = $complete_action[0];
		} else {
			$currentPrefix = 'public';
		}
		$currentAction = str_replace($currentPrefix . '_', '', $action);
		$this->SuperView->currentViewConfig = $this->projectConfig['plugins'][$plugin]['parts'][$part]['views'][$currentPrefix][$currentAction];
		$this->SuperView->currentAction = $action;
		$this->SuperView->execute();

		/* if ($this->SuperView->Template->missing_config_state == 1) {
		  $this->hr();
		  $this->out(__d('superBake', '<error>superBake configuration is missing.</error>'), 1, Shell::QUIET);
		  foreach ($this->SuperView->Template->missing_config as $k => $v) {
		  if ($v == 1) {
		  $this->out(__d('superBake', '<warning>Model "%s" is not defined in your superBake configuration. All links related to its actions have not been built in related views</warning>', $k), 1, Shell::QUIET);
		  }
		  }
		  } */
		$this->hr();
	}

	private function _getPart($plugin, $controller) {
		foreach ($this->projectConfig['plugins'][$plugin]['parts'] as $part => $partConfig) {
			if (isset($partConfig['controller']) && $partConfig['controller']['name'] == $controller) {
				return $part;
			}
		}
		return null;
		$this->speak(__d('superBake', "%s controller not found in plugin %s", array($controller, $plugin)), 'warning', 0);
	}

	public function allowedActions($controller, $prefix) {
		
	}

	/**
	 * Ask user to choose an action for a given plugin/controller
	 * @param string $plugin Plugin name
	 * @param string $controller Controller name
	 * @return string Choosen action name
	 */
	private function _getActionName($plugin, $controller) {
		$actions = $this->getActionList($plugin, $controller);

		$count = count($actions);

		$this->out(__d('cake_console', 'Possible Actions for the "%s" plugin, based on your current config file:', $plugin));
		$len = strlen($count + 1);
		for ($i = 0; $i < $count; $i++) {
			$this->out(sprintf("%${len}d. %s", $i + 1, $actions[$i]));
		}
		$enteredAction = '';

		while (!$enteredAction) {
			$enteredAction = $this->in(__d('cake_console', "Enter a number from the list above, or 'q' to exit"), null, 'q');

			if ($enteredAction === 'q') {
				$this->out(__d('cake_console', 'Exit'));
				$this->_stop();
			}

			if (!$enteredAction || intval($enteredAction) > $count) {
				$this->err(__d('cake_console', "The Action name you supplied was empty,\n" .
								"or the number you selected was not an option. Please try again."));
				$enteredAction = '';
			}
		}
		return $actions[$enteredAction - 1];
	}

	/**
	 * 
	 */
	public function Menus() {
		$this->SuperMenu->params['theme'] = $this->projectConfig['general']['defaultTemplate'];
		$this->SuperMenu->projectConfig = $this->projectConfig;
		// Walking menus
		foreach ($this->projectConfig['menus'] as $menu => $config) {
			$this->out(__d('superBake', 'Generating menu %s', $menu));
			$this->SuperMenu->currentMenu = $menu;
			$this->SuperMenu->currentMenuConfig = $config;
			$this->SuperMenu->execute();
		}
	}

	/**
	 * Dumps the project Config array as PHP array. Use this for debug purpose.
	 */
	public function dump() {
		var_export($this->projectConfig);
	}

	/**
	 * get the option parser.
	 *
	 * @return void
	 */
	public function getOptionParser() {
		//$parser = parent::getOptionParser();
		$name = ($this->plugin ? $this->plugin . '.' : '') . $this->name;
		$parser = new ConsoleOptionParser($name);
		return $parser->description(__d('superBake', 'The Super Bake shell generates plugins, models, views, controllers or both Models/Views/Controllers ' .
								'for your application.' .
								' If run with no command line arguments, Super Bake guides the user through the creation process.' .
								' You can customize the generation process by telling Super Bake where different parts of your application are using command line arguments.'
				))->addSubcommand('plugins', array(
					'help' => __d('superBake', 'Creates all the plugins directories'),
					'parser' => $this->SuperPlugin->getOptionParser()
				))->addSubcommand('models', array(
					'help' => __d('superBake', 'Bakes all the models, in their specfic plugin dir.'),
					'parser' => $this->SuperModel->getOptionParser()
				))->addSubcommand('controllers', array(
					'help' => __d('superBake', 'Bakes all controllers in their specific plugin dir.'),
					'parser' => $this->SuperController->getOptionParser()
				))->addSubcommand('views', array(
					'help' => __d('superBake', 'Bakes all views, for controllers methods, in their specific plugin dir'),
					'parser' => $this->SuperView->getOptionParser()
				))->addSubcommand('mvc', array(
					'help' => __d('superBake', 'Bakes all Models/Controllers/Views, in their specific plugin dirs.'),
				))->addSubcommand('menus', array(
					'help' => __d('superBake', 'Creates the menu file(s).'),
		));
	}

	/**
	 * Checks if $this->args[0] has a $nb number of arguments and if the $types corresponds.
	 * 
	 * For $type, possible values are : 
	 * 		p, for plugin
	 * 		m, for model
	 * 		c, for controller
	 * 		v, for view/action
	 * Note that the array must have the "good" order. i.e.: if you wait for a command line argument
	 * like "PluginName.ControllerName.ActionName", then the function may be called by:
	 * $this->checkArgs(3, array('p', 'c', 'v'));
	 * It will return this array:
	 * array('plugin'=>'PluginName', 'controller'=>ControllerName, 'action'=>'ActionName')
	 * 
	 * @param int $nb Number of arguments wanted
	 * @param array $types array of wanted types
	 * @param string $message Message thrown if the number of arguments submited is invalid
	 * @return array An array with associated plugins/controllers...
	 */
	private function _checkArgs($nb, $types, $message = null) {
		$args = array();
		$return = array();
		$interactive = true;
		if (!empty($this->args)) {
			// Parsing args
			$args = explode('.', $this->args[0]);
			if (count($args) == $nb) {
				//Check first arg
				$i = 0;
				foreach ($types as $type) {
					switch ($type) {
						case 'p': //plugin
							$plugin = null;
							$plugins = $this->_getPluginList();
							if (in_array($args[$i], $plugins)) {
								$plugin = $args[$i];
							} else {
								$this->speak(__d('superBake', "The submited plugin doesn't exists in config file\nMaybe it's just a typo...\nPlease, select one below:"), 'warning', 0);
								$plugin = $this->_getPluginName();
							}
							$return['plugin'] = $plugin;
							break;
						case 'm': //model
							$model = null;
							$models = $this->_getModelList($plugin);
							if (in_array($args[$i], $models)) {
								$model = $args[$i];
							} else {
								$this->speak(__d('superBake', "The submited model doesn't exists in config file\nMaybe it's just a typo...\nPlease, select one below:"), 'warning', 0);
								$model = $this->_getModelName($plugin);
							}
							$return['model'] = $model;
							break;
						case 'c': //controller
							$controller = null;
							$controllers = $this->_getControllerList($plugin);
							if (in_array($args[$i], $controllers)) {
								$controller = $args[$i];
							} else {
								$this->speak(__d('superBake', "The submited controller doesn't exists in config file\nMaybe it's just a typo...\nPlease, select one below:"), 'warning', 0);
								$controller = $this->_getControllerName();
							}
							$return['controller'] = $controller;
							break;
						case 'v': //action in controller
							$action = null;
							$actions = $this->getActionList($plugin, $controller);
							if (in_array($args[$i], $actions)) {
								$action = $args[$i];
							} else {
								$this->speak(__d('superBake', "The submited action doesn't exists in config file\nMaybe it's just a typo...\nPlease, select one below:"), 'warning', 0);
								$action = $this->_getActionName($plugin, $controller);
							}
							$return['action'] = $action;
							break;
						case 's': // Part in plugin
							$part = null;
							$parts = $this->_getPartList($plugin);
							if (in_array($args[$i], $parts)) {
								$part = $args[$i];
							} else {
								$this->speak(__d('superBake', "The submited part doesn't exists in config file\nMaybe it's just a typo...\nPlease, select one below:"), 'warning', 0);
								$part = $this->_getPartName($plugin);
							}
							$return['part'] = $part;
							break;
						case 't': //template
							break;
						default:
							break;
					}
					$i++;
				}
				$interactive = false;
			} else {
				if (is_null($message)) {
					$message = __d('superBake', 'Invalid argument count, switching to interactive mode.');
				}
				$this->speak($message, 'warning', 0);
			}
		}
		if ($interactive == true) {
			//forced interactive mode
			// I know it's a bit redundant, but I don't see how to keep the "typo correction" on submited values (above)
			foreach ($types as $type) {
				switch ($type) {
					case 'p': //plugin
						$this->speak(__d('superBake', "Please, select a plugin below:"), 'warning', 0);
						$plugin = $this->_getPluginName();
						$return['plugin'] = $plugin;
						break;
					case 'm': //model
						$this->speak(__d('superBake', "Please, select a model below:"), 'warning', 0);
						$model = $this->_getModelName($plugin);
						$return['model'] = $model;
						break;
					case 'c': //controller
						$this->speak(__d('superBake', "Please, select a controller below:"), 'warning', 0);
						$controller = $this->_getControllerName($plugin);
						$return['controller'] = $controller;
						break;
					case 'v': //action
						$this->speak(__d('superBake', "Please, select an action below:"), 'warning', 0);
						$action = $this->_getActionName($plugin, $controller);
						$return['action'] = $action;
						break;
					/* case 'a': // action for actionView()
					  $this->speak(__d('superBake', "Please, select an action below:"), 'warning', 0);
					  $action = $this->_getAllActionName();
					  $return['action'] = $action;
					  break; */
					case 't': //template
						break;
					default:
						break;
				}
			}
		}
		return $return;
	}

	/**
	 * Loads the configuration files and make an array of it.
	 * 
	 * @return boolean
	 */
	private function _loadConfig() {
		if ($this->initialized == 1) {
			$this->out('AppShell already initialized');
			return true;
		}

		$configFile = dirname(dirname(__FILE__)) . DS . 'superBakeConfig.yml';

		// Array from config file
		$fileConfig = array();

		//Complete array of options/values
		$projectConfig = array();

		// Getting config file contents
		if (file_exists($configFile)) {
			// Getting vars
			$fileConfig = Spyc::YAMLLoad($configFile);

			$this->prefixError = 0;

			//
			// Creating the complete array:
			//
			//
	
			// General
			$projectConfig['general'] = $fileConfig['general'];

			// Defaults
			$projectConfig['defaults'] = $fileConfig['defaults'];

			//
			//Checking template dir
			if (!is_dir(__DIR__ . DS . '..' . DS . 'Templates' . DS . $projectConfig['general']['defaultTemplate'])) {
				$this->speak(__d('superBake', "The '%s' template is not present in the app/Console/Template dir./n Please create it.", $projectConfig['general']['defaultTemplate']), 'error', 0, 2, 1);
				$this->_stop();
			}

			//Prefixes
			//
			$this->speak("Reading prefixes", 'info', 2);
			// Checking for prefixes
			if ($projectConfig['general']['usePrefixes'] == true) {
				$cakePrefixes = Configure::read('Routing.prefixes');
				if (!is_array($cakePrefixes)) {
					$this->out(__d('superBake', '<warning>The "Routing.prefixes" var is empty (core.php)</warning>'), 1, 0);
					$cakePrefixes = array();
				}
				$this->speak("|    ...From core.php, to compare them with your config file.", 'info', 2);
				// used prefixes -> Routing.prefixes
				foreach ($projectConfig['defaults']['controllers']['actions'] as $prefix => $v) {
					if (!in_array($prefix, $cakePrefixes) && $prefix != 'public') {
						$this->out(__d('superBake', '<error>Prefix %s, present in your config file, is not present in the routing prefixes array (core.php)</error>', $prefix), 1, 0);
						$this->out(__d('superBake', '<warning>This will bring trouble.</warning>'), 1, 0);
						//$this->speak("|    ...But prefix $prefix, defined in your config file is not present in core.php.", 'error');
						$this->prefixError = 1;
					}
					$configPrefixes[] = $prefix;
				}
				// Routing.prefixes -> used prefixes
				foreach ($cakePrefixes as $k => $prefix) {
					if (!in_array($prefix, $configPrefixes)) {
						$this->out(__d('superBake', '<error>Prefix %s, present in the routing prefixes array (core.php), is not present in your config file</error>', $prefix), 1, 0);
						$this->out(__d('superBake', '<warning>This will bring trouble.</warning>'), 1, 0);
						//$this->speak("|    ...But prefix $prefix, defined in your core.php file is not present in your config file.", 'error');
						$this->prefixError = 1;
					}
				}
			} else {
				$this->speak("|    ... And we only use prefixes from the config file only (no check from core.php).", 'warning', 2);
			}

			//
			// Default actions : filling default actions with default values
			foreach ($projectConfig['defaults']['controllers']['actions'] as $prefix => $actions) {
				foreach ($actions as $action => $actionConfig) {
					$projectConfig['defaults']['controllers']['actions'][$prefix][$action] = $projectConfig['defaults']['action'];
					foreach ($actionConfig as $k => $v) {
						$projectConfig['defaults']['controllers']['actions'][$prefix][$action][$k] = $v;
					}
				}
			}
			//
			// Default views : filling default views with default values
			foreach ($projectConfig['defaults']['views'] as $prefix => $views) {
				foreach ($views as $view => $viewConfig) {
					// Replacing view config by default
					$projectConfig['defaults']['views'][$prefix][$view] = $projectConfig['defaults']['view'];
					// Merging defaults with defined values
					$this->_mymerge($projectConfig['defaults']['views'][$prefix][$view], $viewConfig);
				}
			}
			$this->speak("Checking plugins", 'info', 2);
			// Plugins
			foreach ($fileConfig['plugins'] as $plugin => $pluginConfig) {
				$this->speak("+->  Config for plugin $plugin", 'info', 2);
				// General plugin config
				foreach ($projectConfig['defaults']['plugins'] as $key => $val) {
					if (!isset($pluginConfig[$key]) && $key != 'parts') {
						$projectConfig['plugins'][$plugin][$key] = $val;
					}
				}
				if ($plugin == $projectConfig['general']['appBase']) {
					unset($projectConfig['plugins'][$plugin]['pluginDir']);
					$this->speak("|    |    ...This is not a plugin, just /app/ config", 'info', 2);
				}

				// Parts:
				foreach ($pluginConfig['parts'] as $part => $partConfig) {
					$actionsList = array();
					$this->speak("|    +->  Config for $plugin.$part", 'info', 2);
					$hasModel = false;
					$hasController = false;
					$hasViews = false;
					// If part is empty, we assume it has a model/controller and views based on part name.
					if (!is_array($partConfig)) {
						$this->speak("|    |    +->  This part is defined on one line, so I create a MVC config with part name.", 'warning', 2);
						//
						// Models
						$hasModel = true;
						// Loading default model structure
						$partConfig['model'] = $projectConfig['defaults']['models'];
						$partConfig['model']['name'] = Inflector::classify($part);
						//
						//Controllers
						$hasController = true;
						// Loading default controller structure
						$partConfig['controller'] = $projectConfig['defaults']['controllers'];
						$partConfig['controller']['name'] = $part;
						//
						// Views
						$hasViews = true;
						$partConfig['views'] = $projectConfig['defaults']['views'];
						$this->speak("|    |    |    Model and Controller will be '$part', and default views will be added to config.", 'info', 2);
						//
						// Everything should be good, except for individual views configuration.
					} else {
						$this->speak("|    |    |    This part is a little bit complete", 'info', 2);
						// Checking for 'model' entry
						if (isset($partConfig['model'])) {
							$this->speak("|    |    |    +->  Model section defined", 'info', 2);
							$hasModel = true;
							//
							// Model configuration
							// Is model well defined?
							if (!is_array($partConfig['model'])) {
								$this->speak("|    |    |    |    ...On one line only...", 'warning', 2);
								// Model name
								if (empty($partConfig['model'])) {
									$this->speak("|    |    |    |    ...And no name is set, so I use part name as model name...", 'warning', 2);
									// No name set, using part name;	
									$modelName = Inflector::classify($part);
								} else {
									$modelName = $partConfig['model'];
								}
								// Loading defaults
								$partConfig['model'] = $projectConfig['defaults']['models'];
							} else {
								$this->speak("|    |    |    |    ...And has detailed values...", 'info', 2);
								// Model name
								if (!isset($partConfig['model']['name'])) {
									$this->speak("|    |    |    |    ...but no name...", 'warning', 2);
									$modelName = Inflector::classify($part);
								} else {
									$modelName = $partConfig['model']['name'];
								}
								// Copying defined values
								$modelConfig = $partConfig['model'];
								$partConfig['model'] = $projectConfig['defaults']['models'];
								// restoring defined values
								foreach ($modelConfig as $k => $v) {
									$partConfig['model'][$k] = $v;
								}
							}
							// restoring name
							$partConfig['model']['name'] = $modelName;
							$this->speak("|    |    |    |    +->  Model name is '$modelName'", 'info', 2);
						}
						//Checking for 'controller' entry
						if (isset($partConfig['controller'])) {
							$this->speak("|    |    |    +->  Controller section defined", 'info', 2);
							$hasController = true;
							//
							// Controller configuration
							// Is controller well defined?
							if (!is_array($partConfig['controller'])) {
								$this->speak("|    |    |    |    ...On one line only...", 'warning', 2);
								// Controller name
								if (empty($partConfig['controller'])) {
									$this->speak("|    |    |    |    ...And no name is set, so I use part name...", 'warning', 2);
									// No name set, using part name;	
									$controllerName = $part;
								} else {
									$controllerName = $partConfig['controller'];
								}
								// Loading defaults
								$partConfig['controller'] = $projectConfig['defaults']['controllers'];
							} else {
								$this->speak("|    |    |    |    ...And has detailed values...", 'info', 2);
								// Controller name
								if (!isset($partConfig['controller']['name'])) {
									$this->speak("|    |    |    |    ...but no name...", 'warning', 2);
									$controllerName = $part;
								} else {
									$controllerName = $partConfig['controller']['name'];
								}
								// Copying defined values
								$controllerConfig = $partConfig['controller'];
								$partConfig['controller'] = $projectConfig['defaults']['controllers'];
								// 
								// restoring defined values
								foreach ($controllerConfig as $k => $v) {
									// But only if it's not the actions list or the BlackList
									if ($k != 'actions' && $k != 'blackList') {
										$partConfig['controller'][$k] = $v;
									}
								}

								// Restoring Blacklist
								if (isset($controllerConfig['blackList']) && is_array($controllerConfig['blackList'])) {
									$this->speak("|    |    |    |    ...A wild actions blackList has been found...", 'info', 2);
									foreach ($controllerConfig['blackList'] as $prefix => $actions) {
										if (!is_array($actions)) {
											$actions = array($actions);
										}
										$partConfig['controller']['blackList'][$prefix] = $actions;
									}
								}
							}
							// restoring name
							$partConfig['controller']['name'] = $controllerName;
							$this->speak("|    |    |    |    +->  Controller name is '$controllerName'", 'info', 2);
							//
							// Creating action list
							$prefixesList = array(); // This will be used later in views checks
							foreach ($partConfig['controller']['actions'] as $prefix => $actions) {
								$prefixesList[] = $prefix;
								$actionsList[$prefix] = array();
								$actionsString = '';
								foreach ($actions as $action => $actionConfig) {
									if (in_array($action, $partConfig['controller']['blackList'][$prefix])) {
										$this->speak("|    |    |    |    +-> Action $action for prefix $prefix is blacklisted and then removed from config.", 'info', 2);
										unset($partConfig['controller']['actions'][$prefix][$action]);
									} else {
										// Resetting to defaults
										$partConfig['controller']['actions'][$prefix][$action] = $projectConfig['defaults']['action'];
										// Putting back defined values
										if (is_array($actionConfig)) {
											foreach ($actionConfig as $k => $v) {
												$partConfig['controller']['actions'][$prefix][$action][$k] = $v;
											}
										}
										//Completing actions list for view generation.
										$actionsString.=$action;
										if ($partConfig['controller']['actions'][$prefix][$action]['hasView'] == true) {
											// Action list for views
											//array_push($actionsList[$prefix], $action);
											$actionsList[$prefix][] = $action;
											// String to be displayed as debug
											$actionsString.='*';
										}
										$actionsString.='; ';
									}
								}
								$this->speak("|    |    |    |    +->  Actions for prefix $prefix:", 'info', 2);
								$this->speak("|    |    |    |    |    $actionsString", 'info', 2);
							}
						}
						// Checking for 'views' entry
						if (isset($partConfig['views'])) {
							$this->speak("|    |    |    +->  Views section defined", 'info', 2);
							$hasViews = true;
							//
							// Is views well defined ?
							if (!is_array($partConfig['views'])) {
								$this->speak("|    |    |    |    ...On one line only...", 'warning', 2);
								$this->speak("|    |    |    |    ...So I'll use controller actions as base...", 'warning', 2);
								// Loading defaults
								foreach ($actionsList as $prefix => $views) {
									foreach ($views as $view) {
										$partConfig['views'][$prefix][$view] = array();
									}
								}
//								$partConfig['views'] = $actionsList;
							}
							$this->speak("|    |    |    |    ...Filling views...", 'info', 2);
							// Walking prefixes
							foreach ($actionsList as $prefix => $views) {
								// Replacing views by defaults values
								foreach ($views as $view) {
									// Use defaults values for this view (general.defaults.view)
									// PartConfig specific view = default view
//									$partConfig['views'][$prefix][$view] = $projectConfig['defaults']['view'];
									// Check if defaults are set in the 'defaults.views' section (general.defaults.views.$prefix.$view)
									if (isset($projectConfig['defaults']['views'][$prefix][$view])) {
										// PartConfig specific view = default view + default specific view
										$this->_mymerge($partConfig['views'][$prefix][$view], $projectConfig['defaults']['views'][$prefix][$view]);
									}
									// Then use custom definitions (plugin.part.views.prefix.view values)
									// PartConfig specific view = defined specific view
									if (!empty($fileConfig['plugins'][$plugin]['parts'][$part]['views'][$prefix][$view]) && is_array($fileConfig['plugins'][$plugin]['parts'][$part]['views'][$prefix][$view])) {
										$this->_mymerge($partConfig['views'][$prefix][$view], $fileConfig['plugins'][$plugin]['parts'][$part]['views'][$prefix][$view]);
									}
								}
							}
							$this->speak("|    |    |    |    +->  Views are now OK", 'info', 2);
						}
					}
					// Filling main array with the part
					$projectConfig['plugins'][$plugin]['parts'][$part] = $partConfig;
				}
			}
			//
			// Menus				
			$this->speak("Menus", 'info', 2);
			foreach ($fileConfig['menus'] as $menu => $menuConfig) {
				$this->speak("+->  $menu:", 'info', 2);
				$projectConfig['menus'][$menu] = $projectConfig['defaults']['menu'];
				if (is_array($menuConfig)) {
					$this->speak("|    Great, this menu seems to have values defined", 'info', 2);
					foreach ($menuConfig as $k => $v) {
						$projectConfig['menus'][$menu][$k] = $v;
					}
					// Target file
					if (empty($projectConfig['menus'][$menu]['fileName'])) {
						$this->speak("|    ... But no fileName given. Using the menu name instead.", 'warning', 2);
						$projectConfig['menus'][$menu]['fileName'] = strtolower($menu);
					}
					// Template to use
					if (empty($projectConfig['menus'][$menu]['template'])) {
						$this->speak("|    ... But no template given. Using the menu name instead.", 'warning', 2);
						$projectConfig['menus'][$menu]['template'] = strtolower($menu);
					}
				} else {
					$this->speak("|    Nothing defined for this menu. Using defaults", 'warning', 2);
					// Target file
					$projectConfig['menus'][$menu]['fileName'] = strtolower($menu);
					// Template to use
					$projectConfig['menus'][$menu]['template'] = strtolower($menu);
				}
			}


			$this->projectConfig = $projectConfig;
			$this->initialized = 1;
			return true;
		} else {
			$this->out(__('superBake', '<error>The "%s" configuration file does not exists. Please create it.</error>', $configFile));
			$this->_stop();
		}
	}

	/**
	 * Merges two or more arrays, recursively, and only keep newest values.
	 * Modifies array1 and outputs nothing.
	 * 
	 * @param array $array1
	 * @param array $array2
	 * 
	 * @return true;
	 * 
	 * --- Credits goes to RiaD (http://stackoverflow.com/users/768110/riad)
	 * Function found here : http://stackoverflow.com/questions/6975676/php-recursive-merge
	 */
	private function _mymerge(&$a, $b) {
		foreach ($b as $child => $value) {
			if (isset($a[$child])) {
				if (is_array($a[$child]) && is_array($value)) { //merge if they are both arrays
					$this->_mymerge($a[$child], $value);
				}
			} else {
				$a[$child] = $value;
			}
		}
	}

}

?>
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
 * @package       ELCMS.superBake.Shell
 * @license       GPL v3 (http://www.gnu.org/licenses/gpl.html)
 * @version       0.3
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
// SbShell
App::uses('SbShell', 'Sb.Console/Command');

// Model from Cake
App::uses('Model', 'Model');

/**
 * Command-line code generation utility to automate programmer chores.
 *
 * superBake is EL's code generation script, which can help you kickstart
 * application development by writing fully functional skeleton controllers,
 * models, and views. Going further, superBake can also write Unit Tests for you.
 *
 */
class ShellShell extends SbShell {

	/**
	 * Contains tasks to load and instantiate
	 *
	 * @var array
	 */
	public $tasks = array('Sb.SuperModel', 'Sb.SuperController', 'Sb.SuperView', 'Sb.SuperPlugin', 'Sb.SuperFile', 'Sb.SuperRequired');

	/**
	 * The connection being used.
	 *
	 * @var string
	 */
	public $connection = 'default';

	/**
	 * Determines if the current generation is interactive or not
	 *
	 * @var Boolean
	 */
	public $interactive = true;

	/**
	 * 1 if the config is loaded. Defined by loadConfig();
	 *
	 * @var boolean
	 */
	protected $_initialized = 0;

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
	 * Main menu
	 *
	 * @return void
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
		$this->out('|', 1, 0);
		$this->out('|  <info>You currently use the ' . $this->Sbc->getTemplateName() . ' template</info>', 1, 0);
		$this->out('|', 1, 0);
		if (count($this->Sbc->getPrefixesList()) != (count(Configure::read('Routing.prefixes')) + 1)) {
			$this->out('| <warning>--> The amount of routing prefixes defined in your core.php.</warning>', 1, 0);
			$this->out('| <warning>--> differs from the ones defined in your configuration files...</warning>', 1, 0);
			$this->out('|', 1, 0);
		}
		if ($this->Sbc->getErrors() > 0) {
			$this->out('| <warning>--> This file contains errors. Check it.</warning>', 1, 0);
			$this->out('|', 1, 0);
		}
		$this->out('+--[ <error>' . __d('superBake', 'Plugin creation') . '</error> ]', 1, 0);
		$this->out('|  ' . __d('superBake', '    [<bold>P</bold>]lugins (Creates all plugins structures)'), 1, 0);
		$this->out('|  ' . __d('superBake', '    [<bold>S</bold>]pecific entire plugin (M/C/V)'), 1, 0);
		$this->out('|', 1, 0);
		$this->out('+--[ <error>' . __d('superBake', 'Batch generation') . '</error> ]', 1, 0);
		$this->out('|  ' . __d('superBake', '    [<bold>M</bold>]odels (Generates all models)'), 1, 0);
		$this->out('|  ' . __d('superBake', '    [<bold>C</bold>]ontrollers (Generates all controllers)'), 1, 0);
		$this->out('|  ' . __d('superBake', '    [<bold>V</bold>]iews (Generates all views)'), 1, 0);
		$this->out('|  ' . __d('superBake', '    [<bold>A</bold>]ll (Models, Controllers and Views)'), 1, 0);
		$this->out('|  ' . __d('superBake', '    Risk[<bold>Y</bold>] (Models, Controllers, Views, Menus, Files and copy required.)'), 1, 0);
		$this->out('|', 1, 0);
		$this->out('+--[ <error>' . __d('superBake', 'Model generation') . '</error> ]', 1, 0);
		$this->out('|  ' . __d('superBake', '    One plugin mo[<bold>D</bold>]els (All models for a specific plugin)'), 1, 0);
		$this->out('|  ' . __d('superBake', '    [<bold>B</bold>]ake one model'), 1, 0);
		$this->out('|', 1, 0);
		$this->out('+--[ <error>' . __d('superBake', 'Controller generation') . '</error> ]', 1, 0);
		$this->out('|  ' . __d('superBake', '    [<bold>O</bold>]ne plugin controllers (All controllers for a specific plugin)'), 1, 0);
		$this->out('|  ' . __d('superBake', '    Bake one contro[<bold>L</bold>]ler'), 1, 0);
		$this->out('|', 1, 0);
		$this->out('+--[ <error>' . __d('superBake', 'View generation') . '</error> ]', 1, 0);
		$this->out('|  ' . __d('superBake', '<info>View generation is based on the configuration file, and not</info>'));
		$this->out('|  ' . __d('superBake', '<info>from the existing controller. That means that if you have modified your</info>'));
		$this->out('|  ' . __d('superBake', '<info>controllers, the new actions will not be available.</info>'));
		$this->out('|  ' . __d('superBake', '    O[<bold>N</bold>]e plugin views (All views for a specific plugin)'), 1, 0);
		$this->out('|  ' . __d('superBake', '    Bake a view for one [<bold>G</bold>]iven action (plugin/controller specific)'), 1, 0);
		$this->out('|  ' . __d('superBake', '    Views fo[<bold>R</bold>] one given controller'), 1, 0);
		$this->out('|', 1, 0);
		$this->out('+--[ <error>' . __d('superBake', 'Menus') . '</error> ]', 1, 0);
		$this->out('|  ' . __d('superBake', '    M[<bold>E</bold>]nus (Generates menus)'), 1, 0);
		$this->out('|', 1, 0);
		$this->out('+--[ <error>' . __d('superBake', 'Files') . '</error> ]', 1, 0);
		$this->out('|  ' . __d('superBake', '    [<bold>F</bold>]iles (Generates files)'), 1, 0);
		$this->out('|', 1, 0);
		$this->out('+--[ <error>' . __d('superBake', 'Required files') . '</error> ]', 1, 0);
		$this->out('|  ' . __d('superBake', '    Required f[<bold>I</bold>]les (Copies files and dirs)'), 1, 0);
		$this->out('|', 1, 0);
		$this->out('+--[ <error>' . __d('superBake', 'Misc') . '</error> ]', 1, 0);
		$this->out('|  ' . __d('superBake', '    Config [<bold>J</bold>]anitor (Cleans and fills your config. Outputs the result)'), 1, 0);
		$this->out('|  ' . __d('superBake', '    [<bold>Q</bold>]uit'), 1, 0);
		$this->out('|', 1, 0);
		// Used letters (just for info):
		// A B C D E F G H I J K L M N O P Q R S T U V W X Y Z
		// = = = = = = =   = =   = = = = = = = =     =     =

		$classToGenerate = strtoupper($this->in('+--> ' . __d('superBake', 'What would you like to generate ?'), array('A', 'B', 'C', 'D', 'E', 'G', 'I', 'J', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'V', 'Y')));
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
			case 'Y': // Risky
				$this->All();
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
			case 'G': // View for a given PluginName.ControllerName.ActionName
				$this->View();
				break;
			case 'R': // View for a give PluginName.ControllerName
				$this->controllerViews();
				break;
			// Menus: ---------------------------------------------------------
			case 'E': //Menus
				$this->Menus();
				break;
			// Files: ---------------------------------------------------------
			case 'F': // All files
				$this->Files();
				break;
			// Required: ---------------------------------------------------------
			case 'I': // All files
				$this->Required();
				break;
			// Misc: ---------------------------------------------------------
			case 'J': // Outputs a complete config.
				$this->Janitor();
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
	 * Generates models, views, controllers, menus, files and copies the required files.
	 *
	 * Command line access:
	 *  $ cake Sb.Shell all
	 */
	public function MVC() {
		$this->speak(__d('superBake', 'Generating Models, Controllers and Views for ALL plugins.'), 'info', 0, 2, 2);
		$this->Models();
		$this->Controllers();
		$this->Views();
		$this->speak(__d('superBake', 'MVC generation complete.'), 'success', 0, 2, 1);
	}

	/**
	 * Generates a controller from a plugin
	 *
	 * Command line access :
	 *  $ cake Sb.Shell controller
	 *  $ cake Sb.Shell controller PluginName.ControllerName
	 *
	 */
	public function All() {
		$this->speak(__d('superBake', 'models, views, controllers, menus, files and copying required stuff'), 'info', 0, 2, 2);
		$this->Models();
		$this->Controllers();
		$this->Views();
		$this->Menus();
		$this->Files();
		$this->Required();
		$this->speak(__d('superBake', 'Generation complete.'), 'success', 0, 2, 1);
	}

	/**
	 * Generates Models, Controllers and Views for a given plugin
	 *
	 * Command line access:
	 *  $ cake Sb.Shell pluginMVC
	 *  $ cake Sb.Shell pluginMVC PluginName
	 */
	public function pluginMVC() {
		// Task title output
		$this->speak(__d('superBake', 'Building ALL models, controllers and views for an unique plugin'), 'info', 0, 2, 2);

		// Checking arguments
		$args = $this->_checkArgs(1, array('plugin'));

		//Manually set the args value
		$this->args[0] = $args['plugin'];

		// Baking the MVC
		$this->pluginModels();
		$this->pluginControllers();
		$this->pluginViews();

		$this->speak(__d('superBake', 'MVC generated for plugin %s', $args['plugin']), 'success', 0, 2, 1);
		unset($this->args[0]);
	}

	/**
	 * Generates all plugins structures
	 *
	 * Command line access:
	 *  $ cake Sb.Shell plugins
	 */
	public function Plugins() {
		// Task title
		$this->speak(__d('superBake', 'Building all plugins structures'), 'info', 0, 2, 2);

		// updateBootstrap state passed to the task
//		$this->SuperPlugin->updateBootstrap = $updateBootstrap;
		// Plugin list
		$plugins = $this->Sbc->getPluginsToBake();
		$i = 0; // Used to count generated plugins

		foreach ($plugins as $plugin) {
			// Executes generation
			if ($plugin != $this->Sbc->getAppBase()) {
				$this->speak(__d('superBake', "Building $plugin"), 'info', 0, 2);
				//Giving the name of the current plugin to the task
				$this->SuperPlugin->currentPlugin = $plugin;
				// Passing Sbc
				$this->SuperPlugin->Sbc = $this->Sbc;
				// Giving the plugin configuration to the task
				$this->SuperPlugin->pluginConfig = $this->Sbc->getConfig("plugins.$plugin");
				$this->SuperPlugin->execute();
			}
			$i++;
		}

		// Plugin have been created
		if ($i >= 1) {
			$out = array();
			$this->speak(__d('superBake', "Plugin generation is over."), 'success', 0, 2, 1);
		} else { // No plugin to generate
			$this->speak(__d('superBake', "There was no plugins to generate."), 'warning', 0, 2, 1);
		}
		$this->_stop();
	}

	/**
	 * Asks the user for a plugin name
	 *
	 * @return string Plugin name
	 */
	protected function _getPluginName() {

		// List of plugins to bake (with generate: true)
		$plugins = $this->Sbc->getPluginsToBake();
		$count = count($plugins);

		$this->out(__d('cake_console', 'Possible Plugins based on your current config file:'));
		$len = strlen($count + 1);

		// outputting the list
		for ($i = 0; $i < $count; $i++) {
			$this->out(sprintf("%${len}d. %s", $i + 1, $plugins[$i]), 1, Shell::QUIET);
		}
		$enteredPlugin = '';

		// Waiting for a valid choice
		while (!$enteredPlugin) {
			$enteredPlugin = $this->in(__d('cake_console', "Enter a number from the list above, or 'q' to exit"), null, 'q');

			if ($enteredPlugin === 'q') {
				$this->out(__d('cake_console', 'Exit'));
				$this->_stop();
			}
			$intEnteredPlugin = intval($enteredPlugin);
			if (empty($intEnteredPlugin) || $intEnteredPlugin > $count) {
				$this->speak(__d('cake_console', "The plugin name you supplied was empty,\n" .
												"or the number you selected was not an option. Please try again.", 'error', 1));
				$enteredPlugin = '';
			}
		}

		// Return plugin name
		return $plugins[$enteredPlugin - 1];
	}

	/**
	 * Generates a given model
	 *
	 * Command line access:
	 *  $ cake Sb.Shell model
	 *  $ cake Sb.Shell model PluginName.ModelName
	 */
	public function Model() {
		$this->speak(__d('superBake', 'Building unique model'), 'info', 0, 2, 2);
		// Checking arguments
		$args = $this->_checkArgs(2, array('plugin', 'model'));
		$part = $this->Sbc->getModelPart($args['model']);
		$this->_model($args['plugin'], $part);
		$this->speak(__d('superBake', '"%s" model has been generated in plugin "%s"', array($args['model'], $args['plugin'])), 'success', 0, 2, 1);
	}

	/**
	 * Generates all models in all plugins
	 *
	 * Command line access:
	 *  $ cake Sb.Shell models
	 */
	public function Models() {
		$this->speak(__d('superBake', 'Building ALL models for ALL plugins'), 'info', 0, 2, 2);
		$models = $this->Sbc->getModelsToBake();
		foreach ($models as $model => $modelConfig) {
			$this->speak(__d('superBake', 'Generating model %s...', $modelConfig['plugin'] . '.' . $model), 'info', 0, 1, 1);
			$this->_model($modelConfig['plugin'], $modelConfig['part']);
		}
		$this->speak(__d('superBake', 'Models generation complete'), 'success', 0, 2, 1);
	}

	/**
	 * Generates all models in a given plugin
	 *
	 * Command line access:
	 *  $ cake Sb.Shell pluginModels
	 *  $ cake Sb.Shell pluginModels PluginName
	 */
	public function pluginModels() {
		$this->speak(__d('superBake', 'Building ALL models for a plugin'), 'info', 0, 2, 2);
		$args = $this->_checkArgs(1, array('plugin'));
		$models = $this->Sbc->getModelsList($args['plugin']);
		foreach ($models as $model) {
			$part = $this->Sbc->getModelPart($model);
			$this->_model($args['plugin'], $part);
		}
		$this->speak(__d('superBake', 'Models generation complete'), 'success', 0, 2, 1);
	}

	/**
	 * Generates the model in the plugin
	 *
	 * @param string $plugin Plugin name, should not be null.
	 * @param string $part Part name
	 */
	protected function _model($plugin, $part) {
		// Verify if plugin is loaded
		if (!$this->checkIfEnabled($plugin)) {
			return false;
		}
		// SuperBake
		$this->SuperModel->Sbc = $this->Sbc;

		// Curent plugin
		if ($plugin === $this->Sbc->getAppBase()) {
			$this->SuperModel->plugin = null;
		} else {
			$this->SuperModel->plugin = $plugin;
		}
		// Part name
		$this->SuperModel->currentPart = $part;

		// Task execution
		$this->SuperModel->execute();
	}

	/**
	 * Asks the user to choose a model name in a plugin's model list.
	 *
	 * @param string $plugin Plugin name
	 * @return string Choosen model name
	 */
	protected function _getModelName($plugin) {
		// Getting model list for models in $plugin
		$models = $this->Sbc->getModelsList($plugin);
		$count = count($models);

		$this->out(__d('cake_console', 'Possible Models for the "%s" plugin, based on your current config file:', $plugin));
		$len = strlen($count + 1);

		// List output
		for ($i = 0; $i < $count; $i++) {
			$this->out(sprintf("%${len}d. %s", $i + 1, $models[$i]), 1, Shell::QUIET);
		}
		$enteredModel = '';

		// Waiting for a valid choice
		while (!$enteredModel) {
			$enteredModel = $this->in(__d('cake_console', "Enter a number from the list above, or 'q' to exit"), null, 'q');

			if ($enteredModel === 'q') {
				$this->out(__d('cake_console', 'Exit'));
				$this->_stop();
			}
			$intEnteredModel = intval($enteredModel);
			if (empty($intEnteredModel) || $intEnteredModel > $count) {
				$this->speak(__d('cake_console', "The Model name you supplied was empty,\n" .
												"or the number you selected was not an option. Please try again.", 'error', 0));
				$enteredModel = '';
			}
		}
		return $models[$enteredModel - 1];
	}

	/**
	 * Generates a controller from a plugin
	 *
	 * Command line access :
	 *  $ cake Sb.Shell controller
	 *  $ cake Sb.Shell controller PluginName.ControllerName
	 *
	 */
	public function Controller() {
		$this->speak(__d('superBake', 'Building unique controller'), 'info', 0, 2, 2);
		$args = $this->_checkArgs(2, array('plugin', 'controller'));
		$this->_controller($args['plugin'], $args['controller']);
		$this->speak(__d('superBake', '"%s" controller has been generated in plugin "%s"', array($args['controller'], $args['plugin'])), 'success', 0, 2, 1);
	}

	/**
	 * Generates all models in all plugins
	 *
	 * Command line access:
	 *  $ cake Sb.Shell controllers
	 */
	public function Controllers() {
		$this->speak(__d('superBake', 'Building ALL controllers, for ALL plugins'), 'info', 0, 2, 2);
		$controllers = $this->Sbc->getControllersToBake();
		foreach ($controllers as $controller => $controllerConfig) {
			$this->speak(__d('superBake', 'Generating controller %s...', $controllerConfig['plugin'] . '.' . $controller), 'info', 0, 1, 1);
			$this->_controller($controllerConfig['plugin'], $controllerConfig['part']);
		}
		$this->speak(__d('superBake', 'Controller generation complete'), 'success', 0, 2, 1);
	}

	/**
	 * Generates all controllers in a given plugin
	 *
	 * Command line access:
	 *  $ cake Sb.Shell pluginControllers
	 *  $ cake Sb.Shell pluginControllers PluginName
	 */
	public function pluginControllers() {
		$this->speak(__d('superBake', 'Building ALL controllers for a plugin'), 'info', 0, 2, 2);
		$args = $this->_checkArgs(1, array('plugin'));
		$controllers = $this->Sbc->getControllersList($args['plugin']);
		foreach ($controllers as $controller) {
			$this->_controller($args['plugin'], $controller);
		}
		$this->speak(__d('superBake', 'Controller generation complete'), 'success', 0, 2, 1);
	}

	/**
	 * Asks the user to choose a controller name in a plugin's model list.
	 *
	 * @param string $plugin Plugin name
	 * @return string Choosen controller name
	 */
	protected function _getControllerName($plugin) {
		// Getting the controller list for the plugin
		$controllers = $this->Sbc->getControllersList($plugin);
		$count = count($controllers);

		$this->out(__d('cake_console', 'Possible Controllers for the "%s" plugin, based on your current config file:', $plugin));
		$len = strlen($count + 1);
		// List display
		for ($i = 0; $i < $count; $i++) {
			$this->out(sprintf("%${len}d. %s", $i + 1, $controllers[$i]), 1, Shell::QUIET);
		}
		$enteredController = '';

		// Waiting for a valid choice
		while (!$enteredController) {
			$enteredController = $this->in(__d('cake_console', "Enter a number from the list above, or 'q' to exit"), null, 'q');

			if ($enteredController === 'q') {
				$this->out(__d('cake_console', 'Exit'));
				$this->_stop();
			}
			$intEnteredController = intval($enteredController);
			if (empty($intEnteredController) || $intEnteredController > $count) {
				$this->speak(__d('cake_console', "The Controller name you supplied was empty,\n" .
												"or the number you selected was not an option. Please try again.", 'error', 0));
				$enteredController = '';
			}
		}
		return $controllers[$enteredController - 1];
	}

	/**
	 * Returns a YAML array of current YAML configuration.
	 * The returned configuration is the COMPLETE array, as you can see it in the GUI.
	 */
	function Janitor() {
		$this->out('##');
		$this->out('## ' . __d('superBake', 'Delete me, and everything above if you want to have a valid file.'));
		$this->out('## ' . __d('superBake', 'Tip : you can run the shell with the quiet option ($ cake Sb.Shell Janitor -q)'));
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
		$this->out(Spyc::YAMLDump($this->Sbc->getConfig()), 1, 0);
		$this->_stop();
	}

	/**
	 * Bakes a controller
	 *
	 * @param string $plugin Plugin name
	 * @param string $part Part name
	 */
	protected function _controller($plugin, $part) {
		// Verify if plugin is loaded
		if (!$this->checkIfEnabled($plugin)) {
			return false;
		}

		// Passing Sbc
		$this->SuperController->Sbc = $this->Sbc;

		// Current plugin
		$this->SuperController->plugin = ($plugin === $this->Sbc->getAppBase()) ? null : $plugin;

		// Part  name
		$this->SuperController->currentPart = $part;
		// Task execution
		$this->SuperController->execute();
//		}
	}

	/**
	 * Generates a view for a given plugin/controller/action
	 *
	 * Command line access:
	 *  $ cake Sb.Shell view
	 *  $ cake Sb.Shell view PluginName.ControllerName.ActionName
	 */
	public function View() {
		$this->speak(__d('superBake', 'Building unique view'), 'info', 0, 2, 2);
		$args = $this->_checkArgs(3, array('plugin', 'controller', 'view'));
		$this->_view($args['plugin'], $args['controller'], $args['view']);
		$this->speak(__d('superBake', '"%s" view has been generated for controller "%s" in plugin "%s"', array($args['view'], $args['controller'], $args['plugin'])), 'success', 0, 2, 1);
	}

	/**
	 * Generates all plugin's views
	 *
	 * Command line access:
	 *  $ cake Sb.Shell views
	 */
	public function Views() {

		$this->speak(__d('superBake', 'Building ALL views for ALL controllers'), 'info', 0, 2, 2);

		$views = $this->Sbc->getViewsToBake();
		foreach ($views as $plugin => $parts) {
			if ($this->Sbc->getConfig("plugins.$plugin.generate") === true) {
				$this->speak(__d('superBake', 'Plugin %s...', $plugin), 'info', 0, 1, 2);
				foreach ($parts as $part => $prefixes) {
					$this->speak(__d('superBake', 'Part %s...', $part), 'info', 0, 1, 1);
					foreach ($prefixes as $prefix => $actions) {
						foreach ($actions as $action) {
							$this->speak(__d('superBake', 'Generating view %s...', (($prefix === 'public') ? '' : $prefix . '_') . $action), 'info', 0, 1, 1);
							$this->_view($plugin, $part, ($prefix === 'public') ? $action : $prefix . '_' . $action);
						}
					}
				}
			}
		}
		$this->speak(__d('superBake', 'View generation complete'), 'success', 0, 2, 1);
	}

	/**
	 * Generates all views of a given plugin
	 *
	 * Command line access:
	 *  $ cake Sb.Shell pluginViews
	 *  $ cake Sb.Shell pluginViews PluginName
	 */
	public function pluginViews() {
		$this->speak(__d('superBake', 'Building ALL views for a plugin'), 'info', 0, 2, 2);

		$args = $this->_checkArgs(1, array('plugin'));

		$plugin = $args['plugin'];
		$views = $this->Sbc->getViewsToBake();
//		$controllers = $this->_getControllerList($plugin);
		foreach ($views[$plugin] as $part => $prefixes) {
			foreach ($prefixes as $prefix => $actions) {
				foreach ($actions as $action) {
					$this->_view($plugin, $part, $this->Sbc->actionAddPrefix($action, $prefix));
				}
			}
		}
		$this->speak(__d('superBake', 'View generation complete'), 'success', 0, 2, 1);
	}

	/**
	 * Generates all views of a given plugin/controller
	 *
	 * Command line access:
	 *  $ cake Sb.Shell controllerViews
	 *  $ cake Sb.Shell controllerViews PluginName.ControllerName
	 */
	public function controllerViews() {

		$args = $this->_checkArgs(2, array('plugin', 'controller'));

		$plugin = $args['plugin'];
		$controller = $args['controller'];
		$part = $this->Sbc->getControllerPart($controller);

		$this->speak(__d('superBake', 'Building views for controller "%s.%s"', array($plugin, $controller)), 'info', 0, 2, 2);
		$views = $this->Sbc->getViewsToBake();

		foreach ($views[$plugin][$part] as $prefix => $actions) {
			foreach ($actions as $action)
				$this->_view($plugin, $part, $this->Sbc->actionAddPrefix($action, $prefix));
		}
		$this->speak(__d('superBake', 'Views for "%s.%s" have been generated', array($plugin, $controller)), 'success', 0, 2, 1);
	}

	/**
	 * Generates a view for a given plugin/controller/action
	 *
	 * @param string $plugin Plugin name
	 * @param string $part Part name
	 * @param string $action Action name with prefix (admin_index or index,...)
	 *
	 * @return void
	 */
	protected function _view($plugin, $part, $action) {
		// Verify if plugin is loaded
		if (!$this->checkIfEnabled($plugin)) {
			return false;
		}
		// SuperBake
		$this->SuperView->Sbc = $this->Sbc;

		// Current plugin
		$this->SuperView->plugin = ($plugin === $this->Sbc->getAppBase()) ? null : $plugin;

		// Part name
		$this->SuperView->currentPart = $part;

		// Controller Name
		$this->SuperView->controllerName = $this->Sbc->getConfig("plugins.$plugin.parts.$part.controller.name");

		// Verifying prefix
		$cakePrefixes = Configure::read('Routing.prefixes');
		if (is_null($cakePrefixes)) {
			$cakePrefixes = array();
		}
		$actionParts = explode('_', $action);
		if ((count($actionParts) > 1 && in_array($actionParts[0], $cakePrefixes)) || count($actionParts) === 1) {
			if (count($actionParts) > 1 && in_array($actionParts[0], $cakePrefixes)) {
				$this->SuperView->currentPrefix = $actionParts[0];
				$this->SuperView->currentSimpleAction = $actionParts[1];
			} else {
				$this->SuperView->currentPrefix = null;
				$this->SuperView->currentSimpleAction = $action;
			}
			// Action
			$this->SuperView->currentAction = $action;


			// Task execution
			$this->SuperView->execute();
		} else {
			$this->speak(__d('superBake', 'Skipping %s as its prefix is not defined in core.php', $action), 'warning', 0);
		}
	}

	/**
	 * Ask user to choose an action for a given plugin/controller
	 *
	 * @param string $plugin Plugin name
	 * @param string $controller Controller name
	 *
	 * @return string Choosen action name
	 */
	protected function _getViewName($plugin, $controller) {
		$views = $this->Sbc->getViewsToBake($plugin, $controller);
		foreach ($views as $prefix => $acts) {
			foreach ($acts as $act) {
				$actions[] = $this->Sbc->actionAddPrefix($act, $prefix);
			}
		}
		$count = count($actions);

		$this->out(__d('cake_console', 'Possible Actions for the "%s" plugin, based on your current config file:', $plugin));
		$len = strlen($count + 1);
		for ($i = 0; $i < $count; $i++) {
			$this->out(sprintf("%${len}d. %s", $i + 1, $actions[$i]), 1, Shell::QUIET);
		}
		$enteredAction = '';

		while (!$enteredAction) {
			$enteredAction = $this->in(__d('cake_console', "Enter a number from the list above, or 'q' to exit"), null, 'q');

			if ($enteredAction === 'q') {
				$this->out(__d('cake_console', 'Exit'));
				$this->_stop();
			}

			if (!$enteredAction || intval($enteredAction) > $count) {
				$this->speak(__d('cake_console', "The Action name you supplied was empty,\n" .
												"or the number you selected was not an option. Please try again.", 'error', 0));
				$enteredAction = '';
			}
		}
		return $actions[$enteredAction - 1];
	}

	/**
	 * Generates all the menus
	 */
	public function Menus() {
		$this->speak(__d('superBake', 'Building ALL menus'), 'info', 0, 2, 2);
		$menusList = $this->Sbc->getMenusToBake();
		foreach ($menusList as $plugin => $menus) {
			foreach ($menus as $menu) {
				$this->speak(__d('superBake', 'Generating menu %s...', $plugin . '.' . $menu), 'info', 0, 1, 1);
				$this->_menu($plugin, $menu);
			}
		}
		$this->speak(__d('superBake', 'Menu generation complete'), 'success', 0, 2, 1);
	}

	/**
	 * Creates a menu for a plugin.
	 *
	 * @param string $plugin Plugin name
	 * @param string $menu	Menu name
	 */
	protected function _menu($plugin, $menu) {
		// Verify if plugin is loaded
		if (!$this->checkIfEnabled($plugin)) {
			return false;
		}
		// SuperBake
		$this->SuperFile->Sbc = $this->Sbc;

		// File type:
		$this->SuperFile->fileType = 'menu';

		// Plugin:
		$this->SuperFile->plugin = ($plugin === $this->Sbc->getAppBase()) ? null : $plugin;

		// Current menu config
		$this->SuperFile->currentFileConfig = $this->Sbc->getConfig('plugins.' . $plugin . ".menus.$menu");

		// Execute generation
		$this->SuperFile->execute();
	}

	/**
	 * Generates all the menus
	 */
	public function Files() {
		$this->speak(__d('superBake', 'Building ALL files'), 'info', 0, 2, 2);
		$filesList = $this->Sbc->getFilesToBake();
		foreach ($filesList as $plugin => $files) {
			foreach ($files as $file) {
				$this->speak(__d('superBake', 'Generating file %s...', $plugin . '.' . $file), 'info', 0, 1, 1);
				$this->_file($plugin, $file);
			}
		}
		$this->speak(__d('superBake', 'Files generation complete'), 'success', 0, 2, 1);
	}

	/**
	 * Creates a file for a plugin.
	 * @param string $plugin Plugin name
	 * @param string $file File name in configuration
	 */
	protected function _file($plugin, $file) {
		// Verify if plugin is loaded
		if (!$this->checkIfEnabled($plugin)) {
			return false;
		}
		// SuperBake
		$this->SuperFile->Sbc = $this->Sbc;

		// File type:
		$this->SuperFile->fileType = 'file';

		// Plugin:
		$this->SuperFile->plugin = ($plugin === $this->Sbc->getAppBase()) ? null : $plugin;

		// Current file config
		$this->SuperFile->currentFileConfig = $this->Sbc->getConfig('plugins.' . $plugin . ".files.$file");

		// Execute generation
		$this->SuperFile->execute();
	}

	public function Required() {
		// Finds required sections
		$this->speak(__d('superBake', 'Copying all required files'), 'info', 0, 2, 2);
		$requiredList = $this->Sbc->getRequiredToBake();
		foreach ($requiredList as $plugin => $required) {
			foreach ($required as $requiredFile) {
				$this->speak(__d('superBake', 'Copying required file/folder %s...', $plugin . '.' . $requiredFile), 'info', 0, 1, 1);
				$this->_required($plugin, $requiredFile);
			}
		}
		$this->speak(__d('superBake', 'Files and folders copied.'), 'success', 0, 2, 1);
	}

	protected function _required($plugin, $required) {
		// Verify if plugin is loaded
		if (!$this->checkIfEnabled($plugin)) {
			return false;
		}
		// SuperBake
		$this->SuperRequired->Sbc = $this->Sbc;

		// Plugin:
		$this->SuperRequired->plugin = ($plugin === $this->Sbc->getAppBase()) ? null : $plugin;

		// Current file config
		$this->SuperRequired->required = $this->Sbc->getConfig('plugins.' . $plugin . ".required.$required");

		// Execute generation
		$this->SuperRequired->execute();
	}

	/**
	 * get the option parser.
	 *
	 * @return void
	 */
	public function getOptionParser() {
		$parser = parent::getOptionParser();
		$name = ($this->plugin ? $this->plugin . '.' : '') . $this->name;
		$parser = new ConsoleOptionParser($name);
		return $parser->description(__d('superBake', 'The Super Bake shell generates plugins, models, views, files and menus ' .
														'for your application.' .
														' If run with no command line arguments, superBake will guide you through the creation process.' .
														' You can use arguments to a quicker generation.' . "\n\n This help system is quite broken, use the docs instead."
														// Batch generation
						))->addSubcommand('plugins', array(
								'help' => __d('superBake', 'Creates all the plugins directories skeletons.'),
						))->addSubcommand('mvc', array(
								'help' => __d('superBake', 'Bakes all Models/Controllers/Views, in their specific plugin dirs.'),
						))->addSubcommand('all', array(
								'help' => __d('superBake', 'Bakes all Models/Controllers/Views/menus/files, in their specific plugin dirs, and copies required files. Use with care.'),
						))->addSubcommand('models', array(
								'help' => __d('superBake', 'Bakes all the models, in their specfic plugin dir.'),
						))->addSubcommand('controllers', array(
								'help' => __d('superBake', 'Bakes all controllers in their specific plugin dir.'),
						))->addSubcommand('views', array(
								'help' => __d('superBake', 'Bakes all views, for controllers methods, in their specific plugin dir'),
										// Plugins
						))->addSubcommand('pluginMVC', array(
								'help' => __d('superBake', '<pluginName> - Bakes all MVC for a specific plugin.'),
										// Models
						))->addSubcommand('pluginModels', array(
								'help' => __d('superBake', '<pluginName> - Bakes all models in a plugin.'),
						))->addSubcommand('model', array(
								'help' => __d('superBake', '<pluginName>.<modelName> - Bakes a specific model.'),
										// Controllers
						))->addSubcommand('pluginControllers', array(
								'help' => __d('superBake', '<pluginName> - Bakes all controllers in a plugin.'),
						))->addSubcommand('controller', array(
								'help' => __d('superBake', '<pluginName>.<controllerName> - Bakes a specific controller.'),
										// Views
						))->addSubcommand('pluginViews', array(
								'help' => __d('superBake', '<pluginName> - Bakes all views in a plugin.'),
						))->addSubcommand('controllerViews', array(
								'help' => __d('superBake', '<pluginName>.<ControllerName> - Bakes all views in a plugin.'),
						))->addSubcommand('view', array(
								'help' => __d('superBake', '<pluginName>.<controllerName>.<actionName> - Bakes a specific view.'),
										// Menus
						))->addSubcommand('menus', array(
								'help' => __d('superBake', 'Creates the menu file(s).'),
										// Files
						))->addSubcommand('files', array(
								'help' => __d('superBake', 'Generates standalone files.'),
										// Required
						))->addSubcommand('required', array(
								'help' => __d('superBake', 'Copies files and folders.'),
		));
	}

	/**
	 * Checks if $this->args[0] has a $nb number of arguments and if the $types corresponds.
	 *
	 * For $type, possible values are :
	 * 		plugin, for plugin
	 * 		model, for model
	 * 		ccontroller, for controller
	 * 		action, for view/action
	 * Note that the array must have the "right" order. i.e.: if you wait for a command line argument
	 * like "PluginName.ControllerName.ActionName", then the function may be called by:
	 * $this->checkArgs(3, array('plugin', 'ccontroller', 'view'));
	 * It will return this array:
	 * array('plugin'=>'PluginName', 'controller'=>ControllerName, 'view'=>'ActionName')
	 *
	 * @param integer $nb Number of arguments wanted
	 * @param array $types array of wanted types
	 * @param string $message Message thrown if the number of arguments submited is invalid
	 * @return array An array with associated plugins/controllers...
	 */
	protected function _checkArgs($nb, $types, $message = null) {
		$args = array();
		$return = array();
		$interactive = true;
		if (!empty($this->args)) {
			// Parsing args
			$args = explode('.', $this->args[0]);
			if (count($args) === $nb) {
				//Check first arg
				$i = 0;
				foreach ($types as $type) {
					switch ($type) {
						case 'plugin': //plugin
							$plugin = null;
							if (in_array($args[$i], $this->Sbc->getPluginsList())) {
								// Is generate set to true ?
								if (!in_array($args[$i], $this->Sbc->getPluginsToBake())) {
									$this->speak(__d('superBake', "The submited plugin has 'generate' set to false.\nNothing must be done in it. Do something else :)"), 'warning', 0);
									$this->_stop();
								} else {
									$plugin = $args[$i];
								}
							} else {
								$this->speak(__d('superBake', "The submited plugin doesn't exists in config file.\nMaybe it's just a typo...\nPlease, select one below:"), 'warning', 0);
								$plugin = $this->_getPluginName();
							}
							$return['plugin'] = $plugin;
							break;
						case 'model': //model
							$model = null;
							if (in_array($args[$i], $this->Sbc->getModelsList($plugin))) {
								$model = $args[$i];
							} else {
								$this->speak(__d('superBake', "The submited model doesn't exists in config file\nMaybe it's just a typo...\nPlease, select one below:"), 'warning', 0);
								$model = $this->_getModelName($plugin);
							}
							$return['model'] = $model;
							break;
						case 'controller': //controller
							$controller = null;
							$controllers = $this->Sbc->getControllersList($plugin);
							if (in_array($args[$i], $controllers)) {
								$controller = $args[$i];
							} else {
								$this->speak(__d('superBake', "The submited controller doesn't exists in config file\nMaybe it's just a typo...\nPlease, select one below:"), 'warning', 0);
								$controller = $this->_getControllerName($plugin);
							}
							$return['controller'] = $controller;
							break;
						case 'view': //action in controller
							$action = null;
							$actions = $this->Sbc->getViewsToBake($plugin, $controller);
							foreach ($actions as $prefix => $views) {
								foreach ($views as $view) {
									$viewList[] = $this->Sbc->actionAddPrefix($view, $prefix);
								}
							}
							if (in_array($args[$i], $viewList)) {
								$action = $args[$i];
							} else {
								$this->speak(__d('superBake', "The submited action doesn't exists in config file\nMaybe it's just a typo...\nPlease, select one below:"), 'warning', 0);
								$action = $this->_getViewName($plugin, $controller);
							}
							$return['view'] = $action;
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
		if ($interactive === true) {
			//forced interactive mode
			// I know it's a bit redundant, but I don't see how to keep the "typo correction" on submited values (above)
			foreach ($types as $type) {
				switch ($type) {
					case 'plugin': //plugin
						$this->speak(__d('superBake', "Please, select a plugin below:"), 'warning', 0);
						$plugin = $this->_getPluginName();
						$return['plugin'] = $plugin;
						break;
					case 'model': //model
						$this->speak(__d('superBake', "Please, select a model below:"), 'warning', 0);
						$model = $this->_getModelName($plugin);
						$return['model'] = $model;
						break;
					case 'controller': //controller
						$this->speak(__d('superBake', "Please, select a controller below:"), 'warning', 0);
						$controller = $this->_getControllerName($plugin);
						$return['controller'] = $controller;
						break;
					case 'view': //action
						$this->speak(__d('superBake', "Please, select an action below:"), 'warning', 0);
						$action = $this->_getViewName($plugin, $controller);
						$return['view'] = $action;
						break;
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
	protected function _loadConfig() {
		if ($this->_initialized === 1) {
			$this->speak(__d('superbake', 'AppShell already initialized'), 'warning', 0);
			return true;
		}

		// Loading Sbc
		$this->Sbc = new Sbc;
		$this->Sbc->loadConfig();

		$this->_initialized = 1;
		return true;
	}

}

?>
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
	public $tasks = array('SuperModel', 'SuperController', 'SuperView', 'SuperPlugin');

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
	}

	/**
	 * Main "screen"
	 *
	 * @return mixed
	 */
	public function main() {
		$this->out();
		$this->out(__d('superBake', '<info>Welcome to the Super Bake Shell.</info>'));
		$this->out();

		$this->hr();
		$this->out(__d('superBake', '<warning>Read the doc before things turns bad</warning>'), 1, Shell::QUIET);
		$this->hr();
		$this->out(__d('superBake', '[P]lugins (Creates ALL plugins in default path)'));
		$this->out(__d('superBake', '[M]odels (Creates ALL models in their plugins'));
		$this->out(__d('superBake', '[C]ontrollers (with public AND prefixes actions)'));
		$this->out(__d('superBake', '[V]iews (all views, in their plugins)'));
		$this->out(__d('superBake', '[A]ll (Models, Controllers and Views)'));
		$this->out(__d('superBake', '[S]pecific entire plugin (Plugin dir, M/C/V)'));
		$this->out(__d('superBake', '[Q]uit'));

		$classToGenerate = strtoupper($this->in(__d('superBake', 'What would you like to generate ?'), array('P', 'M', 'V', 'C', 'A', 'Q', 'S')));
		switch ($classToGenerate) {
			case 'P':
				$this->Plugins();
				break;
			case 'M':
				$this->Models();
				break;
			case 'V':
				$this->Views();
				break;
			case 'C':
				$this->Controllers();
				break;
			case 'A':
				$this->MVC();
				break;
			case 'S':
				$this->Specific();
				break;
			case 'Q':
				exit(0);
				break;
			default:
				$this->out(__d('superBake', '<warning>You have made an invalid selection. Please choose a type of class to generate by entering P, M, V, C or A.</warning>'));
		}
		$this->hr();
		//Loop
		$this->main();
	}

	/**
	 * Bakes all plugins
	 */
	public function Plugins() {
		$this->projectConfig['updateBootstrap'] = strtoupper($this->in(__d('superBake', 'Do we have to update the app\'s bootstrap file ?'), array('y', 'n'), 'n'));
		$this->out();
		$this->hr();
		$this->out(__d('superBake', "<info>Plugins generation summary:</info>"));
		$this->hr();

		//Passing project configuration to superPlugin Task
		$this->SuperPlugin->projectConfig = $this->projectConfig;

		foreach ($this->projectConfig['plugins'] as $plugin => $pluginConfig) {
			//Giving the name of the current plugin to superPlugin
			$this->SuperPlugin->currentPlugin = $plugin;

			$this->SuperPlugin->execute();
		}
		if ($this->projectConfig['updateBootstrap'] == 'Y') {
			$this->hr();
			$this->out(__d('superBake', "<info>The bootstrap file has been updated. Please re-launch SuperBake " .
							"to reload the new configuration (if you want to use it more)</info>"));
			$this->hr();
			exit();
		}
	}

	/**
	 * Bakes a specific plugin entirely
	 */
	public function Specific() {
		$this->out('', 2);
		$this->out(__d('superBake', '<warning>Sorry, this method is not yet implemented</warning>'));
		$this->out('', 2);
	}

	/**
	 * Bakes all models
	 */
	public function Models() {
		$this->out();
		$this->hr();
		$this->out(__d('superBake', "<info>Models generation summary:</info>"));
		$this->hr();

		// Passing project configuration to superModel Task
		$this->SuperModel->projectConfig = $this->projectConfig;
		// Template to use
		if ($this->projectConfig['askForTemplate'] == false) {
			$this->SuperModel->params['theme'] = $this->projectConfig['defaultTemplate'];
		}
		// Models in plugins
		foreach ($this->projectConfig['plugins'] as $plugin => $config) {
			$this->SuperModel->currentPlugin = $plugin;

			$this->out(__d('superBake', '<info>Generating models for plugin "%s":</info>', $plugin));
			foreach ($config['models'] as $model => $modelConfig) {
				$this->SuperModel->currentModel = $model;
				$this->SuperModel->currentModelConfig = $modelConfig;
				$this->SuperModel->execute();
			}
		}
		$this->SuperModel->currentPlugin = null;
		
		// App models
		$this->out(__d('superBake', '<info>Generating models for app/Model:</info>'));
		foreach ($this->projectConfig['appBase']['models'] as $model => $config) {
			$this->SuperModel->currentModel = $model;
			$this->SuperModel->currentModelConfig = $config;
			$this->SuperModel->execute();
		}
		$this->hr();
	}

	/**
	 * Bakes all controllers
	 */
	public function Controllers() {
		$this->out();
		$this->hr();
		$this->out(__d('superBake', "<info>Controllers generation summary:</info>"));
		$this->hr();

		//Passing project configuration to superController Task
		$this->SuperController->projectConfig = $this->projectConfig;
		if ($this->projectConfig['askForTemplate'] == false) {
			$this->SuperController->params['theme'] = $this->projectConfig['defaultTemplate'];
		}
		//Creation controllers from models in plugins
		foreach ($this->projectConfig['plugins'] as $plugin => $config) {
			$this->out(__d('superBake', '<info>Generating controllers for plugin "%s":</info>', $plugin));
			$this->SuperController->currentPlugin = $plugin;
			foreach ($config['models'] as $model => $modelConfig) {
				$this->SuperController->currentModel = $model;
				$this->SuperController->execute();
			}
		}

		//Creating controllers from models in app/Model
		$this->out(__d('superBake', '<info>Generating controllers from app/Model</info>'));
		$this->SuperController->currentPlugin = null;
		foreach ($this->projectConfig['appBase']['models'] as $model => $config) {
			$this->SuperController->currentModel = $model;
			$this->SuperController->execute();
		}
		$this->hr();
	}

	/**
	 * Bakes all views
	 */
	public function Views() {
		$this->out();
		$this->hr();
		$this->out(__d('superBake', "<info>View generation summary:</info>"));
		$this->hr();

		// Passing configuration to superView Task
		$this->SuperView->projectConfig = $this->projectConfig;
		if ($this->projectConfig['askForTemplate'] == false) {
			$this->SuperView->params['theme'] = $this->projectConfig['defaultTemplate'];
		}
		// Baking views inside plugins
		foreach ($this->projectConfig['plugins'] as $plugin => $config) {
			$this->out(__d('superBake', '<info>Generating views for plugin "%s":</info>', $plugin));
			$this->SuperView->currentPlugin = $plugin;
			foreach ($config['models'] as $model => $modelConfig) {
				$this->SuperView->currentController = $model;
				$this->SuperView->execute();
			}
		}

		//Creating Views from controllers in app/Controller
		$this->out(__d('superBake', '<info>Generating views from app/Controllers</info>'));
		$this->SuperView->currentPlugin = null;
		foreach ($this->projectConfig['appBase']['models'] as $model => $config) {
			$this->SuperView->currentController = $model;
			$this->SuperView->execute();
		}
		if ($this->SuperView->Template->missing_config_state == 1) {
			$this->hr();
			$this->out(__d('superBake', '<error>superBake configuration is missing.</error>'), 1, Shell::QUIET);
			foreach ($this->SuperView->Template->missing_config as $k => $v) {
				if ($v == 1) {
					$this->out(__d('superBake', '<warning>Model "%s" is not defined in your superBake configuration. All links related to its actions have not been built in related views</warning>', $k), 1, Shell::QUIET);
				}
			}
		}
		$this->hr();
	}

	/**
	 * Bakes all Models/Controllers/Views
	 */
	public function MVC() {
		$this->Models();
		$this->Controllers();
		$this->Views();
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
					'help' => __d('superBake', 'Creates all the plugin directories (if not exists)'),
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
		));
	}

}

?>
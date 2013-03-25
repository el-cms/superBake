<?php

/**
 * SuperBake Shell script - superPlugin Task - Generates plugins
 * 
 * @copyright     Copyright 2012, Manuel Tancoigne (http://experimentslabs.com)
 * @author        Manuel Tancoigne <m.tancoigne@gmail.com>
 * @link          http://experimentslabs.com Experiments Labs
 * @package       EL-CMS/superBake
 * @license       GPL v3 (http://www.gnu.org/licenses/gpl.html)
 *
 * This file is based on the lib/Cake/Console/Command/Task/PluginTask.php file 
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
App::uses('File', 'Utility');
App::uses('Folder', 'Utility');

/**
 * The Plugin Task handles creating an empty plugin, ready to be used
 *
 * @package       Cake.Console.Command.Task
 */
class SuperPluginTask extends AppShell {

	/**
	 * path to plugins directory
	 *
	 * @var array
	 */
	public $path = null;

	/**
	 * Path to the bootstrap file. Changed in tests.
	 *
	 * @var string
	 */
	public $bootstrap = null;

	/**
	 * Configuration values, passed from the superBakeShell file
	 * @var array
	 */
	public $projectConfig = array();

	/**
	 * Current plugin name, passed from the superBakeShell file
	 * @var string
	 */
	public $currentPlugin = null;

	/**
	 * initialize
	 *
	 * @return void
	 */
	public function initialize() {
		$this->path = current(App::path('plugins'));
		$this->bootstrap = APP . 'Config' . DS . 'bootstrap.php';
	}

	/**
	 * Execution method always used for tasks
	 *
	 * @return void
	 */
	public function execute() {
		$plugin = Inflector::camelize($this->currentPlugin);
		$pluginPath = $this->_pluginPath($plugin);
		if (is_dir($pluginPath)) {
			$this->out(__d('superBake', '<warning>Plugin: %s already exists, no action taken.</warning>', $plugin));
			$this->out(__d('superBake', 'Path: %s', $pluginPath), 1, Shell::VERBOSE);
			return false;
		} else {
			if (!$this->bake($plugin)) {
				$this->error(__d('superBake', "<warning>An error occurred trying to bake: %s in %s</warning>", $plugin, $this->path . $plugin));
			}
		}
	}

	/**
	 * Bake the plugin, create directories and files
	 *
	 * @param string $plugin Name of the plugin in CamelCased format
	 * @return boolean
	 */
	public function bake($plugin) {
		$pathOptions = App::path('plugins');
		
		// Checking if custom plugin dir or default plugin dir
		if (in_array('pluginDir', $this->projectConfig['plugins'][$this->currentPlugin])) {
			$pluginDir = str_replace('::', DS, $this->projectConfig['plugins'][$this->currentPlugin]['plugin_dir']);
		} else {
			$pluginDir = str_replace('::',DS, $this->projectConfig['defaultPluginDir']);
		}
		// Cleaning path for windows
		$cleanPath = str_replace('\\', '\\\\', $pluginDir . DS);
		$ereg = '@' . $cleanPath . '$@';
		// Verify if the path is in the possible plugins pathes
		foreach ($pathOptions as $possiblePath) {
			if (preg_match($ereg, $possiblePath)) {
				$this->path = $possiblePath;
				$this->out(__d('superBake', 'The "%s" plugin will be created in the "%s" dir.', $plugin, $this->path), 1, Shell::VERBOSE);
			}
		}

		// Checks if folder exists
		if (!is_dir($this->path . $plugin)) {
			$Folder = new Folder($this->path . $plugin);
			$directories = array(
				'Config' . DS . 'Schema',
				'Model' . DS . 'Behavior',
				'Model' . DS . 'Datasource',
				'Console' . DS . 'Command' . DS . 'Task',
				'Controller' . DS . 'Component',
				'Lib',
				'View' . DS . 'Helper',
				'Test' . DS . 'Case' . DS . 'Controller' . DS . 'Component',
				'Test' . DS . 'Case' . DS . 'View' . DS . 'Helper',
				'Test' . DS . 'Case' . DS . 'Model' . DS . 'Behavior',
				'Test' . DS . 'Fixture',
				'Vendor',
				'webroot'
			);

			foreach ($directories as $directory) {
				$dirPath = $this->path . $plugin . DS . $directory;
				$Folder->create($dirPath);
				new File($dirPath . DS . 'empty', true);
			}

			foreach ($Folder->messages() as $message) {
				$this->out($message, 1, Shell::VERBOSE);
			}

			$errors = $Folder->errors();
			if (!empty($errors)) {
				foreach ($errors as $message) {
					$this->error($message);
				}
				return false;
			}


			
			// PluginAppController file
			$controllerFileName = $plugin . 'AppController.php';

			$out = "<?php\n\n";
			$out .= "App::uses('AppController', 'Controller');\n\n";
			$out .= "class {$plugin}AppController extends AppController {\n\n";
			$out .= "}\n";
			$this->createFile($this->path . $plugin . DS . 'Controller' . DS . $controllerFileName, $out);

			// PluginAppModel file
			$modelFileName = $plugin . 'AppModel.php';

			$out = "<?php\n\n";
			$out .= "App::uses('AppModel', 'Model');\n\n";
			$out .= "class {$plugin}AppModel extends AppModel {\n\n";
			$out .= "}\n";
			$this->createFile($this->path . $plugin . DS . 'Model' . DS . $modelFileName, $out);
			
			/**
			 * Additionnal files must be inserted here.
			 */

			// bootstrap file
			if(in_array('haveBootstrap', $this->projectConfig['plugins'][$plugin]) && $this->projectConfig['plugins'][$plugin]['haveBootstrap'] == true){
			//if ($this->projectConfig['plugins'][$plugin]['have_bootstrap'] == true) {
				$out = "<?php\n\n";
				$out .="/* Some comments and file description must go here */\n\n";
				$out .= "// Put your bootstrap configuration here :p\n";
				$this->createFile($this->path . $plugin . DS . 'Config' . DS . 'bootstrap.php', $out);
			}

			// route file
			if (in_array('haveRoutes', $this->projectConfig['plugins'][$plugin]) && $this->projectConfig['plugins'][$plugin]['have_routes'] == true) {
				$out = "<?php\n\n";
				$out .="/* Some comments and file description must go here */\n\n";
				$out .= "// Put your routes configuration here :p\n";
				$this->createFile($this->path . $plugin . DS . 'Config' . DS . 'routes.php', $out);
			}

			// App Bootstrap update
			if ($this->projectConfig['updateBootstrap'] == 'Y') {
				$this->_modifyBootstrap($plugin);
			}

			$this->hr();
			$this->out(__d('cake_console', '<success>The %s plugin has been successfully created in %s</success>', $plugin, $this->path . $plugin), 2);
		} else {
			$this->out(__d('superBake', '<warning>The "%s" plugin was not created, as its folder already exists.</warning>', $plugin));
		}

		return true;
	}

	/**
	 * Update the app's bootstrap.php file.
	 *
	 * @param string $plugin Name of plugin
	 * @return void
	 */
	protected function _modifyBootstrap($plugin) {
		$bootstrap = new File($this->bootstrap, false);
		$contents = $bootstrap->read();
		if (!preg_match("@\n\s*CakePlugin::loadAll@", $contents)) {
			$bootstrap->append("\nCakePlugin::load('$plugin', array('bootstrap' => false, 'routes' => false));");
			$this->out('');
			$this->out(__d('superBake', '%s file modified', $this->bootstrap));
		} else {
			$this->out(__d('superBake', 'CakePlugin::loadAll is defined, no need to update.'), 1, Shell::QUIET);
		}
	}

	/**
	 * get the option parser for the plugin task
	 *
	 * @return void
	 */
	public function getOptionParser() {
		$parser = parent::getOptionParser();
		return $parser->description(__d('superBake', 'Create the plugins directories structures')
		);
	}

}


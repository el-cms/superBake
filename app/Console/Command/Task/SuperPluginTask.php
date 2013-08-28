<?php

/**
 * The Plugin Task handles creating an empty plugin, ready to be used
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
 * @since         CakePHP(tm) v 1.2
 * @license       MIT License (http://www.opensource.org/licenses/mit-license.php)
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
	 * Plugin to bake (passed from shell)
	 * @var string
	 */
	public $currentPlugin = null;

	/**
	 * Plugin configuration (name, path,...) (passed from shell)
	 * @var array
	 */
	public $pluginConfig = null;

	/**
	 * Update bootstrap (passed from shell)
	 * @var bool
	 */
	public $updateBootstrap = null;

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
		// No interactive mode. Creates the structure. Dot.
		$this->path = $this->cleanPath($this->pluginConfig['pluginDir'], true);
		$this->bake($this->currentPlugin);
	}

	/**
	 * Bake the plugin, create directories and files
	 *
	 * @param string $plugin Name of the plugin in CamelCased format
	 * @return boolean
	 */
	public function bake($plugin) {
		$pathOptions = App::path('plugins');
		$ereg = '@' . $this->path . '$@';

		// Verify if the path is in the possible plugins pathes
		foreach ($pathOptions as $possiblePath) {
			if (preg_match($ereg, $possiblePath)) {
				$this->path = $possiblePath;
				$this->out(__d('superBake', 'The "%s" plugin will be created in the "%s" dir.', $plugin, $this->path), 1, Shell::VERBOSE);
			}
		}
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

			$controllerFileName = $plugin . 'AppController.php';

			$out = "<?php\n\n";
			$out .= "App::uses('AppController', 'Controller');\n\n";
			$out .= "class {$plugin}AppController extends AppController {\n\n";
			$out .= "}\n";
			$this->createFile($this->path . $plugin . DS . 'Controller' . DS . $controllerFileName, $out);

			$modelFileName = $plugin . 'AppModel.php';

			$out = "<?php\n\n";
			$out .= "App::uses('AppModel', 'Model');\n\n";
			$out .= "class {$plugin}AppModel extends AppModel {\n\n";
			$out .= "}\n";
			$this->createFile($this->path . $plugin . DS . 'Model' . DS . $modelFileName, $out);

			//$this->_modifyBootstrap($plugin);

			/**
			 * Additionnal files can be inserted here.
			 */
			// bootstrap file
			if (!empty($this->projectConfig['plugins'][$plugin]['haveBootstrap'])) {
				if ($this->projectConfig['plugins'][$plugin]['haveBootstrap'] == true) {
					$out = "<?php\n\n";
					$out .="/* Some comments and file description must go here */\n\n";
					$out .= "// Put your bootstrap configuration here :p\n";
					$this->createFile($this->path . $plugin . DS . 'Config' . DS . 'bootstrap.php', $out);
				}
			}

			// route file
			if (!empty($this->projectConfig['plugins'][$plugin]['haveRoutes'])) {
				if ($this->projectConfig['plugins'][$plugin]['haveRoutes'] == true) {
					$out = "<?php\n\n";
					$out .="/* You should put your plugin's routes here.\n * \n * @todo Remember to define the \"$plugin\" plugin's routes\n */\n";
					$this->createFile($this->path . $plugin . DS . 'Config' . DS . 'routes.php', $out);
				}
			}

			// App Bootstrap update
			if ($this->updateBootstrap == 'Y') {
				$this->_modifyBootstrap($plugin);
			}
			
			$this->hr();
			$this->out(__d('cake_console', '<success>Created:</success> %s in %s', $plugin, $this->path . $plugin), 2);
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
			$bootstrap->append("\nCakePlugin::load('$plugin', array('bootstrap' => false, 'routes' => false));\n");
			$this->out('');
			$this->out(__d('cake_dev', '%s modified', $this->bootstrap));
		}
	}

	/**
	 * get the option parser for the plugin task
	 *
	 * @return void
	 */
	public function getOptionParser() {
		$parser = parent::getOptionParser();
//		return $parser->description(__d('cake_console', 'Create the directory structure, AppModel and AppController classes for a new plugin. ' .
//								'Can create plugins in any of your bootstrapped plugin paths.'
//				))->addArgument('name', array(
//					'help' => __d('cake_console', 'CamelCased name of the plugin to create.')
//		));
	}

}

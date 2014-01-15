<?php

/**
 * The Required task handles needed files from the theme.
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
 * @license       http://www.opensource.org/licenses/mit-license.php MIT License
 */
// SbShell from superBake
App::uses('SbShell', 'Sb.Console/Command');
// Template from superBake
App::uses('TemplateTask', 'Sb.Console/Command/Task');

// File from Cake
App::uses('File', 'Utility');
// Folder from Cake
App::uses('Folder', 'Utility');

/**
 * The Plugin Task handles creating an empty plugin, ready to be used
 *
 * @package       Cake.Console.Command.Task
 */
class superRequiredTask extends SbShell {

	/**
	 * path to plugins directory
	 *
	 * @var array
	 */
	public $path = null;

	/**
	 * Tasks to be loaded by this Task
	 * @var array
	 */
	public $tasks = array('Sb.Template');

	/**
	 * Configuration for the current file/folder
	 * @var array
	 */
	public $required = array();

	/**
	 * Plugin in wich the files must be copied
	 * @var string
	 */
	public $plugin = null;

	/**
	 * Path to the bootstrap file. Changed in tests.
	 *
	 * @var string
	 */
//	public $bootstrap = null;

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
		$this->bake();
	}

	/**
	 * Bake the plugin, create directories and files
	 *
	 * @param string $plugin Name of the plugin in CamelCased format
	 * @return boolean
	 */
	public function bake() {
		// Theme path
		$templatePath = $this->Template->getThemePath();
		// Source dir
		$source = $templatePath . 'required' . DS . $this->cleanPath($this->required['source']);

		// Destination dir
		if (is_null($this->plugin)) {
			$dest = APP . $this->cleanPath($this->required['target']);
		} else {
			$dest = $this->_pluginPath($this->plugin) . $this->cleanPath($this->required['target']);
		}

		if ($this->required['type'] === 'folder') {
			// Folder copy
			$this->copyDir($source, $dest, $this->required['contentOnly']);
		} else {
			// File copy
			$this->copyFile($source, $dest);
		}

		return true;
	}

	/**
	 * Copies the content for a $dir folder to a $dest folder. If $dest does not exists, it will be created.
	 *
	 * @param string $dir Source directory
	 * @param string $dest Target directory
	 * @param bool $contentOnly If set to true, only the content of the folder will be copied. Else, the container will be copied too.
	 */
	function copyDir($dir, $dest, $contentOnly) {
		if (!file_exists($dir)) {
			$this->speak(array("Source directory does not exists:", $dir), 'error', 0);
		} else {

			// Source folder
			$source = new Folder($dir);
			if ($contentOnly === false) {
				// Find the source folder name
				$pathes = explode(DS, $dir);
				//Destination
				$path = $dest . DS . $pathes[count($pathes) - 1];
			} else {
				$path = $dest;
			}
			$this->speak("Copying folder $dir\n to $dest", 'info', 2);
			// Creating the directory
			$newDir = new Folder($path, true);

			// Files and folders list
			$sourceContent = $source->read(true);

			// Folders
			if (count($sourceContent[0]) > 0) {
				foreach ($sourceContent[0] as $folder) {
					$this->copyDir($dir . DS . $folder, $path, false);
				}
			}

			// Files in source dir
			if (count($sourceContent[1]) > 0) {
				foreach ($sourceContent[1] as $file) {
					$this->copyFile($dir . DS . $file, $path . DS . $file);
				}
			}
		}
	}

	/**
	 * Copies a $source file to its $dest.
	 *
	 * @param string $source file to copy
	 * @param string $dest file name
	 * @return boolean
	 */
	public function copyFile($source, $dest) {
		if (!file_exists($source)) {
			$this->speak(array("Source file does not exists:", $source), 'error', 0);
			return false;
		} else {
			$this->speak("Copying file $source\n to $dest", 'comment', 2);
			if (copy($source, $dest)) {
				return true;
			} else {
				return false;
			}
		}
	}

//	/**
//	 * Update the app's bootstrap.php file.
//	 *
//	 * @param string $plugin Name of plugin
//	 * @return void
//	 */
//	protected function _modifyBootstrap($plugin) {
//		$bootstrap = new File($this->bootstrap, false);
//		$contents = $bootstrap->read();
//		if (!preg_match("@\n\s*CakePlugin::loadAll@", $contents)) {
//			$bootstrap->append("\nCakePlugin::load('$plugin', array('bootstrap' => false, 'routes' => false));\n");
//			$this->out('');
//			$this->out(__d('cake_dev', '%s modified', $this->bootstrap));
//		}
//	}

	/**
	 * find and change $this->path to the user selection
	 *
	 * @param array $pathOptions
	 * @return void
	 */
//	public function findPath($pathOptions) {
//		$valid = false;
//		foreach ($pathOptions as $i => $path) {
//			if (!is_dir($path)) {
//				array_splice($pathOptions, $i, 1);
//			}
//		}
//		$max = count($pathOptions);
//		while (!$valid) {
//			foreach ($pathOptions as $i => $option) {
//				$this->out($i + 1 . '. ' . $option);
//			}
//			$prompt = __d('cake_console', 'Choose a plugin path from the paths above.');
//			$choice = $this->in($prompt, null, 1);
//			if (intval($choice) > 0 && intval($choice) <= $max) {
//				$valid = true;
//			}
//		}
//		$this->path = $pathOptions[$choice - 1];
//	}

	/**
	 * get the option parser for the plugin task
	 *
	 * @return void
	 */
//	public function getOptionParser() {
//		$parser = parent::getOptionParser();
//		return $parser->description(__d('cake_console', 'Copy files from Templates/YourTemplate/ to any dir in the app. '
//		));
//	}
}

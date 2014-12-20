<?php

/**
 * The Required task handles needed files from the theme.
 *
 * @copyright     Copyright 2012, Manuel Tancoigne (http://experimentslabs.com)
 * @author        Manuel Tancoigne <m.tancoigne@gmail.com>
 * @link          http://experimentslabs.com Experiments Labs
 * @package       ELCMS.superBake.Task
 * @license       GPL v3 (http://www.gnu.org/licenses/gpl.html)
 * @version       0.3
 *
 * This task is based on the original CakePHP's PluginTask.
 *
 * Added methods/vars:
 * ==============
 * 	copyDir()
 * 	copyFile()
 * -----
 * 	$plugin
 * 	$required
 * 	$tasks
 *
 * Deleted methods/vars:
 * ================
 * 	_interactive()
 * 	_modifyBootstrap()
 * 	findPath()
 * 	getOptionParser()
 * -----
 * 	$bootstrap
 *
 * Modified methods:
 * =================
 * 	bake()
 * 	execute()
 *
 * Original methods/vars:
 * =================
 * 	initialize()
 * -----
 * 	$path
 */
// SbShell from superBake
App::uses('SbShell', 'Sb.Console/Command');
// Template from superBake
//App::uses('TemplateTask', 'Sb.Console/Command/Task');

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
	 * Path to plugins directory
	 *
	 * @var array
	 */
	public $path = null;

	/**
	 * Tasks to be loaded by this Task
	 *
	 * @var array
	 */
	public $tasks = array('Sb.Template');

	/**
	 * Configuration for the current file/folder
	 *
	 * @var array
	 */
	public $required = array();

	/**
	 * Plugin in wich the files must be copied
	 *
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
	 * Override initialize
	 *
	 * Unmodified method.
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
	 *
	 * @return boolean
	 */
	public function bake() {
		// Theme path
		$templatePath = $this->getTemplatePath();
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
	 * @param boolean $contentOnly If set to true, only the content of the folder will be copied. Else, the container will be copied too.
	 *
	 * @return boolean False on errors
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

			// Initializing errors
			$errors = 0;
			// Folders
			if (count($sourceContent[0]) > 0) {
				foreach ($sourceContent[0] as $folder) {
					if (!$this->copyDir($dir . DS . $folder, $path, false)) {
						$this->speak(__d('superBake', 'Error while copying folder %s', $folder), 'error', 1);
						$errors++;
					} else {
						// Verbose
						$this->speak(__d('superBake', 'Folder %s created in %s', array($folder, $path)), 'error', 2);
					}
				}
			}

			// Files in source dir
			if (count($sourceContent[1]) > 0) {
				foreach ($sourceContent[1] as $file) {
					if (!$this->copyFile($dir . DS . $file, $path . DS . $file)) {
						// Output in case of failure/success is handled in copyFile().
						$errors++;
					}
				}
			}

			// Return:
			if ($errors > 0) {
				$this->speak(__d('superBake', 'Some errors were encountered during the copy. Check your permissions.', $file), 'error', 1);
				return false;
			} else {
				return true;
			}
		}
	}

	/**
	 * Copies a $source file to its $dest.
	 *
	 * @param string $source file to copy
	 * @param string $dest file name
	 *
	 * @return boolean Success state
	 */
	public function copyFile($source, $dest) {
		if (!file_exists($source)) {
			$this->speak(array("Source file does not exists:", $source), 'error', 0);
			return false;
		} else {
			if (copy($source, $dest)) {
				$this->speak(array(__d('superBake', "file $source\n copied in $dest")), 'comment', 2, 0, 1);
				return true;
			} else {
				// Verbose
				$this->speak(__d('superBake', 'Copied file %s.', $source), 'error', 2);
				return false;
			}
		}
	}

	/**
	 * get the option parser for the required task
	 *
	 * @return void
	 */
//	public function getOptionParser() {
//		$parser = parent::getOptionParser();
//		return $parser->description(__d('cake_console', 'Copy files from Templates/YourTemplate/ to any dir in the app. '
//		));
//	}
}

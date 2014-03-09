<?php

/**
 * SuperBake Shell script - superFile Task - Generates standalone files
 *
 * @copyright	 Copyright 2012, Manuel Tancoigne (http://experimentslabs.com)
 * @author		Manuel Tancoigne <m.tancoigne@gmail.com>
 * @link		  http://experimentslabs.com Experiments Labs
 * @package	   ELCMS.superBake.Task
 * @license	   GPL v3 (http://www.gnu.org/licenses/gpl.html)
 * @version	   0.3
 *
 * This file is based on the lib/Cake/Console/Command/Task/ViewTask.php file
 * from CakePHP.
 * 
 * Added methods/vars:
 * ==============
 *	getFilePath()
 * -----
 *	$Sbc
 *	$currentFileConfig
 *	$fileType
 *	$plugin
 *
 * Deleted methods/vars:
 * ================
 *	_associations()
 *	_interactive()
 *	_loadController()
 *	_methodsToBake()
 *	all()
 *	bakeActions()
 *	customAction()
 *	getOptionParser()
 *	getTemplate()
 * -----
 *	$controllerName
 *	$noTemplateAction
 *	$scaffoldActions
 * 
 * Modified methods:
 * =================
 *	bake()
 *	execute()
 *	getContent()
 *
 * Original methods/vars:
 * =================
 *	initialize()
 *	----
 *	$path
 *	$tasks
 *	$template
 */

// SbShell from superBake
App::uses('SbShell', 'Sb.Console/Command');

// Bake from superBake
App::uses('BakeTask', 'Sb.Console/Command/Task');

// Template from superBake
App::uses('TemplateTask', 'Sb.Console/Command/Task');

// Controller from Cake
App::uses('Controller', 'Controller');

/**
 * Task class for creating and updating view files.
 */
class SuperFileTask extends BakeTask {

	/**
	 * Tasks to be loaded by this Task
	 *
	 * @var array
	 */
	public $tasks = array('Project', 'Controller', 'DbConfig', 'Sb.Template');

	/**
	 * Path to View directory
	 *
	 * @var array
	 */
	public $path = null;

	/**
	 * The template file to use
	 *
	 * @var string
	 */
	public $template = null;

	/**
	 * A Sbc instance that gives all the methods to handle the config file.
	 *
	 * @var Sbc
	 */
	public $Sbc;

	/**
	 * File type: menu or file
	 *
	 * @var string
	 */
	public $fileType;

	/**
	 * Configuration array for current file/menu
	 *
	 * @var array
	 */
	public $currentFileConfig;

	/**
	 * Current plugin
	 *
	 * @var string
	 */
	public $plugin;

	/**
	 * Override initialize
	 *
	 * @return void
	 */
	public function initialize() {
		//@todo There's something to do with this to point to "app" or "<plugin>" dir...
		$this->path = current(App::path('View'));
	}

	/**
	 * Execution method always used for tasks.
	 *
	 * Note: no parent::execute() is used as arguments are handled in the shell.
	 *
	 * @return void
	 */
	public function execute() {
		// Dirty inclusion of the theme class that may contain logic to create HTML elements
		$themeClass = $this->Template->getThemePath() . DS . 'theme.php';
		if (!file_exists($themeClass)) {
			$this->speak(__d('superBake', 'The current theme has no theme class. It is not necessary, but can help...'), 'warning', 1);
		} else {
			include_once($this->Template->getThemePath() . DS . 'theme.php');
		}

		// Getting content from template
		$content = $this->getContent($this->currentFileConfig['template']);

		// Generating the whole file
		if ($content) {
			$this->bake($this->currentFileConfig['template'], $content);
		}
	}

	/**
	 * Assembles and writes the file.
	 *
	 * @param string $file File to bake
	 * @param string $content Content to write
	 *
	 * @return boolean Success/fail
	 */
	public function bake($file, $content = '') {
		// Info
		$this->speak(__d('superBake', 'Baking ' . $this->Sbc->pluginName($this->plugin) . '.%s %s file...', array($file, $this->fileType)), 'info', 0, 0, 1);
		// Checking if some content has been generated
		if ($content === true) {
			$content = $this->getContent($file);
		}
		if (empty($content)) {
			$this->speak(__d('superBake', 'Content is empty and file has not been writen. Check your template.'), 'error', 0, 0, 1);
			return false;
		}

		// Finding the destination path
		$path = $this->getFilePath();
//		$filename = $path . DS . (($this->fileType === 'menu') ? 'View' . DS : '') . $this->cleanPath($this->currentFileConfig['targetPath'] . DS . $this->currentFileConfig['targetFileName'] . '.' . $this->currentFileConfig['ext']);
		$filename = $path . DS . (($this->fileType === 'menu') ? 'View' . DS : '') . $this->cleanPath($this->currentFileConfig['targetPath']);
		// Saving the file.
		return $this->createFile($filename, $content);
	}

	/**
	 * Builds content from template and variables
	 *
	 * @param string $file name to generate content to
	 * @param array $vars passed for use in templates
	 *
	 * @return string content from template
	 */
	public function getContent($file, $vars = null) {
		foreach ($this->currentFileConfig['options'] as $k => $v) {
			$this->Template->set($k, $v);
		}

		// Current action (template filename)
		$this->Template->set('file', $file);
		// Plugin name
		$this->Template->set('plugin', $this->plugin);
		//Sbc object
		$this->Template->Sbc = $this->Sbc;
		// Vars passed as arguments
		$this->Template->set($vars);

		if ($file) {
			return $this->Template->generate((($this->fileType === 'menu') ? 'menus' . DS : 'files'), $this->cleanPath($file));
		}

		return false;
	}

	/**
	 * Gets the option parser for this task
	 * Removed because useless (need tests)
	 *
	 * @return ConsoleOptionParser
	 */
//	public function getOptionParser() {
//		$parser = parent::getOptionParser();
//		return $parser->description(__d('superBake', 'Bake files, using templates.'));
//	}

	/**
	 * Gets the path for the current file. Checks the plugin property
	 * and returns the correct path.
	 *
	 * Orig. function from BakeTask.php (getPath())
	 *
	 * @return string Path to output.
	 */
	public function getFilePath() {
		$path = $this->path;
		if (isset($this->plugin)) {
			$path = $this->_pluginPath($this->plugin) . $this->name . DS;
		}
		return dirname($path);
	}

}

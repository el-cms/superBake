<?php

/**
 * SuperBake Shell script - superFile Task - Generates standalone files
 * 
 * @copyright     Copyright 2012, Manuel Tancoigne (http://experimentslabs.com)
 * @author        Manuel Tancoigne <m.tancoigne@gmail.com>
 * @link          http://experimentslabs.com Experiments Labs
 * @package       ELCMS.superBake.Task
 * @license       GPL v3 (http://www.gnu.org/licenses/gpl.html)
 * @version       0.3
 *
 * This file is based on the lib/Cake/Console/Command/Task/ViewTask.php file 
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
// SbShell
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
	 * path to View directory
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
	 * Reference to the sbc object
	 * @var object
	 */
	public $sbc;

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
	 * @var string
	 */
	public $plugin;

	/**
	 * Override initialize
	 *
	 * @return void
	 */
	public function initialize() {
		$this->path = current(App::path('View'));
	}

	/**
	 * Execution method always used for tasks
	 *
	 * @return mixed
	 */
	public function execute() {
		// Generating content
		$content = $this->getContent($this->currentFileConfig['template']);
		if ($content) {
			$this->bake($this->currentFileConfig['template'], $content);
		}
	}

	/**
	 * Assembles and writes bakes the view file.
	 *
	 * @param string $action Action to bake
	 * @param string $content Content to write
	 * @return boolean Success
	 */
	public function bake($action, $content = '') {
		if ($content === true) {
			$content = $this->getContent($action);
		}
		if (empty($content)) {
			return false;
		}
		$this->out("\n" . __d('cake_console', 'Baking ' . $this->sbc->pluginName($this->plugin) . '.%s %s file...', array($action, $this->fileType)), 1, Shell::QUIET);
		$path = $this->getFilePath();

		$filename = $path . DS . (($this->fileType == 'menu') ? 'View' . DS : '') . $this->cleanPath($this->currentFileConfig['targetPath'] . DS . $this->currentFileConfig['targetFileName'] . '.' . $this->currentFileConfig['ext']);
		return $this->createFile($filename, $content);
	}

	/**
	 * Builds content from template and variables
	 *
	 * @param string $action name to generate content to
	 * @param array $vars passed for use in templates
	 * @return string content from template
	 */
	public function getContent($action, $vars = null) {
		foreach ($this->currentFileConfig['options'] as $k => $v) {
			$this->Template->set($k, $v);
		}

		$this->Template->set('action', $action);
		// Plugin name
		$this->Template->set('plugin', $this->plugin);
		//Sbc
		$this->Template->sbc = $this->sbc;
		// Other vars
		$this->Template->set($vars);
		$template = $action;
//		$template = $this->cleanPath((($this->fileType == 'menu') ? 'views::' : 'files::') . Inflector::pluralize($this->fileType) . '::' . ((empty($this->currentFileConfig['template'])) ? $this->fileName : $this->currentFileConfig['template'])) . '.ctp';
		if ($template) {
			return $this->Template->generate((($this->fileType == 'menu') ? 'menus' . DS : 'files'), $this->cleanPath($template));
		}

		return false;
	}

	/**
	 * get the option parser for this task
	 *
	 * @return ConsoleOptionParser
	 */
	public function getOptionParser() {
		$parser = parent::getOptionParser();
		return $parser->description(__d('cake_console', 'Bake menus, using templates.'));
	}

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
			$path = $this->_pluginPath($this->plugin) .$this->name. DS;
		}
		return dirname($path);
	}

}

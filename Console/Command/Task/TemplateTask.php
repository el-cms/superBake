<?php

/**
 * Template Task can generate templated output Used in other Tasks
 *
 * Original CakePHP's task, but this one extends SbShell instead of AppShell
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
 * @since         CakePHP(tm) v 1.3
 * @license       http://www.opensource.org/licenses/mit-license.php MIT License
 */
// Utilities to work with files and folders
App::uses('Folder', 'Utility');
// Methods, template-specific
App::uses('Theme', 'Sb.Console/Template');

/**
 * Template Task can generate templated output Used in other Tasks.
 * Acts like a simplified View class.
 *
 * @package       Cake.Console.Command.Task
 */
class TemplateTask extends Theme {

	/**
	 * variables to add to template scope
	 *
	 * @var array
	 */
	public $templateVars = array();

	/**
	 * Paths to look for templates on.
	 * Contains a list of $theme => $path
	 *
	 * @var array
	 */
//	public $templatePaths = array();

	/**
	 * Path to the Template directory.
	 * @var string
	 */
	public $templatePath=null;


	/**
	 * Set variable values to the template scope
	 *
	 * @param string|array $one A string or an array of data.
	 * @param string|array $two Value in case $one is a string (which then works as the key).
	 *   Unused if $one is an associative array, otherwise serves as the values to $one's keys.
	 * @return void
	 */
	public function set($one, $two = null) {
		if (is_array($one)) {
			if (is_array($two)) {
				$data = array_combine($one, $two);
			} else {
				$data = $one;
			}
		} else {
			$data = array($one => $two);
		}

		if (!$data) {
			return false;
		}
		$this->templateVars = $data + $this->templateVars;
	}

	/**
	 * Runs the template
	 *
	 * @param string $directory directory / type of thing you want
	 * @param string $filename template name
	 * @param array $vars Additional vars to set to template scope.
	 * @return string contents of generated code template
	 */
	public function generate($directory, $filename, $vars = null) {
		if ($vars !== null) {
			$this->set($vars);
		}
		$themePath = $this->_pluginPath('Sb') . 'Console' . DS . 'Template'.DS;
		$templateFile = $this->_findTemplate($themePath, $directory, $filename);
		if ($templateFile) {
			extract($this->templateVars);
			ob_start();
			ob_implicit_flush(0);
			include $templateFile;
			$content = ob_get_clean();
			return $content;
		}
		return '';
	}

	/**
	 * Find the theme name for the current operation.
	 * If there is only one theme in $templatePaths it will be used.
	 * If there is a -theme param in the cli args, it will be used.
	 * If there is more than one installed theme user interaction will happen
	 *
	 * @return string returns the path to the selected theme.
	 */
	public function getThemePath() {

		if(is_null($this->templatePath)){
			$this->templatePath=$this->_pluginPath('Sb') . 'Console' . DS . 'Template'.DS;
		}
		return $this->templatePath;

	}

	/**
	 * Find a template inside a directory inside a path.
	 * Will scan all other theme dirs if the template is not found in the first directory.
	 *
	 * @param string $path The initial path to look for the file on. If it is not found fallbacks will be used.
	 * @param string $directory Subdirectory to look for ie. 'views', 'objects'
	 * @param string $filename lower_case_underscored filename you want.
	 * @return string filename will exit program if template is not found.
	 */
	protected function _findTemplate($path, $directory, $filename) {
		$themeFile = $path . $directory . DS . $filename . '.ctp';
		if (file_exists($themeFile)) {
			return $themeFile;
		}
		$this->err(__d('cake_console', 'Could not find template for %s (in %s)', array($filename, $path.$directory)));
		return false;
	}

}

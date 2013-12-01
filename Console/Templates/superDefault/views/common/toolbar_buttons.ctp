<?php

/**
 * PHP file for EL-CMS
 * 
 * This file should be included in any template that needs a "related controllers actions" part.
 * 
 * @copyright     Copyright 2012, Manuel Tancoigne (http://experimentslabs.com)
 * @author        Manuel Tancoigne <m.tancoigne@gmail.com>
 * @link          http://experimentslabs.com Experiments Labs
 * @license       GPL v3 (http://www.gnu.org/licenses/gpl.html)
 * @package       ELCMS.superBake.Templates.Default.Views
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
// ---
// Resetting options to defaults if not defined
// ---
// Hidden controllers
if (!isset($toolbarHiddenControllers)) {
	$toolbarHiddenControllers = array();
}

// Is the view displaying an item ? Define this variable in views that represents an item
// and the delete/Edit actions will be created.
if (!isset($viewIsAnItem)) {
	$viewIsAnItem = false;
}

// ---
// Some functions
// ---
if (!function_exists('newBtGroup')) {

	/**
	 * Creates a new buttons group
	 * 
	 * @param string $title Group name
	 * @param array $content Array of toolbar elements
	 * 
	 * @return string String to add in the HTML
	 */
	function newBtGroup($content) {
		$toolbar = "\t<ul>\n";
		foreach ($content as $item) {
			$toolbar.= "\t\t<li>" . $item . "</li>\n";
		}
		$toolbar .= "\t</ul>\n";
		return $toolbar;
	}

}

// ---
// Creating the toolbar
// ---
// Definying final toolbar (will contain all the elements)
$toolbar = '';

// Total number of elements in the toolbar (used to display it or not)
$toolbarElements = 0;

// ---
// Toolbar for current controller
// ---
$current_toolbar = array();

// Index
if ($this->canDo('index')) {
	$current_toolbar[] = "<?php echo \$this->Html->Link(" . $this->iString("List $pluralHumanName") . "," . $this->url('index', $pluralVar) . ");?>";
}

// Add
if ($this->canDo('add')) {
	$current_toolbar[] = "<?php echo \$this->Html->Link(" . $this->iString("New $singularHumanName") . "," . $this->url('add', $pluralVar) . ");?>";
}

// Edit (Only on view)
if ($this->canDo('edit') && $viewIsAnItem) {
	$current_toolbar[] = "<?php echo \$this->Html->Link(" . $this->iString("Edit this $singularHumanName") . "," . $this->url('edit', $pluralVar, "\${$singularVar}['{$modelClass}']['{$primaryKey}']") . ");?>";
}

// (Only on view)
if ($this->canDo('delete') && $viewIsAnItem) {
	$current_toolbar[] = "<?php echo \$this->Form->postLink(" . $this->iString("Delete this $singularHumanName") . ", " . $this->url('delete', null, "\${$singularVar}['{$modelClass}']['{$primaryKey}']") . ", array('confirm' => __('Are you sure you want to delete \"%s\"?', \${$singularVar}['{$modelClass}']['{$displayField}']), 'title'=>__('Delete this entry')); ?>";
}
// Toolbar : Current controller
if (count($current_toolbar) > 0) {
	// Element
	$toolbar.= '<h3><?php echo ' . $this->iString($pluralHumanName) . "?></h3>\n" . newBtGroup($current_toolbar);
	$toolbarElements++;
}

// ---
// Related controllers actions :
// ---
$done = array();
foreach ($associations as $type => $data) {

	foreach ($data as $alias => $details) {
		if ($details['controller'] != $this->name && !in_array($details['controller'], $done) && !in_array(Inflector::camelize($details['controller']), $toolbarHiddenControllers)) {
			$current_controller_actions = 0;
			$current_toolbar = array();
			// Related controllers actions : List / Add
			if ($this->canDo('index', null, $details['controller'])) {
				$current_controller_actions = 1;
				$title = $this->iString('List ' . strtolower(Inflector::humanize($details['controller'])));
				$current_toolbar[] = "<?php echo \$this->Html->Link('<i class=\"icon-list\"></i> ' . " . $title . "," . $this->url('index', $details['controller']) . ", array('title'=>" . $title . ", 'escape'=> false));?>";
			}
			if ($this->canDo('add', null, $details['controller'])) {
				$current_controller_actions = 1;
				$title = $this->iString('New ' . strtolower(Inflector::humanize(Inflector::singularize($details['controller']))));
				$current_toolbar[] = "<?php echo \$this->Html->Link('<i class=\"icon-plus-sign\"></i> ' . " . $title . "," . $this->url('add', $details['controller']) . ", array('title'=>" . $title . ", 'escape'=> false));?>";
			}
			// Creating toolbar for controller and adding to the /global/ toolbar
			if ($current_controller_actions == 1) {
				$toolbar.='<h3><?php echo ' . $this->iString(Inflector::humanize($details['controller'])) . "?></h3>\n" . newBtGroup($current_toolbar);
			}
			$done[] = $details['controller'];
			$toolbarElements++;
		}
	}
}
// Toolbars wrapping
if ($toolbarElements > 0) {
	echo "<div class=\"actions\">\n$toolbar\n</div>\n";
}
?>
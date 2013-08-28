<?php

/**
 * PHP file for EL-CMS
 * 
 * This file should be included in any template that needs a "related controllers actions" part.
 * 
 * @copyright     Copyright 2012, Manuel Tancoigne (http://experimentslabs.com)
 * @author        Manuel Tancoigne <m.tancoigne@gmail.com>
 * @link          http://experimentslabs.com Experiments Labs
 * @package       EL-CMS/superBake
 * @license       GPL v3 (http://www.gnu.org/licenses/gpl.html)
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
/**
 * This template uses the following options/variables :
 * ====================================================
 * - toolbarHiddenControllers = array(controller1, controller2,...) : an array of models not to be linked to.
 * 		default : array()
 * - actionsPerRow = int: Number of related controllers to display on a row
 * 		default : 4
 */
// ---
// Resetting options to defaults if not defined
// ---
// Hidden controllers
if (!isset($toolbarHiddenControllers)) {
	$toolbarHiddenControllers = array();
}

// Actions per row
if (!isset($actionsPerRow)) {
	$actionsPerRow = 4;
}

// ---
// Some functions
// ---
if (!function_exists('newRow')) {

	/**
	 * Creates a new HTML row-fluid div element
	 * @param string $type Type of row (can be "open", "close" or "both")
	 * @return string|boolean String to add in the HTML, false if bad $type
	 */
	function newRow($type) {
		$open = ''; //"<div class=\"row\">\n";
		$close = ''; //"</div>\n";
		if ($type == 'open') {
			return $open;
		} elseif ($type == 'close') {
			return $close;
		} elseif ($type == 'both') {
			return $close . $open;
		}
		return false;
	}

}
if (!function_exists('newTbGroup')) {

	/**
	 * Creates a new dropdown buttons group
	 * @param string $title Group name
	 * @param array $content Array of toolbar elements
	 * 
	 * @return string String to add in the HTML
	 */
	function newTbGroup($title, $content, $actionsPerRow, $style = 'default') {
		$toolbar='';
//		$toolbar .= "<div class=\"col-lg-" . (12 / $actionsPerRow) . "\">\n";
		$toolbar .="\t<div class=\"btn-group\">\n";
		$toolbar .="\t\t<a class=\"btn dropdown-toggle btn-" . $style . "\" data-toggle=\"dropdown\" href=\"#\"><?php echo " . $title . "; ?><span class=\"caret\"></span></a>\n";
		$toolbar .="\t\t<ul class=\"dropdown-menu\">\n";
		foreach ($content as $item) {
			$toolbar.= "\t\t\t<li>\n\t\t\t\t" . $item . "\t\t\t</li>\n";
		}
		$toolbar .= "\t\t</ul>\n";
		$toolbar .= "\t</div>\n";
//		$toolbar .= "</div>\n";
		return $toolbar;
	}

}
if (!function_exists('newBtGroup')) {

	/**
	 * Creates a new buttons group
	 * @param string $title Group name
	 * @param array $content Array of toolbar elements
	 * 
	 * @return string String to add in the HTML
	 */
	function newBtGroup($title, $content, $actionsPerRow, $style = 'default') {
		$toolbar='';
//		$toolbar .= "<div class=\"col-lg-" . (12 / $actionsPerRow) . "\">\n";
		$toolbar .="\t<div class=\"btn-group\">\n";
//		$toolbar .="\t\t<a class=\"btn dropdown-toggle btn-" . $style . "\" data-toggle=\"dropdown\" href=\"#\"><?php echo " . $title . "; ? ><span class=\"caret\"></span></a>\n";
//		$toolbar .="\t\t<ul class=\"dropdown-menu\">\n";
		foreach ($content as $item) {
			$toolbar.= "\t\t\t\t\t\t\t" . $item . "\t\t\n";
		}
//		$toolbar .= "\t\t</ul>\n";
		$toolbar .= "\t</div>\n";
//		$toolbar .= "</div>\n";
		return $toolbar;
	}

}

// ---
// Creating the toolbar
// ---
// Final toolbar:
$toolbar = '';
// Number of toolbar groups. Used to count items on rows. Default to -1 to know 
// that no row is currently opened
$tbRowElements = -1;
// Total number of elements in the toolbar
$toolbarElements = 0;

//
// Toolbar for current controller
//
$current_toolbar = array();

// Index
if ($this->actionable('index', $pluralVar)) {
	$title = $this->display("List $pluralHumanName");
	$current_toolbar[] = "<?php echo \$this->HTML->Link('<i class=\"icon-list\"></i> ' . " . $title . "," . $this->url('index', $pluralVar) . ", array('class'=>'btn btn-default', 'title' => " . $title . ", 'escape' => false));?>\n";
}

// Add
if ($this->actionable('add', $pluralVar)) {
	$title = $this->display("New $singularHumanName");
	$current_toolbar[] = "<?php echo \$this->HTML->Link('<i class=\"icon-plus-sign\"></i> ' . " . $title . "," . $this->url('add', $pluralVar) . ", array('class'=>'btn btn-default', 'title' => " . $title . ", 'escape' => false));?>\n";
}

// Edit (Only on view)
if ($this->actionable('edit', $pluralVar) && $this->currentAction() == 'view') {
	$title = $this->display("Edit this $singularHumanName");
	$current_toolbar[] = "<?php echo \$this->HTML->Link('<i class=\"icon-pencil\"></i> ' . " . $title . "," . $this->url('edit', $pluralVar, "$".strtolower($modelClass)."['{$modelClass}']['{$primaryKey}']") . ", array('class'=>'btn btn-default', 'title'=>" . $title . ", 'escape'=> false));?>\n";
}

// (Only on view)
if ($this->actionable('delete', $pluralVar) && $this->currentAction() == 'view') {
	$title = '__(\'Delete\')';
	$current_toolbar[] = "<?php echo \$this->Form->postLink('<i class=\"icon-trash\"></i> '." . $title . ", " . $this->url('delete', $pluralVar, "\$this->Form->value('{$modelClass}.{$primaryKey}')") . ", array('confirm' => __('Are you sure you want to delete # %s?', \$this->Form->value('{$modelClass}.{$primaryKey}')), 'title'=>__('Delete this entry'),'class'=>'btn btn-warning',  'escape'=>false)); ?>";
}
// Toolbar
if (count($current_toolbar) > 0) {
	// Row management
	if ($tbRowElements == -1) {
		$toolbar.= newRow('open');
		$tbRowElements = 1;
	}
	// Element
	$toolbar.= newBtGroup($this->display($pluralHumanName), $current_toolbar, $actionsPerRow, 'primary');
	$toolbarElements++;
	if ($tbRowElements == $actionsPerRow) {
		$toolbar.= newRow('close');
		$tbRowElements = -1;
	}
}
// ---
// Related controllers actions :
// ---
$done = array();
foreach ($associations as $type => $data) {

	foreach ($data as $alias => $details) {
		if ($details['controller'] != $this->name && !in_array($details['controller'], $done) && !in_array(Inflector::camelize($details['controller']), $toolbarHiddenControllers)) {
			// Row management
			if ($tbRowElements == -1) {
				$toolbar.= newRow('open');
				$tbRowElements = 1;
			}
			$current_controller_actions = 0;
			$current_toolbar = array();
			// Related controllers actions : List / Add
			if ($this->actionable('index', $details['controller'])) {
				$current_controller_actions = 1;
				$title = $this->display('List ' . strtolower(Inflector::humanize($details['controller'])));
				$current_toolbar[] = "<?php echo \$this->Html->Link('<i class=\"icon-list\"></i> ' . " . $title . "," . $this->url('index', $details['controller']) . ", array('title'=>" . $title . ", 'escape'=> false));?>\n";
			}
			if ($this->actionable('add', $details['controller'])) {
				$current_controller_actions = 1;
				$title = $this->display('New ' . strtolower(Inflector::humanize(Inflector::singularize($details['controller']))));
				$current_toolbar[] = "<?php echo \$this->Html->Link('<i class=\"icon-plus-sign\"></i> ' . " . $title . "," . $this->url('add', $details['controller']) . ", array('title'=>" . $title . ", 'escape'=> false));?>\n";
			}
			// Creating toolbar for controller and adding to the /global/ toolbar
			if ($current_controller_actions == 1) {
				$toolbar.=newTbGroup($this->display(Inflector::humanize($details['controller'])), $current_toolbar, $actionsPerRow);
				// Incrementing the number of elements in row
				$tbRowElements++;
			}
			$done[] = $details['controller'];
			$toolbarElements++;
			// Row management
			if ($tbRowElements == $actionsPerRow) {
				$toolbar.= newRow('close');
				$tbRowElements = -1;
			}
		}
	}
}
// Row management
if ($tbRowElements > -1) {
	$toolbar.=newRow('close');
}
// Toolbars wrapping
if ($toolbarElements > 0) {
	echo "$toolbar\n";
}
?>
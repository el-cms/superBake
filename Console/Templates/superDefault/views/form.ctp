<?php
/**
 * Form view (used for add and edit actions)
 * 
 * @copyright     Copyright 2012, Manuel Tancoigne (http://experimentslabs.com)
 * @author        Manuel Tancoigne <m.tancoigne@gmail.com>
 * @link          http://experimentslabs.com Experiments Labs
 * @license       GPL v3 (http://www.gnu.org/licenses/gpl.html)
 * @package       ELCMS.superBake.Templates.Default.Views
 * @version       0.3
 * 
 * @todo update datePicker: it should open inside the view.
 * 
 * ----
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
//Page headers and licensing
include($themePath . 'views/common/headers.ctp');

/* ----------------------------------------------------------------------------
 * Current template options
 */
// Additionnal CSS
if (!isset($additionnalCSS) || !is_array($additionnalCSS)) {
	$additionnalCSS = array();
}
// Additionnal JS
if (!isset($additionnalJS) || !is_array($additionnalJS)) {
	$additionnalJS = array();
}
// Hidden fields
if (!isset($hiddenFields) || !is_array($hiddenFields)) {
	$hiddenFields = array();
}

// File field
if (!isset($fileField)) {
	$fileField = null;
}

/* ----------------------------------------------------------------------------
 * Toolbar options
 */
// No toolbar option
if (!isset($noToolbar)) {
	$noToolbar = false;
}
?>
<div class="<?php echo $pluralVar; ?> form">
	<?php
	$hasFileField = (!is_null($fileField)) ? ", 'enctype'=>'multipart/form-data'" : '';
	echo "<?php echo \$this->Form->create('$modelClass', array($hasFileField)); ?>\n";
	?>
	<fieldset>
		<legend><?php printf("<?php echo __('%s %s'); ?>", Inflector::humanize($action), $singularHumanName); ?></legend>
		<?php
		echo "\t<?php\n";
		foreach ($fields as $field) {
			if (strpos($action, 'add') !== false && $field == $primaryKey) {
				continue;
			} elseif (!in_array($field, array('created', 'modified', 'updated'))) {
				echo "\t\techo \$this->Form->input('{$field}');\n";
			}
		}
		if (!empty($associations['hasAndBelongsToMany'])) {
			echo '<h2>Associated data:</h2>';
			foreach ($associations['hasAndBelongsToMany'] as $assocName => $assocData) {
				echo "\t\techo \$this->Form->input('{$assocName}');\n";
			}
		}
		echo "?>\n";
		?>
	</fieldset>
	<?php
	echo "<?php echo \$this->Form->end(__('Submit')); ?>\n";
	?>
</div>
<?php
// Toolbar
if ($noToolbar === false) {
	include(dirname(__FILE__) . DS . 'common' . DS . 'toolbar_buttons.ctp');
}
// Additionnal scripts and CSS
$out = '';
foreach ($additionnalCSS as $k => $v) {
	if ($v == true) {
		$out.= "\techo \$this->Html->css('" . $this->cleanPath($k) . "');\n";
	}
}
foreach ($additionnalJS as $k => $v) {
	if ($v == true) {
		$out.="\techo \$this->Html->script('" . $this->cleanPath($k) . "');\n";
	}
}
?>

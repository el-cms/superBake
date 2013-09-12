<?php
/**
 * Form view (used for add and edit actions)
 * 
 * @copyright     Copyright 2012, Manuel Tancoigne (http://experimentslabs.com)
 * @author        Manuel Tancoigne <m.tancoigne@gmail.com>
 * @link          http://experimentslabs.com Experiments Labs
 * @package       EL-CMS/superBake
 * @license       GPL v3 (http://www.gnu.org/licenses/gpl.html)
 * 
 * @todo update datePicker
 *
 * Available and useful vars :
 * ===========================
 * (also available with $this->templateVars (array): )
 * - $primaryKey (Model's primary key name)
 * - $displayField (Field to display (defined in model's Model)
 * - $singularVar (Singular model name)
 * - $singularHumanName (Singular name, human readable)
 * - $pluralVar (Plural name. Use it for url()/actionable()/...)
 * - $pluralHumanName (Plural name, human readable)
 * - $fields (array of model's fields)
 * - $associations (array of associated models. created bybake/superBake in model
 *   file)
 * - $plugin (Current plugin name)
 * - $action (Current action)
 * - $admin (Current routing prefix)
 * (Only available with $this: )
 * - $this->projectConfig (superBake array, read about configuring superBake)
 * 
 * Available and useful methods :
 * ==============================
 * superBake methods in Console/AppShell.php
 * See AppShell file for more usage informations and vars.
 * - $this->url() (returns an array to use as cake URL in redirections, 
 *   $this->Html->Link(), and wherever you want to use it)
 * - $this->display() (returns correctly __('string') or __d('plugin', 'string'))
 * - $this->actionable() (Used to make checks before creating links. Returns false
 *   if action don't exists for current prefix)
 * - $this->alowedActions() (array, current actions available for current prefix)
 * 
 * Options that you can use in config file:
 * ========================================
 * Current template options:
 * -------------------------
 * - additionnalCSS: array of CSS files to load (use '::' as directory separator)
 *   i.e.: array('path::to::css'=>'1')
 * - additionnalJS: array of JS files to load (use '::' as directory separator)
 *   i.e.: array('path::to::script'=>'1')
 * - noToolbar: boolean, default false. If set to true, no toolbar will be generated
 * - hiddenFields: array of fields not to display
 * - fileField : string, default null. The name of a file field (for uploading things). 
 * 
 * Toolbar related options:
 * ------------------------
 * - noToolbar (true|false) : if set to true, no toolbar will be created for the current view.
 * - toolbarHiddenControllers array(controller1, controller2,...) : Controllers links to hide in generation
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

// Toolbar
if ($noToolbar === false) {
	echo "<div class=\"row toolbar\">\n";
	include(dirname(__FILE__) . DS . 'common' . DS . 'toolbar_buttons.ctp');
	echo "</div>\n";
}


if (!function_exists('dateField')) {

	function dateField($name, $data_format = 'dd MM yyyy - hh:ii') {
		$out = "<div class=\"input-group date form_datetime\" id=\"{$name}_dtPicker\">\n";
		$out.="<input type=\"text\" readonly class=\"form-control\" id=\"{$name}_field\" />\n";
		$out.="\t<span class=\"input-group-addon\">
					<span class=\"btn-small\"><i class=\"icon-remove\"></i></span>
					<span class=\"btn-small\"><i class=\"icon-calendar\"></i></span>
				</span>\n";
//		$out.="\t
//					<span class=\"input-group-addon\"><i class=\"icon-remove\"></i></span>
//					<span class=\"input-group-addon\"><i class=\"icon-calendar\"></i></span>
//				\n";
		$out.="</div>\n";
		$out.="\t<?php echo \$this->Form->input('$name', array('type'=>'hidden', 'readonly', 'data-format'=>'$data_format', 'class'=>'form-control', 'div'=>false, 'label'=>false));?>\n";
		$out.="<script type=\"text/javascript\">
				$('#{$name}_field').val($('#Post" . Inflector::camelize($name) . "').val());
				$('#{$name}_dtPicker').datetimepicker({
					format: \"dd MM yyyy - hh:ii:ss\",
					autoclose: true,
					todayBtn: true,
					minuteStep: 5,
					language: 'fr',
					linkField: \"Post" . Inflector::camelize($name) . "\",
					linkFormat: \"yyyy-mm-dd hh:ii:ss\"
				});
				// This is lame, but it updates the fields with db value
				$('#{$name}_dtPicker').datetimepicker('show');
				$('#{$name}_dtPicker').datetimepicker('hide');
			</script>\n";
		return $out;
	}
}

if (!function_exists('openRow')) {
	/**
	 * Opens a form row
	 * @param string $field Field name
	 * @return string
	 */
	function openRow($field) {
		$out = "<div class=\"form-group\">";
		$out.="\t<?php echo \$this->Form->label('$field', '" . ucfirst(strtolower(Inflector::humanize($field))) . "',  array('class' => 'col-lg-2 control-label'))?>\n";
		$out.="\t<div class=\"col-lg-10\">\n";
		return $out;
	}
}
if (!function_exists('closeRow')) {
	/**
	 * Closes a form row
	 * @return string
	 */
	function closeRow() {
		return "\t</div>\n</div>\n";
	}
}
$hasFileField = (!is_null($fileField)) ? ", 'enctype'=>'multipart/form-data'" : '';
echo "<?php echo \$this->Form->create('$modelClass', array('class'=>'form-horizontal'$hasFileField)); ?>\n";

echo "<?php echo \$this->Html->script('bootstrap-datetimepicker.min', array('inline' => false)); ?>\n";
echo "<?php echo \$this->Html->script('locales/bootstrap-datetimepicker.fr', array('inline' => false)); ?>\n";
echo "<?php echo \$this->Html->css('datetimepicker')?>\n";
?>
<fieldset>
	<?php
	foreach ($fields as $field) {
		//Skipping primary key
		if ((strpos($action, 'add') !== false && $field == $primaryKey) || in_array($field, $hiddenFields)) {
			continue;
		} else {
			$displayField = true;
			$displayLabel = true;
			//
			// Field type
			//
			switch ($schema[$field]['type']) {
				case 'datetime':
					if (in_array($field, array('updated', 'created', 'modified')) && strpos($action, 'add') == true) {
						$displayField = false;
					} else {
						$fieldHTML = dateField($field);
					}
					break;
				case 'text':
					$fieldHTML = "\t\t<?php echo \$this->Form->input('$field', array('div'=>false, 'label'=>false, 'class'=>'ckeditor form-control', 'placeholder'=>'$field')); ?>\n";
					$additionnalCSS['ckeditor::contents'] = true;
					$additionnalJS['ckeditor::ckeditor'] = true;
					break;
				default:
					//
					// Field name
					//
					switch ($field) {
					case 'password':
							$fieldValue = null;
							$fieldRequired = "true";
							if (strpos($action, 'edit') == true) {
								$fieldValue = "'value'=>null, ";
								$fieldRequired = "false";
						}
						$fieldHTML = "\t\t<?php echo \$this->Form->input('$field', array('div'=>false, 'label'=>false, $fieldValue'required'=>$fieldRequired, 'class'=>'form-control', 'type'=>'password', 'placeholder'=>'$field')); ?>\n";
						$fieldHTML .= "\t\t<?php echo \$this->Form->input('{$field}2', array('div'=>false, 'label'=>false, 'class'=>'form-control', 'type'=>'password', 'placeholder'=>'Re-type your password')); ?>\n";
						break;
					case $primaryKey:
							$displayLabel = false;
						$fieldHTML = "\t\t<?php echo \$this->Form->input('$field', array('div'=>false, 'label'=>false, 'class'=>'form-control', 'placeholder'=>'$field')); ?>\n";
						break;
					default:
							// Set type to file if fileField
							$isfileField = (!is_null($fileField) && $field == $fileField) ? "'type'=>'file', " : "";
							// No classes for file input
							$inputClass = (!is_null($fileField) && $field == $fileField) ? "" : "'class'=>'form-control', ";
							$fieldHTML = "\t\t<?php echo \$this->Form->input('$field', array($isfileField'div'=>false, 'label'=>false, $inputClass'placeholder'=>'$field')); ?>\n";
						break;
					}
					break;
			}
			if ($displayField == true) {
				// Opens row
				if ($displayLabel) {
					echo openRow($field);
				}
				// Field
				echo $fieldHTML;
				// Close row
				if ($displayLabel) {
					echo closeRow($field);
				}
			}
		}
	}
	if (!empty($associations['hasAndBelongsToMany'])) {
		echo '<h2>Associated data:</h2>';
		foreach ($associations['hasAndBelongsToMany'] as $assocName => $assocData) {
			echo openRow($assocName);
			echo "\t\techo \$this->Form->input('{$assocName}');\n";
			echo closeRow();
		}
	}
	?>
</fieldset>
<?php
echo "<?php echo \$this->Form->end(array('label'=>__('Submit'), 'class'=>'btn btn-primary')); ?>\n";

// Additionnal scripts and CSS
$out = '';
foreach ($additionnalCSS as $k => $v) {
	if ($v == true) {
	$out.= "\techo \$this->HTML->css('" . $this->cleanPath($k) . "');\n";
}
}
foreach ($additionnalJS as $k => $v) {
	if ($v == true) {
	$out.="\techo \$this->HTML->script('" . $this->cleanPath($k) . "');\n";
	}
}
if (!empty($out)) {
	echo "<?php \n $out\n?>";
}
?>

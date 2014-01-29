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
 */
//Page headers and licensing
include $themePath . 'views/common/headers.ctp';

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

// No toolbar option
if (!isset($noToolbar)) {
	$noToolbar = false;
}

/* ----------------------------------------------------------------------------
 *
 * View
 *
 *---------------------------------------------------------------------------*/

/* ----------------------------------------------------------------------------
 * Toolbar
 */
if ($noToolbar === false):
	include dirname(__FILE__) . DS . 'common' . DS . 'toolbar_buttons.ctp';
endif;

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
		foreach ($fields as $field):
			//Skipping primary key
			if ((strpos($action, 'add') !== false && $field === $primaryKey) || in_array($field, $hiddenFields)):
				continue;
			elseif (!in_array($field, array('created', 'modified', 'updated'))):
				echo "\t\techo \$this->Form->input('{$field}');\n";
			endif;
		endforeach;
		if (!empty($associations['hasAndBelongsToMany'])):
			echo '<h2>Associated data:</h2>';
			foreach ($associations['hasAndBelongsToMany'] as $assocName => $assocData):
				echo "\t\techo \$this->Form->input('{$assocName}');\n";
			endforeach;
		endif;
		echo "?>\n";
		?>
	</fieldset>
	<?php
	echo "<?php echo \$this->Form->end(__('Submit')); ?>\n";
	?>
</div>

<?php

/* -----------------------------------------------------------------------------
 * Additionnal scripts and CSS
 */
include $themePath.'views/common/additionnal_js_css.ctp';
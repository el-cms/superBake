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
include $this->templatePath . 'views/common/headers.ctp';

// Preparing schema srtucture and fields to replace the ones from Cake
$this->s_prepareSchemaFields();
// Updating schema array
$schema = $this->templateVars['schema'];
// Updating fields array
$fields = $this->templateVars['fields'];

$haveFileField = $this->s_haveFileField($schema);

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

//
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
	$hasFileField = (!empty($fileField)) ? ", 'enctype'=>'multipart/form-data'" : '';
	echo "<?php echo \$this->Form->create('$modelClass', array($hasFileField)); ?>\n";
	?>
	<fieldset>
		<legend><?php printf("<?php echo __('%s %s'); ?>", Inflector::humanize($action), $singularHumanName); ?></legend>
		<?php
		foreach ($fields as $field):

			// Remove PK if on an 'add' action
			if ((strpos($action, 'add') !== false && $field == $primaryKey) || in_array($field, $hiddenFields)):
				continue;
			else:
				$fieldContent = $this->v_prepareInputField($field, $schema[$field]);
				if($this->v_isFieldForeignKey($field, $associations)){
					$fieldContent['displayString']=$this->v_eFormInput($field);
				}
				echo "${fieldContent['displayString']}\n\n";
			endif;

		endforeach;
		// @todo Convert this with new methods
		if (!empty($associations['hasAndBelongsToMany'])):
			echo '<h2>Associated data:</h2>';
			foreach ($associations['hasAndBelongsToMany'] as $assocName => $assocData):
				echo "\t\techo \$this->Form->input('{$assocName}');\n";
			endforeach;
		endif;
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
include $this->templatePath . 'views/common/additionnal_js_css.ctp';

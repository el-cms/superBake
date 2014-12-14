<?php
/**
 * "View" view (used to display an item)
 *
 * Options from view/controller/part config:
 *  - noToolbar
 *  - hiddenFields
 *  - additionnalCSS
 *  - additionnalJS
 *  - relatedDataHideActionsList
 *  - hasMany_hideActions
 *  - hasMany_hiddenModels
 *  - hasMany_hiddenModelFields
 *  - languageFields
 *
 * @copyright     Copyright 2012, Manuel Tancoigne (http://experimentslabs.com)
 * @author        Manuel Tancoigne <m.tancoigne@gmail.com>
 * @link          http://experimentslabs.com Experiments Labs
 * @license       GPL v3 (http://www.gnu.org/licenses/gpl.html)
 * @package       ELCMS.superBake.Templates.Default.Views
 * @version       0.3
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
/* ----------------------------------------------------------------------------
 * Preparing data from model
 */

// Preparing schema srtucture and fields to replace the ones from Cake
$this->s_prepareSchemaFields();
// Updating schema array
$schema = $this->templateVars['schema'];
// Updating fields array
$fields = $this->templateVars['fields'];


/* ----------------------------------------------------------------------------
 * Current template options
 */

// No toolbar option
if (!isset($noToolbar)) {
	$noToolbar = false;
}

// Hidden fields for current model
if (!isset($hiddenFields) || !is_array($hiddenFields)) {
	$hiddenFields = array();
}

// Additionnal CSS
if (!isset($additionnalCSS) || !is_array($additionnalCSS)) {
	$additionnalCSS = array();
}
// Additionnal JS
if (!isset($additionnalJS) || !is_array($additionnalJS)) {
	$additionnalJS = array();
}

// Related data hide actions list for each entry
if (!isset($relatedDataHideActionsList)) {
	$relatedDataHideActionsList = false;
}

/* ----------------------------------------------------------------------------
 * related data options
 */
// Has Many : hide actions
if (!isset($hasMany_hideActions)) {
	$hasMany_hideActions = false;
}

// Has Many : hidden models
if (!isset($hasMany_hiddenModels) || !is_array($hasMany_hiddenModels)) {
	$hasMany_hiddenModels = array();
}

// Has Many : hidden models fields
if (!isset($hasMany_hiddenModelFields) || !is_array($hasMany_hiddenModelFields)) {
	$hasMany_hiddenModelFields = array();
}

/* ---------------------------------------------------------------------------
 *
 * Preparing contents
 *
 * --------------------------------------------------------------------------- */

// Fields that are on the big side
$textFields = array();
// Fields in a smaller column
$regularFields = array();
$i = 0;
foreach ($fields as $field) {

	$isTextField = false;

	$key = $this->v_isFieldForeignKey($field, $associations);

	// Field is "just" a field
	if (!is_array($key)) {
		// Preparing string to display
		$fieldContent = $this->v_prepareDisplayField($field, $schema[$field]);
		$regularFields[$field] = array(
				'field' => "echo " . $this->iString($this->v_getNiceFieldName($field)) . ";",
				'content' => $fieldContent['displayString']
		);
	} else {
		// Foreign key:
		$fieldContent = $this->v_prepareDisplayFieldForeignKey($field, $key, $schema[$field]);
		$regularFields[$field] = array(
				'field' => "echo " . $this->iString($this->v_getNiceFieldName($field)) . ";",
				'content' => $fieldContent['displayString']
		);
	}
} // end foreach

/* ----------------------------------------------------------------------------
 *
 * View
 *
 * --------------------------------------------------------------------------- */
/* ----------------------------------------------------------------------------
 * Headers and licensing
 */
include $themePath . 'views/common/headers.ctp';

/* ----------------------------------------------------------------------------
 * Toolbar
 */
// This view represents an item:
$viewIsAnItem = true;
if ($noToolbar === false):
	include dirname(__FILE__) . DS . 'common' . DS . 'toolbar_buttons.ctp';
endif;
/* ----------------------------------------------------------------------------
 * Current view
 */
?>
<div class="<?php echo $pluralVar; ?> view">
	<dl>
		<?php
		foreach ($regularFields as $field):
			echo "\t\t<dt><?php {$field['field']} ?></dt>\n";
			echo "\t\t<dd>{$field['content']}</dd>\n";
		endforeach;
		?>
	</dl>
	<?php
	/* ----------------------------------------------------------------------------
	 *
	 * Associations
	 *
	 */

	/* ----------------------------------------------------------------------------
	 * HasOne associations
	 */
	$hasOne = '';
	if (!empty($associations['hasOne'])):
		foreach ($associations['hasOne'] as $alias => $details):

			// Keep a copy of the original fields list for later use
			$originalFieldsList = $details['fields'];
			// Prepare the fields
			$details = $this->s_prepareSchemaRelatedFields($alias, $details, true);

			// View for hasOne associations.
			$hasOne.= "<?php if (isset(\${$singularVar}['{$alias}']['{$details['primaryKey']}'])): ?>\n";
			$hasOne.= '<div class="related">';
			$hasOne.= "<h3><?php echo " . $this->iString("Related " . Inflector::humanize($details['controller'])) . "; ?></h3>";
			$hasOne.="<dl>";

			// Fields
			foreach ($details['fields'] as $field):

				$fieldContent = $this->v_prepareDisplayRelatedField($field, $details, $originalFieldsList, true);
				$hasOne.= "\t\t<dt><?php echo " . $this->iString($this->v_getNiceFieldName($field)) . "; ?></dt>\n";
				$hasOne.= "\t\t<dd>\n\t{$fieldContent['displayString']}\n</dd>\n";

			endforeach;

			$hasOne.="</dl>";
			if ($this->canDo('edit', null, $details['controller'])):
				$hasOne.='<div class="actions">';
				$hasOne.="<ul>";
				$hasOne.="<li><?php echo \$this->Html->link(" . $this->iString("Edit " . Inflector::humanize(Inflector::underscore($alias))) . ", " . $this->url('edit', $details['controller'], null, "\${$singularVar}['{$alias}']['{$details['primaryKey']}']") . "); ?></li>\n";
				$hasOne.="</ul>";
				$hasOne.="</div>";
			endif;

			$hasOne.="</div>";
			$hasOne.="<?php endif; ?>\n";
		endforeach;
	endif; // End of hasOne associations
	echo $hasOne;
	?>
</div>
<?php
/* ----------------------------------------------------------------------------
 * HasMany associations
 */
if (empty($associations['hasMany'])):
	$associations['hasMany'] = array();
endif;
if (empty($associations['hasAndBelongsToMany'])):
	$associations['hasAndBelongsToMany'] = array();
endif;
$relations = array_merge($associations['hasMany'], $associations['hasAndBelongsToMany']);
$i = 0;

$has_assoc = 0;
$lis = '';
$divs = '';

if (count($relations) > 0):
	echo "\t<h2><?php echo __('Related data'); ?></h2>\n";
endif;
foreach ($relations as $alias => $details):
	// Copying the original fields list
	$originalFieldList = $details['fields'];
	// Updating details infos
	$details = $this->s_prepareSchemaRelatedFields($alias, $details);
	// CamelCasing controller name
	$ccController = Inflector::camelize($details['controller']);
	if (!in_array($ccController, $hasMany_hiddenModels)):
		$has_assoc+=1;

		$otherSingularVar = Inflector::variable($alias);
		$otherPluralHumanName = Inflector::humanize($details['controller']);

		// Association name
		echo "<?php echo " . $this->iString($otherPluralHumanName) . "; ?>(<?php echo count(\${$singularVar}['{$alias}']); ?>)\n";

		// Table data
		echo "\t\t\t<?php if (!empty(\${$singularVar}['{$alias}'])): ?>\n";
		echo "\t\t\t<table>\n";
		echo "\t\t\t\t<thead>\n";
		echo "\t\t\t\t\t<tr>\n";

		// Actions Col
		if ($relatedDataHideActionsList === false && $hasMany_hideActions === false):
			echo "\t\t\t\t\t\t<th class=\"actionsCol\"><?php echo __('Actions'); ?></th>\n";
		endif;

		//
		// Fields
		//

  // Headers
		foreach ($details['fields'] as $field):
			echo "\t\t\t\t\t\t<th><?php echo " . $this->iString($this->v_getNiceFieldName($field)) . "; ?></th>\n";
		endforeach;

		echo "\t\t\t\t\t</tr>\n";
		echo "\t\t\t\t</thead>\n";
		echo "\t\t\t\t<tbody>\n";
		echo "\t\t\t\t<?php\n";
		echo "\t\t\t\tforeach (\${$singularVar}['{$alias}'] as \${$otherSingularVar}): ?>\n";
		echo "\t\t\t\t\t<tr>\n";

		// Testing actions
		if ($relatedDataHideActionsList === false):
			if ($hasMany_hideActions == false):
				$hasActions = 0;
				$actions = '';
				$disabled = '';
				// "View" action
				if ($this->canDo('view', null, $ccController)):
					$hasActions = 1;
					$actions.= "\t\t\t\t\t\t\t\t\t<li><?php echo \$this->Html->Link(__('View')," . $this->url('view', $details['controller'], null, "\${$otherSingularVar}['{$details['primaryKey']}']") . ");?></li>\n";
				endif;
				// "Edit" action
				if ($this->canDo('edit', null, $ccController)):
					$hasActions = 1;
					$actions.="\t\t\t\t\t\t\t\t\t<li><?php echo \$this->Html->Link(__('Edit')," . $this->url('edit', $details['controller'], null, "\${$otherSingularVar}['{$details['primaryKey']}']") . ");?></li>\n";
				endif;
				// "Delete" action
				if ($this->canDo('delete', null, $ccController)):
					$hasActions = 1;
					$actions.= "\t\t\t\t\t\t\t\t\t<li><?php echo \$this->Form->postLink(__('Delete'), " . $this->url('delete', $details['controller'], null, "\${$otherSingularVar}['{$details['primaryKey']}']") . ", array('confirm'=>__('Are you sure you want to delete %s?', \${$otherSingularVar}['{$details['primaryKey']}']))); ?></li>\n";
				endif;

				// Disabled button state
				if ($hasActions == 0):
					$disabled = ' disabled';
				endif;
				echo "\t\t\t\t\t\t<td class=\"actions\">\n";
				echo $actions;
				echo "\t\t\t\t\t\t</td>\n";
			endif;
		endif;
		foreach ($details['fields'] as $field):
			$fieldContent = $this->v_prepareDisplayRelatedField($field, $details, $originalFieldList);
			echo "\t\t\t\t\t\t<td>\n{$fieldContent['displayString']}\n</td>\n";
		endforeach;
		echo "\t\t\t\t\t</tr>\n";
		echo "\t\t\t\t<?php endforeach; ?>\n";
		echo "\t\t\t\t</tbody>\n";
		echo "\t\t\t</table>\n";
		echo "\t\t<?php else: ?>\n";
		echo "\t\t<div class=\"text-info\">\n\t\t\t<?php echo " . $this->iString('No "' . strtolower($otherPluralHumanName) . '" associated to current ' . $singularVar) . "; ?>\n\t\t</div>\n";
		echo "\t\t<?php endif; ?>\n";
		echo "\t\t</div>\n";
	endif;
endforeach;
?>
</div>
<?php
/* -----------------------------------------------------------------------------
 * Additionnal scripts and CSS
 */
include $themePath . 'views/common/additionnal_js_css.ctp';

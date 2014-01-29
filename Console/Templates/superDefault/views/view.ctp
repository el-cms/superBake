<?php
/**
 * "View" view (used to display an item)
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
//Page headers and licensing
include $themePath . 'views/common/headers.ctp';


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

// Internationalized fields (for field display. If empty, selects all fields)
$languageFields = (!isset($languageFields)) ? array() : $languageFields;

/* ----------------------------------------------------------------------------
 * Prepare fields to display
 */
if (count($languageFields) > 0) {
	$diff = array();
	$internationalizedFields = array();
	foreach ($languageFields as $lf) {
		foreach ($this->Sbc->getConfig('theme.language.available') as $l) {
			$diff[] = $lf . '_' . $l;
		}
		$fields[] = $lf;
	}
	$fields = array_diff($fields, $diff);
}

/* ----------------------------------------------------------------------------
 *
 * View
 *
 * --------------------------------------------------------------------------- */


/* ----------------------------------------------------------------------------
 * Toolbar
 */
// This view represents an item:
$viewIsAnItem = true;
if ($noToolbar === false):
	include dirname(__FILE__) . DS . 'common' . DS . 'toolbar_buttons.ctp';
endif;
/* ----------------------------------------------------------------------------
 * Current record values
 */
?>

<div class="<?php echo $pluralVar; ?> view">
	<dl>
		<?php
		foreach ($fields as $field):
			if (!in_array($field, $hiddenFields)):

				// Field belongs to another model (foreign key)
				$isKey = false;
				if (!empty($associations['belongsTo'])):
					foreach ($associations['belongsTo'] as $alias => $details):
						if ($field === $details['foreignKey']):
							$isKey = true;
							echo "\t\t\t<dt><?php echo " . $this->iString(Inflector::humanize(Inflector::underscore($alias))) . "; ?></dt>\n";
							echo "\t\t\t<dd><?php echo \$this->Html->link(\${$singularVar}['{$alias}']['{$details['displayField']}'], " . $this->url('view', $details['controller'], null, "\${$singularVar}['{$alias}']['{$details['primaryKey']}']") . "); ?></dd>\n";
							break;
						endif;
					endforeach;
				endif;
				// Field is just a field
				if ($isKey !== true):
					// Field is a language field
					if (count($languageFields) > 0 && in_array($field, $languageFields)):
						$content = "((!empty(\${$singularVar}['{$modelClass}']['{$field}']))?\${$singularVar}['{$modelClass}']['{$field}']:'<i " . sTheme::v_tooltip("'." . $this->iString('This item has not been translated yet. This is the original version.') . ".'", 'fa fa-warning text-warning') . " ></i> '.\${$singularVar}['{$modelClass}']['{$field}_default'])";
					else:
						// Field is just a field
						$content = "\${$singularVar}['{$modelClass}']['{$field}']";
					endif;
					// Display the field
					echo "\t\t\t<dt><?php echo " . $this->iString(Inflector::humanize($field)) . "; ?></dt>\n";
					echo "\t\t\t<dd><?php echo $content; ?></dd>\n";
				endif;
			endif;
		endforeach;
		?>
	</dl>

	<?php
	/* ----------------------------------------------------------------------------
	 * HasOne associations
	 */
	if (!empty($associations['hasOne'])):
		foreach ($associations['hasOne'] as $alias => $details):
			?>
			<div class="related">
				<h3><?php echo "<?php echo " . $this->iString("Related " . Inflector::humanize($details['controller'])) . "; ?>"; ?></h3>
				<?php echo "<?php if (!empty(\${$singularVar}['{$alias}'])): ?>\n"; ?>
				<dl>
					<?php
					foreach ($details['fields'] as $field):
						echo "\t\t<dt><?php echo " . $this->iString(Inflector::humanize($field)) . "; ?></dt>\n";
						echo "\t\t<dd>\n\t<?php echo \${$singularVar}['{$alias}']['{$field}']; ?>\n&nbsp;</dd>\n";
					endforeach;
					?>
				</dl>
				<?php echo "<?php endif; ?>\n"; ?>
				<div class="actions">
					<ul>
						<li><?php echo "<?php echo \$this->Html->link(" . $this->iString("Edit " . Inflector::humanize(Inflector::underscore($alias))) . ", " . $this->url('edit', $details['controller'], null, "\${$singularVar}['{$alias}']['{$details['primaryKey']}']") . "); ?></li>\n"; ?>
					</ul>
				</div>
			</div>
			<?php
		endforeach;
	endif;

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
	$active = 'class="active"';
	$inline_active = ' active';
	$lis = '';
	$divs = '';
	foreach ($relations as $alias => $details):
		// CamelCasing controller name
		$ccController = Inflector::camelize($details['controller']);
		if (!in_array($ccController, $hasMany_hiddenModels)):
			$has_assoc+=1;

			$otherSingularVar = Inflector::variable($alias);
			$otherPluralHumanName = Inflector::humanize($details['controller']);
			?>
			<h3><?php echo "<?php echo __('Related " . $otherPluralHumanName . "'); ?>"; ?></h3>
			<?php echo "<?php if (!empty(\${$singularVar}['{$alias}'])): ?>\n"; ?>
			<table cellpadding = "0" cellspacing = "0">
				<tr>
					<?php
					// Fields names
					foreach ($details['fields'] as $field):
						if ((isset($hasMany_hiddenModelFields[Inflector::camelize($details['controller'])]) && !in_array($field, $hasMany_hiddenModelFields[Inflector::camelize($details['controller'])])) || !isset($hasMany_hiddenModelFields[Inflector::camelize($details['controller'])])):
							echo "\t<th><?php echo " . $this->iString(Inflector::humanize($field)) . "; ?></th>\n";
						endif;
					endforeach;

					// Action col
					if ($relatedDataHideActionsList === false && $hasMany_hideActions === false):
						echo "\t<th class=\"actions\"><?php echo __('Actions'); ?></th>\n";
					endif;
					?>
				</tr>
				<?php
				echo "\t<?php foreach (\${$singularVar}['{$alias}'] as \${$otherSingularVar}): ?>\n";
				echo "\t\t<tr>\n";
				// Fields values
				foreach ($details['fields'] as $field):
					if ((isset($hasMany_hiddenModelFields[Inflector::camelize($details['controller'])]) && !in_array($field, $hasMany_hiddenModelFields[Inflector::camelize($details['controller'])])) || !isset($hasMany_hiddenModelFields[Inflector::camelize($details['controller'])])):
						echo "\t\t\t<td><?php echo \${$otherSingularVar}['{$field}']; ?></td>\n";
					endif;
				endforeach;

				// Testing actions
				if ($relatedDataHideActionsList === false && $hasMany_hideActions === false):
					// Action buttons
					echo "\t\t\t<td class=\"actions\">\n";
					$hasActions = 0;
					$disabled = '';
					// "View" action
					if ($this->canDo('view', null, $ccController)):
						$hasActions = 1;
						echo "\t\t\t\t<?php echo \$this->Html->Link(__('View')," . $this->url('view', $details['controller'], null, "\${$otherSingularVar}['{$details['primaryKey']}']") . ", array('title'=>__('View'), 'escape'=> false));?>\n";
					endif;
					// "Edit" action
					if ($this->canDo('edit', null, $ccController)):
						$hasActions = 1;
						echo"\t\t\t\t<?php echo \$this->Html->Link(__('Edit')," . $this->url('edit', $details['controller'], null, "\${$otherSingularVar}['{$details['primaryKey']}']") . ", array('title'=>__('Edit'), 'escape'=> false));?>\n";
					endif;
					// "Delete" action
					if ($this->canDo('view', null, $ccController)):
						$hasActions = 1;
						echo "\t\t\t\t<?php echo \$this->Form->postLink(__('Delete'), " . $this->url('delete', $details['controller'], null, "\${$otherSingularVar}['{$details['primaryKey']}']") . ", array('confirm'=>__('Are you sure you want to delete %s?', \${$otherSingularVar}['{$details['primaryKey']}']), 'title'=>__('Delete'), 'escape'=>false)); ?>\n";
					endif;
					if ($hasActions === 0):
						echo "\t\t\t\t<?php echo " . $this->iString('No action.') . "; ?>";
					endif;
					echo "\t\t\t</td>\n";
				endif;

				echo "\t\t</tr>\n";

				echo "\t<?php endforeach; ?>\n";
				?>
			</table>
			<?php
			echo "<?php else:?>\n";
			echo "<div><?php echo " . $this->iString('There is no ' . strtolower(Inflector::humanize(Inflector::underscore($alias))) . " related to this " . strtolower($singularHumanName)) . ";?></div>\n";
			echo "<?php endif;?>\n";
		endif;
	endforeach;
	?>
</div>
<?php
/* -----------------------------------------------------------------------------
 * Additionnal scripts and CSS
 */
include $themePath . 'views/common/additionnal_js_css.ctp';

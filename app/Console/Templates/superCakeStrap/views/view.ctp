<?php
/**
 * "View" view (used to display an item)
 * 
 * @copyright     Copyright 2012, Manuel Tancoigne (http://experimentslabs.com)
 * @author        Manuel Tancoigne <m.tancoigne@gmail.com>
 * @link          http://experimentslabs.com Experiments Labs
 * @package       EL-CMS/superBake
 * @license       GPL v3 (http://www.gnu.org/licenses/gpl.html)
 * 
 * Variables available in this template :
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
 * - $associations (array of associated models. created by bake/superBake in model
 *   file)
 * - $plugin (Current plugin name)
 * - $action (Current action)
 * - $admin (Current routing prefix, empty for public)
 * (Only available with $this: )
 * - $this->projectConfig (superBake array, read about configuring superBake)
 * 
 * Available and useful methods :
 * ==============================
 * superBake methods in Console/AppShell.php
 * See AppShell file for more usage informations and vars.
 * - $this->url() (returns an array to use as cake URL in redirections, 
 *   $this->HTML->Link(), and wherever you want to use it)
 * - $this->display() (returns correctly __('string') or __d('plugin', 'string'))
 * - $this->actionable() (Used to make checks before creating links. Returns false
 *   if action don't exists for current prefix)
 * - $this->alowedActions() (array, current actions available for current prefix)
 * 
 * Options that can be defined in config file for this view :
 * ==========================================================
 * Current template options:
 * -------------------------
 * - hiddenFields : an array of fields names that must not be displayed, for the current controller data
 * 					i.e. : array('password', 'realName',...)
 * - hasMany_hideActions: true|false If set to true, actions columns on "has Many" associated data table will not be baked
 * - hasMany_hiddenModels: an array of model names that must not be created as related data.
 * - hasMany_hiddenModelFields: an array of model/fields that must not be created.
 * - additionnalCSS: array of CSS files to load (use '::' as directory separator)
 *   i.e.: array('path::to::css'=>'1')
 * - additionnalJS: array of JS files to load (use '::' as directory separator)
 *   i.e.: array('path::to::script'=>'1')
 * 
 * Toolbar related options:
 * ------------------------
 * - noToolbar (true|false) : if set to true, no toolbar will be created for the current view.
 * - toolbarHiddenControllers array(controller1, controller2,...) : Controllers links to hide in generation
 * 
 * 
 * @todo : test hasOne associations : must check links before display, must test display...
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
 * Toolbar options
 */
// No toolbar option
if (!isset($noToolbar)) {
	$noToolbar = false;
}

/* ----------------------------------------------------------------------------
 * Current template options
 */
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

// Toolbar : Hidden controllers are handled in the toolbar template file
if ($noToolbar === false) {
	echo "<div class=\"row toolbar\">\n";
	include(dirname(__FILE__) . DS . 'common' . DS . 'toolbar_buttons.ctp');
	echo "</div>\n";
}
/* ----------------------------------------------------------------------------
 * Current record values
 */
?>
<dl>
	<?php
	foreach ($fields as $field) {
		if (!in_array($field, $hiddenFields)) {
			$isKey = false;
			if (!empty($associations['belongsTo'])) {
				foreach ($associations['belongsTo'] as $alias => $details) {
					if ($field === $details['foreignKey']) {
						$isKey = true;
						echo "\t\t\t<dt><?php echo " . $this->display(Inflector::humanize(Inflector::underscore($alias))) . "; ?></dt>\n";
						echo "\t\t\t<dd><?php echo \$this->Html->link(\${$singularVar}['{$alias}']['{$details['displayField']}'], " . $this->url('view', $details['controller'], "\${$singularVar}['{$alias}']['{$details['primaryKey']}']") . "); ?></dd>\n";
						break;
					}
				}
			}
			if ($isKey !== true) {
				echo "\t\t\t<dt><?php echo " . $this->display(Inflector::humanize($field)) . "; ?></dt>\n";
				echo "\t\t\t<dd><?php echo \${$singularVar}['{$modelClass}']['{$field}']; ?></dd>\n";
			}
		}
	}
	?>
</dl>
<?php
/* ----------------------------------------------------------------------------
 * HasOne associations
 */
if (!empty($associations['hasOne'])) :
	foreach ($associations['hasOne'] as $alias => $details):
		?>
		<h2><?php echo "<?php echo " . $this->display("Related " . Inflector::humanize($details['controller'])) . "; ?>"; ?></h2>
		<div>
			<?php echo "<?php if (!empty(\${$singularVar}['{$alias}'])): ?>\n"; ?>
			<dl>
				<?php
				foreach ($details['fields'] as $field) {
					echo "\t\t<dt><?php echo " . $this->display(Inflector::humanize($field)) . "; ?></dt>\n";
					echo "\t\t<dd>\n\t<?php echo \${$singularVar}['{$alias}']['{$field}']; ?>\n&nbsp;</dd>\n";
				}
				?>
			</dl>
			<?php echo "<?php endif; ?>\n"; ?>
			<div class="actions">
				<ul>
					<li><?php echo "<?php echo \$this->Html->link(" . $this->display("Edit " . Inflector::humanize(Inflector::underscore($alias))) . ", " . $this->url('edit', $details['controller'], "\${$singularVar}['{$alias}']['{$details['primaryKey']}']") . "); ?></li>\n"; ?>
				</ul>
			</div>
		</div>
		<?php
	endforeach;
endif;

/* ----------------------------------------------------------------------------
 * HasMany associations
 */
if (empty($associations['hasMany'])) {
	$associations['hasMany'] = array();
}
if (empty($associations['hasAndBelongsToMany'])) {
	$associations['hasAndBelongsToMany'] = array();
}
$relations = array_merge($associations['hasMany'], $associations['hasAndBelongsToMany']);
$i = 0;

$has_assoc = 0;
$active = 'class="active"';
$inline_active = ' active';
$lis = '';
$divs = '';
foreach ($relations as $alias => $details) {
	// CamelCasing controller name
	$ccController = Inflector::camelize($details['controller']);
	if (!in_array($ccController, $hasMany_hiddenModels)) {
		//Allowed actions for this controller/prefix
		//$allowedActions = $this->allowedActions($ccController, $admin);
		$has_assoc+=1;

		$otherSingularVar = Inflector::variable($alias);
		$otherPluralHumanName = Inflector::humanize($details['controller']);
		// Tabs headers
		$lis.="\t\t\t<li $active>\n\t\t\t\t<a href=\"#tab{$details['controller']}\" data-toggle=\"tab\"><?php echo " . $this->display($otherPluralHumanName) . "; ?> <span class=\"badge\"><?php echo count(\${$singularVar}['{$alias}']); ?></span></a>\n\t\t\t</li>\n";
		// Tabs contents
		$divs.="\t\t<div class=\"tab-pane $inline_active\" id=\"tab{$details['controller']}\">\n";

		// Table data
		$divs.= "\t\t\t<?php if (!empty(\${$singularVar}['{$alias}'])): ?>\n";
		$divs.= "\t\t\t<table class=\"table table-hover table-condensed tableSection\">\n";
		$divs.= "\t\t\t\t<thead>\n";
		$divs.= "\t\t\t\t\t<tr>\n";

		// Actions Col
		if ($relatedDataHideActionsList === false) {
			if ($hasMany_hideActions == false) {
				$divs.= "\t\t\t\t\t\t<th class=\"actionsCol\"><?php echo __('Actions'); ?></th>\n";
			}
		}
		foreach ($details['fields'] as $field) {
			if (!empty($hasMany_hiddenModelFields[$details['controller']]) && !in_array($field, $hasMany_hiddenModelFields[$details['controller']])) {
				$divs.= "\t\t\t\t\t\t<th><?php echo " . $this->display(Inflector::humanize($field)) . "; ?></th>\n";
			}
		}

		$divs.= "\t\t\t\t\t</tr>\n";
		$divs.= "\t\t\t\t</thead>\n";
		$divs.= "\t\t\t\t<tbody>\n";
		$divs.= "\t\t\t\t<?php\n";
		$divs.= "\t\t\t\t\$i = 0;\n";
		$divs.= "\t\t\t\tforeach (\${$singularVar}['{$alias}'] as \${$otherSingularVar}): ?>\n";
		$divs.= "\t\t\t\t\t<tr>\n";

		// Testing actions
		if ($relatedDataHideActionsList === false) {
			if ($hasMany_hideActions == false) {
				$hasActions = 0;
				$actions = '';
				$disabled = '';
				// "View" action
				if ($this->actionable('view', $ccController)) {
					$hasActions = 1;
					$actions.= "\t\t\t\t\t\t\t\t\t<li><?php echo \$this->HTML->Link('<i class=\"icon-eye-open\"></i> ' . __('View')," . $this->url('view', $details['controller'], "\${$otherSingularVar}['{$details['primaryKey']}']") . ", array('title'=>__('View'), 'escape'=> false));?></li>\n";
				}
				// "Edit" action
				if ($this->actionable('edit', $ccController)) {
					$hasActions = 1;
					$actions.="\t\t\t\t\t\t\t\t\t<li><?php echo \$this->HTML->Link('<i class=\"icon-pencil\"></i> ' . __('Edit')," . $this->url('edit', $details['controller'], "\${$otherSingularVar}['{$details['primaryKey']}']") . ", array('title'=>__('Edit'), 'escape'=> false));?></li>\n";
				}
				// "Delete" action
				if ($this->actionable('view', $ccController)) {
					$hasActions = 1;
					$actions.= "\t\t\t\t\t\t\t\t\t<li><?php echo \$this->Form->postLink('<i class=\"icon-trash\"></i> ' .__('Delete'), " . $this->url('delete', $details['controller'], "\${$otherSingularVar}['{$details['primaryKey']}']") . ", array('confirm'=>__('Are you sure you want to delete %s?', \${$otherSingularVar}['{$details['primaryKey']}']), 'title'=>__('Delete'), 'escape'=>false)); ?></li>\n";
				}

				// Disabled button state
				if ($hasActions == 0) {
					$disabled = ' disabled';
				}
				$divs.= "\t\t\t\t\t\t<td class=\"actions\">\n";
				$divs.= "\t\t\t\t\t\t\t<div class=\"btn-group\">\n";
				$divs.= "\t\t\t\t\t\t\t\t<a class=\"btn btn-xs btn-default dropdown-toggle$disabled\" data-toggle=\"dropdown\" href=\"#\">\n";
				$divs.= "\t\t\t\t\t\t\t\t\t<i class=\"icon-cog\"></i>\n";
				$divs.= "\t\t\t\t\t\t\t\t\t<span class=\"caret\"></span>\n";
				$divs.= "\t\t\t\t\t\t\t\t</a>\n";
				$divs.= "\t\t\t\t\t\t\t\t<ul class=\"dropdown-menu\">\n";
				$divs.= $actions;
				$divs.= "\t\t\t\t\t\t\t\t</ul>\n";
				$divs.= "\t\t\t\t\t\t\t</div>\n";
				$divs.= "\t\t\t\t\t\t</td>\n";
			}
		}
		foreach ($details['fields'] as $field) {
			if ((isset($hasMany_hiddenModelFields[$details['controller']]) && !in_array($field, $hasMany_hiddenModelFields[$details['controller']]) || !isset($hasMany_hiddenModelFields[$details['controller']]))) {
				$divs.= "\t\t\t\t\t\t<td><?php echo \${$otherSingularVar}['{$field}']; ?></td>\n";
			}
		}
		$divs.= "\t\t\t\t\t</tr>\n";
		$divs.= "\t\t\t\t<?php endforeach; ?>\n";
		$divs.= "\t\t\t\t</tbody>\n";
		$divs.= "\t\t\t</table>\n";
		$divs.= "\t\t<?php else: ?>\n";
		$divs.= "\t\t<div class=\"text-info\">\n\t\t\t<?php echo " . $this->display('No "' . strtolower($otherPluralHumanName) . '" associated to current ' . $singularVar) . "; ?>\n\t\t</div>\n";
		$divs.= "\t\t<?php endif; ?>\n";
		$divs.= "\t\t</div>\n";
		$active = '';
		$inline_active = '';
	}
}
/* ----------------------------------------------------------------------------
 * Display associations
 */
if ($has_assoc > 0) {
	echo "\t<h2><?php echo __('Related data'); ?></h2>\n";
	echo "\t\t<ul class=\"nav nav-tabs\">\n";
	echo $lis;
	echo "\t\t</ul>\n";
	echo "\t<div class=\"tab-content\">\n";
	echo $divs;
	echo "\t</div>\n";
}

// Additionnal scripts and CSS
$out = '';
foreach ($additionnalCSS as $k => $v) {
	$out.= "\techo \$this->HTML->css('" . $this->cleanPath($k) . "');\n";
}
foreach ($additionnalJS as $k => $v) {
	$out.="\techo \$this->HTML->script('" . $this->cleanPath($k) . "');\n";
}
if (!empty($out)) {
	echo "<?php \n $out\n?>";
}
?>

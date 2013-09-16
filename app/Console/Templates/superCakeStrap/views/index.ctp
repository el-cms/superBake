<?php
/**
 * Index view
 * 
 * @copyright     Copyright 2012, Manuel Tancoigne (http://experimentslabs.com)
 * @author        Manuel Tancoigne <m.tancoigne@gmail.com>
 * @link          http://experimentslabs.com Experiments Labs
 * @package       EL-CMS/superBake
 * @license       GPL v3 (http://www.gnu.org/licenses/gpl.html)
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
 * Options that can be defined in config file for this view :
 * ==========================================================
 * Current template options:
 * -------------------------
 * - hiddenFields : an array of fields names that must not be displayed.
 * 					i.e. : array('password', 'realName',...)
 * - sortableFields : an array of fields that must have a sorting link.
 * 					i.e. : array('id', 'username', 'created',...)
 * - additionnalCSS: array of CSS files to load (use '::' as directory separator)
 *   i.e.: array('path::to::css'=>'1')
 * - additionnalJS: array of JS files to load (use '::' as directory separator)
 *   i.e.: array('path::to::script'=>'1')
 * - hideActionsList: boolean, hide the actions list for each entries
 * 
 * Toolbar related options:
 * ------------------------
 * - noToolbar (true|false) : if set to true, no toolbar will be created for the current view.
 * - toolbarHiddenControllers array(controller1, controller2,...) : Controllers links to hide in generation
 * 
 * ---
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
// Hidden fields
if (!isset($hiddenFields) || !is_array($hiddenFields)) {
	$hiddenFields = array();
}

// Sortable fields
if (!isset($sortableFields) || !is_array($sortableFields)) {
	$sortableFields = array();
}

// Additionnal CSS
if (!isset($additionnalCSS) || !is_array($additionnalCSS)) {
	$additionnalCSS = array();
}
// Additionnal JS
if (!isset($additionnalJS) || !is_array($additionnalJS)) {
	$additionnalJS = array();
}

// Hide actions list for entries
if (!isset($hideActionsList)) {
	$hideActionsList = false;
}

/* ----------------------------------------------------------------------------
 * Toolbar options
 */
// No toolbar option
if (!isset($noToolbar)) {
	$noToolbar = false;
}

// Toolbar : Hidden controllers are handled in the toolbar template file
if ($noToolbar === false) {
	echo "<div class=\"row toolbar\">\n";
	include(dirname(__FILE__) . DS . 'common' . DS . 'toolbar_buttons.ctp');
	echo "</div>\n";
}



/* ----------------------------------------------------------------------------
 * View
 */
?>

<table class="table table-hover table-condensed">
	<thead>
		<tr>
			<?php
			if (!$hideActionsList) {
				echo "<th><?php echo __('Actions')?></th>\n";
			}
			foreach ($fields as $field) {
				// First field, sortable with icon.
				if (!in_array($field, $hiddenFields)) {
					if (in_array($field, $sortableFields)) {
						echo "\t\t\t\t<th>\n";
						echo "\t\t\t\t\t<?php if (\$this->Paginator->sortDir() == 'desc' && \$this->Paginator->sortKey() == '$field'): ?>\n";
						echo "\t\t\t\t\t\t<i class=\"icon-sort-by-alphabet\"></i>\n";
						echo "\t\t\t\t\t<?php elseif(\$this->Paginator->sortDir() == 'asc' && \$this->Paginator->sortKey() == '$field') : ?>\n";
						echo "\t\t\t\t\t\t\t<i class=\"icon-sort-by-alphabet-alt\"></i>\n";
//						echo "\t\t\t\t\t<?php else : ? >\n";
//						echo "\t\t\t\t\t\t\t<i class=\"icon-sort-unsorted\"></i>\n";
						echo "\t\t\t\t\t<?php endif; ?>\n";
						echo "\t\t\t\t\t<?php echo \$this->Paginator->sort('{$field}', " . $this->display($field) . "); ?>\n";
						echo "\t\t\t\t</th>\n";
						//$i++;
					} else {
						echo "\t\t\t\t<th><?php echo " . $this->display($field) . "; ?></th>\n";
					}
				}
			}
			?>
		</tr>
	</thead>


	<tbody>
		<?php
		/* --------------------------------------------------------------------
		 * Actions on current item
		 */
		$item_edit = 0; // Items not editable
		$item_delete = 0; // Items non deletable
		$item_view = 0; // Items non viewable
		//Checking edit action
		if ($this->actionable('edit', $pluralVar)) {
			$item_edit = 1;
		}

		// Checking delete action
		if ($this->actionable('delete', $pluralVar)) {
			$item_delete = 1;
		}

		// Checking view action
		if ($this->actionable('view', $pluralVar)) {
			$item_view = 1;
		}

		// Adding only the delete and edit actions, as the view action is handled
		// in another column
		$item_actions = $item_delete + $item_edit;

		echo "<?php foreach (\${$pluralVar} as \${$singularVar}): ?>\n";

		echo "\t\t\t<tr>\n";
		if (!$hideActionsList) {
			echo "\t\t\t\t<td>\n";
			echo "\t\t\t\t\t<div class=\"btn-group\">\n";
			if ($item_actions > 0) { // Enabled, with submenu of actions
				echo "\t\t\t\t\t\t<a class=\"btn btn-xs btn-default dropdown-toggle\" data-toggle=\"dropdown\" href=\"#\">\n";
				echo "\t\t\t\t\t\t\t<i class=\"icon-cog\"></i>\n";
				echo "\t\t\t\t\t\t\t<span class=\"caret\"></span>\n";
				echo "\t\t\t\t\t\t</a>\n";
				echo "\t\t\t\t\t\t<ul class=\"dropdown-menu\">\n";
				if ($item_edit == 1) {
					echo "\t\t\t\t\t\t\t<li><?php echo \$this->Html->Link('<i class=\"icon-list\"></i>&nbsp;' . __('Edit'), " . $this->url('edit', null, "\${$singularVar}['{$modelClass}']['{$primaryKey}']") . ", array('escape'=> false));?></li>\n";
				}
				if ($item_delete == 1) {
					echo "\t\t\t\t\t\t\t<li><?php echo \$this->Form->postLink('<i class=\"icon-trash\"></i>&nbsp;' . __('Delete'), " . $this->url('delete', null, "\${$singularVar}['{$modelClass}']['{$primaryKey}']") . ", array('confirm'=>__('Are you sure you want to delete # %s?', \${$singularVar}['{$modelClass}']['{$primaryKey}']), 'title'=>__('Delete'), 'escape'=>false));?></li>\n";
				}
				echo "\t\t\t\t\t\t</ul>\n";
			} else { // Disabled state
				echo "\t\t\t\t\t\t<a class=\"btn btn-small btn-default\" disabled=\"disabled\" href=\"#\">\n";
				echo "\t\t\t\t\t\t\t<i class=\"icon-cog\"></i>\n";
				echo "\t\t\t\t\t\t</a>\n";
			}
		}
		echo "\t\t\t\t\t</div>\n";
		echo "\t\t\t\t</td>\n";
		/* --------------------------------------------------------------------
		 * Fields
		 */
		$i = 0; //used for creating the view link on the first element.
		foreach ($fields as $field) {
			if (!in_array($field, $hiddenFields)) {
				$isKey = false;
				/*
				 * Related controllers fields
				 */
				if (!empty($associations['belongsTo'])) {
					foreach ($associations['belongsTo'] as $alias => $details) {
						if ($field === $details['foreignKey']) {
							$isKey = true;
							echo "\t\t\t\t<td>\n";
							if ($this->actionable('view', $details['controller'])) {
								echo "\t\t\t\t\t<?php echo \$this->Html->link(\${$singularVar}['{$alias}']['{$details['displayField']}'], " . $this->url('view', $details['controller'], "\${$singularVar}['{$alias}']['{$details['primaryKey']}']") . "); ?>\n";
							} else {
								echo "\t\t\t\t\t<?php echo \${$singularVar}['{$alias}']['{$details['displayField']}']; ?>\n";
							}
							echo "\t\t\t\t</td>\n";
							break;
						}
					}
				}
				if ($isKey !== true) {
					//First element AND viewable
					if ($i == 0 && $item_view == 1) {
						echo "\t\t\t\t<td><?php echo \$this->Html->link(" . $this->display("\${$singularVar}['{$modelClass}']['{$primaryKey}']") . ", " . $this->url('view', null, "\${$singularVar}['{$modelClass}']['{$primaryKey}']") . ")?></td>\n";
						$i++;
					} else {
						echo "\t\t\t\t<td><?php echo h(\${$singularVar}['{$modelClass}']['{$field}']); ?>&nbsp;</td>\n";
					}
				}
			}
		}
		echo "\t\t\t</tr>\n";

		echo "\t\t\t<?php endforeach; ?>\n";
		?>
	</tbody>
</table>

<?php
// Pagination links, template is in views/common/
include(dirname(__FILE__) . DS . 'elements' . DS . 'pagination.ctp');

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
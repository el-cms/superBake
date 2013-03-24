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
 *   $this->HTML->Link(), and wherever you want to use it)
 * - $this->display() (returns correctly __('string') or __d('plugin', 'string'))
 * - $this->actionable() (Used to make checks before creating links. Returns false
 *   if action don't exists for current prefix)
 * - $this->alowedActions() (array, current actions available for current prefix)
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
include($themePath . 'views/common/headers.php');
?>
<div class="<?php echo $pluralVar; ?> form">
	<?php echo "<?php echo \$this->Form->create('{$modelClass}'); ?>\n"; ?>
	<fieldset>
		<legend><?php echo "<?php " . $this->display(Inflector::humanize($action) . " " . $singularHumanName) . "?>"; ?></legend>
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
			foreach ($associations['hasAndBelongsToMany'] as $assocName => $assocData) {
				echo "\t\techo \$this->Form->input('{$assocName}');\n";
			}
		}
		echo "\t?>\n";
		?>
	</fieldset>
	<?php
	echo "<?php echo \$this->Form->end(__('Submit')); ?>\n";
	?>
</div>
<div class="actions">
	<h3><?php echo "<?php echo __('Actions'); ?>"; ?></h3>
	<ul>

		<?php if (strpos($action, 'add') === false): ?>
			<?php if ($this->actionable('delete', $pluralVar)): ?>
				<li><?php echo "<?php echo \$this->Form->postLink(__('Delete'), " . $this->url('delete', $pluralVar, "\$this->Form->value('{$modelClass}.{$primaryKey}')") . ", null, " . $this->display('Are you sure you want to delete # %s?', "\$this->Form->value('{$modelClass}.{$primaryKey}')") . "); ?>"; ?></li>
			<?php endif; ?>
		<?php endif; ?>
		<?php if ($this->actionable('index', $pluralVar)): ?>
			<li><?php echo "<?php echo \$this->Html->link(" . $this->display("List " . $pluralHumanName) . ", " . $this->url('index', $pluralVar) . "); ?>"; ?></li>
		<?php endif; ?>
		<?php
		/*
		 * Related actions
		 */
		include(dirname(__FILE__).DS.'common'.DS.'related_actions.php');
		?>
	</ul>
</div>
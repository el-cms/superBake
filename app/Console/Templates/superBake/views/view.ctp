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
<div class="<?php echo $pluralVar; ?> view">
	<h2><?php echo "<?php  echo " . $this->display($singularHumanName) . " ?>"; ?></h2>
	<dl>
		<?php
		foreach ($fields as $field) {
			$isKey = false;
			if (!empty($associations['belongsTo'])) {
				foreach ($associations['belongsTo'] as $alias => $details) {
					if ($field === $details['foreignKey']) {
						$isKey = true;
						echo "\t\t<dt><?php echo " . $this->display(Inflector::humanize(Inflector::underscore($alias))) . "; ?></dt>\n";
						if ($this->actionable('view', $details['controller'])) {
							echo "\t\t<dd>\n\t\t\t<?php echo \$this->Html->link(" . $this->display("\${$singularVar}['{$alias}']['{$details['displayField']}']") . ", " . $this->url('view', $details['controller'], "\${$singularVar}['{$alias}']['{$details['primaryKey']}']") . "); ?>\n\t\t\t&nbsp;\n\t\t</dd>\n";
						} else {
							echo "\t\t<dd>\n\t\t\t<?php echo " . $this->display("\${$singularVar}['{$alias}']['{$details['displayField']}']") . "; ?>\n\t\t\t&nbsp;\n\t\t</dd>\n";
						}
						break;
					}
				}
			}
			if ($isKey !== true) {
				echo "\t\t<dt><?php echo " . $this->display(Inflector::humanize($field)) . "; ?></dt>\n";
				echo "\t\t<dd>\n\t\t\t<?php echo h(" . $this->display("\${$singularVar}['{$modelClass}']['{$field}']") . "); ?>\n\t\t\t&nbsp;\n\t\t</dd>\n";
			}
		}
		?>
	</dl>
</div>
<div class="actions">
	<h3><?php echo "<?php echo __('Actions'); ?>"; ?></h3>
	<ul>
		<?php
		$actions = 0;
		if ($this->actionable('edit', $pluralVar)) {
			echo "\t\t<li><?php echo \$this->Html->link(" . $this->display("Edit " . $singularHumanName) . ", " . $this->url('edit', null, "\${$singularVar}['{$modelClass}']['{$primaryKey}']") . "); ?> </li>\n";
			$actions = 1;
		}
		if ($this->actionable('delete', $pluralVar)) {
			echo "\t\t<li><?php echo \$this->Form->postLink(" . $this->display("Delete " . $singularHumanName) . ", " . $this->url('delete', null, "\${$singularVar}['{$modelClass}']['{$primaryKey}']") . ", null, __('Are you sure you want to delete # %s?', \${$singularVar}['{$modelClass}']['{$primaryKey}'])); ?> </li>\n";
			$actions = 1;
		}
		if ($this->actionable('index', $pluralVar)) {
			echo "\t\t<li><?php echo \$this->Html->link(" . $this->display("List " . $singularHumanName) . ", " . $this->url('index', $admin) . "); ?> </li>\n";
			$actions = 1;
		}
		if ($this->actionable('add', $pluralVar)) {
			echo "\t\t<li><?php echo \$this->Html->link(" . $this->display("New " . $singularHumanName) . ", " . $this->url('index', $admin) . "); ?> </li>\n";
			$actions = 1;
		}

		$done = array();
		foreach ($associations as $type => $data) {
			foreach ($data as $alias => $details) {
				if ($details['controller'] != $this->name && !in_array($details['controller'], $done)) {
					if ($this->actionable('index', $details['controller'])) {
						echo "\t\t<li><?php echo \$this->Html->link(" . $this->display("List " . Inflector::humanize($details['controller'])) . ", " . $this->url('index', $details['controller']) . "); ?> </li>\n";
						$actions = 1;
					}
					if ($this->actionable('add', $details['controller'])) {
						echo "\t\t<li><?php echo \$this->Html->link(" . $this->display("New " . Inflector::humanize(Inflector::underscore($alias))) . ", " . $this->url('add', $details['controller']) . "); ?> </li>\n";
						$actions = 1;
					}
					$done[] = $details['controller'];
				}
			}
		}
		if ($actions == 0) {
			echo "\t\t<li><?php echo __('Sorry, no action available.'); ?></li>\n";
		}
		?>
	</ul>
</div>
<?php
if (!empty($associations['hasOne'])) :
	foreach ($associations['hasOne'] as $alias => $details):
		?>
		<div class="related">
			<h3><?php echo "<?php echo " . $this->display("Related " . Inflector::humanize($details['controller'])) . "; ?>"; ?></h3>
			<?php echo "<?php if (!empty(\${$singularVar}['{$alias}'])): ?>\n"; ?>
			<dl>
				<?php
				foreach ($details['fields'] as $field) {
					echo "\t\t<dt><?php echo " . $this->display("" . Inflector::humanize($field)) . "; ?></dt>\n";
					echo "\t\t<dd>\n\t<?php echo \${$singularVar}['{$alias}']['{$field}']; ?>\n&nbsp;</dd>\n";
				}
				?>
			</dl>
			<?php echo "<?php endif; ?>\n"; ?>
			<div class="actions">
				<ul>
					<?php if ($this->actionable('edit', $details['controller'])): ?>
						<li><?php echo "<?php echo \$this->Html->link(" . $this->display("Edit " . Inflector::humanize(Inflector::underscore($alias))) . ", " . $this->url('edit', $details['controller'], "\${$singularVar}['{$alias}']['{$details['primaryKey']}']") . "); ?></li>\n"; ?></li>
					<?php else: ?>
						<li><?php echo "<?php echo __('Sorry, no action available.'); ?>" ?></li>
					<?php endif; ?>
				</ul>
			</div>
		</div>
		<?php
	endforeach;
endif;
if (empty($associations['hasMany'])) {
	$associations['hasMany'] = array();
}
if (empty($associations['hasAndBelongsToMany'])) {
	$associations['hasAndBelongsToMany'] = array();
}
$relations = array_merge($associations['hasMany'], $associations['hasAndBelongsToMany']);
$i = 0;
foreach ($relations as $alias => $details):
	$otherSingularVar = Inflector::variable($alias);
	$otherPluralHumanName = Inflector::humanize($details['controller']);
	?>
	<div class="related">
		<h3><?php echo "<?php echo " . $this->display("Related " . $otherPluralHumanName) . "; ?>"; ?></h3>
		<?php echo "<?php if (!empty(\${$singularVar}['{$alias}'])): ?>\n"; ?>
		<table cellpadding = "0" cellspacing = "0">
			<tr>
				<?php
				foreach ($details['fields'] as $field) {
					echo "\t\t<th><?php echo " . $this->display(Inflector::humanize($field)) . "; ?></th>\n";
				}
				?>
				<th class="actions"><?php echo "<?php echo __('Actions'); ?>"; ?></th>
			</tr>
			<?php
			echo "\t<?php
		\$i = 0;
		foreach (\${$singularVar}['{$alias}'] as \${$otherSingularVar}): ?>\n";
			echo "\t\t<tr>\n";
			foreach ($details['fields'] as $field) {
				echo "\t\t\t<td><?php echo \${$otherSingularVar}['{$field}']; ?></td>\n";
			}

			echo "\t\t\t<td class=\"actions\">\n";
			$actions = 0;
			if ($this->actionable('view', $details['controller'])) {
				$actions = 1;
				echo "\t\t\t\t<?php echo \$this->Html->link(__('View'), " . $this->url('view', $details['controller'], "\${$otherSingularVar}['{$details['primaryKey']}']") . "); ?>\n";
			}
			if ($this->actionable('edit', $details['controller'])) {
				$actions = 1;
				echo "\t\t\t\t<?php echo \$this->Html->link(__('Edit'), " . $this->url('edit', $details['controller'], "\${$otherSingularVar}['{$details['primaryKey']}']") . "); ?>\n";
			}
			if ($this->actionable('delete', $details['controller'])) {
				$actions = 1;
				echo "\t\t\t\t<?php echo \$this->Form->postLink(__('Delete')," . $this->url('delete', $details['controller'], "\${$otherSingularVar}['{$details['primaryKey']}']") . ", null, __('Are you sure you want to delete # %s?', \${$otherSingularVar}['{$details['primaryKey']}'])); ?>\n";
			}
			if ($actions == 0) {
				echo "\t\t\t\t<?php echo __('Sorry, no action available.'); ?>";
			}
			echo "\t\t\t</td>\n";
			echo "\t\t</tr>\n";

			echo "\t<?php endforeach; ?>\n";
			?>
		</table>
		<?php echo "<?php endif; ?>\n\n"; ?>
		<?php if ($this->actionable('add', $details['controller'])): ?>
			<div class="actions">
				<ul>
					<li><?php echo "<?php echo \$this->Html->link(" . $this->display("New " . Inflector::humanize(Inflector::underscore($alias))) . ", " . $this->url('add', $details['controller']) . "); ?>"; ?></li>
				</ul>
			</div>
		<?php endif; ?>
	</div>
<?php endforeach; ?>

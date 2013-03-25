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
include($themePath . 'views/common/headers.php');
?>
<div class="<?php echo $pluralVar; ?> index">
	<h2><?php echo "<?php echo " . $this->display($pluralHumanName) . "; ?>"; ?></h2>
	<h1>SIMPLE DIVS VERSION !</h1>
	<div>
		<?php echo "<?php echo __('Sort by:');?>" ?> 
		<?php foreach ($fields as $field): ?>
			<strong><?php echo "<?php echo \$this->Paginator->sort('{$field}'); ?>"; ?></strong>
		<?php endforeach; ?>
	</div>
	<hr />
	<?php
	echo "<?php foreach (\${$pluralVar} as \${$singularVar}): ?>\n";
	echo "\t<div>\n";
	foreach ($fields as $field) {
		$isKey = false;
		if (!empty($associations['belongsTo'])) {
			foreach ($associations['belongsTo'] as $alias => $details) {
				if ($field === $details['foreignKey']) {
					$isKey = true;
					if ($this->actionable('view', $details['controller'])) {
						echo "\t\t\n\t\t\t<?php echo \$this->Html->link(\${$singularVar}['{$alias}']['{$details['displayField']}'], " . $this->url('view', $details['controller'], "\${$singularVar}['{$alias}']['{$details['primaryKey']}']") . "); ?>\n\t\t\n";
					} else {
						echo "\t\t\n\t\t\t<?php echo \${$singularVar}['{$alias}']['{$details['displayField']}']; ?>\n\t\t\n";
					}
					break;
				}
			}
		}
		if ($isKey !== true) {
			echo "\t\t<?php echo h(\${$singularVar}['{$modelClass}']['{$field}']); ?>&nbsp;\n";
		}
	}
	echo "\t\t<div>\n";
	$actions = 0;
	if ($this->actionable('view', $pluralVar) == true) {
		$actions = 1;
		echo "\t\t\t<?php echo \$this->Html->link(__('View'), " . $this->url('view', $pluralVar, "\${$singularVar}['{$modelClass}']['{$primaryKey}']") . "); ?>\n";
	}
	if ($this->actionable('edit', $pluralVar)) {
		$actions = 1;
		echo "\t\t\t<?php echo \$this->Html->link(__('Edit'), " . $this->url('edit', $pluralVar, "\${$singularVar}['{$modelClass}']['{$primaryKey}']") . "); ?>\n";
	}
	if ($this->actionable('delete', $pluralVar)) {
		$actions = 1;
		echo "\t\t\t<?php echo \$this->Form->postLink(__('Delete'), " . $this->url('delete', $pluralVar, "\${$singularVar}['{$modelClass}']['{$primaryKey}']") . ", null, __('Are you sure you want to delete # %s?', \${$singularVar}['{$modelClass}']['{$primaryKey}'])); ?>\n";
	}
	if ($actions == 0) {
		echo "\t\t\t<?php echo __('Sorry, no action available');?>";
	}
	echo "\t\t</div>\n";
	echo "\t</div>\n";

	echo "<?php endforeach; ?>\n";
	?>
	<p>
		<?php echo "<?php
	echo \$this->Paginator->counter(array(
	'format' => __('Page {:page} of {:pages}, showing {:current} records out of {:count} total, starting on record {:start}, ending on {:end}')
	));
	?>"; ?>
	</p>
	<div class="paging">
		<?php
		echo "<?php\n";
		echo "\t\techo \$this->Paginator->prev('< ' . __('Previous'), array(), null, array('class' => 'prev disabled'));\n";
		echo "\t\techo \$this->Paginator->numbers(array('separator' => ''));\n";
		echo "\t\techo \$this->Paginator->next(__('Next') . ' >', array(), null, array('class' => 'next disabled'));\n";
		echo "\t?>\n";
		?>
	</div>
</div>
<div class="actions">
	<h3><?php echo "<?php echo __('Actions'); ?>"; ?></h3>
	<ul><?php $actions = 0; ?>
		<?php
		if ($this->actionable('add', $pluralVar)):
			$actions = 1;
			?>
			<li><?php echo "<?php echo \$this->Html->link(" . $this->display("New " . $singularHumanName) . ", " . $this->url('add', $pluralVar) . "); ?>"; ?></li>
			<?php
		endif;
		$done = array();
		foreach ($associations as $type => $data) {
			foreach ($data as $alias => $details) {
				if ($details['controller'] != $this->name && !in_array($details['controller'], $done)) {
					if ($this->actionable('index', $details['controller'])):
						$actions = 1;
						echo "\t\t<li><?php echo \$this->Html->link(" . $this->display("List " . Inflector::humanize($details['controller'])) . "," . $this->url('index', $details['controller']) . "); ?> </li>\n";
					endif;
					if ($this->actionable('new', $details['controller'])):
						$actions = 1;
						echo "\t\t<li><?php echo \$this->Html->link(" . $this->display("New " . Inflector::humanize(Inflector::underscore($alias))) . ", " . $this->url('add', $details['controller']) . "); ?> </li>\n";
					endif;
					$done[] = $details['controller'];
				}
				
			}
		}
		if ($actions == 0) {
			echo "\t\t<li><?php echo __('Sorry, no action available');?></li>";
		}
		?>
	</ul>
</div>

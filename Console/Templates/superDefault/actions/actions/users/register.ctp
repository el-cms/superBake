<?php
/**
 * Controllers actions template for EL-CMS baking
 *
 * This file is used during controllers generation and adds a kind of "register" action
 * to the controller.
 *
 * @copyright     Copyright 2012, Manuel Tancoigne (http://experimentslabs.com)
 * @author        Manuel Tancoigne <m.tancoigne@gmail.com>
 * @link          http://experimentslabs.com Experiments Labs
 * @license       GPL v3 (http://www.gnu.org/licenses/gpl.html)
 * @package       ELCMS.superBake.Templates.Default.Actions
 * @version       0.3
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

//$compact = array(); ?>

/**
 * <?php echo $admin.$a ?> method
 *
 * @return void
 */
public function <?php echo $admin.$a ?>() {
	<?php
	// Support for a different layout. Look at the snippet for more info.
	include $themePath . 'actions/snippets/layout_support.ctp';
	?>
	if ($this->request->is('post')) {
		$this-><?php echo $currentModelName; ?>->create();
		$this->request->data['<?php echo $currentModelName ?>']['group_id'] = 2;
		$this->request->data['<?php echo $currentModelName ?>']['status'] = 1;
		if ($this-><?php echo $currentModelName; ?>->save($this->request->data)) {
			<?php echo $this->setFlash('Your account has been successfully created. Please log in', 'success', 'login');?>
		} else {
			<?php echo $this->setFlash('Your account could not be created. Please try again', 'error', $a);?>
		}
		$this->set('title_for_layout', <?php echo $this->iString($a)?>);
	}
<?php
/**
 * Fetching associations to make them available for the view
 *
 * @todo add an option in config to hide some associations (ex: groups)
 */
foreach (array('belongsTo', 'hasAndBelongsToMany') as $assoc):
	foreach ($modelObj->{$assoc} as $associationName => $relation):
		if (!empty($associationName)):
			$otherModelName = $this->_modelName($associationName);
			$otherPluralName = $this->_pluralName($associationName);
			echo "\t\t\${$otherPluralName} = \$this->{$currentModelName}->{$otherModelName}->find('list');\n";
			$compact[] = "'{$otherPluralName}'";
		endif;
	endforeach;
endforeach;
if (!empty($compact)):
	echo "\t\t\$this->set(compact(".join(', ', $compact)."));\n";
endif;
?>
}
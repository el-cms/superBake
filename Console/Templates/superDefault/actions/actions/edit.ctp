<?php
/**
 * Controllers actions template for EL-CMS baking
 *
 * This file is used during controllers generation and adds basic "Edit" action
 * to the controllers.
 *
 * This file is an updated file from cakePHP.
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
$compact = array();
?>

	/**
	 * <?php echo $admin . $a ?> method from snippet <?php echo __FILE__ ?>
	 *
	 * @throws NotFoundException
	 * @param string $id
	 * @return void
	 */
	public function <?php echo $admin . $a; ?>($id = null) {
		<?php
		// Support for a different layout. Look at the snippet for more info.
		include $themePath . 'actions/snippets/layout_support.ctp';
		?>
		if (!$this-><?php echo $currentModelName; ?>->exists($id)) {
			throw new NotFoundException(<?php echo $this->iString('Invalid ' . strtolower($singularHumanName)) ?>);
		}
		if ($this->request->is('post') || $this->request->is('put')) {
			if ($this-><?php echo $currentModelName; ?>->save($this->request->data)) {
<?php if ($wannaUseSession): ?>
				<?php echo $this->setFlash(ucfirst(strtolower($singularHumanName)) . ' has been saved', 'success'); ?>
				$this->redirect(<?php echo $this->url('index', $controllerName) ?>);
<?php else: ?>
				$this->flash(<?php echo $this->iString('The ' . strtolower($singularHumanName) . ' has been saved.') ?>, <?php echo $this->url('index', $controllerName) ?>);
<?php endif; ?>
			} else {
<?php if ($wannaUseSession): ?>
				<?php echo $this->setFlash(ucfirst(strtolower($singularHumanName)) . ' could not be saved', 'success'); ?>
<?php endif; ?>
			}
		} else {
			$options = array('conditions' => array('<?php echo $currentModelName; ?>.' . $this-><?php echo $currentModelName; ?>->primaryKey => $id));
			$<?php echo lcfirst($currentModelName); ?>Data = $this-><?php echo $currentModelName; ?>->find('first', $options);
			$this->request->data = $<?php echo lcfirst($currentModelName); ?>Data;
		}
		$this->set('title_for_layout', <?php echo $this->iString('Edit ' . strtolower(Inflector::singularize(Inflector::humanize(Inflector::underscore($currentModelName)))) . ' %s', '$' . lcfirst($currentModelName) . "Data['$currentModelName'][\$this->${currentModelName}->" . ((!empty($modelObj->displayField)) ? 'displayField' : 'primaryKey') . "]"); ?>);
<?php
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
	echo "\t\t\$this->set(compact(" . join(', ', $compact) . "));\n";
endif;
?>
}
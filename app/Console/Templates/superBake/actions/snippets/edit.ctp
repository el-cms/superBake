<?php
/**
 * Controllers actions template for EL-CMS baking
 * 
 * This file is used during controllers generation and adds basic CRUD actions
 * to the controllers.
 * 
 * This file is an updated file from cakePHP.
 * 
 * @copyright     Copyright 2012, Manuel Tancoigne (http://experimentslabs.com)
 * @author        Manuel Tancoigne <m.tancoigne@gmail.com>
 * @link          http://experimentslabs.com Experiments Labs
 * @package       EL-CMS/Console/Controllers
 * @license       GPL v3 (http://www.gnu.org/licenses/gpl.html)
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
$compact = array(); ?>

	/**
	 * <?php echo $admin.$a ?> method
	 *
	 * @throws NotFoundException
	 * @param string $id
	 * @return void
	 */
	public function <?php echo $admin.$a; ?>($id = null) {
		if (!$this-><?php echo $currentModelName; ?>->exists($id)) {
			throw new NotFoundException(<?php echo $this->display('Invalid '. strtolower($singularHumanName))?>);
		}
		if ($this->request->is('post') || $this->request->is('put')) {
			if ($this-><?php echo $currentModelName; ?>->save($this->request->data)) {
<?php if ($wannaUseSession): ?>
				$this->Session->setFlash(<?php echo $this->display('The '. strtolower($singularHumanName) .' has been saved')?>);
				$this->redirect(<?php echo $this->url('index', $controllerName)?>);
<?php else: ?>
				$this->flash(<?php echo $this->display('The '.strtolower($singularHumanName) .' has been saved.')?>, <?php echo $this->url('index', $controllerName)?>);
<?php endif; ?>
			} else {
<?php if ($wannaUseSession): ?>
				$this->Session->setFlash(<?php echo $this->display('The '.strtolower($singularHumanName) .' could not be saved. Please, try again.')?>);
<?php endif; ?>
			}
		} else {
			$options = array('conditions' => array('<?php echo $currentModelName; ?>.' . $this-><?php echo $currentModelName; ?>->primaryKey => $id));
			$this->request->data = $this-><?php echo $currentModelName; ?>->find('first', $options);
		}
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
			echo "\t\t\$this->set(compact(".join(', ', $compact)."));\n";
		endif;
	?>
	}
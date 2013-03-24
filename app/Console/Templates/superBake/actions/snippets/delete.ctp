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
?>

	/**
	 * <?php echo $admin.$a ?> method
	 *
	 * @throws NotFoundException
	 * @throws MethodNotAllowedException
	 * @param string $id
	 * @return void
	 */
	public function <?php echo $admin.$a; ?>($id = null) {
		$this-><?php echo $currentModelName; ?>->id = $id;
		if (!$this-><?php echo $currentModelName; ?>->exists()) {
			throw new NotFoundException(<?php echo $this->display('Invalid '.strtolower($singularHumanName))?>);
		}
		$this->request->onlyAllow('post', 'delete');
		if ($this-><?php echo $currentModelName; ?>->delete()) {
<?php if ($wannaUseSession): ?>
			$this->Session->setFlash(<?php echo $this->display(ucfirst(strtolower($singularHumanName)).' deleted')?>);
			$this->redirect(<?php echo $this->url('index', $controllerName)?>);
<?php else: ?>
			$this->flash(<?php echo $this->display(ucfirst(strtolower($singularHumanName)).' deleted')?>, <?php echo $this->url('index', $controllerName)?>);
<?php endif; ?>
		}
<?php if ($wannaUseSession): ?>
		$this->Session->setFlash(<?php echo $this->display(ucfirst(strtolower($singularHumanName)).' was not deleted')?>);
<?php else: ?>
		$this->flash(<?php echo $this->display(ucfirst(strtolower($singularHumanName)).' was not deleted')?> , <?php echo $this->url('index', $controllerName)?>);
<?php endif; ?>
		$this->redirect(<?php echo $this->url('index', $controllerName)?>);
	}
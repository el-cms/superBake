<?php
/**
 * Users controller modifications for EL-CMS baking
 *
 * This file is used during UsersController generation. It adds the "login"
 * action to the controller.
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
/*
 * You don't need to set this options, only if you want to keep the default layout
 * on a prefixed action. 
 */

$strippedPrefix=  str_replace('_', '', $admin);
if(!isset($projectConfig['plugins'][(is_null($plugin))?$projectConfig['general']['appBase']:$plugin]['parts'][$currentPart]['controller']['actions'][$strippedPrefix][$a]['options']['publicLayout'])){
	$publicLayout=false;
}else{
	$publicLayout=$projectConfig['plugins'][(is_null($plugin))?$projectConfig['general']['appBase']:$plugin]['parts'][$currentPart]['controller']['actions'][$strippedPrefix][$a]['options']['publicLayout'];
}
?>

	/**
	 * This method logs an user in the ACL system
	 */
	public function <?php echo $admin.$a ?>() {

		<?php if($publicLayout==true){ echo "\$this->layout = 'default';\n";}?>
		if(is_array($this->Auth->User)){
			$this->Session->setFlash(__('You are already logged in'), 'flash_info');
			$this->Flash->redirect('/');
		}
		if ($this->request->is('post')) {
			if ($this->Auth->login()) {
				$this->Session->setFlash(<?php echo $this->display('You are now connected')?>, 'flash_success');
				$this->redirect($this->Auth->redirect());
			} else {
				$this->Session->setFlash(<?php echo $this->display('Your username or password was incorrect.')?>, 'flash_error');
			}
		}
	}
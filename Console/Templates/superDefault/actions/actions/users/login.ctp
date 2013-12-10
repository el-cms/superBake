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

//Prefix without the _
$strippedPrefix = str_replace('_', '', $admin);

//Layout
if(!isset($layout)){
	$layout=null;
}
//$layout = $this->sbc->getConfig('plugins.' . $this->sbc->pluginName($plugin) . ".parts.$currentPart.controller.actions.$strippedPrefix.$a.options.publicLayout");
?>

/**
* This method logs an user in the ACL system
*/
public function <?php echo $admin . $a ?>() {
<?php
if (!is_null($layout)) {
	echo "\$this->layout = $layout;\n";
}?>
		if(is_array($this->Auth->User)){
			<?php echo $this->setFlash('You are already logged in', 'info');?>
			$this->Flash->redirect('/');
		}
			if ($this->request->is('post')) {
			if ($this->Auth->login()) {
			<?php echo $this->setFlash('You are now connected', 'success');?>
			$this->redirect($this->Auth->redirect());
			} else {
				<?php echo $this->setFlash('Your username or password was incorrect', 'error');?>
			}
		}
	}
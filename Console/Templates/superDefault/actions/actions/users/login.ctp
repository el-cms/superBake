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
?>

/**
* This method logs an user in the ACL system
*/
public function <?php echo $admin . $a ?>() {
<?php
if (!is_null($layout)) :
	echo "\$this->layout = $layout;\n";
endif;
// theme.enableAcl should be true to enable this action.
if($this->Sbc->getConfig('theme.enableAcl')==true): ?>
		if($this->Auth->loggedIn()){
			<?php echo $this->setFlash('You are already logged in', 'info');?>
			$this->redirect('/');
		}
			if ($this->request->is('post')) {
			if ($this->Auth->login()) {
			<?php echo $this->setFlash('You are now connected', 'success');?>
			$this->redirect($this->Auth->redirect());
			} else {
				<?php echo $this->setFlash('Your username or password was incorrect', 'error');?>
			}
		}
<?php
else:
	echo $this->setFlash('Acls are not enabled, you can\\\'t use this action. To enable Acls, set the <code>theme.enableAcl</code> to true in your config file, and run superBake again.', 'error');
endif;
		?>
		$this->set('title_for_layout', <?php echo $this->iString(ucfirst(Inflector::humanize(Inflector::underscore($a))))?>);
	}
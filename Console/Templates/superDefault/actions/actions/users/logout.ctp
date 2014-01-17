<?php
/**
 * Users controller modifications for EL-CMS baking
 *
 * This file is used during UsersController generation. It adds the "logout"
 * action to the controller.
 * 
 * 'theme.enableAcl' must be true to use this method.
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
?>

	/**
	 * This methods logs an user out of the system
	 */
	public function <?php echo $admin.$a ?>() {
<?php if($this->Sbc->getConfig('theme.enableAcl')):?>
		<?php echo $this->setFlash('You are now disconnected', 'info');?>
		$this->redirect($this->Auth->logout());
<?php else:
		// 'theme.enableAcl' set to false, so the methods will display a flash
		// message and do nothing.
		echo "\n\t\t".$this->setFlash('Acls are not enabled, you can\\\'t use this action.'
				. ' To enable Acls, set the <code>theme.enableAcl</code> to true in your config file,'
				. ' and run superBake again.', 'error');
		echo "\n\t\t\$this->redirect('/');";
endif;?>
	}
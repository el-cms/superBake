<?php
/**
 * User model modifications for EL-CMS baking
 *
 * This file contains methods and vars used in the User model to make it works
 * as requester (for ACLs), hash and compare passwords.
 * 
 * @copyright     Copyright 2012, Manuel Tancoigne (http://experimentslabs.com)
 * @author        Manuel Tancoigne <m.tancoigne@gmail.com>
 * @link          http://experimentslabs.com Experiments Labs
 * @package       EL-CMS/Console/Models
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
	 * <?php echo $name?> acts as requester for ACLs
	 * @var array
	 */
	public $actsAs = array('Acl' => array('type' => 'requester'));

	/**
	 * Hash passwords before save
	 *
	 */
	public function beforeSave($options = array()) {
		App::uses('Security', 'Utility');
		App::uses('String', 'Utility');

		# empty password -> do not update
		if (empty($this->data['<?php echo $name ?>']['<?php echo $passField ?>'])) {
			unset($this->data['<?php echo $name ?>']['<?php echo $passField ?>']);
		} else {
			$this->data['<?php echo $name ?>']['<?php echo $passField ?>'] = Security::hash($this->data['<?php echo $name ?>']['<?php echo $passField ?>'], null, true);
		}
		#@todo : See things about <?php echo $name ?>.key...
		//$this->data['<?php echo $name ?>']['key'] = String::uuid();

		return true;
	}
	
	/**
	 * Validate empty passwords for users updates
	 * 
	 * @return true
	 */
	public function beforeValidate($options = array()) {
		if (isset($this->data['<?php echo $name ?>']['id'])) {
			$this->validate['<?php echo $passField ?>']['allowEmpty'] = true;
		}

		return true;
	}
	
	/**
	 * Compares passwords
	 * @param array $check
	 * @return boolean
	 */
	public function comparePwd($check) {
		$check['<?php echo $passField ?>'] = trim($check['<?php echo $passField ?>']);

		// User id not set, so we should be on an 'add' action
		if (!isset($this->data['<?php echo $name ?>']['id']) && strlen($check['<?php echo $passField ?>']) < 6) {
			return false;
		}

		// User id set, we should be on an 'update' action
		// User id set, AND
		// empty password OR empty password2 (as browsers can autofill password field)
		if (isset($this->data['<?php echo $name ?>']['id']) && (empty($check['<?php echo $passField ?>']) || empty($check['<?php echo $passCheckField ?>']))) {
			return true;
		}

		$r = ($check['<?php echo $passField ?>'] == $this->data['<?php echo $name ?>']['<?php echo $passCheckField ?>'] && strlen($check['<?php echo $passField ?>']) >= 6);

		//if (!$r) {
		//	$this->invalidate('<?php echo $passCheckField ?>', __d('user', 'Passwords missmatch.'));
		//}

		return $r;
	}
	
	
	/**
	 * Binds <?php echo $name ?> to roles model for ACL
	 */
	public function parentNode() {
        if (!$this->id && empty($this->data)) {
            return null;
        }
        if (isset($this->data['<?php echo $name ?>']['group_id'])) {
            $groupId = $this->data['<?php echo $name ?>']['group_id'];
        } else {
            $groupId = $this->field('group_id');
        }
        if (!$groupId) {
            return null;
        } else {
            return array('Group' => array('id' => $groupId));
        }
    }

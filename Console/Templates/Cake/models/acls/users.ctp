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
 * @license       GPL v3 (http://www.gnu.org/licenses/gpl.html)
 * @package       ELCMS.superBake.Templates.Default.Models
 * @version       0.3
 */

/* ----------------------------------------------------------------------------
 *
 * Options
 *
 * --------------------------------------------------------------------------*/

/* ----------------------------------------------------------------------------
 * Configuration from theme:
 */

// User status field
$userStatusField = $this->Sbc->getConfig('theme.components.Auth.userStatusField');
// User model
$userModel = $this->Sbc->getConfig('theme.components.Auth.userModel');
$userModelPK = $this->Sbc->getConfig('theme.components.Auth.userModelPK');
// Password field
$passField = $this->Sbc->getConfig('theme.components.Auth.userPassField');
// Role Model
$roleModel = $this->Sbc->getConfig('theme.components.Auth.roleModel');
// Role PK
$roleModelPK = $this->Sbc->getConfig('theme.components.Auth.roleModelPK');

/* ----------------------------------------------------------------------------
 *
 * Snippet
 *
 * --------------------------------------------------------------------------*/
?>

	/**
	 * Hash passwords before save
	 *
	 * @return void
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

		return true;
	}

	/**
	 * Validate empty passwords for users updates
	 *
	 * @return true
	 */
	public function beforeValidate($options = array()) {
		if (isset($this->data['<?php echo $name ?>']['<?php echo $userModelPK; ?>'])) {
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
		if (isset($this->data['<?php echo $name ?>']['id']) && (empty($check['<?php echo $passField ?>']) || empty($check['<?php echo $passField ?>_verify']))) {
			return true;
		}

		$r = ($check['<?php echo $passField ?>'] === $this->data['<?php echo $name ?>']['<?php echo $passField ?>_verify'] && strlen($check['<?php echo $passField ?>']) >= 6);

		//if (!$r) {
		//	$this->invalidate('<?php echo $passField ?>_verify', __d('user', 'Passwords missmatch.'));
		//}

		return $r;
	}


	/**
	 * Binds <?php echo $name ?> to <?php echo $roleModel?> model for ACL
	 */
	public function parentNode() {
        if (!$this->id && empty($this->data)) {
            return null;
        }
        if (isset($this->data['<?php echo $name ?>']['<?php echo Inflector::underscore($roleModel)."_$roleModelPK"?>'])) {
            $groupId = $this->data['<?php echo $name ?>']['<?php echo Inflector::underscore($roleModel)."_$roleModelPK"?>'];
        } else {
            $groupId = $this->field('<?php echo Inflector::underscore($roleModel)."_$roleModelPK"?>');
        }
        if (!$groupId) {
            return null;
        } else {
            return array('<?php echo $roleModel; ?>' => array('<?php echo $roleModelPK; ?>' => $groupId));
        }
    }

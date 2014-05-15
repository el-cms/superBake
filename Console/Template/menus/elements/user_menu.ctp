<?php
/**
 * Users menu that displays links for the current user.
 *
 * Options:
 * ========
 *  - isPublicMenu       bool, false*      If set to true, this is meant to be used in public menus,
 *                                         so there is a check to see if an user is logged in.
 *
 * @copyright     Copyright 2012, Manuel Tancoigne (http://experimentslabs.com)
 * @author        Manuel Tancoigne <m.tancoigne@gmail.com>
 * @link          http://experimentslabs.com Experiments Labs
 * @license       GPL v3 (http://www.gnu.org/licenses/gpl.html)
 * @package       ELCMS.superBake.Templates.default.Menus
 * @version       0.3
 */
// Users menu, if logged in
if ($isPublicMenu):
	echo "<?php if (is_array(\$this->Session->read('Auth.User'))) { ?>\n";
endif;
?>
<div class="menu-plugin">
	<div class="menu-header"><?php echo "<?php echo \$this->Session->read('Auth.User.username') ?>" ?></div>
	<ul>
		<li><?php echo "<?php echo \$this->Html->link(" . $this->iString('Admin - Dashboard') . ", " . $this->url('dashboard', 'Users', 'admin') . '); ?>' ?></li>
		<li><?php echo "<?php echo \$this->Html->link(" . $this->iString('Profile') . ", " . $this->url('profile', 'Users', 'admin', "\$this->Session->read('Auth.User.id')") . '); ?>' ?></li>
		<li><?php echo "<?php echo \$this->Html->link(" . $this->iString('Public site') . ", '/'); ?>" ?></li>
		<li><?php echo "<?php echo \$this->Html->link(" . $this->iString('Log out') . ", " . $this->url('logout', 'Users', 'public') . '); ?>' ?></li>

	</ul>
</div>
<?php
if ($isPublicMenu):
	echo "<?php\n}\n?>";
endif;
?>
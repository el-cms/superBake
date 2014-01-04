<?php
/**
 * Register view
 *
 * @copyright     Copyright 2012, Manuel Tancoigne (http://experimentslabs.com)
 * @author        Manuel Tancoigne <m.tancoigne@gmail.com>
 * @link          http://experimentslabs.com Experiments Labs
 * @license       GPL v3 (http://www.gnu.org/licenses/gpl.html)
 * @package       ELCMS.superBake.Templates.Default.Views
 * @version       0.3
 *
 * ---
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

<?php echo "<?php echo \$this->Form->create('User'); ?>"?>
<fieldset>
		<?php echo "<?php\n
		\techo \$this->Form->input('username', array('placeholder'=>__('User Name')));
		\techo \$this->Form->input('password', array('placeholder'=>__('Password')));
		\techo \$this->Form->input('password2', array('label' => __('Please confirm you password:'), 'type'=>'password', 'placeholder'=>__('Password (again)'), 'required'=>true));
		\techo \$this->Form->input('email', array('placeholder'=>__('Email')));
	\t?>"?>
</fieldset>
<?php echo "<?php echo \$this->Form->end(array('label'=>__('Submit'))); ?>"?>

<?php
/**
 * Login view
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
//Has a register part ?
if (!isset($hasRegister)) {
	$hasRegister = true;
}
?>

<div class="row">
	<?php
	if ($hasRegister === true) {
		?>
		<div class="col-lg-6">
			<h2><?php echo "<?php echo __('Register');?>" ?></h2>
		</div>
		<?php
	}
	?>
	<div class="col-lg-<?php echo ($hasRegister === true) ? '6' : '3 col-lg-push-4'; ?>">
		<div class="well well-small ">
			<?php echo "<?php echo \$this->Form->create('User', array('role'=>'form')); ?>"; ?>
			<legend><?php echo "<?php echo __('Please, sign in');?>" ?></legend>
			<div class="form-group">
			<?php echo "<?php
			\techo \$this->Form->input('email', array('placeholder' => __('User name'), 'div'=>false, 'label'=>false, 'class'=>'form-control'));
			\techo \$this->Form->input('password', array('placeholder' => __('Password'), 'div'=>false, 'label'=>false, 'class'=>'form-control'));
			?>\n";?>
			</div>
			<?php echo "<?php echo \$this->Form->end(array('label' => __('Login'))); ?>\n" ?>
		</div>
	</div>
</div>

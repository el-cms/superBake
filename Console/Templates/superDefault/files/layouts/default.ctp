<?php
/**
 * Admin layout
 * 
 * @copyright     Copyright 2012, Manuel Tancoigne (http://experimentslabs.com)
 * @author        Manuel Tancoigne <m.tancoigne@gmail.com>
 * @link          http://experimentslabs.com Experiments Labs
 * @license       GPL v3 (http://www.gnu.org/licenses/gpl.html)
 * @package       ELCMS.superBake.Templates.Default.Files
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
//Page headers and licensing

echo "<?php
\$cakeDescription = __d('cake_dev', 'CakePHP: the rapid development php framework');
?>"
?>
<!DOCTYPE html>
<html>
	<head>
		<?php echo "<?php echo \$this->Html->charset(); ?>"; ?>
		<title>
			<?php echo "<?php echo \$cakeDescription ?>"; ?>:
			<?php echo "<?php echo \$title_for_layout; ?>"; ?>
		</title>
		<?php echo "<?php
		echo \$this->Html->meta('icon');

		echo \$this->Html->css('cake.generic');
		echo \$this->Html->css('superBake');

		echo \$this->fetch('meta');
		echo \$this->fetch('css');
		echo \$this->fetch('script');
	?>" ?>
	</head>
	<body>
		<div id="container">
			<div id="header">
				<h1><?php echo "<?php echo \$this->Html->link(\$cakeDescription, 'http://cakephp.org'); ?>" ?></h1>
			</div>
			<div id="menu">
				<?php echo "<?php echo \$this->element('menus/public');?>" ?>
			</div>
			<div id="content">

				<?php echo "<?php echo \$this->Session->flash(); ?>" ?>

				<?php echo "<?php echo \$this->fetch('content'); ?>" ?>
			</div>
			<div id="footer">
				<?php echo "<?php
				echo \$this->Html->link(
				\$this->Html->image('cake.power.gif', array('alt' => \$cakeDescription, 'border' => '0')),
				'http://www.cakephp.org/',
				array('target' => '_blank', 'escape' => false)
				);
				?>" ?>
			</div>
		</div>
		<?php echo "<?php echo \$this->element('sql_dump'); ?>" ?>
	</body>
</html>

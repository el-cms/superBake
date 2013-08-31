<?php
/**
 *
 * PHP 5
 *
 * CakePHP(tm) : Rapid Development Framework (http://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 * @link          http://cakephp.org CakePHP(tm) Project
 * @package       app.View.Pages
 * @since         CakePHP(tm) v 0.10.0.1076
 * @license       http://www.opensource.org/licenses/mit-license.php MIT License
 */
?>
<div class="row">
	<div class="col-lg-6">
		<?php
		if (!Configure::read('debug')):
			throw new NotFoundException();
		endif;
		App::uses('Debugger', 'Utility');
		?>
		<h2><?php echo __d('cake_dev', 'Release Notes for CakePHP %s.', Configure::version()); ?></h2>
		<p>
			<a href="http://cakephp.org/changelogs/<?php echo Configure::version(); ?>"><?php echo __d('cake_dev', 'Read the changelog'); ?> </a>
		</p>
		<?php
		if (Configure::read('debug') > 0):
			Debugger::checkSecurityKeys();
		endif;
		?>
		<div id="url-rewriting-warning" class="text-danger">
			<i class="icon-remove"></i> <strong><?php echo __d('cake_dev', 'URL rewriting is not properly configured on your server.'); ?><br /></strong>
			<ol>
				<li><a target="_blank" href="http://book.cakephp.org/2.0/en/installation/url-rewriting.html">Help me configure it</a></li>
				<li><a target="_blank" href="http://book.cakephp.org/2.0/en/development/configuration.html#cakephp-core-configuration">I don't / can't use URL rewriting</a></li>
			</ol>
		</div>
		<?php
		/* -------------
		 * PHP version
		 */
		if (version_compare(PHP_VERSION, '5.2.8', '>=')):
			echo '<p class="text-success">';
			echo '<i class="icon-ok"></i> ' . __d('cake_dev', 'Your version of PHP is 5.2.8 or higher.');
			echo '</p>';
		else:
			echo '<p class="text-warning">';
			echo '<i class="icon-warning-sign"></i> ' . __d('cake_dev', 'Your version of PHP is too low. You need PHP 5.2.8 or higher to use CakePHP.');
			echo '</p>';
		endif;

		/* -------------
		 * Temp dir test
		 */
		if (is_writable(TMP)):
			echo '<p class="text-success">';
			echo '<i class="icon-ok"></i> ' . __d('cake_dev', 'Your tmp directory is writable.');
			echo '</p>';
		else:
			echo '<p class="text-warning">';
			echo '<i class="icon-warning-sign"></i> ' . __d('cake_dev', 'Your tmp directory is NOT writable.');
			echo '</p>';
		endif;

		/* --------------
		 * Cache setings
		 */
		$settings = Cache::settings();
		if (!empty($settings)):
			echo '<p class="text-success">';
			echo '<i class="icon-ok"></i> ' . __d('cake_dev', 'The %s is being used for core caching. To change the config edit APP/Config/core.php ', '<em>' . $settings['engine'] . 'Engine</em>');
			echo '</p>';
		else:
			echo '<p class="text-warning">';
			echo '<i class="icon-warning-sign"></i> ' . __d('cake_dev', 'Your cache is NOT working. Please check the settings in APP/Config/core.php');
			echo '</p>';
		endif;

		/* ----------------
		 * DB config
		 */
		$filePresent = null;
		if (file_exists(APP . 'Config' . DS . 'database.php')):
			echo '<p class="text-success">';
			echo '<i class="icon-ok"></i> ' . __d('cake_dev', 'Your database configuration file is present.');
			$filePresent = true;
			echo '</p>';
		else:
			echo '<p class="text-warning">';
			echo '<i class="icon-warning-sign"></i> ' . __d('cake_dev', 'Your database configuration file is NOT present.');
			echo '<br/>';
			echo __d('cake_dev', 'Rename APP/Config/database.php.default to APP/Config/database.php');
			echo '</p>';
		endif;
		?>
		<?php
		if (isset($filePresent)):
			App::uses('ConnectionManager', 'Model');
			try {
				$connected = ConnectionManager::getDataSource('default');
			} catch (Exception $connectionError) {
				$connected = false;
				$errorMsg = $connectionError->getMessage();
				if (method_exists($connectionError, 'getAttributes')) {
					$attributes = $connectionError->getAttributes();
					if (isset($errorMsg['message'])) {
						$errorMsg .= '<br />' . $attributes['message'];
					}
				}
			}
			?>
			<?php
			if ($connected && $connected->isConnected()):
				echo '<p class="text-success">';
				echo '<i class="icon-ok"></i> ' . __d('cake_dev', 'Cake is able to connect to the database.');
				echo '</p>';
			else:
				echo '<p class="text-warning">';
				echo '<i class="icon-warning-sign"></i> ' . __d('cake_dev', 'Cake is NOT able to connect to the database.');
				echo '<br /><br />';
				echo $errorMsg;
				echo '</p>';
			endif;
			?>
		<?php endif; ?>
		<?php
		App::uses('Validation', 'Utility');
		if (!Validation::alphaNumeric('cakephp')) {
			echo '<p class="text-warning">';
			echo '<i class="icon-warning-sign"></i> ' . __d('cake_dev', 'PCRE has not been compiled with Unicode support.');
			echo '<br/>';
			echo __d('cake_dev', 'Recompile PCRE with Unicode support by adding <code>--enable-unicode-properties</code> when configuring');
			echo '</p>';
		}
		?>

		<p>
			<?php
			if (CakePlugin::loaded('DebugKit')):
				echo '<p class="text-success">';
				echo '<i class="icon-ok"></i> ' . __d('cake_dev', 'DebugKit plugin is present');
				echo '</p>';
			else:
				echo '<p class="text-warning">';
				echo '<i class="icon-warning-sign"></i> ' . __d('cake_dev', 'DebugKit is not installed. It will help you inspect and debug different aspects of your application.');
				echo __d('cake_dev', 'You can install it from %s', $this->Html->link('github', 'https://github.com/cakephp/debug_kit'));
				echo '</p>';
			endif;
			?>
		</p>

		<h3><?php echo __d('cake_dev', 'Editing this Page'); ?></h3>
		<p>
			<?php
			echo __d('cake_dev', 'To change the content of this page, edit: APP/View/Pages/home.ctp.<br />
To change its layout, edit: APP/View/Layouts/default.ctp.<br />
You can also add some CSS styles for your pages at: APP/webroot/css.');
			?>
		</p>

		<h3><?php echo __d('cake_dev', 'Getting Started'); ?></h3>
		<p>
			<?php
			echo $this->Html->link(
					sprintf('<strong>%s</strong> %s', __d('cake_dev', 'New'), __d('cake_dev', 'CakePHP 2.0 Docs')), 'http://book.cakephp.org/2.0/en/', array('target' => '_blank', 'escape' => false)
			);
			?>
		</p>
		<p>
			<?php
			echo $this->Html->link(
					__d('cake_dev', 'The 15 min Blog Tutorial'), 'http://book.cakephp.org/2.0/en/tutorials-and-examples/blog/blog.html', array('target' => '_blank', 'escape' => false)
			);
			?>
		</p>

		<h3><?php echo __d('cake_dev', 'Official Plugins'); ?></h3>
		<p>
		<ul>
			<li>
				<?php echo $this->Html->link('DebugKit', 'https://github.com/cakephp/debug_kit') ?>:
				<?php echo __d('cake_dev', 'provides a debugging toolbar and enhanced debugging tools for CakePHP applications.'); ?>
			</li>
			<li>
				<?php echo $this->Html->link('Localized', 'https://github.com/cakephp/localized') ?>:
				<?php echo __d('cake_dev', 'contains various localized validation classes and translations for specific countries'); ?>
			</li>
		</ul>
		</p>

		<h3><?php echo __d('cake_dev', 'More about Cake'); ?></h3>
		<p>
			<?php echo __d('cake_dev', 'CakePHP is a rapid development framework for PHP which uses commonly known design patterns like Active Record, Association Data Mapping, Front Controller and MVC.'); ?>
		</p>
		<p>
			<?php echo __d('cake_dev', 'Our primary goal is to provide a structured framework that enables PHP users at all levels to rapidly develop robust web applications, without any loss to flexibility.'); ?>
		</p>

		<dl class="dl-horizontal">
			<dt><a href="http://cakefoundation.org/"><?php echo __d('cake_dev', 'Cake Software Foundation'); ?></a></dt>
			<dd><?php echo __d('cake_dev', 'Promoting development related to CakePHP'); ?></dd>
			<dt><a href="http://www.cakephp.org"><?php echo __d('cake_dev', 'CakePHP'); ?> </a></dt>
			<dd><?php echo __d('cake_dev', 'The Rapid Development Framework'); ?></dd>
			<dt><a href="http://book.cakephp.org"><?php echo __d('cake_dev', 'CakePHP Documentation'); ?> </a></dt>
			<dd><?php echo __d('cake_dev', 'Your Rapid Development Cookbook'); ?></dd>
			<dt><a href="http://api.cakephp.org/"><?php echo __d('cake_dev', 'CakePHP API'); ?> </a></dt>
			<dd><?php echo __d('cake_dev', 'Quick Reference'); ?></dd>
			<dt><a href="http://bakery.cakephp.org"><?php echo __d('cake_dev', 'The Bakery'); ?> </a></dt>
			<dd><?php echo __d('cake_dev', 'Everything CakePHP'); ?></dd>
			<dt><a href="http://plugins.cakephp.org"><?php echo __d('cake_dev', 'CakePHP plugins repo'); ?> </a></dt>
			<dd><?php echo __d('cake_dev', 'A comprehensive list of all CakePHP plugins created by the community'); ?></dd>
			<dt><a href="https://groups.google.com/group/cake-php"><?php echo __d('cake_dev', 'CakePHP Google Group'); ?> </a></dt>
			<dd><?php echo __d('cake_dev', 'Community mailing list'); ?></dd>
			<dt><a href="irc://irc.freenode.net/cakephp">irc.freenode.net #cakephp</a></dt>
			<dd><?php echo __d('cake_dev', 'Live chat about CakePHP'); ?></dd>
			<dt><a href="https://github.com/cakephp/"><?php echo __d('cake_dev', 'CakePHP Code'); ?> </a></dt>
			<dd><?php echo __d('cake_dev', 'For the Development of CakePHP Git repository, Downloads'); ?></dd>
			<dt><a href="https://cakephp.lighthouseapp.com/"><?php echo __d('cake_dev', 'CakePHP Lighthouse'); ?> </a></dt>
			<dd><?php echo __d('cake_dev', 'CakePHP Tickets, Wiki pages, Roadmap'); ?></dd>
		</dl>
	</div>
	<div class="col-lg-6">
		<h2>superBake help:</h2>
		<?php include(CAKE.'..'.DS.'..'.DS.'docs'.DS.'pages'.DS.'start.ctp');?>
	</div>
</div>
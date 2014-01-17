<?php
/**
 * Headers for generated files (menus, required,...)
 *
 * This file must be included once in all of your template files, as it defines
 * some vars used in the process (and creates the copyright header)
 *
 * @copyright     Copyright 2012, Manuel Tancoigne (http://experimentslabs.com)
 * @author        Manuel Tancoigne <m.tancoigne@gmail.com>
 * @link          http://experimentslabs.com Experiments Labs
 * @license       GPL v3 (http://www.gnu.org/licenses/gpl.html)
 * @package       ELCMS.superBake.Templates.Default.Views
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


$com_plugin = '';
if (!empty($plugin)) {
	$com_plugin = $plugin . '/';
}
echo "<?php\n";
?>

/**
* File generated on <?php echo date('Y-m-d H:i:s'); ?> by superBake.
*
* @copyright     Copyright 2012-<?php echo date('Y') ?>, <?php echo $this->Sbc->getConfig('general.editorName') ?> (<?php echo $this->Sbc->getConfig('general.editorWebsite') ?>)
* @author        <?php echo $this->Sbc->getConfig('general.editorName') ?> <<?php echo $this->Sbc->getConfig('general.editorEmail')?>><?php echo "\n";?>
* @link          <?php echo $this->Sbc->getConfig('general.editorWebsite') ?> <?php echo $this->Sbc->getConfig('general.editorWebsiteName') . "\n" ?>
* @package       <?php echo $this->Sbc->getConfig('general.basePackage') ?>/<?php echo $plugin . "\n" ?>
*
<?php
$licenseTemplate = dirname(dirname(dirname(__FILE__))) . DS . 'common' . DS . 'licenses' . DS . $this->Sbc->getConfig('general.editorLicenseTemplate') . '.ctp';
if (file_exists($licenseTemplate)):
	include $licenseTemplate;
	echo "\n";
endif;
?>
*/

<?php echo "?>\n\n"; ?>

<?php
/**
 * PHP file for EL-CMS
 * 
 * This file mus be included once in all of your template views, as it defines
 * some vars used in the process (and creates the copyright header)
 * 
 * @copyright     Copyright 2012, Manuel Tancoigne (http://experimentslabs.com)
 * @author        Manuel Tancoigne <m.tancoigne@gmail.com>
 * @link          http://experimentslabs.com Experiments Labs
 * @package       EL-CMS/superBake
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

$com_plugin='';
if(!empty($plugin)){$com_plugin=$plugin.'/';}
echo "<?php\n";
?>

 /**
 * app/<?php echo $com_plugin . 'View/'.$pluralVar.'/' . $action ?>.php
 * File generated on <?php echo date('Y-m-d H:i:s'); ?> by superBake with template "<?php echo stripslashes(str_replace(dirname($themePath), '', $themePath)); ?>".
 *
 * This file contains the <?php echo $action ?> view for <?php echo $pluralVar?> Controller.
 * 
 * @copyright     Copyright 2012-<?php echo date('Y') ?>, <?php echo $projectConfig['editorName'] ?> (<?php echo $projectConfig['editorWebsite'] ?>)
 * @author        <?php echo $projectConfig['editorName'] ?> <?php echo $projectConfig['editorEmail'] . "\n" ?>
 * @link          <?php echo $projectConfig['editorWebsite'] ?> <?php echo $projectConfig['editorWebsiteName'] . "\n" ?>
 * @package       <?php echo $projectConfig['basePackage'] ?>/<?php echo $plugin . "\n" ?>
 *
<?php
$licenseTemplate = dirname(dirname(dirname(__FILE__))) . DS . 'common' . DS . 'licenses' . DS . $projectConfig['editorLicenseTemplate'] . '.ctp';
if (file_exists($licenseTemplate)) {
	include($licenseTemplate);
	echo "\n";
}?>
 */

<?php echo "?>\n\n";?>

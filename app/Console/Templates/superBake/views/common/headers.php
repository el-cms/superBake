<?php
/**
 * PHP file for EL-CMS
 * 
 * This file should be included once in all of your template views, as it creates the copyright
 * header for the generated files.
 * 
 * Feel free to adapt this template to your own app with your own license. I mean
 * not this header, but the header below :)
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

// Looking if in plugin to write in comments.
$com_plugin='';
if(!empty($plugin)){$com_plugin=$plugin.'/';}
echo "<?php\n";
?>
/**
 * app/<?php echo "${com_plugin}View/${pluralHumanName}/$action" ?>.ctp 
 *
 * This file contains the "<?php echo $action ?>" layout for "<?php echo $action ?>()" action
 * of the "<?php echo $pluralHumanName ?>" controller.
 * 
 * @copyright     Copyright 2012, Your Name (http://yourwebsite.com)
 * @author        Your Name <your@e.mail>
 * @link          http://yourwebsite.com yourWebsiteName
 * @package       <YOURAPP>/<?php echo $plugin ?>
 *
 * @license       License Name (Link)
 *
 * ----
 * 
 *  Here comes your license description
 */
<?php echo "?>\n\n";?>

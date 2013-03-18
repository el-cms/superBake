<?php

/**
 * PHP file for EL-CMS
 * 
 * This file should be included in any template that needs a "related controllers actions" part.
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
$done = array();
foreach ($associations as $type => $data) {
	foreach ($data as $alias => $details) {
		if ($details['controller'] != $this->name && !in_array($details['controller'], $done)) {
			$actions = 0;
			if ($this->actionable('index', $details['controller'])):
				$actions = 1;
				echo "\t\t<li><?php echo \$this->Html->link(" . $this->display("List " . Inflector::humanize($details['controller'])) . ", " . $this->url('index', $details['controller']) . "); ?> </li>\n";
			endif;
			if ($this->actionable('add', $details['controller'])):
				$actions = 1;
				echo "\t\t<li><?php echo \$this->Html->link(" . $this->display("New " . Inflector::humanize(Inflector::underscore($alias))) . ", " . $this->url('add', $details['controller']) . "); ?> </li>\n";
			endif;
			if ($actions == 0) {
				echo "<?php __('Sorry, no action available.'); ?>";
			}
			$done[] = $details['controller'];
		}
	}
}
?>
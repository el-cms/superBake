<?php
/**
 * Controllers actions template for EL-CMS baking
 * 
 * This file is used during controllers generation and adds basic "index" action
 * to the controllers.
 * 
 * This file is an updated file from cakePHP.
 * 
 * @copyright     Copyright 2012, Manuel Tancoigne (http://experimentslabs.com)
 * @author        Manuel Tancoigne <m.tancoigne@gmail.com>
 * @link          http://experimentslabs.com Experiments Labs
 * @package       EL-CMS/Console/Controllers
 * @license       GPL v3 (http://www.gnu.org/licenses/gpl.html)
 * 
 * ----
 * 
 * Options that you can use in config file:
 * ========================================
 * These options are available through a 
 * 
 * Current template options:
 * -------------------------
 * - defaultSortBy: string Name of the field the view will be sorted by. Default: null
 * - defaultSortOrder: string (asc/desc) Default sorting order: default: asc
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
/* ----------------------------------------------------------------------------
 * Current template options
 */
// Default sorting field
$defaultSortBy = (!isset($options['defaultSortBy'])) ? null : $options['defaultSortBy'];

// Default sorting order
$defaultSortOrder = (!isset($options['defaultSortOrder'])) ? 'desc' : $options['defaultSortOrder'];

// Default recursive find
$recursiveDepth = (!isset($options['recursiveDepth'])) ? 0 : $options['recursiveDepth'];
?>

	/**
	 * <?php echo $admin.$a ?> method from snippet <?php echo __FILE__ ?>
	 *
	 * Basic index action
	 *
	 * @return void
	 */
	public function <?php echo $admin.$a ?>() {
		$this-><?php echo $currentModelName ?>->recursive = <?php echo $recursiveDepth?>;
		<?php if (!is_null($defaultSortBy)): ?>
		$this->paginate=array('order'=>array('<?php echo $defaultSortBy ?>'=> '<?php echo $defaultSortOrder ?>'));
		<?php endif; ?>
		$this->set('<?php echo $pluralName ?>', $this->paginate());
		$this->set('title_for_layout', '<?php echo ucfirst(Inflector::pluralize(Inflector::humanize(Inflector::underscore($currentModelName)))) ?>');
	}
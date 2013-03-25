<?php
/**
 * "Missing model" snippet. This in included during model generation when a 
 * snippet is not found. It contains comments on the missing snippet, and a todo statement.
 * 
 * @copyright     Copyright 2012, Manuel Tancoigne (http://experimentslabs.com)
 * @author        Manuel Tancoigne <m.tancoigne@gmail.com>
 * @link          http://experimentslabs.com Experiments Labs
 * @package       EL-CMS/Console/Models
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
 ?>

/**
 * <?php echo __d('superBake', 'A model snippet is missing.')."\n"?>
 * <?php echo __d('superBake', 'It was referenced as %s in config file, and should have been created in file',$v)."\n"?>
 * "<?php echo $additionnalCode ?>".
 *
 * <?php echo __d('superBake', 'Please do something with this.')."\n"?>
 * 
 * @todo <?php echo __d('superBake', 'Write the superBake model snippet for %s, in %s', array($v, $additionnalCode))."\n"?>
 */
 
 
<?php
/**
 * Users controller modifications for EL-CMS baking
 *
 * This file is used during UsersController generation. It adds an empty action
 * to the app, and a todo reminder in comments.
 * 
 * You can safely edit this template, but don't remove it as it's needed by superBake
 * 
 * @copyright     Copyright 2012, Manuel Tancoigne (http://experimentslabs.com)
 * @author        Manuel Tancoigne <m.tancoigne@gmail.com>
 * @link          http://experimentslabs.com Experiments Labs
 * @license       GPL v3 (http://www.gnu.org/licenses/gpl.html)
 * @package       ELCMS.superBake.Templates.Default.Actions
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
?>

	/**
	 * <?php echo $admin.$a?>() is not implemented
	 *
	 * @todo implement <?php echo $a?>() and if possible, make the "<?php echo $snippetFile?>" snippet of this
	 * and share it with the rest of the world.
	 */
	public function <?php echo $admin.$a?>() {
		throw new NotFoundException(__('"%s()" is not yet implemented (snippet should be here: "%s").', array(<?php echo "'$a', '$snippetFile'"?>)));
	}
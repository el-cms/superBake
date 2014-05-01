<?php
/**
 * Empty action in replacement of non-existing templates.
 *
 * You can safely edit this template, but don't remove it as it's needed by superBake
 *
 * @copyright     Copyright 2012, Manuel Tancoigne (http://experimentslabs.com)
 * @author        Manuel Tancoigne <m.tancoigne@gmail.com>
 * @link          http://experimentslabs.com Experiments Labs
 * @license       GPL v3 (http://www.gnu.org/licenses/gpl.html)
 * @package       ELCMS.superBake.Templates.Default.Actions
 * @version       0.3
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
<?php
/**
 * "Missing model" snippet. This in included during model generation when a 
 * snippet is not found. It contains comments on the missing snippet, and a todo statement.
 * 
 * @copyright     Copyright 2012, Manuel Tancoigne (http://experimentslabs.com)
 * @author        Manuel Tancoigne <m.tancoigne@gmail.com>
 * @link          http://experimentslabs.com Experiments Labs
 * @license       GPL v3 (http://www.gnu.org/licenses/gpl.html)
 * @package       ELCMS.superBake.Templates.Default.Models
 * @version       0.3
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
 
 
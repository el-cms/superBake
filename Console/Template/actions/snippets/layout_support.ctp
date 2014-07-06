<?php
/**
 * Snippet for actions templates.
 *
 * This snippet adds a layout support for actions and should be included in actions
 * templates that uses different layouts.
 *
 * @copyright     Copyright 2012, Manuel Tancoigne (http://experimentslabs.com)
 * @author        Manuel Tancoigne <m.tancoigne@gmail.com>
 * @link          http://experimentslabs.com Experiments Labs
 * @license       GPL v3 (http://www.gnu.org/licenses/gpl.html)
 * @package       ELCMS.superBake.Templates.Default.Actions.Snippets
 * @version       0.3
 *
 */
/* ----------------------------------------------------------------------------
 *
 * Options
 *
 * --------------------------------------------------------------------------*/
// Layout used by current action.
if(!isset($layout)){
	$layout=null;
}

/* ----------------------------------------------------------------------------
 *
 * Snippet
 *
 * --------------------------------------------------------------------------*/

// If no layout is specified in config file for current action, no specific layout declaration will be made.
if (!is_null($layout)) :
	echo "\$this->layout = '$layout';\n";
endif;
?>
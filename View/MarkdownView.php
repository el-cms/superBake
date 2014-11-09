<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * Markdown View
 *
 * Convert Markdown views into HTML on the fly
 *
 * PHP 5
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the below copyright notice.
 *
 * @author    Simon Males <sime@sime.net.au>
 * @copyright 2012 Simon Males (http://sime.net.au)
 * @license   MIT License (http://www.opensource.org/licenses/mit-license.php)
 * @since     CakePHP(tm) v 2.2
 */
App::uses('View', 'View');
//App::import('Markdown', 'Sb.Markdown');
App::import('Markdown', 'Sb.MarkdownExtended');

class MarkdownView extends View {

	/**
	 * Constructor - Initialize Markdown parser.
	 *
	 * @param Controller $controller A controller object to pull View::_passedVars from.
	 */
	public function __construct(Controller $controller = null) {
		parent::__construct($controller);
		$this->Markdown = new MarkdownExtraExtended_Parser();
	}

	/**
	 * Renders view for given view file and layout. With a touch of Markdown magic
	 *
	 * Render triggers helper callbacks, which are fired before and after the view are rendered,
	 * as well as before and after the layout.  The helper callbacks are called:
	 *
	 * - `beforeRender`
	 * - `afterRender`
	 * - `beforeLayout`
	 * - `afterLayout`
	 *
	 * If View::$autoRender is false and no `$layout` is provided, the view will be returned bare.
	 *
	 * View and layout names can point to plugin views/layouts.  Using the `Plugin.view` syntax
	 * a plugin view/layout can be used instead of the app ones.  If the chosen plugin is not found
	 * the view will be located along the regular view path cascade.
	 *
	 * @param string $view   Name of view file to use
	 * @param string $layout Layout to use.
	 *
	 * @return string Rendered Element
	 * @throws CakeException if there is an error in the view.
	 */
	public function render($view = null, $layout = null) {

		if ($this->hasRendered) {
			return true;
		}
		$this->Blocks->set('content', '');
		// Small hack to allow different pathes:
		$base = explode('::', $view);
		if (count($base) > 1) {
			// We have an additionnal path :)
			$viewFileName = str_replace('::', DS, $view);
		} else {
			$viewFileName = $this->_getViewFileName($view);
		}

		if ($view !== false && $viewFileName) {
			$this->_currentType = self::TYPE_VIEW;
			$this->getEventManager()->dispatch(new CakeEvent('View.beforeRender', $this, array($viewFileName)));
			$content = $this->_render($viewFileName);

			// Transform Markdown into HTML
			$this->Blocks->set('content', $this->Markdown->transform($content));
			$this->getEventManager()->dispatch(new CakeEvent('View.afterRender', $this, array($viewFileName)));
		}
		// Use the correct view/layout
		// Check current layout
		if ($layout === null) {
			$layout = $this->layout;
		}
		if ($layout && $this->autoLayout) {
			//Render in view first
			if (!Configure::read('Sb.Croogo')) {
				$this->Blocks->set('content', $this->renderLayout('', 'doc'));
			} 
			$this->Blocks->set('content', $this->renderLayout('', $layout));
		}
		$this->hasRendered = true;
		return $this->Blocks->get('content');
	}

}

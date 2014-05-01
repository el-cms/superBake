<?php

class DocsController extends SbAppController {

	public $uses = array();

	/**
	 * Displays a help file formatted in markdown.
	 * Original method from CakePHP's PagesController file.
	 *
	 * @param mixed What page to display
	 * @return void
	 * @throws NotFoundException When the view file could not be found
	 * 	or MissingViewException in debug mode.
	 */
	public function display() {
		$this->viewClass = 'Sb.Markdown';
		$this->layout = 'doc';
		$path = func_get_args();

		$docs=null;
		if(!empty($this->request->params['named']['docs'])){
			$docs = $this->request->params['named']['docs'];
		}

		$base = null;
		// If you want to add other pathes, don't forget to add a trailing '::'
		switch ($docs) {
			case 'template':
				$base = CakePlugin::path('Sb').'Console/Template/docs::';
				break;
			default:
				$base = CakePlugin::path('Sb').'Docs::';
				break;
		}


		$count = count($path);
		if (!$count) {
			return $this->redirect('/');
		}
		$page = $subpage = $title_for_layout = null;

		if (!empty($path[0])) {
			$page = $path[0];
		}
		if (!empty($path[1])) {
			$subpage = $path[1];
		}
		if (!empty($path[$count - 1])) {
			$title_for_layout = Inflector::humanize($path[$count - 1]);
		}
		$this->set(compact('page', 'subpage', 'title_for_layout'));

		try {
			$this->render($base.implode('/', $path), null);
		} catch (MissingViewException $e) {
			if (Configure::read('debug')) {
				throw $e;
			}
			throw new NotFoundException();
		}
	}

}

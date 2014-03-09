<?php

/**
 * This class contains methods for graphical elements creation.
 *
 * Methods beginning with :
 * 		v_ are for views
 * 		c_ are for controllers
 * 		m_ are for models
 *
 * @todo Find a way to access to the Sbc object
 *
 * @copyright     Copyright 2012, Manuel Tancoigne (http://experimentslabs.com)
 * @author        Manuel Tancoigne <m.tancoigne@gmail.com>
 * @link          http://experimentslabs.com Experiments Labs
 * @license       GPL v3 (http://www.gnu.org/licenses/gpl.html)
 * @package       ELCMS.superBake.Templates.Default
 * @version       0.3
 */
class sTheme {

	/**
	 * Returns some infos on the current theme
	 *
	 * @return string Some theme info. Use it where you want.
	 */
	public static function themeInfos() {
		return('Default theme.');
	}

	/* **************************************************************************
	 *
	 * Methods for views
	 *
	 * *********************************************************************** */

	/**
	 * Creates an alert div with given class and content
	 *
	 * @param string $content
	 * @param string $class
	 * @return string
	 */
	public static function v_alert($content, $class) {
		return "<div class=\"$class\">\n$content\n</div>";
	}

	/**
	 * Creates a new buttons group
	 *
	 * @param string $title Group name
	 * @param array $content Array of toolbar elements
	 *
	 * @return string String to add in the HTML
	 */
	public static function v_newBtGroup($content) {
		$toolbar = "\t<ul>\n";
		foreach ($content as $item) {
			$toolbar.= "\t\t<li>" . $item . "</li>\n";
		}
		$toolbar .= "\t</ul>\n";
		return $toolbar;
	}

	/* *************************************************************************
	 *
	 * Methods for controllers
	 *
	 * *********************************************************************** */

	/**
	 * outputs a condition for a find/paginate call by replacing vars by actual code.
	 *
	 * @param string $condition The condition string
	 *
	 * @return string replacement string or $condition if nothing is found.
	 */
	public static function c_indexConditions($condition) {
		switch ($condition) {
			case '%now%':
				return 'date("Y-m-d H:i:s")';
				break;
			case '%self%':
				return "\$this->Session->read('Auth.User.id')";
			default:
				return "'$condition'";
				break;
		}
	}

	/**
	 * Parses a given byte count.
	 *
	 * Function from Drupal, found here : https://api.drupal.org/api/drupal/includes!common.inc/function/parse_size/6
	 *
	 * @param string A size expressed as a number of bytes with optional SI or IEC binary unit prefix (e.g. 2, 3K, 5MB, 10G, 6GiB, 8 bytes, 9mbytes).
	 *
	 * @return integer Representation of the size, in bytes
	 */
	public function c_parseSize($size) {
		$suffixes = array(
			'' => 1,
			'k' => 1024,
			'm' => 1048576, // 1024 * 1024
			'g' => 1073741824, // 1024 * 1024 * 1024
		);
		if (preg_match('/([0-9]+)\s*(k|m|g)?(b?(ytes?)?)/i', $size, $match)) {
			return $match[1] * $suffixes[strtolower($match[2])];
		}
	}

	/**
	 * Determine the maximum file upload size by querying the PHP settings.
	 *
	 * Function from Drupal, found here : https://api.drupal.org/api/drupal/includes!file.inc/function/file_upload_max_size/6
	 *
	 * @staticvar integer $max_size
	 * @return integer A file size limit in bytes based on the PHP upload_max_filesize and post_max_size
	 */
	public function c_getFileUploadMaxSize() {
		static $max_size = -1;

		if ($max_size < 0) {
			$upload_max = parseSize(ini_get('upload_max_filesize'));
			$post_max = parseSize(ini_get('post_max_size'));
			$max_size = ($upload_max < $post_max) ? $upload_max : $post_max;
		}
		return $max_size;
	}

}

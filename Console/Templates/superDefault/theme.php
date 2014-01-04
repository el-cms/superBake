<?php

/**
 * This class contains methods for graphical elements creation.
 * @author Manuel Tancoigne <m.tancoigne@gmail.com>
 *
 * Methods beginning with :
 * 		v_ are for views
 * 		c_ are for controllers
 * 		m_ are for models
 *
 * @todo Find a way to access to the sbc object
 *
 * @author Manuel Tancoigne
 */
class stheme {

	/**
	 *@return string  Some theme info. Pretty useless.
	 */
	public static function themeInfos() {
		return('Default theme.');
	}

	public static function v_alert($content, $class) {
		return "<div class=\"$class\">$content</div>";
	}

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

}

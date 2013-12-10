<?php

/**
 * This class contains methods for graphical elements creation.
 *
 * @author Manuel Tancoigne
 */
class stheme {

	/**
	 * Array of options from 
	 */
	public static $o = array();

	public static function themeInfos() {
		return('Default theme.');
	}

	public static function alert($content, $class, $haveCloseButton = false) {
		$alert = "<div class=\"$class\">";
		//if ($haveCloseButton == true) {
		//	$alert.='<button type="button" class="close" data-dismiss="alert">&times;</button>';
		//}
		$alert.=$content;
		$alert.='</div>';
		return $alert;
	}

}

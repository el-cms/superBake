<?php
/**
 * Controllers actions template for EL-CMS baking
 * 
 * This file is used during controllers generation and adds a basic "Add" actions
 * to the controller.
 * 
 * This file is an updated file from cakePHP.
 * 
 * @copyright     Copyright 2012, Manuel Tancoigne (http://experimentslabs.com)
 * @author        Manuel Tancoigne <m.tancoigne@gmail.com>
 * @link          http://experimentslabs.com Experiments Labs
 * @package       EL-CMS/Console/Controllers
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
/* * *****************************************
 * Functions used during generation
 */

if (!function_exists('parse_size')) {

	/**
	 * Parses a given byte count.
	 * 
	 * Function from Drupal, found here : https://api.drupal.org/api/drupal/includes!common.inc/function/parse_size/6
	 * 
	 * @param string A size expressed as a number of bytes with optional SI or IEC binary unit prefix (e.g. 2, 3K, 5MB, 10G, 6GiB, 8 bytes, 9mbytes).
	 * @return int representation of the size, in bytes
	 */
	function parse_size($size) {
		$suffixes = array(
			'' => 1,
			'k' => 1024,
			'm' => 1048576, // 1024 * 1024
			'g' => 1073741824, // 1024 * 1024 * 1024
		);
		if (preg_match('/([0-9]+)\s*(k|m|g)?(b?(ytes?)?)/i', $size, $match)) {
			return $match[1] * $suffixes[drupal_strtolower($match[2])];
		}
	}

}

if (!function_exists('file_upload_max_size')) {

	/**
	 * Determine the maximum file upload size by querying the PHP settings.
	 * 
	 * Function from Drupal, found here : https://api.drupal.org/api/drupal/includes!file.inc/function/file_upload_max_size/6
	 * 
	 * @staticvar int $max_size
	 * @return int A file size limit in bytes based on the PHP upload_max_filesize and post_max_size
	 */
	function file_upload_max_size() {
		static $max_size = -1;

		if ($max_size < 0) {
			$upload_max = parse_size(ini_get('upload_max_filesize'));
			$post_max = parse_size(ini_get('post_max_size'));
			$max_size = ($upload_max < $post_max) ? $upload_max : $post_max;
		}
		return $max_size;
	}

}

/* * *****************************************
 * Options for this action
 */
	if (!isset($fileField)) {
		$fileField = null;
	} else {
		// File defaults
		if (!is_array($fileField)) {
			$fileField = array(
				'type'=>'file',
				'file' => $fileField,
				'forbiddenExts' => array('exe'),
				'maximumSize' => file_upload_max_size(), // Maximum size defined by server max upload by default
				'path'=>'files',
			);
		}
	}
?>
	/**
	* <?php echo $admin . $a ?> method from snippet <?php echo __FILE__ ?>
	 *
	 * @return void
	 */
	public function <?php echo $admin . $a ?>() {
		if ($this->request->is('post')) {
		<?php if (!is_null($fileField)): ?>
			// Handling file upload : Check if <?php echo $fileField['type'] ?> is uploaded:
			
			if(!empty($this->data['<?php echo $currentModelName;?>']['upload']['<?php echo $fileField['name']?>']))
                {
                        $file = $this->data['<?php echo $currentModelName;?>']['upload']; //put the data into a var for easy use

                        $ext = substr(strtolower(strrchr($file['name'], '.')), 1); //get the extension
                        $arr_ext = array('jpg', 'jpeg', 'gif'); //set allowed extensions

                        //only process if the extension is valid
                        if(in_array($ext, $arr_ext))
                        {
                                //do the actual uploading of the file. First arg is the tmp name, second arg is 
                                //where we are putting it
                                move_uploaded_file($file['tmp_name'], WWW_ROOT . 'img/uploads/users/' . $file['name']);

                                //prepare the filename for database entry
                                $this->data['User']['image'] = $file['name'];
                        }
                }

                //now do the save
                if($this->User->save($this->data)) {...} else {...}
		 <?endif;?>
			$this-><?php echo $currentModelName; ?>->create();
			if ($this-><?php echo $currentModelName; ?>->save($this->request->data)) {
<?php if ($wannaUseSession): ?>
				$this->Session->setFlash(<?php echo $this->display('The ' . strtolower($singularHumanName) . ' has been saved') ?>, 'flash_success');
				$this->redirect(<?php echo $this->url('index', $controllerName) ?>);
<?php else: ?>
				$this->flash(<?php echo $this->display(ucfirst(strtolower($currentModelName)) . ' saved.') ?>, <?php echo $this->url('index', $controllerName) ?>);
<?php endif; ?>
			} else {
<?php if ($wannaUseSession): ?>
				$this->Session->setFlash(<?php echo $this->display('The ' . strtolower($singularHumanName) . ' could not be saved. Please, try again.') ?>, 'flash_error');
<?php endif; ?>
			}
		}
		$this->set('title_for_layout', 'Add <?php echo strtolower(Inflector::singularize(Inflector::humanize(Inflector::underscore($currentModelName)))) ?>');
<?php
	foreach (array('belongsTo', 'hasAndBelongsToMany') as $assoc):
		foreach ($modelObj->{$assoc} as $associationName => $relation):
			if (!empty($associationName)):
				$otherModelName = $this->_modelName($associationName);
				$otherPluralName = $this->_pluralName($associationName);
				echo "\t\t\${$otherPluralName} = \$this->{$currentModelName}->{$otherModelName}->find('list');\n";
				$compact[] = "'{$otherPluralName}'";
			endif;
		endforeach;
	endforeach;
	if (!empty($compact)):
			echo "\t\t\$this->set(compact(" . join(', ', $compact) . "));\n";
	endif;
?>
	}
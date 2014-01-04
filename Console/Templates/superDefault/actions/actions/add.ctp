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
			return $match[1] * $suffixes[strtolower($match[2])];
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

/* ******************************************
 * Options for this action
 */

if (!isset($options['fileField'])) {
	$fileField = null;
} else {
	if (!is_array($options['fileField'])) {
		$fileField = array('type' => 'file',
			'name' => (empty($options['fileField']) ? 'file' : $options['fileField']),
			'allowedExts' => array('zip', 'jpg', 'cvs'), // Put whatever here.
			'maximumSize' => file_upload_max_size(), // Maximum size defined by server max upload by default
			'path' => 'uploads',
			'type' => 'file',
		);
	} else {
		$fileField = $options['fileField'];
	}

	// Prepares a string to display with a list of exts
	// That's usefull to prepare error messages
	$i = 0;
	$fileExtsString = '';
	foreach ($fileField['allowedExts'] as $ext) {
		if ($i == 0) {
			$fileExtsString.=$ext;
		}
		$fileExtsString.=", $ext";
		$i++;
	}
	$fileExtsString="'$fileExtsString'";
}

?>
	/**
	* <?php echo $admin . $a ?> method from snippet <?php echo __FILE__ ?>
	*
	* @return void
	*/
	public function <?php echo $admin . $a ?>() {
		if ($this->request->is('post')) {
		// Creating entry
		$this-><?php echo $currentModelName;?>->create();
		<?php if (!is_null($fileField)): ?>
			// Verifying file presence
			if (!empty($this->data['<?php echo $currentModelName;?>']['<?php echo $fileField['name'];?>']['name'])) {
				// Put the data into a var for easy use
				$file = $this->data['<?php echo $currentModelName;?>']['<?php echo $fileField['name'];?>'];
				// Get the extension
				$ext = substr(strtolower(strrchr($file['name'], '.')), 1);
				// Only process if the extension is valid
				if (in_array($ext, <?php echo var_export($fileField['allowedExts'])?>)) {
					// Final file name
					// By default, a time value is added at the end of the file to avoid filename problems. Feel free to change this.
					$filename = substr($file['name'], 0, -(strlen($ext) + 1)) . '-' . time() . '.' . $ext;
			<?php
			// Checking file type
			switch ($fileField['type']) {
					case 'file':
						// Just move to dest folder
						?>
						move_uploaded_file($file['tmp_name'], WWW_ROOT . '<?php echo $this->cleanPath($fileField['path'])?>' . $filename);
						<?php
						break;
					case 'image':?>
						// Creating thumbnail:
						$thumb = new SimpleImage;
						// loading uploaded image
						if (!$thumb->load($file['tmp_name'])) {
							<?php echo $this->setFlash('Image cannot be opened. Please try again', 'error');?>
							$this->redirect(<?php echo $this->url('add')?>);
						}
						<?php
						// Must we create thumbnail ?
						if(isset($fileField['thumbs'])): ?>
						// Thumb width
						$thumb->resizeToWidth(<?php echo $fileField['thumbWidth']?>);

						// Saving thumbnail
						if (!$thumb->save(WWW_ROOT . '<?php echo $this->cleanPath($fileField['thumbs'], true)?>' . $filename)) {
							<?php echo $this->setFlash('The thumbnail cannot be saved.', 'error');?>
							$this->redirect(<?php echo $this->url('add')?>);
						}

						// Following lines are an example of croping for square thumbs
						// For cropping, we need to use an image larger as the previous thumb.
						// So we reset the image to original. No need to do this if you want to create
						// a cropped thumb smaller than the previous one.
						/*
						// Reset thumb
						$thumb->reset();
						// If final thumb isn't good for you, you can downsize image before croping it
						$thumb->centerCrop(<?php echo $fileField['thumbWidth']?>, <?php echo $fileField['thumbWidth']?>);
						if (!$thumb->save(WWW_ROOT . '<?php echo $this->cleanPath($fileField['thumbs']."::${fileField['thumbWidth']}x${fileField['thumbWidth']}", true)?>' . $filename)) {
							<?php echo $this->setFlash('The square thumb cannot be saved', 'error');?>
							$this->redirect(<?php echo $this->url('add')?>);
						}*/
						<?php endif; ?>
						// Resize file if needed
						$thumb->reset();
						if($thumb->getWidth()>'<?php echo $fileField['imageMaxWidth']?>'){
							$thumb->resizeToWidth(<?php echo $fileField['imageMaxWidth']?>);
						}

						// Saving file
						if (!$thumb->save(WWW_ROOT . '<?php echo $this->cleanPath($fileField['path'], true)?>' . $filename)) {
							<?php echo $this->setFlash('The file cannot be saved.', 'error');?>
							$this->redirect(<?php echo $this->url('add')?>);
						}
						<?php
						break;
					default:
						echo "//\n// UNKNOWN FILE TYPE SPECIFIED\n//\n";
						$this->speak(__d('superBake', 'Unknown file type specified in action.', 'error'));
						break;
				}
			?>

					//File name for DB entry
					$this->request->data['<?php echo $currentModelName;?>']['<?php echo $fileField['name']?>'] = $filename;
				} else {
					// An error has occured
					<?php /*$this->Session->setFlash(<?php echo $this->iString('Wrong file extension. Allowed extensions are: %s.', $fileExtsString)?>);*/ ?>
					<?php echo $this->setFlash('Wrong file extension. Allowed extensions are $fileString', 'warning');?>
					$this->redirect(array('admin' => 'admin_', 'plugin' => 'gallery', 'controller' => 'gallery_items', 'action' => 'index'));
				}
			}else {
				<?php echo $this->setFlash('No file has been uploaded', 'error');?>
				$this->redirect(array('admin' => 'admin_', 'plugin' => 'resellers', 'controller' => 'sellers', 'action' => 'index'));
			}

		 <?php endif;?>
			if ($this-><?php echo $currentModelName; ?>->save($this->request->data)) {
			<?php if ($wannaUseSession): ?>
				<?php echo $this->setFlash('The ' . strtolower($singularHumanName) . ' has been saved', 'success');?>
				$this->redirect(<?php echo $this->url('index', $controllerName) ?>);
			<?php else: ?>
				$this->flash(<?php echo $this->iString(ucfirst(strtolower($currentModelName)) . ' saved.') ?>, <?php echo $this->url('index', $controllerName) ?>);
			<?php endif; ?>
			} else {
			<?php if ($wannaUseSession): ?>
				<?php echo $this->setFlash('The ' . strtolower($singularHumanName) . ' could not be saved. Please try again.', 'error');?>
			<?php endif; ?>
			}
		}
		$this->set('title_for_layout', 'New <?php echo strtolower(Inflector::singularize(Inflector::humanize(Inflector::underscore($currentModelName)))) ?>');
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
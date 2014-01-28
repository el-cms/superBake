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
			'maximumSize' => sTheme::c_getFileUploadMaxSize(), // Maximum size defined by server max upload by default
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
		if ($i === 0) {
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
			switch ($fileField['type']):
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
							<?php echo $this->setFlash('Image cannot be opened. Please try again', 'error', $a, array('redirect'=>false));?>
						}
						<?php
						// Must we create thumbnail ?
						if(isset($fileField['thumbs'])): ?>
						// Thumb width
						$thumb->resizeToWidth(<?php echo $fileField['thumbWidth']?>);

						// Saving thumbnail
						if (!$thumb->save(WWW_ROOT . '<?php echo $this->cleanPath($fileField['thumbs'], true)?>' . $filename)) {
							<?php echo $this->setFlash('The thumbnail cannot be saved.', 'error', $a, array('redirect'=>false));?>
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
							<?php echo $this->setFlash('The square thumb cannot be saved', 'error', $a, array('redirect'=>false));?>
						}*/
						<?php endif; ?>
						// Resize file if needed
						$thumb->reset();
						if($thumb->getWidth()>'<?php echo $fileField['imageMaxWidth']?>'){
							$thumb->resizeToWidth(<?php echo $fileField['imageMaxWidth']?>);
						}

						// Saving file
						if (!$thumb->save(WWW_ROOT . '<?php echo $this->cleanPath($fileField['path'], true)?>' . $filename)) {
							<?php echo $this->setFlash('The file cannot be saved.', 'error', $a, array('redirect'=>false));?>
						}
						<?php
						break;
					default:
						echo "//\n// UNKNOWN FILE TYPE SPECIFIED\n//\n";
						$this->speak(__d('superBake', 'Unknown file type specified in action.', 'error'));
						break;
				endswitch;
			?>

					//File name for DB entry
					$this->request->data['<?php echo $currentModelName;?>']['<?php echo $fileField['name']?>'] = $filename;
				} else {
					// An error has occured
					<?php echo $this->setFlash('Wrong file extension. Allowed extensions are $fileString', 'warning', $a, array('redirect'=>false));?>
				}
			}else {
				<?php echo $this->setFlash('No file has been uploaded', 'error', $a, array('redirect'=>false));?>
			}

		 <?php endif;?>
			if ($this-><?php echo $currentModelName; ?>->save($this->request->data)) {
				<?php echo $this->setFlash('The ' . strtolower($singularHumanName) . ' has been saved', 'success', $a);?>
			} else {
				<?php echo $this->setFlash('The ' . strtolower($singularHumanName) . ' could not be saved. Please try again.', 'error', $a);?>
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
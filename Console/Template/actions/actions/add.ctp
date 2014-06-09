<?php
/**
 * "Add" action
 *
 * Options:
 * ========
 *  - hiddenAssociations       array          Array of belongsTo associations to remove
 *  - fileField                array|null*    Optionnal file field configuration (see examples below)
 *  - layout                   string|null*   Alternative layout
 *  - conditions               array|null     List of conditions for new/updated items
 *
 * Other:
 * ======
 *  Nothing
 *
 * Examples of configurations:
 * ===========================
 * ## In case of a form to add text content, there's nothing to add in options.
 * options:
 *
 * ## In case of a form wich should upload a file:
 * options:
 *   ## Defining the options
 *   fileField:
 *     ## Needed file type: image or file
 *     type: file
 *     ## Needed name of the field that stores the filename
 *     name: fieldname
 *     ## Needed list of allowed extensions
 *     allowedExts:
 *       odt
 *       tar.gz
 *       csv
 *     ## Optionnal maximum file size (if empty, server limit will be used).
 *     ## File size can be expressed in o/Ko/Mo/Go
 *     maximumSize: 2Mo
 *     ## Needed target folder where the file must be stored
 *     target: uploads
 *
 * ## In case of a form for an image that will belong to the currently authetificated user:
 * options:
 *   conditions:
 *      ## Put the foreign key name (and don't forget to hide the field in view options)
 *      user_id: %self%
 *   fileField:
 *     type: file
 *     name: fieldname
 *     allowedExts:
 *       jpg
 *       jpeg
 *       png
 *       gif
 *     path: uploads
 *     ## Optionnal thumbnails configuration:
 *     thumbs:
 *       ## Desired width, in px.
 *       thumbsWidth: 100
 *
 *
 * @copyright     Copyright 2012, Manuel Tancoigne (http://experimentslabs.com)
 * @author        Manuel Tancoigne <m.tancoigne@gmail.com>
 * @link          http://experimentslabs.com Experiments Labs
 * @license       GPL v3 (http://www.gnu.org/licenses/gpl.html)
 * @package       ELCMS.superBake.Templates.Default.Actions
 * @version       0.3
 */

/* ----------------------------------------------------------------------------
 *
 * Options
 *
 * --------------------------------------------------------------------------*/
// Include common options
include 'common/common_options.ctp';

/* ----------------------------------------------------------------------------
 * Current action options:
 */

//
// Hidden associations: for belongsTo associations that should not be displayed on form
if (!isset($options['hiddenAssociations'])) {
	$hiddenAssociations = array();
} else {
	$hiddenAssociations = $options['hiddenAssociations'];
}

//
// File field
if (!isset($options['fileField'])) {
	$fileField = null;
} else {
	if (!is_array($options['fileField'])) {
		$fileField = array('type' => 'file',
				'name' => (empty($options['fileField']) ? 'file' : $options['fileField']),
				'allowedExts' => array('zip', 'jpg', 'cvs'), // Put whatever here.
				'maximumSize' => $this->c_getFileUploadMaxSize(), // Maximum size defined by server max upload by default
				'path' => 'uploads',
				'type' => 'file',
		);
	} else {
		$fileField = $options['fileField'];
		if(!isset($fileField['maximumSize'])){
			$fileField['maximumSize']=$this->c_getFileUploadMaxSize();
		}
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
	$fileExtsString = "'$fileExtsString'";
}

/* ----------------------------------------------------------------------------
 *
 * Action
 *
 * --------------------------------------------------------------------------*/
?>
/**
 * <?php echo $admin . $a ?> method from snippet <?php echo __FILE__ ?>
 *
 * @return void
 */
public function <?php echo $admin . $a ?>() {
	<?php
	// Support for a different layout. Look at the snippet for more info.
	include $themePath . 'actions/snippets/layout_support.ctp';
	?>
	if ($this->request->is('post')) {
		<?php

		// Check if the added item should be assigned to a given user
		if($this->isComponentEnabled('Auth')):
			$userId=Inflector::singularize(Inflector::tableize($this->Sbc->getConfig('theme.components.Auth.userModel'))).'_'.$this->Sbc->getConfig('theme.components.Auth.userModelPK');
			if(isset($conditions[$userId]) && $conditions[$userId]==='%self%'):
//				$itemIsForSelf=true;
				$this->speak(__d('superBake', '  - Added items belongs to the logged in user.'), 'comment');
				echo "// Assigning user Id to the new ".strtolower($currentModelName)."\n";
				echo "\$this->request->data['$currentModelName']['$userId'] = \$this->Session->read('Auth.".$this->Sbc->getConfig('theme.components.Auth.userModel').".".$this->Sbc->getConfig('theme.components.Auth.userModelPK')."');\n";
			else:
				$this->speak(__d('superBake', '  - Added items can be assignated to a given user.'), 'comment');
			endif;
		endif;
		?>

		// Creating entry
		$this-><?php echo $currentModelName; ?>->create();
		<?php

		//
		// Case of a file field
		//
		if (!is_null($fileField)): ?>
		// Verifying file presence
		if (!empty($this->data['<?php echo $currentModelName; ?>']['<?php echo $fileField['name']; ?>']['name'])) {
		// Put the data into a var for easy use
		$file = $this->data['<?php echo $currentModelName; ?>']['<?php echo $fileField['name']; ?>'];
		// Get the extension
		$ext = substr(strtolower(strrchr($file['name'], '.')), 1);
		// Only process if the extension is valid
		if (in_array($ext, <?php echo var_export($fileField['allowedExts']) ?>)) {
		// Final file name
		// By default, a time value is added at the end of the file to avoid filename problems. Feel free to change this.
		$filename = substr($file['name'], 0, -(strlen($ext) + 1)) . '-' . time() . '.' . $ext;
	<?php
	//
	// Checking file type
	switch ($fileField['type']):
		// Simple file:
		case 'file':
			?>
			move_uploaded_file($file['tmp_name'], WWW_ROOT . '<?php echo $this->cleanPath($fileField['path']) ?>' . $filename);
			<?php
			break;

		// Image
		case 'image':
			?>
			// Creating thumbnail:
			$thumb = new SimpleImage;
			// loading uploaded image
			if (!$thumb->load($file['tmp_name'])) {
			<?php echo $this->setFlash('Image cannot be opened. Please try again', 'error', $a, array('redirect' => false)); ?>
			}
			<?php
			// Must we create thumbnail ?
			if (isset($fileField['thumbs'])):
				?>
				// Thumb width
				$thumb->resizeToWidth(<?php echo $fileField['thumbWidth'] ?>);

				// Saving thumbnail
				if (!$thumb->save(WWW_ROOT . '<?php echo $this->cleanPath($fileField['thumbs'], true) ?>' . $filename)) {
				<?php echo $this->setFlash('The thumbnail cannot be saved.', 'error', $a, array('redirect' => false)); ?>
				}

				// Following lines are an example of croping for square thumbs
				// For cropping, we need to use an image larger as the previous thumb.
				// So we reset the image to original. No need to do this if you want to create
				// a cropped thumb smaller than the previous one.
				/*
				// Reset thumb
				$thumb->reset();
				// If final thumb isn't good for you, you can downsize image before croping it
				$thumb->centerCrop(<?php echo $fileField['thumbWidth'] ?>, <?php echo $fileField['thumbWidth'] ?>);
				if (!$thumb->save(WWW_ROOT . '<?php echo $this->cleanPath($fileField['thumbs'] . "::${fileField['thumbWidth']}x${fileField['thumbWidth']}", true) ?>' . $filename)) {
				<?php echo $this->setFlash('The square thumb cannot be saved', 'error', $a, array('redirect' => false)); ?>
				}*/
			<?php endif; ?>
			// Resize file if needed
			$thumb->reset();
			if($thumb->getWidth()>'<?php echo $fileField['imageMaxWidth'] ?>'){
			$thumb->resizeToWidth(<?php echo $fileField['imageMaxWidth'] ?>);
			}

			// Saving file
			if (!$thumb->save(WWW_ROOT . '<?php echo $this->cleanPath($fileField['path'], true) ?>' . $filename)) {
			<?php echo $this->setFlash('The file cannot be saved.', 'error', $a, array('redirect' => false)); ?>
			}
			<?php
			break;

		// Default behaviour:
		default:
			echo "//\n// UNKNOWN FILE TYPE SPECIFIED\n//\n";
			$this->speak(__d('superBake', 'Unknown file type specified in action.', 'error'));
			break;
	endswitch;


			//
			// Creating the entry in DB
			//
			?>
			//File name for DB entry
			$this->request->data['<?php echo $currentModelName; ?>']['<?php echo $fileField['name'] ?>'] = $filename;
		} else {
			// An error has occured
			<?php echo $this->setFlash('Wrong file extension. Allowed extensions are $fileString', 'warning', $a, array('redirect' => false)); ?>
		}
	}else {
		<?php echo $this->setFlash('No file has been uploaded', 'error', $a, array('redirect' => false)); ?>
	}

	<?php endif; ?>
	if ($this-><?php echo $currentModelName; ?>->save($this->request->data)) {
		<?php echo $this->setFlash('The ' . strtolower($singularHumanName) . ' has been saved', 'success', $a); ?>
	} else {
		<?php echo $this->setFlash('The ' . strtolower($singularHumanName) . ' could not be saved. Please try again.', 'error', $a); ?>
	}
}
$this->set('title_for_layout', <?php echo $this->iString('New ' . strtolower(Inflector::singularize(Inflector::humanize(Inflector::underscore($currentModelName))))) ?>);
<?php
foreach (array('belongsTo', 'hasAndBelongsToMany') as $assoc):
	foreach ($modelObj->{$assoc} as $associationName => $relation):
		if (!empty($associationName) && !in_array($this->_modelName($associationName), $hiddenAssociations)):
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
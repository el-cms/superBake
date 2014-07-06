# A few steps to use the SimpleImage library in cakePHP:

 * Create a folder named "Image" in `<cakeInstall>/app/Lib/`
 * Create a file named SimpleImage.php and paste the content of the [SimpleImage class](https://github.com/el-cms/superBake/blob/dev/Console/Template/required/libs/Image/SimpleImage.php)
 * In the controllers you want to use the class, add `App::uses('SimpleImage', 'Image');` _before_ the class definition.
 * SimpleImage is now available in actions where you need to use the class (usually an add or edit action)

## Few examples:

### Basic thumbnailing

Assuming you want to create a Gallery Item, here is the code for the add() action:

```PHP
if ($this->request->is('post')) {
  // Creating entry
  $this->GalleryItem->create();
  
  // Verifying file presence
  if (!empty($this->data['GalleryItem']['file']['name'])) {
    
    // Put the data into a var for easy use
    $file = $this->data['GalleryItem']['file'];
    
    // Get the extension
    $ext = substr(strtolower(strrchr($file['name'], '.')), 1);
    // Only process if the extension is valid
    if (in_array($ext, array(
      0 => 'jpg',
      1 => 'png',
      2 => 'gif',
      ))) {
      
      // Final file name
      // By default, a time value is added at the end of the file to avoid filename problems. Feel free to change this.
      $filename = substr($file['name'], 0, -(strlen($ext) + 1)) . '-' . time() . '.' . $ext;
        
      // Creating object:
      $img = new SimpleImage;
        
      // loading uploaded image
      if (!$img->load($file['tmp_name'])) {
        $this->Session->setFlash(__('Image cannot be opened. Please try again'), 'flash_error');
        $this->redirect(array('admin_' => true, 'plugin' => 'gallery', 'controller' => 'gallery_items', 'action' => 'add'));
      }
        
      // Resize file if needed (to match your layout or keep space on server)
      if ($img->getWidth() > '900') {
        $img->resizeToWidth(900);
      }

      // Watermark (assuming you have a "watermark.png" file in webroot/img"
      $img->waterMark(WWW_ROOT.'img/watermark.png');
        
      // Saving image (resized and watermarked)
      if (!$img->save(WWW_ROOT . 'img/uploads/' . $filename)) {
        $this->Session->setFlash(__('The file cannot be saved.'), 'flash_error');
        $this->redirect(array('admin_' => true, 'plugin' => 'gallery', 'controller' => 'gallery_items', 'action' => 'add'));
      }

      //Reloading image as there is a problem with the watermark (must be fixed in the lib :/ )
      // Usually, you just have to use $img->reset() to revert all changes done.
      if (!$img->load($file['tmp_name'])) {
        $this->Session->setFlash(__('Image cannot be opened. Please try again'), 'flash_error');
        $this->redirect(array('admin_' => true, 'plugin' => 'gallery', 'controller' => 'gallery_items', 'action' => 'add'));
      }
      
      // Resizing image
      $img->resizeToHeight(100);

      // Saving file
      if (!$img->save(WWW_ROOT . 'img/uploads/thumbs/' . $filename)) {
        $this->Session->setFlash(__('The image cannot be saved.'), 'flash_error');
        $this->redirect(array('admin_' => true, 'plugin' => 'gallery', 'controller' => 'gallery_items', 'action' => 'add'));
      }


      //File name for DB entry (Assuming you have a 'file' field in the table)
        $this->request->data['GalleryItem']['file'] = $filename;
      } else {
        // An error has occured
        $this->Session->setFlash(__('Wrong file extension. Allowed extensions are $fileString'), 'flash_warning');
        $this->redirect(array('admin' => 'admin_', 'plugin' => 'gallery', 'controller' => 'gallery_items', 'action' => 'index'));
      }
    } else {
      $this->Session->setFlash(__('No file has been uploaded'), 'flash_error');
      $this->redirect(array('admin' => 'admin_', 'plugin' => 'resellers', 'controller' => 'sellers', 'action' => 'index'));
    }

    if ($this->GalleryItem->save($this->request->data)) {
      $this->Session->setFlash(__d('gallery', 'The gallery item has been saved'), 'flash_success');
      $this->redirect(array('admin_' => true, 'plugin' => 'gallery', 'controller' => 'gallery_items', 'action' => 'index'));
    } else {
      $this->Session->setFlash(__d('gallery', 'The gallery item could not be saved. Please try again.'), 'flash_error');
    }
  }
}
```

## Methods explanation
Look at the class comments, it should be easy to understand.

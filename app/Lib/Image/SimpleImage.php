<?php

/*
 * File: SimpleImage.php
 * 
 * This class perform very basic images manipulation.
 * 
 * ---------------------------------------------------------------------------
 * Modified by Manuel Tancoigne <m.tancoigne@gmail.com>
 * Date: 09/2013
 * ---
 * Original Author: Simon Jarvis
 * Copyright: 2006 Simon Jarvis
 * Original Date: 08/11/06
 * Original Link: http://www.white-hat-web-design.co.uk/articles/php-image-resizing.php
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 2
 * of the License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details:
 * http://www.gnu.org/licenses/gpl.html
 *
 */

class SimpleImage {

	// Original image
	var $image;
	// Modified image
	var $currentImage;
	// Image type
	var $image_type;
	// Log of actions
	var $log = array();

	/**
	 * Loads an image
	 * @param string $filename Image to load
	 */
	function load($filename) {
		if (!$image_info = getimagesize($filename)) {
			return false;
		}
		$this->image_type = $image_info[2];
		if ($this->image_type == IMAGETYPE_JPEG) {
			if (!$this->image = imagecreatefromjpeg($filename)) {
				return false;
			}
		} elseif ($this->image_type == IMAGETYPE_GIF) {
			if (!$this->image = imagecreatefromgif($filename)) {
				return false;
			}
		} elseif ($this->image_type == IMAGETYPE_PNG) {
			if (!$this->image = imagecreatefrompng($filename)) {
				return false;
			}
		}
		$this->currentImage = $this->image;
		// Logging this
		$this->log('Image loaded (' . $filename . ')');
		return true;
	}

	/**
	 * Saves an image 
	 * @param string $filename Image file name
	 * @param bool $original Save the original image if true, modified if false.
	 * @param string $image_type Image format
	 * @param int $compression Compression rate (mainly for Jpgs)
	 * @param int $permissions Permissions for the new file
	 */
	function save($filename, $original = false, $image_type = null, $compression = 75, $permissions = null) {
		// Selecting image
		$image = ($original === true) ? $this->image : $this->currentImage;
		if ($image_type == null) {
			$image_type = $this->image_type;
		}
//		die (var_dump($this->image_type));
		if ($image_type == IMAGETYPE_JPEG) {
			if (!imagejpeg($image, $filename, $compression)) {
				die('ERROR HERE');
				return false;
			}
		} elseif ($image_type == IMAGETYPE_GIF) {
			if (!imagegif($image, $filename)) {
				return false;
			}
		} elseif ($image_type == IMAGETYPE_PNG) {
			if (!imagepng($image, $filename)) {
				return false;
			}
		}
		if ($permissions != null) {
			chmod($filename, $permissions);
		}
		$this->log((($original === true) ? 'Original' : 'Copy') . ' image saved successfully.');
		return true;
	}

	/**
	 * Returns the image without saving it. Useful for direct rendering
	 * 
	 * @param string $image_type Image format
	 * @param bool $original Display original image if true, modified image if false.
	 */
	function output($image_type = IMAGETYPE_JPEG, $original = true) {
		$image = ($original == true) ? $this->image : $this->currentImage;
		if ($image_type == IMAGETYPE_JPEG) {
			imagejpeg($image);
		} elseif ($image_type == IMAGETYPE_GIF) {
			imagegif($image);
		} elseif ($image_type == IMAGETYPE_PNG) {
			imagepng($image);
		}
	}

	/**
	 * Gets the image width
	 * @return int
	 */
	function getWidth() {
		return imagesx($this->currentImage);
	}

	/**
	 * Gets the image height
	 * @param bool $currentImage If false, will return original image width, else the current image (modified one)
	 * @returns int
	 */
	function getHeight() {
		return imagesy($this->currentImage);
	}

	/**
	 * Resize image to desired height. Width will be resized with a ratio.
	 * @param int $height Desired height
	 */
	function resizeToHeight($height) {
		$ratio = $height / $this->getHeight();
		$width = $this->getWidth() * $ratio;
		$this->resize($width, $height);
		$this->log('Image resized to height ' . $height);
	}

	/**
	 * Resize image to desired width. Height will be resized with a ratio.
	 * @param int $width Desired width
	 */
	function resizeToWidth($width) {
		$ratio = $width / $this->getWidth();
		$height = $this->getheight() * $ratio;
		$this->resize($width, $height);
		$this->log('Image resized to width ' . $width);
	}

	/**
	 * Resize an image using a certain scale.
	 * @param int $scale Scale factor
	 */
	function scale($scale) {
		$width = $this->getWidth() * $scale / 100;
		$height = $this->getheight() * $scale / 100;
		$this->resize($width, $height);
		$this->log('(Scale of ' . $scale . ')');
	}

	/**
	 * Resize image to given width and height
	 * @param int $width Desired width
	 * @param int $height Desired height
	 */
	function resize($width, $height) {
		$new_image = imagecreatetruecolor($width, $height);
		imagecopyresampled($new_image, $this->currentImage, 0, 0, 0, 0, $width, $height, $this->getWidth(), $this->getHeight());
		$this->currentImage = $new_image;
	}

	/**
	 * Crops the current image to the desired dimensions
	 * @param int $width Desired width
	 * @param int $height Desired height
	 * @param int $startX Crop horizontal start point
	 * @param int $startY Crop vertical start point
	 */
	function crop($width, $height, $startX = 0, $startY = 0, $scale=.5) {
		// Getting image width/height
		$imageX=$this->getHeight();
		$imageY=$this->getWidth();
		
		// Finding the longest side
		$biggestSide=($imageX>$imageY)?$imageX:$imageY;
		// Determinating the crop sizes
//		$cropX=$biggestSide;
//		$cropY=$biggestSide;
		$cropX=$biggestSide*$scale;
		$cropY=$biggestSide*$scale;
//		$cropX=$imageX*$scale;
//		$cropY=$imageY*$scale;
		
		//Creating image
		$new_image = imagecreatetruecolor($width, $height);
		imagecopyresampled($new_image, $this->currentImage, 0, 0, $startX, $startY, $width, $height, $cropX, $cropY);
		$this->currentImage = $new_image;

		$this->log('Image cropped (X=' . $width . ', Y=' . $height . ')');
	}

	function centerCrop($width, $height) {
		// Determining start points
		$imgX=$this->getWidth();
		$imgY=$this->getHeight();
		
		$startX=($imgX-$width)/2;
		$startY=($imgY-$height)/2;
		debug ("W=$width, H=$height, SX=$startX, SY=$startY");
		$this->crop($width, $height, (int)$startX, (int)$startY);
	}

	/**
	 * Adds a message to the modification log
	 * 
	 * @param string $message Message to add
	 */
	function log($message) {
		$this->log[] = $message;
	}

	/**
	 * Resets the current image to original image.
	 * 
	 * @return boolean
	 */
	function reset() {
		$this->currentImage = $this->image;
		$this->log=array();
		return true;
	}

	/**
	 * Returns the log array.
	 * 
	 * @return array
	 */
	function getLog() {
		return $this->log;
	}
	
	function resizeSmallestTo($size){
		if($this->getHeight()>=$this->getWidth()){
			//Must resize width
			$this->resizeToWidth($size);
		}else{
			//Must resize height
			$this->resizeToHeight($size);
		}
	}
	function resizeBiggestTo($size){
		if($this->getHeight()<=$this->getWidth()){
			//Must resize width
			$this->resizeToWidth($size);
		}else{
			//Must resize height
			$this->resizeToHeight($size);
		}
	}

}

?>
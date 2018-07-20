<?php

class Image {

	private $thumb;

	/**
	 * Constructor
	 */
	public function __construct(){
		$this->thumb = new Imagick();
	}

	/**
	 * Create a thumbnail of an image
	 * @param  string $path  The path to the original image
	 * @param  int 			 $width The desired width for the thumbnail
	 * @return 				 No return value
	 */
	public function thumbnail($path, $width) {

		if(!getimagesize($path))
			return "Not an image";

		$dir = pathinfo($path, PATHINFO_DIRNAME);
		$file = 'thumb_'.pathinfo($path, PATHINFO_FILENAME);
		$ext = strtolower(pathinfo($path, PATHINFO_EXTENSION));
		$combined = $dir.'/'.$file.'.'.$ext;

		if(is_file($combined))
			return "Thumbnail already exists";

		list($org_w, $org_h) = getimagesize($path);
		$ratio = $org_w / $org_h;

		$this->thumb->readImage($path);
		$this->thumb->resizeImage($width, ($width/$ratio), Imagick::FILTER_LANCZOS, 1);
		$this->thumb->writeImage($combined);
		$this->thumb->clear();
		$this->thumb->destroy();
	}
}

?>
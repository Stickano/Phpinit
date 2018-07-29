<?php

class Upload {

	private $file_input;
	private $filetypes;

	private $rename;
	private $required;
	private $unique_filename;
	private $return_on_success;
	private $overwrite;

	private $no_file;
	private $no_accepted_extension;
	private $exceeded_filesize;
	private $no_overwrite;

	private $max_file_size;
	private $converter;

	/**
	 * Constructor
	 * @param string $input_file Name of the file-element
	 * @param array  $filetypes  Accepted file-types
	 */
	public function __construct($input_file, $filetypes=array()){
		$this->file_input            = $input_file;
		$this->filetypes             = $filetypes;

		$this->rename                = false;
		$this->required              = false;
		$this->unique_filename       = false;
		$this->return_on_success     = true;
		$this->overwrite             = false;

		$this->no_file               = "No file selected";
		$this->no_accepted_extension = "File type not eligible";
		$this->exceeded_filesize     = "File size is to large";
		$this->no_overwrite          = "File already exists";

		$this->max_file_size         = 2;
		$this->converter             = 1048576;
	}


	/*
	 *	Methods to set the various return messages
	 */
	public function setNoFile($string) {
		$this->no_file = $string;
	}

	public function setNotAccepted($string) {
		$this->no_accepted_extension = $string;
	}

	public function setExceededSize($string) {
		$this->exceeded_filesize = $string;
	}

	public function setNoOverwrite($string) {
		$this->no_overwrite = $string;
	}

	/*
	 *	Methods to set the various options for uploads
	 */
	public function required() {
		$this->required = true;
	}

	public function rename($string) {
		$this->rename = $string;
	}

	public function uniqueFilename() {
		$this->unique_filename = true;
	}

	public function overwrite() {
		$this->overwrite = true;
	}

	/*
	 * Filesize options
	 */
	public function setFilesize($int) {
		$this->max_file_size = $int;
	}

	public function kbFilesize() {
		$this->converter = 1024;
	}



	/**
	 * Easily manageable upload scrip
	 * @param  string $dir Path to upload the file
	 * @return string      Return errors/success (as defined)
	 */
	public function upload($dir) {

		# Confirm a file is selected
		if($_FILES[$this->file_input]['size'] == 0){
			if($this->required == true)
				throw new Exception($this->no_file);
			else
				return;
		}

		# Create the folder if needed
		if(!is_dir($dir))
			mkdir($dir);

		# Make sure the path has a trailing /
		if(substr($dir, -1) != "/")
			$dir .= "/";

		# Set file extension (do a little shortening if needed)
		$file_ext = strtolower(pathinfo($_FILES[$this->file_input]['name'], PATHINFO_EXTENSION));
		if($file_ext == "pjpeg")
			$file_ext = "jpg";
		elseif($file_ext == "jpeg")
			$file_ext = "jpg";

		# Confirm that it is an accepted file extension
		if(!in_array($file_ext, $this->filetypes))
			throw new Exception($this->no_accepted_extension);

		# Confirm the file-size
		if($_FILES[$this->file_input]['size'] > $this->max_file_size*$this->converter)
			throw new Exception($this->exceeded_filesize);

		# If only images is allowed as file-types,
		# 	perform an additional security check
		$image_extension = array("jpg", "gif", "png");
		$all_images = true;
		foreach ($filetypes as $type) {
			if(!in_array(strtolower($type), $image_extension))
				$all_images = false;
		}

		if($all_images == true
			&& !getimagesize($_FILES[$this->file_input]['tmp_name']))
			throw new Exception($this->no_accepted_extension);

		# Set the name of the file (rename if selected)
		if($this->rename == false){
			$ext_info = pathinfo($_FILES[$this->file_input]['name']);
			$filename = basename(
						$_FILES[$this->file_input]['name'],
						'.'.$ext_info['extension']);
			$filename = filter_var($filename,  FILTER_SANITIZE_STRING);
		}else
			$filename = $this->rename;

		# Generate a unique filename (if selected)
		if($this->unique_filename == true){
			$dash = null;
			if($filename != null || !empty($filename))
				$dash = "-";
			$random = substr(md5(microtime()),0,6);
			$filename .= $dash.$random;
		}

		# Place the extension suffix in the name
		$filename .= '.'.$file_ext;

		# Check if the file already exists
		if(file_exists($dir.$filename) && $this->overwrite == false)
			throw new Exception($this->no_overwrite);

		# Transfer file
		if(!move_uploaded_file(
			$_FILES[$this->file_input]['tmp_name'], $dir.$filename))
			throw new Exception($_FILES[$this->file_input]['error']);

		return $dir.$filename;
	}

}

?>
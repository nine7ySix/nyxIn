<?php
	//                   [nyxIn/uploadHandler.php]
	//
	//	This file came with the File Uploader by Andrew Valums. His Github can
	//	be found here: https://github.com/valums and the page for the file
	//	uploader can be found on https://github.com/valums/file-uploader. The
	//	changes made to this file was the addition of the crop_sqaure function
	//	and the SQL queries used to add the images into the database.
	//

	require("core/variables.php");
/**
 * Handle file uploads via XMLHttpRequest
 */
class qqUploadedFileXhr {
	/**
	 * Save the file to the specified path
	 * @return boolean TRUE on success
	 */
	function save($path) {	
		$input = fopen("php://input", "r");
		$temp = fopen("php://temp", "wb"); 
		
		$realSize = stream_copy_to_stream($input, $temp);
		fclose($input);
		
		if ($realSize != $this->getSize()){
			return false;
		}
		
		$target = fopen($path, "w");		
		fseek($temp, 0, SEEK_SET);
		stream_copy_to_stream($temp, $target);
		fclose($target);

		return true;
	}
	function getName() {
		return $_GET['qqfile'];
	}
	function getSize() {
		if (isset($_SERVER["CONTENT_LENGTH"])){
			return (int)$_SERVER["CONTENT_LENGTH"];			
		} else {
			throw new Exception('Getting content length is not supported.');
		}	  
	}   
}

/**
 * Handle file uploads via regular form post (uses the $_FILES array)
 */
class qqUploadedFileForm {  
	/**
	 * Save the file to the specified path
	 * @return boolean TRUE on success
	 */
	function save($path) {
		if(!move_uploaded_file($_FILES['qqfile']['tmp_name'], $path)){
			return false;
		}
		return true;
	}
	function getName() {
		return $_FILES['qqfile']['name'];
	}
	function getSize() {
		return $_FILES['qqfile']['size'];
	}
}

class qqFileUploader {
	
	private $allowedExtensions = array();
	private $sizeLimit = 10485760;
	private $file;

	function __construct(array $allowedExtensions = array(), $sizeLimit = 10485760){		
		$allowedExtensions = array_map("strtolower", $allowedExtensions);
			
		$this->allowedExtensions = $allowedExtensions;		
		$this->sizeLimit = $sizeLimit;
		
		$this->checkServerSettings();	   

		if (isset($_GET['qqfile'])) {
			$this->file = new qqUploadedFileXhr();
		} elseif (isset($_FILES['qqfile'])) {
			$this->file = new qqUploadedFileForm();
		} else {
			$this->file = false; 
		}
	}
	
	private function checkServerSettings(){		
		$postSize = $this->toBytes(ini_get('post_max_size'));
		$uploadSize = $this->toBytes(ini_get('upload_max_filesize'));		
		
		if ($postSize < $this->sizeLimit || $uploadSize < $this->sizeLimit){
			$size = max(1, $this->sizeLimit / 1024 / 1024) . 'M';			 
			die("{'error':'increase post_max_size and upload_max_filesize to $size'}");	
		}		
	}
	
	private function toBytes($str){
		$val = trim($str);
		$last = strtolower($str[strlen($str)-1]);
		switch($last) {
			case 'g': $val *= 1024;
			case 'm': $val *= 1024;
			case 'k': $val *= 1024;		
		}
		return $val;
	}
	
	/**
	 * Returns array('success'=>true) or array('error'=>'error message')
	 */
	function handleUpload($uploadDirectory, $replaceOldFile = FALSE) {
		global $nyxIn;
		
		if (!is_writable($uploadDirectory)){
			return array('error' => "Server error. Upload directory isn't writable.");
		}
		
		if (!$this->file){
			return array('error' => 'No files were uploaded.');
		}
		
		$size = $this->file->getSize();
		
		if ($size == 0) {
			return array('error' => 'File is empty');
		}
		
		if ($size > $this->sizeLimit) {
			return array('error' => 'File is too large');
		}
		
		$pathinfo = pathinfo($this->file->getName());

		/* The variable $filename has been converted to it's RandomIntSHA1 safename */
		/* RandomInt to prevent similar filenames and to disguise files */
		$RandomInt = rand(0, 999999999);
		
		$filename = $pathinfo['filename'];
		$filename = $RandomInt.sha1($filename);
		$ext = $pathinfo['extension'];

		if($this->allowedExtensions && !in_array(strtolower($ext), $this->allowedExtensions)){
			$these = implode(', ', $this->allowedExtensions);
			return array('error' => 'File has an invalid extension, it should be one of '. $these . '.');
		}

		if(!$replaceOldFile){
			/// don't overwrite previous files that were uploaded
			while (file_exists($uploadDirectory . $filename . '.' . $ext)) {
				$filename .= rand(10, 99);
			}
		}
		
		if ($this->file->save($uploadDirectory . $filename . '.' . $ext)){
			$this->square_crop($uploadDirectory . $filename . '.' . $ext, $uploadDirectory . $filename . '_thumb.' . $ext, $nyxIn['preferences']['thumbnail_length'], 90);
			/* Because we have successfully uploaded the file into the server, write into database */
			/* Re-assign variables to fit those in the database, I know, some have already been assigned above */
			$gallery_id = $_GET['gallery_id'];
			$upload_timestamp = time();
			$filename = $pathinfo['filename'];
			$safename = $RandomInt.sha1($filename);
			$fileextension = $ext;
			$filesize = $size;

			$safename_thumb = $safename."_thumb.".$fileextension;

			$nyxIn['db']->query("INSERT INTO ".$nyxIn['db_prefix']."images (gallery_id, moderation_status, upload_timestamp, filename, safename, fileextension, filesize) VALUES('$gallery_id', '0', '$upload_timestamp', '$filename', '$safename', '$fileextension', '$filesize') ") or die($nyxIn['db']->error);
			
			if($gallery_id==0) {

			} else {
				$nyxQuery_SelectGalleryMetadata = $nyxIn['db']->query("SELECT * FROM ".$nyxIn['db_prefix']."galleries WHERE id=$gallery_id") or die($nyxIn['db']->error);
				$nyxInGallery = $nyxQuery_SelectGalleryMetadata->fetch_object();

				$nyxInGalleryThumbnailFile = $nyxInGallery->thumbnail;

				if(trim($nyxInGalleryThumbnailFile)=="") {
					$nyxIn['db']->query("UPDATE ".$nyxIn['db_prefix']."galleries SET thumbnail='$safename_thumb' WHERE id=$gallery_id") or die($nyxIn['db']->error);
				}
			}
		
			return array('success'=>true);
		} else {
			return array('error'=> 'Could not save uploaded file.' .
				'The upload was cancelled, or server error encountered');
		}
	}

	 
	//Custom //http://www.abeautifulsite.net/blog/2009/08/cropping-an-image-to-make-square-thumbnails-in-php/
	function square_crop($src_image, $dest_image, $thumb_size = 64, $jpg_quality = 90) {
	 
		// Get dimensions of existing image
		$image = getimagesize($src_image);
	 
		// Check for valid dimensions
		if( $image[0] <= 0 || $image[1] <= 0 ) return false;
	 
		// Determine format from MIME-Type
		$image['format'] = strtolower(preg_replace('/^.*?\//', '', $image['mime']));
	 
		// Import image
		switch( $image['format'] ) {
			case 'jpg':
			case 'jpeg':
				$image_data = imagecreatefromjpeg($src_image);
			break;
			case 'png':
				$image_data = imagecreatefrompng($src_image);
			break;
			case 'gif':
				$image_data = imagecreatefromgif($src_image);
			break;
			default:
				// Unsupported format
				return false;
			break;
		}
	 
		// Verify import
		if( $image_data == false ) return false;
	 
		// Calculate measurements
		if( $image[0] & $image[1] ) {
			// For landscape images
			$x_offset = ($image[0] - $image[1]) / 2;
			$y_offset = 0;
			$square_size = $image[0] - ($x_offset * 2);
		} else {
			// For portrait and square images
			$x_offset = 0;
			$y_offset = ($image[1] - $image[0]) / 2;
			$square_size = $image[1] - ($y_offset * 2);
		}
	 
		// Resize and crop
		$canvas = imagecreatetruecolor($thumb_size, $thumb_size);
		if( imagecopyresampled(
			$canvas,
			$image_data,
			0,
			0,
			$x_offset,
			$y_offset,
			$thumb_size,
			$thumb_size,
			$square_size,
			$square_size
		)) {
	 
			// Create thumbnail
			switch( strtolower(preg_replace('/^.*\./', '', $dest_image)) ) {
				case 'jpg':
				case 'jpeg':
					return imagejpeg($canvas, $dest_image, $jpg_quality);
				break;
				case 'png':
					return imagepng($canvas, $dest_image);
				break;
				case 'gif':
					return imagegif($canvas, $dest_image);
				break;
				default:
					// Unsupported format
					return false;
				break;
			}
	 
		} else {
			return false;
		}
	 
	}
}
 


// list of valid extensions, ex. array("jpeg", "xml", "bmp")
$allowedExtensions = array("jpeg", "jpg", "png", "gif");
// max file size in bytes
$sizeLimit = 10 * 1024 * 1024;

$uploader = new qqFileUploader($allowedExtensions, $sizeLimit);
$result = $uploader->handleUpload('uploads/');
// to pass data through iframe you will need to encode all html tags
echo htmlspecialchars(json_encode($result), ENT_NOQUOTES);

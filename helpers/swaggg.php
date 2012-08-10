<?php
/**
 * @package Swaggg
 * @author Mike Lay <michael.lay.email@gmail.com>
 * @license MIT
 */
defined('C5_EXECUTE') or die('Access Denied.');
/**
 * Helpers to be used throughout the Swaggg
 *
 * @package Swaggg
 * @author Mike Lay <michael.lay.email@gmail.com>
 * @license MIT
 */
class SwagggHelper {
	
	/**
	 * Imports and prefixes file
	 *
	 * Adds file to Swaggg file set
	 *
	 * <code>
	 * <?php
	 * $file_version = SwagggHelper::importFile($_FILES['image']['tmp_name']);
	 * ?>
	 * </code>
	 * @see SwagggUploadBlockController
	 * @param string $temp_file Full path and file name
	 * @return FileVersion
	 */
	public static function importFile($temp_file) {

		Loader::library('file/importer');

		$extension = self::getExtension($temp_file);
		$file_name = 'swaggg_'.uniqid().'.'.$extension;

		$file_importer = new FileImporter();
		$file_version = $file_importer->import($temp_file, $file_name);

		$file_set = FileSet::getByName('Swaggg');
		if(!$file_set) {
			throw new SwagggException('Unable to get Swaggg File Set.');
		}
		$file_set->addFileToSet($file_version);


		/**
		 * If we get an error at this point lets throw an
		 * exception
		 */
		if(!$file_version instanceof FileVersion) {
			throw new SwagggException(
				'Unable to import uploaded file. Error: '.
				FileImporter::getErrorMessage($file)
			);
		}

		return $file_version;
	}

	/**
	 * Checks file for type and returns
	 * extension suffix to be added to file
	 *
	 * @param string $file full path to file
	 * @return string extension suffix
	 */
	public static function getExtension($file) {
		$file_type = exif_imagetype($file);
		switch($file_type) {
			case IMAGETYPE_GIF:
				return 'gif';
			case IMAGETYPE_JPEG:
				return 'jpg';
			case IMAGETYPE_PNG:
				return 'png';
		}
		throw new SwagggException('File is not gif, jpeg or png');
	}

	/**
	 * Takes in an array and strips the null values
	 *
	 * @param array $values Values to strip of nulls
	 * @return array
	 */
	public static function stripNulls(array $values) {
		foreach($values as $key => $value) {
			if($value === null) {
				unset($values[$key]);
			}
		}
		return $values;
	}

	/**
	 * Returns the percentage of the first value
	 * in the second value
	 *
	 * @param $a
	 * @param $b
	 * @return integer
	 */
	public function calculatePercentage($a, $b) {
		if(!$a) {
			return 0;
		}
		if(!$b) {
			return 100;
		}
		return round(
			$a / ($a + $b) * 100
		);
	}

}

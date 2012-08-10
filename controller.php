<?php
/**
 * @package Swaggg
 * @author Mike Lay <michael.lay.email@gmail.com>
 * @license MIT
 */
defined('C5_EXECUTE') or die('Access Denied.');
/**
 * @package Swaggg
 * @author Mike Lay <michael.lay.email@gmail.com>
 */
class SwagggPackage extends Package {

	protected $pkgHandle = "swaggg";
	protected $appVersionRequired = "5.5";
	protected $pkgVersion = "0.1";

	public function getPackageName() {
		return t('Swaggg');
	}

	public function getPackageDescription() {
		return t('Swaggg Upload and Judgement');
	}

	/**
	 * Installs block swaggg_upload
	 * Installs block swaggg_view
	 * Adds FileSet Swaggg
	 */
	public function install() {
		$pkg = parent::install();

		BlockType::installBlockTypeFromPackage('swaggg_upload', $pkg);
		BlockType::installBlockTypeFromPackage('swaggg_view', $pkg);

		Loader::model('file_set');
		/**
		 * We set the uid to 1 for super user
		 */
		FileSet::createAndGetSet('Swaggg', FileSet::TYPE_PUBLIC, 1);
	}

	/**
	 * Registers autoloader by reregistering CoreAutoload
	 * so we can grap SwagggHelper etc before
	 * CoreAutoload tries to load it
	 * Would really be nice if CoreAutoload did
	 * a file_exists() first
	 */
	public function on_start() {
		spl_autoload_unregister('CoreAutoload');
		spl_autoload_register(
			array(
				'SwagggPackage',
				'autoload'
			), true
		);
		spl_autoload_register(
			'CoreAutoload',
			true
		);
	}

	/**
	 * Checks for and include predefined classes
	 *
	 * Checks if file exists before including
	 * @param string $class_name
	 */
	public static function autoload($class_name) {
		if(strpos($class_name, 'Swaggg') !== 0) {
			return false;
		}
		switch($class_name) {

			case 'Swaggg':
				$file = DIR_PACKAGES.'/swaggg/models/swaggg.php';
				if(file_exists($file)) {
					include $file;
				}
				return true;

			case 'SwagggHelper':
				$file = DIR_PACKAGES.'/swaggg/helpers/swaggg.php';
				if(file_exists($file)) {
					include $file;
				}
				return true;

			case 'SwagggValidationHelper':
				$file = DIR_PACKAGES.'/swaggg/helpers/swaggg_validation.php';
				if(file_exists($file)) {
					include $file;
				}
				return true;

			case 'SwagggException':
				$file = DIR_PACKAGES.'/swaggg/exceptions/swaggg.php';
				if(file_exists($file)) {
					include $file;
				}
				return true;

			case 'SwagggVote':
				$file = DIR_PACKAGES.'/swaggg/models/swaggg_vote.php';
				if(file_exists($file)) {
					include $file;
				}
				return true;
		}
	}
}

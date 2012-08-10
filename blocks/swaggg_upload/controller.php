<?php
/**
 * @package Swaggg
 * @author Mike Lay <michael.lay.email@gmail.com>
 * @license MIT
 */
defined('C5_EXECUTE') or die('Access Denied.');
/**
 * Produces a form that can be used to add Swaggg hopefuls
 *
 * @package Swaggg
 * @author Mike Lay <michael.lay.email@gmail.com>
 */
class SwagggUploadBlockController extends BlockController {

	protected $btTable = "btSwagggUpload";
	protected $btInterfaceWidth = "400";
	protected $btInterfaceHeight = "400";
	protected $btCacheBlockRecord = true;
	protected $btCacheBlockOutput = true;
	protected $btCacheBlockOutputOnPost = false;
	protected $btCacheBlockOutputForRegisteredUsers = true;
	protected $btCacheBlockOutputLifetime = CACHE_LIFETIME;

	public function getBlockTypeName() {
		return t('Swaggg Upload');
	}

	public function getBlockTypeDescription() {
		return t('Upload your swagggest');
	}

	public function getJavascriptStrings() {
	}

	public function view() {
		$form = Loader::helper('form');
		$token = Loader::helper('validation/token');
		$this->set('form', $form);
		$this->set('token', $token);
	}

	/**
	 * Uploads, validates and saves description and image from form
	 */
	public function action_upload() {
		$response = $this->validateUploadForm();
		if($response !== true) {
			$this->set('errors', $response);
			return;
		}

		$file = SwagggHelper::importFile($_FILES['image']['tmp_name']);

		$swaggg = Swaggg::create();
		$swaggg->set('image_id', $file->getFileID());
		$swaggg->set('description', $this->request('description'));
		$swaggg->save();

		$this->set('swaggg', $swaggg);
		$this->set('upload_successful', true);
	}

	/**
	 * Takes form data from post and validates it
	 *
	 * @see action_upload()
	 */
	protected function validateUploadForm() {
		$val = Loader::helper('validation/form');
		$val->setData($this->post());
		$val->setFiles();
		$val->addRequired('image',t('Image is required.'), ValidationFormHelper::VALID_UPLOADED_IMAGE_REQUIRED);
		$val->addUploadedImage('image',t('Image must be a .png, .gif or .jpg.'));

		/**
		 * There is no file size validation
		 * So we do it here and call invalidate
		 * ourselves
		 */
		$file_size = $_FILES['image']['size'];
		if($file_size > 2097152) {
			$val->invalidate(t('Maximum file size is 2MB'));
		}

		if(strlen($this->post('description') > 4000)) {
			$val->invalidate(t('Description can be a maximum of 4000 characters'));
		}

		if($val->test()) {
			return true;
		}

		return $val->getError()->getList();
	}

}

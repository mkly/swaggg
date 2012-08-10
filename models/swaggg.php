<?php
/**
 * @package Swaggg
 * @author Mike Lay <michael.lay.email@gmail.com>
 * @license MIT
 */
defined('C5_EXECUTE') or die('Access Denied.');
/**
 * Main Swaggg model
 *
 * This will be the model used for Swaggg entries
 *
 * @package Swaggg
 * @author Mike Lay <michael.lay.email@gmail.com>
 * @license MIT
 */
class Swaggg {

	protected static $table = 'pkSwagggs';

	/**
	 * Holds whether any values have been set
	 * after getting object but not saved
	 * to the database
	 * @see isChanged()
	 */
	protected $changed = false;

	/**
	 * Database Fields
	 * @static
	 */
	protected static $field_names = array(
		'id',
		'image_id',
		'description',
		'swaggg_votes',
		'swoggg_votes'
	);

	/**
	 * Database Fields and values
	 */
	protected $fields = array(
		'id' => null,
		'image_id' => null,
		'description' => null,
		'swaggg_votes' => null,
		'swoggg_votes' => null
	);

	/**
	 * Constructor is protected and called by factory methods
	 *
	 * @see assignFieldsFromArray()
	 * @params array $data optional
	 * @see findBySQL()
	 * @see findOneBySQL()
	 * @see findOneByID()
	 * @see findManyBySQL()
	 */
	protected function __construct(array $data = array()) {
		$this->assignFieldsFromArray($data);
		$this->markAsNotChanged();
	}

	/**
	 * Takes an array and fills in the field array
	 *
	 * @param array $data
	 * @return Swagg
	 */
	protected function assignFieldsFromArray(array $data = array()) {
		foreach($this->fields as $field => $value) {
			$this->set($field, $data[$field]);
		}
		return $this;
	}

	/**
	 * Factory method to create a new Swaggg
	 *
	 * Must still call save() to commit to database
	 *
	 * <code>
	 * <?php
	 * Swaggg::create(
	 * 	array(
	 * 		'image_id' => 3,
	 * 		'description' => $desc
	 * 	)
	 * );
	 * ?>
	 *
	 * @static
	 * @params array $data optional initial values
	 * @return Swaggg
	 */
	public static function create(array $data = array()) {
		return new Swaggg($data);
	}

	/**
	 * Factory method to find a single Swaggg by ID
	 *
	 * <code>
	 * <?php
	 * $swaggg = Swaggg::findOneByID($id);
	 * ?>
	 * </code>
	 * @static
	 * @param integer $id
	 * @return Swaggg
	 */
	public static function findOneByID($id) {
		return self::findOneBySQL('WHERE id=?', array($id));
	}

	/**
	 * Factory method to find a single Swaggg by SQL
	 *
	 * <code>
	 * <?php
	 * $swaggg = Swaggg::findOneBySQL('WHERE id = ?', array(1));
	 * ?>
	 * </code>
	 * @static
	 * @param string $where optional SQL WHERE statement
	 * @param array $params optional parametized array of values
	 * @return Swaggg
	 * @see findBySQL()
	 */
	public static function findOneBySQL($where = '', array $params = array()) {
		return self::findBySQL($where, $params, 'GetRow');
	}

	/**
	 * Factory method to find many Swaggs by SQL
	 *
	 * <code>
	 * <?php
	 * $swagggs = Swaggg::findManyBySQL('WHERE description LIKE %?%', array('pizza'));
	 * ?>
	 * @static
	 * @param string $where optional SQL WHERE statement
	 * @param array $params optional parametized array of values
	 * @return Swaggg
	 * @see findBySQL()
	 */
	public static function findManyBySQL($where = '', array $params = array()) {
		return self::findBySQL($where, $params, 'GetAll');
	}

	/**
	 * Factory method to find one or more Swagggs by SQL
	 *
	 * <code>
	 * <?php
	 * Swaggg:findBySQL('WHERE id = ?', array(1));
	 * ?>
	 * @static
	 * @param string $where SQL WHERE statement
	 * @param array $params parametized array of values
	 * @param string $method either GetRow or GetAll
	 * @return mixed either array of Swagggs or one Swaggg
	 */
	public static function findBySQL($where, array $params, $method) {
		$db = Loader::db();
		$fields = implode(',', self::getFieldNames());
		$query = 'SELECT '.$fields.' FROM '.self::$table.' '.$where;

		/**
		 * @see ADODB::GetRow()
		 */
		if($method === 'GetRow') {
			$response = $db->GetRow($query, $params);
			if($response) {
				return new Swaggg($response);
			}
			return false;
		}

		/**
		 * @see ADODB::GetAll()
		 */
		if($method === 'GetMany') {
			$responses = $db->GetAll($query, $params);
			if($responses !== false) {
				$swagggs = array();
				foreach($responses as $response) {
					$swagggs[] = new Swaggg($response);	
				}
				return $swagggs;
			}
			return false;
		}

		throw new SwagggException(
			'Method must be GetRow or GetAll. Received '.$method.'.'
		);
	}

	/**
	 * Gets a field from a Swaggg
	 *
	 * If field is not in $fields property array
	 * throws SwagggException
	 * <code>
	 * <?php
	 * $description = $swaggg->get('description');
	 * ?>
	 * </code>
	 * @param string $field Swaggg field to get
	 * @return mixed
	 */
	public function get($field) {
		if(array_key_exists($field, $this->fields)) {
			return $this->fields[$field];
		}
		throw new SwagggException('Swaggg field "'.$field.'" not found.');
	}

	/**
	 * Get Image File object
	 *
	 * Takes a field name in lowercase and
	 * calls the corresponding method
	 * If method does not exists throws SwagggException
	 *
	 * @param string $field lowercase computed field to get
	 * @return File
	 */
	public function getComputed($field) {
		$args = func_get_args();
		$args = array_slice($args, 1);
		$method = 'computed'.Loader::helper('text')->camelcase($field);
		if(method_exists('Swaggg', $method)) {
			return call_user_func_array(
				array($this, $method),
				$args
			);
		}
		throw new SwagggException('Computed field "'.$field.'" not found.');
	}

	/**
	 * Get Image File object
	 *
	 * @return File
	 */
	protected function computedImage() {
		return File::getByID($this->get('image_id'));
	}

	/**
	 * Get Thumbnail object
	 *
	 * @param $width
	 * @param $height
	 * @param $crop optional
	 * @return StdClass
	 */
	protected function computedThumbnail($width, $height, $crop = false) {
		return Loader::helper('image')->getThumbnail($this->getComputed('image'), $width, $height, $crop);
	}

	/**
	 * Get Percentage of Swaggg Votes
	 * rounded to whole number
	 *
	 * @return integer
	 */
	protected function computedSwagggPercentage() {
		return SwagggHelper::calculatePercentage(
			$this->get('swaggg_votes'),
			$this->get('swoggg_votes')
		);
	}

	/**
	 * Get Percentage of Swoggg Votes
	 * rounded to whole number
	 *
	 * @return integer
	 */
	protected function computedSwogggPercentage() {
		return SwagggHelper::calculatePercentage(
			$this->get('swoggg_votes'),
			$this->get('swaggg_votes')
		);
	}

	/**
	 * Gets $fields as array
	 *
	 * <code>
	 * <?php
	 * $fields = $swaggg->getFieldsAsArray();
	 * ?>
	 * </code>
	 * @return array
	 */
	public function getFieldsAsArray($strip_nulls = false) {
		if($strip_nulls === true) {
			return SwagggHelper::stripNulls($this->fields);
		}
		return $this->fields;
	}

	/**
	 * Gets $fields as json encoded string
	 *
	 * <code>
	 * <?php
	 * $json_encoded = $swaggg->getFieldsAsJson();
	 * ?>
	 * </code>
	 * @return string json data
	 */
	public function getFieldsAsJson() {
		return json_encode($this->fields);
	}

	/**
	 * Gets an array of all teh field names
	 *
	 * @static
	 * @return array
	 */
	public static function getFieldNames() {
		return self::$field_names;
	}

	/**
	 * Sets a field on a Swaggg
	 *
	 * If field is not in $fields property array
	 * throws SwagggException
	 * <code>
	 * $swaggg->set('description', 'Pizza is so good');
	 * </code>
	 * @param string $field field to set
	 * @param mixed $value to set
	 * @return Swaggg
	 */
	public function set($field, $value) {
		if(array_key_exists($field, $this->fields)) {
			$this->fields[$field] = $value;
			$this->markAsChanged();
			return $this;
		}
		throw new SwagggException('Swaggg field "'.$field.'" not found.');
	}

	/**
	 * Indicates if object has been changed
	 * since retrieved from database
	 *
	 * @see set()
	 * return bool $changed
	 */
	public function isChanged() {
		return $this->changed;
	}

	/**
	 * Marks object as being changed since
	 * retreiving from the database
	 *
	 * @retrun Swaggg
	 */
	protected function markAsChanged() {
		$this->changed = true;
		return $this;
	}

	/**
	 * Marks object as not changed
	 *
	 * @return Swaggg
	 */
	protected function markAsNotChanged() {
		$this->changed = false;
		return $this;
	}

	/**
	 * Resets Swaggg to current database
	 *
	 * @return Swaggg
	 */
	public function reset() {
		$db = Loader::db();
		$fields = implode(',',$this->getFieldNames());
		$response = $db->GetRow(
			'SELECT '.$fields.' FROM '.self::$table.' WHERE id=?',
			array($this->get('id'))
		);
		if($response !== false) {
			$this->assignFieldsFromArray($response);
			$this->markAsNotChanged();
			return $this;
		}
		throw new SwagggException(
			'Database error in reset(). Error: '.$db->ErrorMsg()
		);
	}

	/**
	 * Increments total number of swaggg votes
	 *
	 * <code>
	 * <?php
	 * $swaggg->incrementSwaggg(true);
	 * ?>
	 * </code>
	 * @see SwagggVote
	 * @params bool $save_now specified if value should be saved to database immediately
	 */
	public function incrementSwaggg($save_now = false) {
		$this->set('swaggg_votes', $this->get('swaggg_votes') + 1);
		if($save_now === false) {
			return $this;
		}
		if($save_now === true) {
			return $this->save();
		}
		/**
		 * If we get here $save_now isn't boolean.
		 * probably ended up with null or int
		 */
		throw new SwagggException(
			'Must be of type boolean not '.gettype($save_now).'.'
		);
	}

	/**
	 * Increments total number of swaggg votes
	 *
	 * <code>
	 * <?php
	 * $swaggg->incrementSwoggg();
	 * ?>
	 * </code>
	 * @params bool $save_now specified if value should be saved to database immediately
	 */
	public function incrementSwoggg($save_now = false) {
		$this->set('swoggg_votes', $this->get('swoggg_votes') + 1);
		if($save_now === false) {
			return $this;
		}
		if($save_now === true) {
			return $this->save();
		}
		/**
		 * If we get here $save_not isn't boolean.
		 * probably ended up with null or int
		 */
		throw new SwagggException(
			'Must be of type boolean not '.gettype($save_now).'.'
		);
	}

	/**
	 * Creates or Updates database based upon
	 * id field being set
	 *
	 * If save failes $errors will be filled with
	 * the list of errors
	 *
	 * If successful a fresh record will be pulled from
	 * database
	 * <code>
	 * <?php
	 * $swaggg = Swaggg::create(array('description' => 'Best Pizza'));
	 * $swaggg->set('image_id', 44);
	 * if($swaggg->save() === false) {
	 * 	$errors = $this->getErrors();
	 * }
	 * ?>
	 * returns bool indicating if save was successful
	 */
	public function save() {

		$db = Loader::db();

		if($this->get('id') === null) {
			$res = $db->AutoExecute(
				self::$table,
				$this->getFieldsAsArray(true),
				'INSERT'
			);
			/**
			 * Take fresh from db
			 */
			$this->set('id', $db->Insert_ID());
			$this->reset();
			return true;
		}

		if($this->get('id') !== null) {
			$res = $db->AutoExecute(
				self::$table,
				$this->getFieldsAsArray(true),
				'UPDATE',
				'id='.$this->get('id')
			);
			/**
			 * Take fresh from db
			 */
			$this->reset();
			return true;
		}
	}
}

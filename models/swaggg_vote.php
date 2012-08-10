<?php
/**
 * @package Swaggg
 * @author Mike Lay <michael.lay.email@gmail.com>
 * @license MIT
 */
defined('C5_EXECUTE') or die('Access Denied.');

/**
 * SwagggVote Model
 *
 * Uses $_SESSION to track number of Swaggg votes
 * and can return whether or not the user is over
 * the limit of votes for a particular Swagg
 *
 * @package Swaggg
 * @author Mike Lay <michael.lay.email@gmail.com>
 * @license MIT
 */
class SwagggVote {

	public $max_votes = 5;

	/**
	 * Assigns initial values from $_SESSION['swaggg_times_voted']
	 *
	 * If $_SESSION['swaggg_times_voted'] is not set it creates
	 * it as a blank array.
	 * Sets $max_votes based on either gloabl defined constant
	 * in /site/config.php or via property max_votes
	 *
	 * @param integer $swaggg_id Id of Swaggg to count
	 */
	public function __construct($swaggg_id) {
		$this->swaggg_id = $swaggg_id;
		if(isset($_SESSION['swaggg_times_voted']) === false) {
			$_SESSION['swaggg_times_voted'] = array();
		}

		$this->session =& $_SESSION['swaggg_times_voted'];
		/**
		 * if SWAGGG_MAX_VOTES is defined we override max votes
		 */
		if(defined('SWAGGG_MAX_VOTES')) {
			$this->max_votes = SWAGGG_MAX_VOTES;
		}
	}

	/**
	 * Returns the number of times voted from
	 * $_SESSION value
	 *
	 * @return integer
	 */
	public function getTimesVoted() {
		if(isset($this->session[$this->swaggg_id])) {
			return (int) $this->session[$this->swaggg_id];
		}
		return 0;
	}
	
	/**
	 * Increments the number of times voted for a Swaggg
	 *
	 * @return Swaggg
	 */
	public function incrementTimesVoted() {
		if(isset($this->session[$this->swaggg_id])) {
			$this->session[$this->swaggg_id] += 1;
		} else {
			$this->session[$this->swaggg_id] = 0;
		}
		return $this->session[$this->swaggg_id];
	}

	/**
	 * Returns whether or not the user is over
	 * the specified number of votes per swaggg allowed
	 *
	 * @return bool
	 */
	public function isOverTheLimit($limit = false) {
		if($limit === false) {
			$limit = $this->max_votes;
		}
		if(isset($this->session[$this->swaggg_id])) {
			if($this->session[$this->swaggg_id] > $limit) {
				return true;
			}
		}
		return false;
	}
}

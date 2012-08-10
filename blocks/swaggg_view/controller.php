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
class SwagggViewBlockController extends BlockController {

	protected $btTable = "btSwagggView";
	protected $btInterfaceWidth = "400";
	protected $btInterfaceHeight = "400";
	protected $btCacheBlockRecord = true;
	protected $btCacheBlockOutput = false;
	protected $btCacheBlockOutputOnPost = false;
	protected $btCacheBlockOutputForRegisteredUsers = false;
	protected $btCacheBlockOutputLifetime = CACHE_LIFETIME;

	public function getBlockTypeName() {
		return t('Swaggg View');
	}

	public function getBlockTypeDescription() {
		return t('View and pass judgment on swaggg and swoggg.');
	}

	public function on_page_view() {
		$html = Loader::helper('html');
		$this->addFooterItem(
			$html->javascript(
				'jquery.gradienttext.min.js',
				'swaggg'
			)
		);
	}

	public function view() {
		$form = Loader::helper('form');
		$swaggg = $this->getRandomSwaggg();
		$next_swaggg_link =
			Loader::helper('navigation')
			->getLinkToCollection(
				Page::getCurrentPage()
			)
		;

		$this->set('form', $form);
		$this->set('swaggg', $swaggg);
		$this->set('next_swaggg_link', $next_swaggg_link);
	}

	/**
	 * Increments Swaggg unless user has
	 * voted above the limit
	 */
	public function action_swaggg() {
		$id = intval($this->post('swaggg_id'));
		if($id < 1) {
			$this->set('error', t('Invalid Swaggg Id'));
			return true;
		}

		$swaggg = Swaggg::findOneByID($id);
		if($swaggg) {
			$swaggg_vote = new SwagggVote($id);
			$swaggg_vote->incrementTimesVoted();
			/**
			 * If over the limit of votes per person
			 * we return and do not store the vote
			 */
			if($swaggg_vote->isOverTheLimit() === true) {
				$this->set(
					'error',
					t('Easy there killler. Too many votes for that one')
				);
				return true;
			}

			$swaggg->incrementSwaggg(true);

			$this->set('swaggg', $swaggg);
			$this->set('swaggg_incremented', true);
			return true;
		}
		$this->set('error', t('Swaggg ID not found'));
	}
	
	/**
	 * Increments swoggg unless user has
	 * voted about the limit
	 */
	public function action_swoggg() {
		$id = intval($this->post('swaggg_id'));
		if($id < 1) {
			$this->set('error', t('Invalid Swaggg Id'));
			return true;
		}

		$swaggg = Swaggg::findOneByID($id);
		if($swaggg) {
			$swaggg_vote = new SwagggVote($id);
			$swaggg_vote->incrementTimesVoted();
			/**
			 * If over the limit of votes per person
			 * we return and do not store the vote
			 */
			if($swaggg_vote->isOverTheLimit() === true) {
				$this->set(
					'error',
					t('Woah hot shot. I know it sucks, but calm down. Too many votes for that one')
				);
				return true;
			}

			$swaggg->incrementSwoggg(true);

			$this->set('swaggg', $swaggg);
			$this->set('swoggg_incremented', true);
			return true;
		}
		throw new SwagggException('No swaggg found by searched for id: '.$id.'.');
	}

	/**
	 * Gets a random Swaggg for view
	 *
	 * @see view()
	 */
	protected function getRandomSwaggg() {
		return Swaggg::findOneBySQL('ORDER BY RAND() LIMIT 1');
	}
}

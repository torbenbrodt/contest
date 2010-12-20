<?php
require_once(WCF_DIR.'lib/data/contest/jury/ViewableContestJury.class.php');
require_once(WCF_DIR.'lib/data/contest/interaction/ContestInteractionList.class.php');

/**
 * interaction helper
 *
 * @author	Torben Brodt
 * @copyright	2010 easy-coding.de
 * @license	GNU General Public License <http://opensource.org/licenses/gpl-3.0.html>
 * @package	de.easy-coding.wcf.contest
 */
class ContestInteraction {

	/**
	 * @var Contest
	 */
	protected $contest = null;

	/**
	 * @var integer
	 */
	protected $sum = 0;

	/**
	 *
	 */
	public function __construct(Contest $contest) {
		$this->contest = $contest;

		if(!$this->contest->enableInteraction) {
			throw new Exception('contest interaction needs to be enabled.');
		}
	}

	protected $winners = array();

	/**
	 * helper
	 */
	protected function chooseWinner(ContestPrice $price, array &$owners) {

		// there are more prices than participants
		if($this->sum == 0) {
			return null;
		}

		$rand = rand(1, $this->sum);

		$add = 0;
		foreach($owners as $key => $owner) {
			if($random <= $add + $owner->c) {

				// users can only win once
				unset($owners[$key]);
				$this->sum -= $owner->c;

				return $owner;
			}
			$add += $owner->c;
		}

		// error
		return null;
	}

	/**
	 * finish the contest
	 */
	public function finish() {

		if(!($this->contest->state == 'scheduled' && $this->contest->untilTime < time())) {
			throw new Exception('contest still needs to be scheduled but time has to be over.');
		}

		// make a jury instance from the contst owner
		$jury = ContestJury::find($this->contest->contestID, $this->contest->userID, $this->contest->groupID);
		if($jury === null) {
			$jury = ContestJuryEditor::create(
				$this->contest->contestID,
				$this->contest->userID,
				$this->contest->groupID,
				$state = 'accepted'
			);
		}

		$userID = $this->contest->userID;
		if($userID == 0 && $this->contest->groupID > 0) {
			$sql = "SELECT          userID
				FROM            wcf".WCF_N."_user_to_groups
				WHERE           groupID = ".intval($this->contest->groupID);
			$row = WCF::getDB()->getFirstRow($sql);
			$userID = $row['userID'];
		}

		if(!$userID) {
			throw new Exception('cannot determine a user from the ratings will be added.');
		}

		$classIDs = array_keys($this->contest->getClasses());
		$ratingoptionIDs = array_keys(ContestRatingoption::getByClassIDs($classIDs));
		if(empty($ratingoptionIDs)) {
			throw new Exception('cannot determine a ratingoption needed for contest ratings to be added.');
		}

		// get interactions
		$interactionList = new ContestInteractionList();
		$interactionList->sqlConditions .= 'contestID = '.intval($this->contest->contestID);
		$interactionList->sqlLimit = 0;
		$interactionList->readObjects();
		$owners = $interactionList->getObjects();

		foreach($owners as $owner) {
			$this->sum += $owner->c;
		}

		// get prices
		$priceList = new ContestPriceList();
		$priceList->sqlConditions .= 'contestID = '.intval($this->contest->contestID);
		$priceList->sqlLimit = 0;
		$priceList->readObjects();

		$score = 255;

		if($priceList->countObjects() > $score) {
			throw new Exception('maximum numer of '.$score.' prices for per contest is allowed.');
		}

		foreach($priceList->getObjects() as $price) {
		
			// choose a winner
			$owner = $this->chooseWinner($price, $owners);
			
			// error, there are more prices than participants
			if(!$owner) {
				throw new Exception('there are more prices than participants.');
			}

			// create pseudo solution
			$solution = ContestSolutionEditor::create(
				$this->contest->contestID,
				$owner->participantID,
				$message = '',
				$state = 'accepted'
			);
			
			foreach($ratingoptionIDs as $ratingOptionID) {
				// create pseudo rating
				$rating = ContestSolutionRatingEditor::create(
					$solution->solutionID, 
					$ratingOptionID,
					$score,
					$userID
				);
			}
			
			// decrease score
			$score--;
		}

		// close contest state
		$this->contest->getEditor()->update($status = 'finished');
	}
}
?>
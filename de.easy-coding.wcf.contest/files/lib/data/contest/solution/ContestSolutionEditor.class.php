<?php
// wcf imports
require_once(WCF_DIR.'lib/data/contest/solution/ContestSolution.class.php');

/**
 * Provides functions to manage entry solutions.
 *
 * @author	Torben Brodt
 * @copyright 2010 easy-coding.de
 * @license	GNU General Public License <http://opensource.org/licenses/gpl-3.0.html>
 * @package	de.easy-coding.wcf.contest
 */
class ContestSolutionEditor extends ContestSolution {
	/**
	 * Creates a new entry solution.
	 *
	 * @param	integer		$contestID
	 * @param	string		$message
	 * @param	integer		$userID
	 * @param	integer		$groupID
	 * @param	integer		$time
	 * @return	ContestSolutionEditor
	 */
	public static function create($contestID, $message, $userID, $groupID, $time = TIME_NOW) {
		$sql = "INSERT INTO	wcf".WCF_N."_contest_solution
					(contestID, userID, groupID, message, time)
			VALUES		(".intval($contestID).", ".intval($userID).", ".intval($groupID).", '".escapeString($message)."', ".$time.")";
		WCF::getDB()->sendQuery($sql);
		
		// get id
		$solutionID = WCF::getDB()->getInsertID("wcf".WCF_N."_contest_solution", 'solutionID');
		
		// update entry
		$sql = "UPDATE	wcf".WCF_N."_contest
			SET	solutions = solutions + 1
			WHERE	contestID = ".$contestID;
		WCF::getDB()->sendQuery($sql);
		
		return new ContestSolutionEditor($solutionID);
	}
	
	/**
	 * Updates this entry solution.
	 *
	 * @param	string		$message
	 */
	public function update($message) {
		$sql = "UPDATE	wcf".WCF_N."_contest_solution
			SET	message = '".escapeString($message)."'
			WHERE	solutionID = ".$this->solutionID;
		WCF::getDB()->sendQuery($sql);
	}
	
	/**
	 * Deletes this entry solution.
	 */
	public function delete() {
		// update entry
		$sql = "UPDATE	wcf".WCF_N."_contest
			SET	solutions = solutions - 1
			WHERE	contestID = ".$this->contestID;
		WCF::getDB()->sendQuery($sql);
		
		// delete solution
		$sql = "DELETE FROM	wcf".WCF_N."_contest_solution
			WHERE		solutionID = ".$this->solutionID;
		WCF::getDB()->sendQuery($sql);
	}
	
	public static function getStates() {
		return array(
			'unknown',
			'accepted',
			'declined'
		);
	}
}
?>

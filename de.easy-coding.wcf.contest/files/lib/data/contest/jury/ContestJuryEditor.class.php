<?php
// wcf imports
require_once(WCF_DIR.'lib/data/contest/jury/ContestJury.class.php');

/**
 * Provides functions to manage contest jurys.
 *
 * @author	Torben Brodt
 * @copyright	2010 easy-coding.de
 * @license	GNU General Public License <http://opensource.org/licenses/gpl-3.0.html>
 * @package	de.easy-coding.wcf.contest
 */
class ContestJuryEditor extends ContestJury {
	
	/**
	 * Creates a new jury.
	 *
	 * @param	integer		$contestID
	 * @param	integer		$userID
	 * @param	integer		$groupID
	 * @param	string		$state
	 * @return	ContestJuryEditor
	 */
	public static function create($contestID, $userID, $groupID, $state) {
		// check primary keys
		$sql = "SELECT		*
			FROM		wcf".WCF_N."_contest_jury
			WHERE		contestID = ".intval($contestID)."
			AND		userID = ".intval($userID)."
			AND		groupID = ".intval($groupID);
		$row = WCF::getDB()->getFirstRow($sql);
		
		if($row) {
			$update = false;

			if(($row['state'] == 'invited' && $state == 'applied')
			  || ($row['state'] == 'applied' && $state == 'invited')) {
				$state = 'accepted';
				$update = true;
			} else if ($state != $row['state']) {
				$update = true;
			}
			
			$entry = new self($row);
			if($update) {
				$entry->update($contestID, $userID, $groupID, $state);
			}
			return $entry;
		}
	
		$sql = "INSERT INTO	wcf".WCF_N."_contest_jury
					(contestID, userID, groupID, state, time)
			VALUES		(".intval($contestID).", ".intval($userID).", ".intval($groupID).", '".escapeString($state)."', ".TIME_NOW.")";
		WCF::getDB()->sendQuery($sql);
		
		// get new id
		$juryID = WCF::getDB()->getInsertID("wcf".WCF_N."_contest_jury", 'juryID');

		// update entry
		$sql = "UPDATE	wcf".WCF_N."_contest
			SET	jurys = jurys + 1
			WHERE	contestID = ".intval($contestID);
		WCF::getDB()->sendQuery($sql);
		
		// send event
		require_once(WCF_DIR.'lib/data/contest/owner/ContestOwner.class.php');
		require_once(WCF_DIR.'lib/data/contest/event/ContestEventEditor.class.php');
		ContestEventEditor::create($contestID, $userID, $groupID, __CLASS__, array(
			'state' => $state,
			'juryID' => $juryID,
			'owner' => ContestOwner::get($userID, $groupID)->getName()
		));
		
		return new ContestJuryEditor($juryID);
	}
	
	/**
	 * Updates this jury.
	 *
	 * @param	integer		$contestID
	 * @param	integer		$userID
	 * @param	integer		$groupID
	 * @param	string		$state
	 */
	public function update($contestID, $userID, $groupID, $state) {
		$sql = "UPDATE	wcf".WCF_N."_contest_jury
			SET	contestID = ".intval($contestID).", 
				userID = ".intval($userID).", 
				groupID = ".intval($groupID).", 
				state = '".escapeString($state)."'
			WHERE	juryID = ".intval($this->juryID);
		WCF::getDB()->sendQuery($sql);
		
		// send event
		require_once(WCF_DIR.'lib/data/contest/owner/ContestOwner.class.php');
		require_once(WCF_DIR.'lib/data/contest/event/ContestEventEditor.class.php');
		ContestEventEditor::create($contestID, $userID, $groupID, __CLASS__, array(
			'state' => $state,
			'juryID' => $this->juryID,
			'owner' => ContestOwner::get($userID, $groupID)->getName()
		));
	}
	
	/**
	 * Deletes this jury.
	 */
	public function delete() {
		// update entry
		$sql = "UPDATE	wcf".WCF_N."_contest
			SET	jurys = jurys - 1
			WHERE	contestID = ".intval($this->contestID);
		WCF::getDB()->sendQuery($sql);
		
		// delete jury
		$sql = "DELETE FROM	wcf".WCF_N."_contest_jury
			WHERE		juryID = ".intval($this->juryID);
		WCF::getDB()->sendQuery($sql);
	}
	
	/**
	 * 'invited', 'accepted', 'declined', 'applied'
	 */
	public static function getStates($current = '', $flag = 0) {
		require_once(WCF_DIR.'lib/data/contest/state/ContestState.class.php');

		$arr = array($current);
		switch($current) {
			case 'invited':
				if($flag & (ContestState::FLAG_USER | ContestState::FLAG_CREW)) {
					$arr[] = 'accepted';
					$arr[] = 'declined';
				}
			break;
			case 'accepted':
			case 'declined':
			case 'applied':
				if($flag & (ContestState::FLAG_CONTESTOWNER | ContestState::FLAG_CREW)) {
					$arr[] = 'accepted';
					$arr[] = 'declined';
				}
			break;
			default:
				$arr = array();
				$arr[] = 'applied';
			break;
		}
		return ContestState::translateArray($arr);
	}
}
?>

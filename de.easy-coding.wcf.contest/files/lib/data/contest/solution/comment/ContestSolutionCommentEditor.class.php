<?php
// wcf imports
require_once(WCF_DIR.'lib/data/contest/solution/comment/ContestSolutionComment.class.php');

/**
 * Provides functions to manage entry comments.
 *
 * @author	Torben Brodt
 * @copyright	2010 easy-coding.de
 * @license	GNU General Public License <http://opensource.org/licenses/gpl-3.0.html>
 * @package	de.easy-coding.wcf.contest
 */
class ContestSolutionCommentEditor extends ContestSolutionComment {
	/**
	 * Creates a new entry comment.
	 *
	 * @param	integer		$solutionID
	 * @param	string		$comment
	 * @param	integer		$userID
	 * @param	string		$username
	 * @param	integer		$time
	 * @return	ContestSolutionCommentEditor
	 */
	public static function create($solutionID, $comment, $userID, $username, $time = TIME_NOW) {
		$sql = "INSERT INTO	wcf".WCF_N."_contest_solution_comment
					(solutionID, userID, username, comment, time)
			VALUES		(".intval($solutionID).", ".intval($userID).", '".escapeString($username)."', '".escapeString($comment)."', ".$time.")";
		WCF::getDB()->sendQuery($sql);
		
		// get id
		$commentID = WCF::getDB()->getInsertID("wcf".WCF_N."_contest_solution_comment", 'commentID');
		
		// update entry
		$sql = "UPDATE	wcf".WCF_N."_contest_solution
			SET	comments = comments + 1
			WHERE	solutionID = ".intval($solutionID);
		WCF::getDB()->sendQuery($sql);
		
		// sent event
		require_once(WCF_DIR.'lib/data/contest/event/ContestEventEditor.class.php');
		require_once(WCF_DIR.'lib/data/contest/owner/ContestOwner.class.php');
		ContestEventEditor::create($solutionID, $userID, $groupID = 0, __CLASS__, array(
			'commentID' => $commentID,
			'owner' => ContestOwner::get($userID, $groupID)->getName()
		));
		
		return new ContestSolutionCommentEditor($commentID);
	}
	
	/**
	 * Updates this entry comment.
	 *
	 * @param	string		$comment
	 */
	public function update($comment) {
		$sql = "UPDATE	wcf".WCF_N."_contest_solution_comment
			SET	comment = '".escapeString($comment)."'
			WHERE	commentID = ".intval($this->commentID);
		WCF::getDB()->sendQuery($sql);
	}
	
	/**
	 * Deletes this entry comment.
	 */
	public function delete() {
		// update entry
		$sql = "UPDATE	wcf".WCF_N."_contest_solution
			SET	comments = comments - 1
			WHERE	solutionID = ".intval($this->solutionID);
		WCF::getDB()->sendQuery($sql);
		
		// delete comment
		$sql = "DELETE FROM	wcf".WCF_N."_contest_solution_comment
			WHERE		commentID = ".intval($this->commentID);
		WCF::getDB()->sendQuery($sql);
	}
}
?>

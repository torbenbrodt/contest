<?php
// wcf imports
require_once(WCF_DIR.'lib/data/contest/ViewableContestEntryList.class.php');

/**
 * Represents a list of contest entries.
 * 
 * @author	Torben Brodt
 * @copyright	2009 TBR Solutions
 * @license	GNU General Public License <http://opensource.org/licenses/gpl-3.0.html>
 * @package	de.easy-coding.wcf.contest
 */
class ContestEntryOverviewList extends ViewableContestEntryList {
	/**
	 * Creates a new ContestEntryOverviewList object.
	 */
	public function __construct() {
		$this->sqlSelects = 'user_table.*, avatar.*';
		$this->sqlJoins = "LEFT JOIN wcf".WCF_N."_user user_table ON (user_table.userID = contest.userID) LEFT JOIN wcf".WCF_N."_avatar avatar ON (avatar.avatarID = user_table.avatarID)";
	}
}
?>
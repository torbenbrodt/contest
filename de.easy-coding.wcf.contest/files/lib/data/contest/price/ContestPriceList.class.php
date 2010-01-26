<?php
// wcf imports
require_once(WCF_DIR.'lib/data/DatabaseObjectList.class.php');
require_once(WCF_DIR.'lib/data/contest/price/ViewableContestPrice.class.php');

/**
 * Represents a list of contest prices.
 * 
 * @author	Torben Brodt
 * @copyright 2010 easy-coding.de
 * @license	GNU General Public License <http://opensource.org/licenses/gpl-3.0.html>
 * @package	de.easy-coding.wcf.contest
 */
class ContestPriceList extends DatabaseObjectList {
	/**
	 * list of prices
	 * 
	 * @var array<ContestPrice>
	 */
	public $prices = array();

	/**
	 * sql order by statement
	 *
	 * @var	string
	 */
	public $sqlOrderBy = 'contest_price.position';
	
	/**
	 * @see DatabaseObjectList::countObjects()
	 */
	public function countObjects() {
		$sql = "SELECT	COUNT(*) AS count
			FROM	wcf".WCF_N."_contest_price contest_price
			".(!empty($this->sqlConditions) ? "WHERE ".$this->sqlConditions : '');
		$row = WCF::getDB()->getFirstRow($sql);
		return $row['count'];
	}
	
	/**
	 * @see DatabaseObjectList::readObjects()
	 */
	public function readObjects() {
		$sql = "SELECT		".(!empty($this->sqlSelects) ? $this->sqlSelects.',' : '')."
					user_table.username,
					user_table.disableAvatar,  
					user_table.avatarID,  
					user_table.gravatar,
					contest_sponsor.userID, 
					contest_sponsor.groupID, 
					group_table.groupName, 
					avatar_table.*,
					contest_price.*
			FROM 		wcf".WCF_N."_contest_price contest_price
			LEFT JOIN	wcf".WCF_N."_contest_sponsor contest_sponsor
			ON		(contest_sponsor.sponsorID = contest_price.sponsorID)
			LEFT JOIN	wcf".WCF_N."_user user_table
			ON		(user_table.userID = contest_sponsor.userID)
			LEFT JOIN	wcf".WCF_N."_avatar avatar_table
			ON		(avatar_table.avatarID = user_table.avatarID)
			LEFT JOIN	wcf".WCF_N."_group group_table
			ON		(group_table.groupID = contest_sponsor.groupID)
			".$this->sqlJoins."
			".(!empty($this->sqlConditions) ? "WHERE ".$this->sqlConditions : '')."
			".(!empty($this->sqlOrderBy) ? "ORDER BY ".$this->sqlOrderBy : '');
		$result = WCF::getDB()->sendQuery($sql, $this->sqlLimit, $this->sqlOffset);
		while ($row = WCF::getDB()->fetchArray($result)) {
			$this->prices[] = new ViewableContestPrice(null, $row);
		}
	}
	
	/**
	 * @see DatabaseObjectList::getObjects()
	 */
	public function getObjects() {
		return $this->prices;
	}
}
?>

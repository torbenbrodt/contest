<?php

/**
 * the supermod is the crew... the only persons who can apply contests
 *
 * @author	Torben Brodt
 * @copyright	2010 easy-coding.de
 * @license	GNU General Public License <http://opensource.org/licenses/gpl-3.0.html>
 * @package	de.easy-coding.wcf.contest
 */
class ContestCrew {
	const OPTION_NAME = 'mod.contest.isSuperMod';

	/**
	 * is this user member of the contest crew?
	 *
	 * @return	boolean
	 */
	public static function isMember() {
		return WCF::getUser()->getPermission(self::OPTION_NAME);
	}
	
	/**
	 *
	 * @return	integer
	 */
	public static function getOptionID() {
		// get all options and filter options with low priority
		$groupOptionIDs = array();
		$sql = "SELECT		optionName, optionID 
			FROM		wcf".WCF_N."_group_option option_table,
					wcf".WCF_N."_package_dependency package_dependency
			WHERE 		option_table.packageID = package_dependency.dependency
					AND package_dependency.packageID = ".PACKAGE_ID."
					AND optionName = '".self::OPTION_NAME."'
			ORDER BY	package_dependency.priority";
		$row = WCF::getDB()->getFirstRow($sql);
		return $row ? $row['optionID'] : 0;
	}
	
	/**
	 * return user id array
	 */
	public static function getUserIDs() {
		$ids = array();
		$sql = "SELECT		DISTINCT userID
			FROM		wcf".WCF_N."_group_option_value group_option_value
			INNER JOIN	wcf".WCF_N."_user_to_groups user_to_groups
			ON		user_to_groups.groupID = group_option_value.groupID
			AND		group_option_value.optionValue = 1
			WHERE 		optionID = ".self::getOptionID();
		$result = WCF::getDB()->sendQuery($sql);
		while ($row = WCF::getDB()->fetchArray($result)) {
			$ids[] = intval($row['userID']);
		}
		return $ids;
	}
}
?>

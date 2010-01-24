<?php
// wcf imports
require_once(WCF_DIR.'lib/data/user/notification/object/AbstractNotificationObjectType.class.php');
require_once(WCF_DIR.'lib/data/contest/jurytalk/ContestEntryJurytalkNotificationObject.class.php');

/**
 * An implementation of NotificationObjectType to support the usage of an user contest jurytalks as a warning object.
 *
 * @author	Torben Brodt
 * @copyright	2009 TBR Jurytalks
 * @license	GNU General Public License <http://opensource.org/licenses/gpl-3.0.html>
 * @package	de.easy-coding.wcf.contest.commentNotification
 */
class ContestEntryJurytalkNotificationObjectType extends AbstractNotificationObjectType {

	/**
	 * @see NotificationObjectType::getObjectByID()
	 */
	public function getObjectByID($objectID) {
		// get object
		$jurytalk = new ContestEntryJurytalkNotificationObject($objectID);
		if (!$jurytalk->jurytalkID) return null;

		// return object
		return $jurytalk;
	}

	/**
	 * @see NotificationObjectType::getObjectByObject()
	 */
	public function getObjectByObject($object) {
		// build object using its data array
		$jurytalk = new ContestEntryJurytalkNotificationObject(null, $object);
		if (!$jurytalk->jurytalkID) return null;

		// return object
		return $jurytalk;
	}

	/**
	 * @see NotificationObjectType::getObjectsByIDArray()
	 */
	public function getObjectsByIDArray($objectIDArray) {
		$jurytalks = array();
		$sql = "SELECT		*
			FROM 		wcf".WCF_N."_contest_jurytalk
			WHERE 		jurytalkID IN (".implode(',', $objectID).")";
		$result = WCF::getDB()->sendQuery($sql);
		while ($row = WCF::getDB()->fetchArray($result)) {
			$jurytalks[$row['jurytalkID']] = new ContestEntryJurytalkNotificationObject(null, $row);
		}
		
		// return objects
		return $jurytalks;
	}

	/**
	 * @see NotificationObjectType::getPackageID()
	 */
	public function getPackageID() {
		return WCF::getPackageID('de.easy-coding.wcf.contest');
	}
}
?>
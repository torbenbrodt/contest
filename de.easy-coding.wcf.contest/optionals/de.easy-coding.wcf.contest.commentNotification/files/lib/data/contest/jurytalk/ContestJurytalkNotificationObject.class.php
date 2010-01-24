<?php
// wcf imports
require_once(WCF_DIR.'lib/data/user/notification/object/NotificationObject.class.php');
require_once(WCF_DIR.'lib/data/contest/jurytalk/ViewableContestJurytalk.class.php');

/**
 * An implementation of NotificationObject to support the usage of an user contest entry jurytalk as a notification object.
 *
 * @author	Torben Brodt
 * @copyright 2010 easy-coding.de
 * @license	GNU General Public License <http://opensource.org/licenses/gpl-3.0.html>
 * @package	de.easy-coding.wcf.contest.commentNotification
 */
class ContestJurytalkNotificationObject extends ViewableContestJurytalk implements NotificationObject {

	/**
	 * @see ViewableContestJurytalk:__construct
	 */
	public function __construct($jurytalkID, $row = null) {
		// construct from old data if possible
		if (is_object($row)) {
			$row = $row->data;
		}
		parent::__construct($jurytalkID, $row);
	}
		
	/**
	 * @see NotificationObject::getObjectID()
	 */
	public function getObjectID() {
		return $this->jurytalkID;
	}

	/**
	 * @see NotificationObject::getTitle()
	 */
	public function getTitle() {
	}

	/**
	 * @see NotificationObject::getURL()
	 */
	public function getURL() {
		return 'index.php?page=Contest&contestID='.$this->contestID.'&jurytalkID='.$this->jurytalkID.'#jurytalk'.$this->jurytalkID;
	}

	/**
	 * @see NotificationObject::getIcon()
	 */
	public function getIcon() {
		return 'contest';
	}

	/**
	 * @see ViewableContestJurytalk::getFormattedJurytalk()
	 */
	public function getFormattedMessage($outputType = 'text/html') {
		require_once(WCF_DIR.'lib/data/message/bbcode/SimpleMessageParser.class.php');
		return SimpleMessageParser::getInstance()->parse($this->jurytalk);
	}

}
?>

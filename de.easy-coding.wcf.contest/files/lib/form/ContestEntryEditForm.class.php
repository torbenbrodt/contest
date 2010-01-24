<?php
// wcf imports
require_once(WCF_DIR.'lib/form/MessageForm.class.php');
require_once(WCF_DIR.'lib/data/contest/ContestEntryEditor.class.php');
require_once(WCF_DIR.'lib/data/contest/class/ContestClass.class.php');
require_once(WCF_DIR.'lib/data/contest/jury/ContestJury.class.php');
require_once(WCF_DIR.'lib/data/contest/sponsor/ContestSponsor.class.php');
require_once(WCF_DIR.'lib/data/contest/participant/ContestParticipant.class.php');
require_once(WCF_DIR.'lib/data/contest/price/ContestPrice.class.php');
require_once(WCF_DIR.'lib/page/util/menu/PageMenu.class.php');

/**
 * Shows the form for adding new contest entries.
 *
 * @author	Torben Brodt
 * @copyright	2009 TBR Solutions
 * @license	GNU General Public License <http://opensource.org/licenses/gpl-3.0.html>
 * @package	de.easy-coding.wcf.contest
 */
class ContestEntryEditForm extends MessageForm {
	// system
	public $templateName = 'contestEntryEdit';
	public $showPoll = false;
	public $showSignatureSetting = false;
	public $tags = '';
	
	// form parameters
	public $ownerID = 0;
	public $userID = 0;
	public $groupID = 0;
	public $state = '';
	public $from = '';
	public $to = '';
	
	/**
	 * attachment list editor
	 * 
	 * @var	AttachmentListEditor
	 */
	public $attachmentListEditor = null;
	
	/**
	 * list of class ids
	 * 
	 * @var	array<integer>
	 */
	public $classIDArray = array();
	
	/**
	 * list of available classes
	 * 
	 * @var	array<ContestClass>
	 */
	public $availableClasses = array();
	
	/**
	 * list of available groups
	 * 
	 * @var	array<Group>
	 */
	public $availableGroups = array();
	
	/**
	 *
	 */
	public $states = array();
	
	/**
	 * @see Page::readParameters()
	 */
	public function readParameters() {
		parent::readParameters();
		
		// check permissions
		if (MODULE_CONTEST != 1 || !WCF::getUser()->userID) {
			throw new IllegalLinkException();
		}

		if (isset($_REQUEST['contestID'])) $this->contestID = intval($_REQUEST['contestID']);
		$this->entry = new ContestEntryEditor($this->contestID);
		if (!$this->entry->contestID || !$this->entry->isEditable()) {
			throw new IllegalLinkException();
		}
	}
	
	/**
	 * @see Page::readData()
	 */
	public function readData() {
		parent::readData();
		
		// default values
		if (!count($_POST)) {
			$this->subject = $this->entry->subject;
			$this->text = $this->entry->message;
			$this->enableSmilies =  $this->entry->enableSmilies;
			$this->enableHtml = $this->entry->enableHtml;
			$this->enableBBCodes = $this->entry->enableBBCodes;
			$this->userID = $this->entry->userID;
			$this->groupID = $this->entry->groupID;
			$this->state = $this->entry->state;
			$this->from = $this->entry->from;
			$this->to = $this->entry->to;
			$this->classIDArray = array_keys($this->entry->getClasses());
			
			if($this->groupID > 0) {
				$this->ownerID = $this->groupID;
			}
			
			// tags
			if (MODULE_TAGGING) {
				$this->tags = TaggingUtil::buildString($this->entry->getTags(array((count(Language::getAvailableContentLanguages()) > 0 ? WCF::getLanguage()->getLanguageID() : 0))));
			}
		}
		
		$this->availableClasses = ContestClass::getClasses();
		$this->states = ContestEntryEditor::getStates();
		$this->readAvailableGroups();
	}
	
	/**
	 * returns the groups for which the user is admin
	 */
	protected function readAvailableGroups() {
		$sql = "SELECT		usergroup.*, (
						SELECT	COUNT(*)
						FROM	wcf".WCF_N."_user_to_groups
						WHERE	groupID = usergroup.groupID
					) AS members
			FROM 		wcf".WCF_N."_group usergroup
			WHERE		groupID IN (
						SELECT	groupID
						FROM	wcf".WCF_N."_group_leader
						WHERE	leaderUserID = ".WCF::getUser()->userID."
							OR leaderGroupID IN (".implode(',', WCF::getUser()->getGroupIDs()).")
					)
			ORDER BY 	groupName";
		$result = WCF::getDB()->sendQuery($sql);
		while ($row = WCF::getDB()->fetchArray($result)) {
			$this->availableGroups[$row['groupID']] = new Group(null, $row);
		}
	}
	
	/**
	 * @see Form::readFormParameters()
	 */
	public function readFormParameters() {
		parent::readFormParameters();
		
		if (isset($_POST['tags'])) $this->tags = StringUtil::trim($_POST['tags']);
		if (isset($_POST['send'])) $this->send = (boolean) $_POST['send'];
		if (isset($_POST['classIDArray']) && is_array($_POST['classIDArray'])) $this->classIDArray = $_POST['classIDArray'];
		if (isset($_POST['ownerID'])) $this->ownerID = intval($_POST['ownerID']);
		
		if ($this->ownerID == 0) {
			$this->userID = WCF::getUser()->userID;
		} else {
			$this->groupID = $this->ownerID;
		}
	}
	
	/**
	 * @see Form::validate()
	 */
	public function validate() {
		parent::validate();
		
		$this->availableClasses = ContestClass::getClasses();
		
		// validate class ids
		foreach ($this->classIDArray as $key => $classID) {
			if (!isset($this->availableClasses[$classID])) unset($this->classIDArray[$key]);
		}
		
		if(count($this->classIDArray) == 0) {
			throw new UserInputException('classes'); 
		}
		
		if($this->ownerID != 0) {
			$this->readAvailableGroups();
		
			// validate group ids
			if(!array_key_exists($this->ownerID, $this->availableGroups)) {
				throw new UserInputException('ownerID'); 
			}
		}
	}
	
	/**
	 * @see Form::save()
	 */
	public function save() {
		parent::save();
		
		// save entry
		$this->entry->update($this->userID, $this->groupID, $this->subject, $this->text, $this->getOptions(), 
			$this->classIDArray, $this->participants, $this->jurys, $this->prices, $this->sponsors, $this->attachmentListEditor);
		$this->saved();
		
		// save tags
		if (MODULE_TAGGING) {
			$tagArray = TaggingUtil::splitString($this->tags);
			if (count($tagArray)) $entry->updateTags($tagArray);
		}
		
		// forward
		HeaderUtil::redirect('index.php?page=ContestEntry&contestID='.$entry->contestID.SID_ARG_2ND_NOT_ENCODED);
		exit;
	}
	
	/**
	 * @see Page::assignVariables()
	 */
	public function assignVariables() {
		parent::assignVariables();
		
		WCF::getTPL()->assign(array(
			'action' => 'add',
			'userID' => WCF::getUser()->userID,
			'tags' => $this->tags,
			'insertQuotes' => (!count($_POST) && empty($this->text) ? 1 : 0),
			'availableClasses' => $this->availableClasses,
			'availableGroups' => $this->availableGroups,
			'ownerID' => $this->ownerID,
			'classIDArray' => $this->classIDArray,
			'states' => $this->states,
			'state' => $this->state,
			'from' => $this->from,
			'to' => $this->to,
			
		));
	}
	
	/**
	 * @see Page::show()
	 */
	public function show() {
		// set active header menu item
		PageMenu::setActiveMenuItem('wcf.header.menu.user.contest');
		
		// check permission
		WCF::getUser()->checkPermission('user.contest.canUseContest');
		
		if (!MODULE_CONTEST) {
			throw new IllegalLinkException();
		}
		
		// check upload permission
		if (MODULE_ATTACHMENT != 1 || !WCF::getUser()->getPermission('user.contest.canUploadAttachment')) {
			$this->showAttachments = false;
		}
		
		// get attachments editor
		if ($this->attachmentListEditor == null) {
			require_once(WCF_DIR.'lib/data/attachment/MessageAttachmentListEditor.class.php');
			$this->attachmentListEditor = new MessageAttachmentListEditor(array(), 'contestEntry', WCF::getPackageID('de.easy-coding.wcf.contest'), WCF::getUser()->getPermission('user.contest.maxAttachmentSize'), WCF::getUser()->getPermission('user.contest.allowedAttachmentExtensions'), WCF::getUser()->getPermission('user.contest.maxAttachmentCount'));
		}
		
		parent::show();
	}
}
?>
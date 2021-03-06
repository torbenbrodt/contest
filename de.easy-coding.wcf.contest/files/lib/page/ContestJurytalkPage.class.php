<?php
// wcf imports
require_once(WCF_DIR.'lib/page/MultipleLinkPage.class.php');
require_once(WCF_DIR.'lib/data/contest/ViewableContest.class.php');
require_once(WCF_DIR.'lib/data/contest/jurytalk/ContestJurytalkList.class.php');
require_once(WCF_DIR.'lib/page/util/menu/PageMenu.class.php');
require_once(WCF_DIR.'lib/page/util/menu/ContestMenu.class.php');
require_once(WCF_DIR.'lib/data/contest/ContestSidebar.class.php');

/**
 * show/edit jurytalk entries
 * 
 * @author	Torben Brodt
 * @copyright	2010 easy-coding.de
 * @license	GNU General Public License <http://opensource.org/licenses/gpl-3.0.html>
 * @package	de.easy-coding.wcf.contest
 */
class ContestJurytalkPage extends MultipleLinkPage {
	// system
	public $templateName = 'contestJurytalk';
	
	/**
	 * entry id
	 *
	 * @var	integer
	 */
	public $contestID = 0;
	
	/**
	 * entry object
	 * 
	 * @var	ContestJurytalk
	 */
	public $entry = null;
	
	/**
	 * list of jurytalks
	 *
	 * @var ContestJurytalkList
	 */
	public $jurytalkList = null;
	
	/**
	 * jurytalk id
	 * 
	 * @var	integer
	 */
	public $jurytalkID = 0;
	
	/**
	 * jurytalk object
	 * 
	 * @var	ContestJurytalk
	 */
	public $jurytalk = null;
	
	/**
	 * action
	 * 
	 * @var	string
	 */
	public $action = '';
	
	/**
	 * contest sidebar
	 * 
	 * @var	ContestSidebar
	 */
	public $sidebar = null;
	
	/**
	 * @see Form::readParameters()
	 */
	public function readParameters() {
		parent::readParameters();
		
		// get entry
		if (isset($_REQUEST['contestID'])) $this->contestID = intval($_REQUEST['contestID']);
		$this->entry = new ViewableContest($this->contestID);
		if (!$this->entry->contestID || !$this->entry->isJurytalkable()) {
			throw new IllegalLinkException();
		}
		
		// init jurytalk list
		$this->jurytalkList = new ContestJurytalkList();
		$this->jurytalkList->sqlConditions .= 'contest_jurytalk.contestID = '.$this->contestID;
		$this->jurytalkList->sqlOrderBy = 'contest_jurytalk.time DESC';
	}
	
	/**
	 * @see Form::readData()
	 */
	public function readData() {
		parent::readData();
		
		// read objects
		$this->jurytalkList->sqlOffset = ($this->pageNo - 1) * $this->itemsPerPage;
		$this->jurytalkList->sqlLimit = $this->itemsPerPage;
		$this->jurytalkList->readObjects();
		
		// init sidebar
		$this->sidebar = new ContestSidebar($this->entry);
	}
	
	/**
	 * @see MultipleLinkForm::countItems()
	 */
	public function countItems() {
		parent::countItems();
		
		return $this->jurytalkList->countObjects();
	}
	
	/**
	 * @see Form::assignVariables()
	 */
	public function assignVariables() {
		parent::assignVariables();
		
		// display branding
		require_once(WCF_DIR.'lib/util/ContestUtil.class.php');
		ContestUtil::assignVariablesBranding();
		
		// init form
		if ($this->action == 'edit') {
			require_once(WCF_DIR.'lib/form/ContestJurytalkEditForm.class.php');
			new ContestJurytalkEditForm($this->entry);
		}
		else if($this->entry->isJurytalkable()) {
			require_once(WCF_DIR.'lib/form/ContestJurytalkAddForm.class.php');
			new ContestJurytalkAddForm($this->entry);
		}

		$this->sidebar->assignVariables();		
		WCF::getTPL()->assign(array(
			'entry' => $this->entry,
			'contestID' => $this->contestID,
			'userID' => $this->entry->userID,
			'jurytalks' => $this->jurytalkList->getObjects(),
			'templateName' => $this->templateName,
			'allowSpidersToIndexThisPage' => true,
			
			'contestmenu' => ContestMenu::getInstance(),
		));
	}
	
	/**
	 * @see Form::show()
	 */
	public function show() {
		// set active header menu item
		PageMenu::setActiveMenuItem('wcf.header.menu.user.contest');
		
		// set active menu item
		ContestMenu::getInstance()->setContest($this->entry);
		ContestMenu::getInstance()->setActiveMenuItem('wcf.contest.menu.link.jurytalk');
		
		// check permission
		WCF::getUser()->checkPermission('user.contest.canViewContest');
		
		if (!MODULE_CONTEST) {
			throw new IllegalLinkException();
		}
		
		parent::show();
	}
}
?>

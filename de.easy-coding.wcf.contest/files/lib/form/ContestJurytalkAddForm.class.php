<?php
// wcf imports
require_once(WCF_DIR.'lib/form/CaptchaForm.class.php');
require_once(WCF_DIR.'lib/data/contest/Contest.class.php');
require_once(WCF_DIR.'lib/data/contest/jurytalk/ContestJurytalkEditor.class.php');

/**
 * Shows the form for adding contest entry jurytalks.
 *
 * @author	Torben Brodt
 * @copyright 2010 easy-coding.de
 * @license	GNU General Public License <http://opensource.org/licenses/gpl-3.0.html>
 * @package	de.easy-coding.wcf.contest
 */
class ContestJurytalkAddForm extends CaptchaForm {
	// parameters
	public $message = '';
	
	/**
	 * entry editor
	 *
	 * @var Contest
	 */
	public $entry = null;
	
	/**
	 * Creates a new ContestJurytalkAddForm object.
	 *
	 * @param	Contest	$entry
	 */
	public function __construct(Contest $entry) {
		$this->entry = $entry;
		parent::__construct();
	}
	
	/**
	 * @see Page::readParameters()
	 */
	public function readParameters() {
		parent::readParameters();
		
		// get entry
		if (!$this->entry->isJurytalkable()) {
			throw new PermissionDeniedException();
		}
	}
	
	/**
	 * @see Form::readFormParameters()
	 */
	public function readFormParameters() {
		parent::readFormParameters();
		
		// get parameters
		if (isset($_POST['message'])) $this->message = StringUtil::trim($_POST['message']);
	}
	
	/**
	 * @see Form::validate()
	 */
	public function validate() {
		parent::validate();
		
		if (empty($this->message)) {
			throw new UserInputException('message');
		}
		
		if (StringUtil::length($this->message) > WCF::getUser()->getPermission('user.contest.maxSolutionLength')) {
			throw new UserInputException('message', 'tooLong');
		}
	}
	
	/**
	 * @see Form::save()
	 */
	public function save() {
		parent::save();
		
		// save jurytalk
		$jurytalk = ContestJurytalkEditor::create($this->entry->contestID, $this->message, WCF::getUser()->userID, WCF::getUser()->username);
		$this->saved();
		
		// forward
		HeaderUtil::redirect('index.php?page=ContestJurytalk&contestID='.$this->entry->contestID.'&jurytalkID='.$jurytalk->jurytalkID.SID_ARG_2ND_NOT_ENCODED.'#jurytalk'.$jurytalk->jurytalkID);
		exit;
	}
	
	/**
	 * @see Page::assignVariables()
	 */
	public function assignVariables() {
		parent::assignVariables();
		
		WCF::getTPL()->assign(array(
			'message' => $this->message,
			'maxTextLength' => WCF::getUser()->getPermission('user.contest.maxSolutionLength')
		));
	}
}
?>

<?php
// wcf imports
require_once(WCF_DIR.'lib/form/AbstractForm.class.php');
require_once(WCF_DIR.'lib/data/contest/Contest.class.php');
require_once(WCF_DIR.'lib/data/contest/jury/ContestJuryEditor.class.php');
require_once(WCF_DIR.'lib/util/ContestUtil.class.php');

/**
 * Shows the form for inviting contest jurys.
 *
 * @author	Torben Brodt
 * @copyright	2010 easy-coding.de
 * @license	GNU General Public License <http://opensource.org/licenses/gpl-3.0.html>
 * @package	de.easy-coding.wcf.contest
 */
class ContestJuryInviteForm extends AbstractForm {
	/**
	 *
	 * @var array<ContestJury>
	 */
	protected $jurys = array();
	
	/**
	 * contest editor
	 *
	 * @var Contest
	 */
	public $contest = null;
	
	/**
	 * Creates a new ContestJuryAddForm object.
	 *
	 * @param	Contest	$contest
	 */
	public function __construct(Contest $contest) {
		$this->contest = $contest;
		parent::__construct();
	}
	
	/**
	 * @see Page::readParameters()
	 */
	public function readParameters() {
		parent::readParameters();
		
		// get contest
		if (!$this->contest->isOwner()) {
			throw new PermissionDeniedException();
		}
	}
	
	/**
	 * @see Form::readFormParameters()
	 */
	public function readFormParameters() {
		parent::readFormParameters();
		
		if (isset($_POST['jury']) && is_array($_POST['jury'])) $this->jurys = $_POST['jury'];
	}
	
	/**
	 * @see Form::save()
	 */
	public function save() {
		parent::save();
		
		// save jury
		$inserts = '';
		foreach ($this->jurys as $jury) {
			$userID = $groupID = 0;
			switch($jury['type']) {
				case 'user':
					$userID = intval($jury['id']);
				break;
				case 'user':
					$groupID = intval($jury['id']);
				break;
			}
			ContestJury::create($this->contest->contestID, $userID, $groupID, 'invited');
		}
		
		$this->saved();
		
		// forward
		HeaderUtil::redirect('index.php?page=ContestJury&contestID='.$this->contest->contestID.SID_ARG_2ND_NOT_ENCODED);
		exit;
	}
	
	/**
	 * @see Page::assignVariables()
	 */
	public function assignVariables() {
		parent::assignVariables();
		
		WCF::getTPL()->assign(array(
			'jurys' => $this->jurys,
		));
	}
}
?>
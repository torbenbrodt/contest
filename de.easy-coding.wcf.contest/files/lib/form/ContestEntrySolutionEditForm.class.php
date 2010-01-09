<?php
// wcf imports
require_once(WCF_DIR.'lib/form/ContestEntrySolutionAddForm.class.php');

/**
 * Shows the form for editing contest entry solutions.
 *
 * @author	Torben Brodt
 * @copyright	2009 TBR Solutions
 * @license	GNU General Public License <http://opensource.org/licenses/gpl-3.0.html>
 * @package	de.easy-coding.wcf.contest
 */
class ContestEntrySolutionEditForm extends ContestEntrySolutionAddForm {
	/**
	 * solution editor
	 *
	 * @var ContestEntrySolutionEditor
	 */
	public $solutionObj = null;
	
	/**
	 * Creates a new ContestEntrySolutionEditForm object.
	 *
	 * @param	ContestEntrySolution		$solution
	 */
	public function __construct(ContestEntrySolution $solution) {
		$this->solutionObj = $solution->getEditor();
		CaptchaForm::__construct();
	}
	
	/**
	 * @see Page::readParameters()
	 */
	public function readParameters() {
		CaptchaForm::readParameters();
		
		// get solution
		if (!$this->solutionObj->isEditable()) {
			throw new PermissionDeniedException();
		}
	}
	
	/**
	 * @see Form::save()
	 */
	public function save() {
		CaptchaForm::save();
		
		// save solution
		$this->solutionObj->update($this->solution);
		$this->saved();
		
		// forward
		HeaderUtil::redirect('index.php?page=ContestEntry&contestID='.$this->solutionObj->contestID.'&solutionID='.$this->solutionObj->solutionID.SID_ARG_2ND_NOT_ENCODED.'#solution'.$this->solutionObj->solutionID);
		exit;
	}
	
	/**
	 * @see Page::readData()
	 */
	public function readData() {
		parent::readData();
		
		if (!count($_POST)) {
			$this->solution = $this->solutionObj->solution;
		}
	}
}
?>

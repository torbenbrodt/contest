<?php
// wcf imports
require_once(WCF_DIR.'lib/form/ContestSolutionAddForm.class.php');

/**
 * Shows the form for editing contest entry solutions.
 *
 * @author	Torben Brodt
 * @copyright 2010 easy-coding.de
 * @license	GNU General Public License <http://opensource.org/licenses/gpl-3.0.html>
 * @package	de.easy-coding.wcf.contest
 */
class ContestSolutionEditForm extends ContestSolutionAddForm {
	/**
	 * solution editor
	 *
	 * @var ContestSolutionEditor
	 */
	public $solutionObj = null;
	
	/**
	 * Creates a new ContestSolutionEditForm object.
	 *
	 * @param	ContestSolution		$solution
	 */
	public function __construct(ContestSolution $solution) {
		$this->solutionObj = $solution->getEditor();
		AbstractForm::__construct();
	}
	
	/**
	 * @see Page::readParameters()
	 */
	public function readParameters() {
		AbstractForm::readParameters();
		
		// get solution
		if (!$this->solutionObj->isEditable()) {
			throw new PermissionDeniedException();
		}
	}
	
	/**
	 * @see Form::save()
	 */
	public function save() {
		AbstractForm::save();
		
		// save solution
		$this->solutionObj->update($this->message);
		$this->saved();
		
		// forward
		HeaderUtil::redirect('index.php?page=ContestSolution&contestID='.$this->solutionObj->contestID.'&solutionID='.$this->solutionObj->solutionID.SID_ARG_2ND_NOT_ENCODED.'#solution'.$this->solutionObj->solutionID);
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

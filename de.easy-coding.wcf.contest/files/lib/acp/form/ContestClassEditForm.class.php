<?php
// wcf imports
require_once(WCF_DIR.'lib/acp/form/ContestClassAddForm.class.php');

/**
 * Shows class edit form.
 * 
 * @author	Torben Brodt
 * @copyright	2010 easy-coding.de
 * @license	GNU General Public License <http://opensource.org/licenses/gpl-3.0.html>
 * @package	de.easy-coding.wcf.contest
 */
class ContestClassEditForm extends ContestClassAddForm {

	/**
	 * Permission
	 *
	 * @var	string
	 */
	public $neededPermissions = 'admin.contest.canEditClass';

	/**
	 * Languages
	 *
	 * @var array<string>
	 */
	public $languages = array();

	/**
	 * @see Page::readParameters()
	 */
	public function readParameters() {
		parent::readParameters();

		// language
		if (isset($_REQUEST['languageID'])) $this->languageID = intval($_REQUEST['languageID']);
		else $this->languageID = WCF::getLanguage()->getLanguageID();

		// class item
		if (isset($_REQUEST['classID'])) $this->classID = intval($_REQUEST['classID']);
		$this->contestClass = new ContestClassEditor($this->classID);
		if (!$this->contestClass->classID) {
			throw new IllegalLinkException();
		}
	}

	/**
	 * @see Page::readData()
	 */
	public function readData() {
		parent::readData();

		// get all available languages
		$this->languages = WCF::getLanguage()->getLanguageCodes();

		// default values
		if (!count($_POST)) {
			$this->position = $this->contestClass->position;
			$this->parentClassID = $this->contestClass->parentClassID;

			if (WCF::getLanguage()->getLanguageID() != $this->languageID) {
				$language = new Language($this->languageID);
			}
			else {
				$language = WCF::getLanguage();
			}

			$this->topic = $language->get('wcf.contest.class.item.'.$this->contestClass->classID);
			if ($this->topic == 'wcf.contest.class.item.'.$this->contestClass->classID) $this->topic = "";
			$this->text = $language->get('wcf.contest.class.item.'.$this->contestClass->classID.'.description');
			if ($this->text == 'wcf.contest.class.item.'.$this->contestClass->classID.'.description') $this->text = "";
		}
	}

	/**
	 * @see Form::save()
	 */
	public function save() {
		AbstractForm::save();

		// update item
		$this->contestClass->update($this->topic, $this->text, $this->languageID);
		$this->saved();

		// show success message
		WCF::getTPL()->assign('success', true);
	}

	/**
	 * @see Page::assignVariables()
	 */
	public function assignVariables() {
		parent::assignVariables();

		WCF::getTPL()->assign(array(
			'action' => 'edit',
			'classID' => $this->classID,
			'languages' => $this->languages
		));
	}
}
?>

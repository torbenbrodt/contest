<?php
/**
 * NamedUserException shows a (well) styled page with the given error message.
 * 
 * @author	Marcel Werk
 * @copyright	2001-2009 WoltLab GmbH
 * @license	GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @package	com.woltlab.wcf
 * @subpackage	system.exception
 * @category 	Community Framework
 */
class NamedUserException extends UserException {
	/**
	 * Creates a new NamedUserException object.
	 * 
	 * @param	string		$message
	 */
	public function __construct($message) {
		parent::__construct($message);
	}
	
	/**
	 * Shows a styled page with the given error message.
	 */
	public function show() {
		WCF::getTPL()->assign(array(
			'name' => get_class($this),
			'file' => $this->getFile(),
			'line' => $this->getLine(),
			'message' => $this->getMessage(),
			'stacktrace' => $this->getTraceAsString()
		));
		WCF::getTPL()->display('userException');
	}
}
?>
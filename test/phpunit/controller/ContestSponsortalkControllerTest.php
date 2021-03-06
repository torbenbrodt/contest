<?php

/**
 * searches for all xml files and tries to parse xml
 */
class ContestSponsortalkControllerTest extends WCFHTTPTest {
	protected $user = null;
	protected $contest = null;

	/**
	 * Sets up the fixture, for example, opens a network connection.
	 * This method is called before a test is executed.
	 */
	protected function setUp() {
		parent::setUp();
		
		require_once(WCF_DIR.'lib/data/contest/ContestEditor.class.php');
		$this->deleteArray[] = $this->user = $this->createUser();
		$this->deleteArray[] = $this->contest = ContestEditor::create(
			$userID = $this->user->userID,
			$groupID = 0,
			$subject = __METHOD__.' subject',
			$message = __METHOD__.' message',
			$options = array()
		);
	}
	
	public function testContestPage() {
		$user = $this->user;
		$contest = $this->contest;
		
		// save two sponsortalk entries
		require_once(WCF_DIR.'lib/data/contest/sponsortalk/ContestSponsortalkEditor.class.php');
		$this->deleteArray[] = $sponsortalk = ContestSponsortalkEditor::create(
			$contestID = $contest->contestID,
			$sponsortalk = __METHOD__.' sponsortalk #1',
			$userID = $user->userID,
			$username = $user->username
		);
		$this->deleteArray[] = $sponsortalk = ContestSponsortalkEditor::create(
			$contestID = $contest->contestID,
			$sponsortalk = __METHOD__.' sponsortalk #2',
			$userID = $user->userID,
			$username = $user->username
		);

		$raised = false;
		try {
			$this->runHTTP('page=ContestSponsortalk&contestID='.$contest->contestID);
		} catch(Exception $e) {
			$raised = true;
		}
		$this->assertTrue($raised, "user should not be allowed to access a private contest");
		
		// now try with owner
		$this->setCurrentUser($user);
		$this->runHTTP('page=ContestSponsortalk&contestID='.$contest->contestID);
		$this->assertEquals(count(WCF::getTPL()->get('sponsortalks')), 2);
		
		// now try with sponsor member who was invited
		$this->deleteArray[] = $sponsoruser = $this->createUser();
		
		require_once(WCF_DIR.'lib/data/contest/sponsor/ContestSponsorEditor.class.php');
		$this->deleteArray[] = $sponsor = ContestSponsorEditor::create(
			$contestID = $contest->contestID,
			$userID = $sponsoruser->userID,
			$groupID = 0,
			$state = 'invited'
		);
		
		$this->setCurrentUser($sponsoruser);
		$this->runHTTP('page=ContestSponsortalk&contestID='.$contest->contestID);
		
		// invited members should only see first entry
		$this->assertEquals(count(WCF::getTPL()->get('sponsortalks')), 1);
		
		// accepted members should have 2 entries
		$sponsor->update($contestID, $userID, $groupID, 'accepted');
		$this->runHTTP('page=ContestSponsortalk&contestID='.$contest->contestID);
		$this->assertEquals(count(WCF::getTPL()->get('sponsortalks')), 2);
	}
}
?>

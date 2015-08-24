<?php
/**
 * API module to send MassMessages
 *
 * @file
 * @ingroup API
 * @author Ike Hecht
 * @license http://www.gnu.org/copyleft/gpl.html GNU General Public License 2.0 or later
 */
class ApiMassMessageEmail extends ApiMassMessage {

	public function execute() {
		if ( !$this->getUser()->isAllowed( 'massmessage') ) {
			$this->dieUsageMsg( 'permissiondenied' );
		}

		$data = $this->extractRequestParams();

		$status = new Status();
		MassMessage::verifyData( $data, $status );
		if ( !$status->isOK() ) {
			$this->dieStatus( $status );
		}

		//This is the only significant line changed from the parent
		$count = MassMessageEmail::submit( $this->getContext(), $data );

		$this->getResult()->addValue(
			null,
			$this->getModuleName(),
			array( 'result' => 'success', 'count' => $count )
		);
	}

	/**
	 * @see ApiBase::getExamplesMessages()
	 */
	protected function getExamplesMessages() {
		return array(
			'action=massmessageemail&spamlist=Signpost%20Spamlist&subject=New%20Signpost' .
			'&message=Please%20read%20it&token=TOKEN'
				=> 'apihelp-massmessage-example-1',
		);
	}

	public function getHelpUrls() {
		return array();
	}

}

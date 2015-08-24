<?php

/**
 * Form to allow users to send email messages
 * to a lot of users at once.
 *
 * @file
 * @author Ike Hecht
 * @license http://www.gnu.org/copyleft/gpl.html GNU General Public License 2.0 or later
 */
class SpecialMassMessageEmail extends SpecialMassMessage {

	public function __construct() {
		// Call grandparent constructor. Less than ideal.
		/** @todo Create new permission? */
		SpecialPage::__construct( 'MassMessageEmail', 'massmessage' );
	}

	/**
	 * Callback function
	 * Does some basic verification of data
	 * Decides whether to show the preview screen or the submitted message
	 *
	 * @param $data Array
	 * @return Status|bool
	 */
	public function callback( array $data ) {

		MassMessage::verifyData( $data, $this->status );

		// Die on errors.
		if ( !$this->status->isOK() ) {
			$this->state = 'form';
			return $this->status;
		}

		if ( $this->state === 'submit' ) {
			// This is the only line that was changed from the parent.
			$this->count = MassMessageEmail::submit( $this->getUser(), $data );
			return $this->status;
		} else { // $this->state can only be 'preview' here
			$this->preview( $data );
			return false; // No submission attempted
		}
	}
}

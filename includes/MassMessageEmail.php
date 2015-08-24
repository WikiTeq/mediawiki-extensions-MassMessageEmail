<?php

/**
 * Some core functions needed by the extension.
 *
 * @file
 * @author Ike Hecht
 * @license http://www.gnu.org/copyleft/gpl.html GNU General Public License 2.0 or later
 */

class MassMessageEmail extends MassMessage {

	/**
	 * Send out the message!
	 * Note that this function does not perform validation on $data
	 *
	 * @param User $user who the message was from (for logging)
	 * @param array $data
	 * @return int number of pages delivered to
	 */
	public static function submit( User $user, array $data ) {
		$spamlist = self::getSpamlist( $data['spamlist'] );

		// Get the array of pages to deliver to.
		$pages = MassMessageTargets::getTargets( $spamlist );

		// Log it.
		self::logToWiki( $spamlist, $user, $data['subject'] );

		// Insert it into the job queue.
		$params = array(
			'data' => $data,
			'pages' => $pages,
			'class' => 'MassMessageJob',
		);

		/* BEGIN MASSMESSAGEEMAIL CODE */
		$params['class'] = 'MassMessageEmailJob';
		$job = new MassMessageSubmitJob( $spamlist, $params );
		/* END MASSMESSAGEEMAIL CODE */

		JobQueueGroup::singleton()->push( $job );

		return count( $pages );
	}
}

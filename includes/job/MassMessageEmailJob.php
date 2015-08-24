<?php
/**
 *
 * @file
 * @ingroup JobQueue
 * @author Ike Hecht
 * @license http://www.gnu.org/copyleft/gpl.html GNU General Public License 2.0 or later
 */
class MassMessageEmailJob extends MassMessageJob {

	public function __construct( Title $title, array $params, $id = 0 ) {
		// Copied from parent
		if ( isset( $params['title'] ) ) {
			$title = Title::newFromText( $params['title'] );
		}

		// Call grandparent constructor. Less than ideal.
		Job::__construct( 'MassMessageEmailJob', $title, $params, $id );
	}

	/**
	 * Send a message to a user
	 * Modified from the TranslationNotification extension
	 *
	 * @return bool
	 */
	protected function sendMessage() {
		$title = $this->normalizeTitle( $this->title );
		if ( $title === null ) {
			return true; // Skip it
		}

		$this->title = $title;

		if ( $this->isOptedOut( $this->title ) ) {
			$this->logLocalSkip( 'skipoptout' );
			return true; // Oh well.
		}

		// If we're sending to a User:/User talk: page, make sure the user exists.
		// Redirects are automatically followed in getLocalTargets
		if ( $title->getNamespace() === NS_USER || $title->getNamespace() === NS_USER_TALK ) {
			$user = User::newFromName( $title->getBaseText() );
			if ( !$user || !$user->getId() ) { // Does not exist
				$this->logLocalSkip( 'skipnouser' );
				return true;
			}
		}
		/* BEGIN MASSMESSAGEEMAIL CODE */
		if ( $title->getNamespace() == NS_USER || $title->getNamespace() == NS_USER_TALK ) {
			if ( $user->canReceiveEmail() ) {
				// Generate plain text ...
				$text = $this->makeText();
				// Make sure we don't send relative links in the email. Shouldn't that be a ParserOption?
				global $wgArticlePath, $wgServer;
				$oldArticlePath = $wgArticlePath;
				$wgArticlePath = $wgServer . $wgArticlePath;
				$parser = new Parser();
				$parserOutput = $parser->parse( $text, $this->getTitle(), new ParserOptions() );
				// ... and also generate HTML from the wikitext, which really makes sense since
				// we're sending an email
				$html = $parserOutput->getText();
				$status = $user->sendMail( $this->params['subject'], array( 'text' => $text, 'html' => $html ) );
				$wgArticlePath = $oldArticlePath;
				if ( !$status->isGood() ) {
					/** @todo This should really be sending a code - not a message */
					$this->logLocalFailure( $status->getMessage() );
				}
				return true;
			}
		}
		/* END MASSMESSAGEEMAIL CODE */

		// If the page is using a different discussion system, handle it specially
		if ( class_exists( 'LqtDispatch' ) && LqtDispatch::isLqtPage( $title ) ) {
			// This is the same check that LQT uses internally
			$this->addLQTThread();
		} elseif ( $title->hasContentModel( 'flow-board' )
			// But it can't be a Topic: page, see bug 71196
			&& defined( 'NS_TOPIC' ) && !$title->inNamespace( NS_TOPIC ) ) {
			$this->addFlowTopic();
		} else {
			$this->editPage();
		}

		return true;
	}
}

<?php
/**
 * JobQueue class to queue other jobs
 *
 *
 * @file
 * @ingroup JobQueue
 * @author Ike Hecht
 * @license http://www.gnu.org/copyleft/gpl.html GNU General Public License 2.0 or later
 */
class MassMessageEmailSubmitJob extends Job {

	public function __construct( Title $title, array $params, $id = 0 ) {
		parent::__construct( 'MassMessageEmailSubmitJob', $title, $params, $id );
	}

	/**
	 * Queue some more jobs!
	 *
	 * @return bool
	 */
	public function run() {
		$data = $this->params['data'];
		$pages = $this->params['pages'];
		$jobsByTarget = array();

		foreach ( $pages as $page ) {
			$title = Title::newFromText( $page['title'] );
			// Store the title as plain text to avoid namespace/interwiki prefix
			// collisions, see bug 57464 and 58524
			$data['title'] = $page['title'];

			/* BEGIN MASSMESSAGEEMAIL CODE */
			$jobsByTarget[$page['wiki']][] = new MassMessageEmailJob( $title, $data );
			/* END MASSMESSAGEEMAIL CODE */
		}

		foreach ( $jobsByTarget as $wiki => $jobs ) {
			JobQueueGroup::singleton( $wiki )->push( $jobs );
		}

		return true;
	}

}

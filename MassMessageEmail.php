<?php
/*
 * Adds email capability to the MassMessage extension
 * See https://mediawiki.org/wiki/Extension:MassMessage
 *
 * @file
 * @ingroup Extensions
 * @author Ike Hecht
 * @license http://www.gnu.org/copyleft/gpl.html GNU General Public License 2.0 or later
 */

if ( !defined( 'MEDIAWIKI' ) ) {
	exit;
}

if ( !class_exists( 'MassMessage' ) ) {
	// This extension was tested with MassMessage version 0.2.0
	throw new Exception( 'Requires MassMessage extension' );
}

$wgExtensionCredits['specialpage'][] = array(
	'path' => __FILE__,
	'name' => 'MassMessageEmail',
	'author' => 'Ike Hecht',
	'url' => 'https://www.mediawiki.org/wiki/Extension:MassMessageEmail',
	'descriptionmsg' => 'massmessageemail-desc',
	'version' => '0.1.0',
	'license-name' => 'GPL-2.0+',
);

$wgMessagesDirs['MassMessageEmail'] = __DIR__ . '/i18n';
$wgExtensionMessagesFiles['MassMessageEmailAlias'] = __DIR__ . '/MassMessageEmail.alias.php';

$wgAutoloadClasses['SpecialMassMessageEmail'] = __DIR__ . '/includes/SpecialMassMessageEmail.php';
$wgAutoloadClasses['MassMessageEmail'] = __DIR__ . '/includes/MassMessageEmail.php';
$wgAutoloadClasses['MassMessageEmailJob'] = __DIR__ . '/includes/job/MassMessageEmailJob.php';
$wgAutoloadClasses['MassMessageEmailSubmitJob'] = __DIR__ . '/includes/job/MassMessageEmailSubmitJob.php';

$wgSpecialPages['MassMessageEmail'] = 'SpecialMassMessageEmail';

$wgJobClasses['MassMessageEmailJob'] = 'MassMessageEmailJob';
$wgJobClasses['MassMessageEmailSubmitJob'] = 'MassMessageEmailSubmitJob';

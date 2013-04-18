<?php 
require_once("../../../../../config.php");
require_once("{$CFG->dirroot}/course/format/mashup/classes/MashupDatabaseHelper.php");
require_once("{$CFG->dirroot}/course/format/mashup/classes/omdl/OmdlExporter.php");
$id = required_param('courseId', PARAM_INT);
require_login($id);

$PAGE->set_url('/course/format/mashup/classes/actions/exportOmdlAction.php', array('id' => $id));

//TODO add in the has_capability() check so only course_creator/teacher can do this

if (isset($_GET['courseId']) && isset($_GET['pageId'])){
	$courseId = $_GET['courseId'];
	$pageId = $_GET['pageId'];
	$omdlExporter = new OMDLExporter($courseId, $pageId);
	// Send the headers

	header('Content-type: text/xml');
	header('Pragma: public');
	header('Cache-control: private');
	header('Expires: -1');
	header('Content-Disposition: attachment; filename="moodle-mashup-page-'.$pageId.'.xml"');
	
	echo $omdlExporter->toXml();
}		
?>
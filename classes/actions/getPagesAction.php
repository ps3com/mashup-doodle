<?php 
require_once("../../../../../config.php");
require_once("{$CFG->dirroot}/course/format/mashup/classes/MashupDatabaseHelper.php");
require_once("{$CFG->dirroot}/course/format/mashup/classes/InstanceGenerator.php");
$id = required_param('courseId', PARAM_INT);
require_login($id);
$PAGE->set_url('/course/format/mashup/classes/actions/getPagesAction.php', array('id' => $id));
//TODO add in the has_capability() check so only course_creator/teacher can do this

if (isset($_GET['courseId']) ){
	$json = array();
	$courseId = $_GET['courseId'];
	$mashupDatabaseHelper = new MashupDatabaseHelper();
	$pages = $mashupDatabaseHelper->getPagesForCourse($courseId);
	if(sizeof($pages)>0){
		foreach($pages as $page) {
			array_push($json, $page->toJson());
		}
	}
	else{
		$page = new MashupPage(null, "default", $courseId, $pageLayout=3);
		$pageId = $page->serialize();
		array_push($json, $page->toJson());
	}
	echo json_encode($json);
}		
?>
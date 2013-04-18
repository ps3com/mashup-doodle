<?php 
require_once("../../../../../config.php");
require_once("{$CFG->dirroot}/course/format/mashup/classes/InstanceGenerator.php");
$id = required_param('courseId', PARAM_INT);
require_login($id);
$PAGE->set_url('/course/format/mashup/classes/actions/newPageAction.php', array('id' => $id));


//TODO add in the has_capability() check so only course_creator/teacher can do this

if (isset($_GET['pageName']) && isset($_GET['pageLayout']) && isset($_GET['courseId']) ){
	//$page="";
	$pageName = $_GET['pageName'];
	$pageLayout = $_GET['pageLayout'];
	$courseId = $_GET['courseId'];
	$mashupPage = new MashupPage(null, $pageName, $courseId, $pageLayout);
	$pageId = $mashupPage->serialize();
	$page = $mashupPage->toJson();
	echo json_encode($page);
}		
?>
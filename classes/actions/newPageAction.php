<?php 
require_once("../../../../../config.php");
require_once("{$CFG->dirroot}/course/format/mashup/classes/InstanceGenerator.php");
$id = required_param('courseId', PARAM_INT);
require_login($id);
$PAGE->set_url('/course/format/mashup/classes/actions/newPageAction.php', array('id' => $id));

// set permissions to prevent students etc to execute this
$course = $PAGE->course;
$context = get_context_instance(CONTEXT_COURSE, $course->id);
if (!has_capability('moodle/course:manageactivities', $context)) {
	echo "You do not have permission to complete this action";
}
else{
	if (isset($_GET['pageName']) && isset($_GET['pageLayout']) && isset($_GET['courseId']) ){
		//$page="";
		$pageName =  htmlspecialchars($_GET['pageName'], ENT_QUOTES);
		$pageLayout = $_GET['pageLayout'];
		$courseId = $_GET['courseId'];
		$mashupPage = new MashupPage(null, $pageName, $courseId, $pageLayout);
		$pageId = $mashupPage->serialize();
		$page = $mashupPage->toJson();
		echo json_encode($page);
	}		
}
?>
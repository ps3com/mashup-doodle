<?php 
require_once("../../../../../config.php");
require_once("{$CFG->dirroot}/course/format/mashup/classes/MashupDatabaseHelper.php");
$id = required_param('courseId', PARAM_INT);
require_login($id);
$PAGE->set_url('/course/format/mashup/classes/actions/removeWidgetAction.php', array('id' => $id));

// set permissions to prevent students etc to execute this
$course = $PAGE->course;
$context = get_context_instance(CONTEXT_COURSE, $course->id);
if (!has_capability('moodle/course:manageactivities', $context)) {
	echo "You do not have permission to complete this action";
}
else{
	if (isset($_POST['courseId']) && isset($_POST['widgetId'])){
		$mashupDatabaseHelper = new MashupDatabaseHelper();
		$result="";
		$courseId = $_POST['courseId'];
		$widgetId = $_POST['widgetId'];
		$result.=$mashupDatabaseHelper->deleteWidget($widgetId);
		echo $result;
	}	
}	
?>
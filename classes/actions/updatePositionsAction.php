<?php 
require_once("../../../../../config.php");
require_once("{$CFG->dirroot}/course/format/mashup/classes/MashupDatabaseHelper.php");
$id = required_param('courseId', PARAM_INT);
require_login($id);
$PAGE->set_url('/course/format/mashup/classes/actions/updatePositionsAction.php', array('id' => $id));


//TODO add in the has_capability() check so only course_creator/teacher can do this

if (isset($_POST['courseId']) && isset($_POST['dataEnv'])){
	$mashupDatabaseHelper = new MashupDatabaseHelper();
	$result="";
	$courseId = $_POST['courseId'];
	$json = $_POST['dataEnv'];
	$gridsterWidgets = json_decode($json);
	foreach ($gridsterWidgets as $gridsterWidget){
		if(!$mashupDatabaseHelper->updateWidget($gridsterWidget, $courseId)){
			$result.= $mashupDatabaseHelper->get_error() ."\n";
		}
	}
	echo $result;
}		
?>
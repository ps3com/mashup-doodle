<?php 
require_once("../../../../../config.php");
require_once("{$CFG->dirroot}/course/format/mashup/classes/InstanceGenerator.php");
$id = required_param('courseId', PARAM_INT);
require_login($id);
$PAGE->set_url('/course/format/mashup/classes/actions/newInstanceAction.php', array('id' => $id));


//TODO add in the has_capability() check so only course_creator/teacher can do this

if (isset($_GET['url']) && isset($_GET['title']) && isset($_GET['widgetType']) && isset($_GET['courseId'])  && isset($_GET['pageId']) ){

	$layout="";
	$url = $_GET['url'];
	$title = $_GET['title'];
	$widgetType = $_GET['widgetType'];
	$courseId = $_GET['courseId'];
	$pageId = $_GET['pageId'];
	$moodleWidget = new MoodleWidget(null, $courseId, $url, $title, $widgetType, 1, 1, 1, 1, $pageId);
	$instanceGenerator = new InstanceGenerator($moodleWidget->serialize());
	$layout.=$instanceGenerator->getInstanceMarkup($moodleWidget);
	echo $layout;
}		
?>
<?php 
require_once("../../../../../config.php");
require_once("{$CFG->dirroot}/course/format/mashup/classes/InstanceGenerator.php");
require_once("{$CFG->dirroot}/course/format/mashup/classes/connectors/opensocial/ShindigConnectorService.php");
$id = required_param('courseId', PARAM_INT);
require_login($id);
$PAGE->set_url('/course/format/mashup/classes/actions/newInstanceAction.php', array('id' => $id));

// set permissions to prevent students etc to execute this
$course = $PAGE->course;
$context = get_context_instance(CONTEXT_COURSE, $course->id);
if (!has_capability('moodle/course:manageactivities', $context)) {
	echo "You do not have permission to complete this action";
}
else{
	if (isset($_GET['url']) && isset($_GET['widgetType']) && isset($_GET['courseId'])  && isset($_GET['pageId']) ){	
		$errors="";
		$errorFlag = false;
		$json = array();
		$url = $_GET['url'];
		$title = "unknown";
		if(isset($_GET['title'])){
			$title = $_GET['title'];
		}
		$widgetType = $_GET['widgetType'];
		$courseId = $_GET['courseId'];
		$pageId = $_GET['pageId'];
		if($widgetType == 2){
			$shindigconnection = new ShindigConnectorService($CFG->mashup_shindig_url);
			$metadataTitle = $shindigconnection->parseMetadataForTitle($url);
			if(!strncmp($metadataTitle, "Error", strlen("Error"))){
				$errors .= $metadataTitle;
				$errorFlag = true;
			}else{
				$title = $metadataTitle;
			}
		}
		if(!$errorFlag){
			$moodleWidget = new MoodleWidget(null, $courseId, $url, $title, $widgetType, 1, 1, 1, 1, $pageId);
			$instanceGenerator = new InstanceGenerator($moodleWidget->serialize());
			$instance = $instanceGenerator->getWidgetInstance($moodleWidget, $courseId);
			$moodleWidget->setUrl($instance->getUrl());
			array_push($json, $moodleWidget->toJson());
			echo json_encode($json);
		}
		else{
			echo $errors;
		}
		
	}
}	
?>
<?php 
require_once("../../../../../config.php");
require_once("{$CFG->dirroot}/course/format/mashup/classes/MashupDatabaseHelper.php");
require_once("{$CFG->dirroot}/course/format/mashup/classes/InstanceGenerator.php");
$id = required_param('courseId', PARAM_INT);
require_login($id);
$PAGE->set_url('/course/format/mashup/classes/actions/getInstancesForPageAction.php', array('id' => $id));

if (isset($_GET['pageId']) ){
	$json = array();	
	$pageId = $_GET['pageId'];
	$mashupDatabaseHelper = new MashupDatabaseHelper();
	$pageLayout = $mashupDatabaseHelper->getLayoutForPage($pageId);	
	$layoutData = array('layout' => $pageLayout);	
	array_push($json, $layoutData);
	$persistedWidgets = $mashupDatabaseHelper->getWidgetsForPage($pageId);
	foreach ($persistedWidgets as $persistedWidget){
		$instanceGenerator = new InstanceGenerator();
		try{	
			$instance = $instanceGenerator->getWidgetInstance($persistedWidget, $id);
			if($instance instanceof WidgetInstance){
				$persistedWidget->setUrl($instance->getUrl());
				array_push($json, $persistedWidget->toJson());
			}				
		}
		catch (Exception $e){
			//
		}
	}	
	echo json_encode($json);
}		
?>
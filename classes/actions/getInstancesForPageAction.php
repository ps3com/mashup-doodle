<?php 
require_once("../../../../../config.php");
require_once("{$CFG->dirroot}/course/format/mashup/classes/MashupDatabaseHelper.php");
require_once("{$CFG->dirroot}/course/format/mashup/classes/InstanceGenerator.php");
$id = required_param('courseId', PARAM_INT);
require_login($id);
$PAGE->set_url('/course/format/mashup/classes/actions/getInstancesForPageAction.php', array('id' => $id));

//TODO add in the has_capability() check so only course_creator/teacher can do this

if (isset($_GET['mashupPageId']) ){

	$layout="";
	$mashupPageId = $_GET['mashupPageId'];
	$mashupDatabaseHelper = new MashupDatabaseHelper();	
	$pageLayout = $mashupDatabaseHelper->getLayoutForPage($mashupPageId);
	
	$layout = '<div class="gridster" style="width:100%" data-mashup-cols="'.$pageLayout.'">'.PHP_EOL;
	$layout.='<ul>'.PHP_EOL;
	$persistedWidgets = $mashupDatabaseHelper->getWidgetsForPage($mashupPageId);
	foreach ($persistedWidgets as $persistedWidget){
		$instanceGenerator = new InstanceGenerator($persistedWidget->getId());
		$layout.=$instanceGenerator->getInstanceMarkup($persistedWidget);
	}
	$layout.='</ul>'.PHP_EOL;
	$layout.='</div>'.PHP_EOL;
	echo $layout;
}		
?>
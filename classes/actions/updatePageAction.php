<?php 
require_once("../../../../../config.php");
require_once("{$CFG->dirroot}/course/format/mashup/classes/MashupPageHelper.php");
$id = required_param('courseId', PARAM_INT);
require_login($id);
$PAGE->set_url('/course/format/mashup/classes/actions/updatePageAction.php', array('id' => $id));

// set permissions to prevent students etc to execute this
$course = $PAGE->course;
$context = get_context_instance(CONTEXT_COURSE, $course->id);
if (!has_capability('moodle/course:manageactivities', $context)) {
	echo "You do not have permission to complete this action";
}
else{
	if (isset($_GET['pageName']) && isset($_GET['pageLayout']) && isset($_GET['courseId']) && isset($_GET['pageId'])){
		$newPageName =  htmlspecialchars($_GET['pageName'], ENT_QUOTES);
		$newLayout = $_GET['pageLayout'];
		$courseId = $_GET['courseId'];
		$pageId = $_GET['pageId'];
		// create a page object and populate it from the DB
		$mashupPage = new MashupPage($pageId);
		$mashupPage->deserialize();
		
		// Remember the original page layout & title
		$originalLayout = $mashupPage->getPageLayout(); 
		$originalTitle = $mashupPage->getTitle();
		
		// only continue if either the layout or title has changed
		if($originalTitle != $newPageName || $originalLayout != $newLayout){
			// update this page with new settings
			$mashupPage->setTitle($newPageName);
			$mashupPage->setPageLayout($newLayout);
			// commit changes back to DB
			$pageId = $mashupPage->reserialize();
		}	
		// now update the widget positions
		if ($originalLayout != $newLayout){
			$mashupPageHelper = new MashupPageHelper();
			if($originalLayout==2 && $newLayout==1){
				// move all from column 2 to column 1
				$mashupPageHelper->moveWidgetsToNewColumn($pageId, 2, 1);
			}
			else if($originalLayout==3 && $newLayout==1){
				// move all from column 3 to column 1
				$mashupPageHelper->moveWidgetsToNewColumn($pageId, 3, 1);
				// move all from column 2 to column 1
				$mashupPageHelper->moveWidgetsToNewColumn($pageId, 2, 1);
			}
			else if($originalLayout==3 && $newLayout==2){
				// move all from column 3 to column 1
				$mashupPageHelper->moveWidgetsToNewColumn($pageId, 3, 1);
			}
			else if($originalLayout==4 && $newLayout==1){
				// move all from column 4 to column 1
				$mashupPageHelper->moveWidgetsToNewColumn($pageId, 4, 1);
				// move all from column 3 to column 1
				$mashupPageHelper->moveWidgetsToNewColumn($pageId, 3, 1);
				// move all from column 2 to column 1
				$mashupPageHelper->moveWidgetsToNewColumn($pageId, 2, 1);
			}
			else if($originalLayout==4 && $newLayout==2){
				// move all from column 4 to column 1
				$mashupPageHelper->moveWidgetsToNewColumn($pageId, 4, 1);
				// move all from column 3 to column 1
				$mashupPageHelper->moveWidgetsToNewColumn($pageId, 3, 1);
			}
			else if($originalLayout==4 && $newLayout==3){
				// move all from column 4 to column 1
				$mashupPageHelper->moveWidgetsToNewColumn($pageId, 4, 1);
			}
		}
	}
	$page = $mashupPage->toJson();
	echo json_encode($page);
}
?>
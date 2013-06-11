<?php
require_once("../../../../config.php");
global $CFG;
echo '
var mashup_properties = {
		htmlParent: "mashup-content", // the html object to add the mashup to
		courseId: null,			   // courseId - you will have to set this before calling MashupEngine.init()
		importOMDLPageUrl: "'.$CFG->wwwroot.'/course/format/mashup/classes/actions/importOmdlAction.php",
		exportOMDLPageUrl: "'.$CFG->wwwroot.'/course/format/mashup/classes/actions/exportOmdlAction.php",
		getWidgetsForPageUrl: "'.$CFG->wwwroot.'/course/format/mashup/classes/actions/getInstancesForPageAction.php",
		addNewPageUrl: "'.$CFG->wwwroot.'/course/format/mashup/classes/actions/newPageAction.php",
		getPagesUrl: "'.$CFG->wwwroot.'/course/format/mashup/classes/actions/getPagesAction.php",
		removePageUrl: "'.$CFG->wwwroot.'/course/format/mashup/classes/actions/removePageAction.php",
		removeWidgetUrl: "'.$CFG->wwwroot.'/course/format/mashup/classes/actions/removeWidgetAction.php",
		newWidgetInstanceUrl: "'.$CFG->wwwroot.'/course/format/mashup/classes/actions/newInstanceAction.php",
		updatePositionsUrl: "'.$CFG->wwwroot.'/course/format/mashup/classes/actions/updatePositionsAction.php",
		version: "0.3"
};
';
?>
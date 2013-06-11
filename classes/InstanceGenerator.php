<?php
require_once("{$CFG->dirroot}/config.php");
require_once("{$CFG->dirroot}/course/format/mashup/classes/connectors/w3c/Widget.php");
require_once("{$CFG->dirroot}/course/format/mashup/classes/connectors/w3c/WookieConnectorService.php");
require_once("{$CFG->dirroot}/course/format/mashup/classes/connectors/opensocial/ShindigConnectorService.php");
require_once("{$CFG->dirroot}/course/format/mashup/classes/renderer/OpenSocialRenderer.php");
require_once("{$CFG->dirroot}/course/format/mashup/classes/renderer/W3CRenderer.php");
require_once("{$CFG->dirroot}/course/format/mashup/classes/MoodleWidget.php");

class InstanceGenerator {

	function __construct() {}
	
	public function getWidgetInstance($persistedWidget, $courseId){
		global $USER, $CFG;
		if($persistedWidget->getWidgetType() == 1){
			$w3cRenderer = new W3CRenderer($persistedWidget, $courseId);
			return $w3cRenderer->getRenderedScript();
		}
		else{
			$openSocialRenderer = new OpenSocialRenderer($persistedWidget);
			return $openSocialRenderer->getRenderedScript();
		}
	}
}
?>
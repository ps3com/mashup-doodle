<?php
//require_once("../../../../../config.php");

class W3CRenderer {

	private $script_block;
	private $wookieconnection = null;
	private $widget;
	private $courseId;
	
	function __construct($widget, $courseId) {
		global $USER,$CFG;
		$this->courseId = $courseId;
		$this->widget = $widget;
		$this->wookieconnection = new WookieConnectorService ($CFG->mashup_wookie_url, $CFG->mashup_wookie_key, $widget->getId(), $USER->id, $USER->username);
		$this->script_block =
		'%1$s{"type": "%2$s",' .
		' "regionWidgetId": "%3$s",' .
		' "widgetUrl": "%4$s", ' .
		' "height": "%5$s", ' .
		' "width": "%6$s",' .
		' "collapsed": %7$s, ' .
		' "widgetId": "%8$s",' .
		' "locked": %9$s,' .
		' "hideChrome": %10$s,' .
		' "subPage": {"id": %11$s, "name": "%12$s", "isDefault": %13$s}' .
		'}';
	}	
	
	public function getRenderedScript(){
		return new WidgetInstance($this->getWidgetScript(), $this->widget->getId(), $this->widget->getTitle(), 350, 400);
	}
	
	private function getWidgetScript(){
		// todo mix in prefs
		$sBlock  = sprintf($this->script_block,
				"",//item.getRegion().getId(),
				"W3C",//Constants.WIDGET_TYPE,
				$this->widget->getId(),//item.getId(),
				$this->getInstanceUrl(),
				"400", //height
				"60", //width
				"false",//item.isCollapsed(),
				$this->widget->getId(),//widget.getId(),
				"false",//item.isLocked(),
				"false",//item.isHideChrome(),
				"null",//pageId,
				"",//pageName,
				"false"//isDefault
		);
		return $sBlock;
	}
	
	private function getInstanceUrl(){
		global $USER,$CFG;$PAGE;
		$widgetInstance = $this->wookieconnection->getOrCreateInstance ($this->widget->getUrl());
		//set username
		$this->setProperty($widgetInstance, "username", $USER->username, $this->wookieconnection);
		//$course = $PAGE->course;
		$context = get_context_instance(CONTEXT_COURSE, $this->courseId);
		if (has_capability('moodle/course:manageactivities', $context)) {
			$this->setProperty($widgetInstance, "moderator", "true", $this->wookieconnection);
			$this->setProperty($widgetInstance, "conference-manager", "true", $this->wookieconnection);
		}				
		return $widgetInstance->getUrl();
	}

	private function setProperty($widgetInstance, $key,$value, $wookieconnection){
		global $USER, $COURSE,$CFG;
		$newProperty = new Property ( $key, $value );
		$r = $wookieconnection->setProperty ( $widgetInstance, $newProperty );
	}
	
	private function addParticipant($widgetinstance, $wookieconnection){
		global $USER, $COURSE, $CFG;
		if ($USER->picture != ""){
			$src = $CFG->httpswwwroot.'/user/pix.php/'.$USER->id.'/f1.jpg';
		} else {
			$src = $CFG->httpswwwroot.'/pix/u/f1.png';
		}
		$thisUser = new User ( $USER->id, $USER->username, $src );
		$wookieconnection->addParticipant($widgetinstance, $thisUser);
	}
}

?>
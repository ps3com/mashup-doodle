<?php
require_once("{$CFG->dirroot}/course/format/mashup/classes/connectors/opensocial/ShindigConnectorService.php");
require_once("{$CFG->dirroot}/course/format/mashup/classes/connectors/opensocial/SecurityTokenHandler.php");

class OpenSocialRenderer {
	
	private $script_block;
	private $shindigconnection = null;
	private $widget;
	private $tokenHandler;
	
	function __construct($widget) {
		global $USER,$CFG;
		$this->widget = $widget;
		$this->shindigconnection = new ShindigConnectorService($CFG->mashup_shindig_url);
		$this->tokenHandler = new SecurityTokenHandler($this->widget);
		$this->script_block = 
				'%1$s{"type": "%2$s",' .
				' "regionWidgetId": "%3$s",' .
				' "widgetUrl": "%4$s", ' .
				' "securityToken": "%5$s", ' .
				' "metadata": %6$s,' .
				' "userPrefs": %7$s,' .
				' "collapsed": %8$s, ' .
				' "widgetId": "%9$s",' .
				' "locked": %10$s,' .
				' "hideChrome": %11$s,' .
				' "subPage": {"id": %12$s, "name": "%13$s", "isDefault": %14$s}' .
				'}';
	}
	
	public function getRenderedScript(){
		//$onlyconsonants = str_replace("8080", "8081",$this->getWidgetScript());
		
		return new WidgetInstance($this->getWidgetScript(), $this->widget->getId(), $this->widget->getTitle(), 350, 400);
	}
	
	private function getWidgetScript(){
		// todo mix in prefs
		$sBlock  = sprintf($this->script_block,
		"",//item.getRegion().getId(),
		"OpenSocial",//Constants.WIDGET_TYPE,
		intval($this->widget->getId()),//item.getId(),
		$this->widget->getUrl(),
		$this->tokenHandler->getPlainSecurityToken(),
		$this->shindigconnection->getMetadata($this->widget->getUrl()),
		"{}",//userPrefs.toString(),
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
}

?>
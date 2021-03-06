<?php
require_once("{$CFG->dirroot}/course/format/mashup/classes/omdl/OmdlConstants.php");
/**
 * Simple bean to model the identifier and url link for a given omdl widget
 */
class OmdlWidgetReference {

	private $widgetIdentifier;
	private $widgetLink;
	private $widgetType;

	function __construct($widgetIdentifier, $widgetLink, $widgetType) {
		$this->widgetIdentifier = $widgetIdentifier;
		$this->widgetLink = $widgetLink;
		$this->widgetType = $widgetType;
	}

	public function getWidgetTypeFromFormatType(){
		if($this->widgetType == OmdlConstants::MOODLE_APP_TYPE_OPENSOCIAL){
			return OmdlConstants::APP_TYPE_OPENSOCIAL;			
		}else if($this->widgetType == OmdlConstants::MOODLE_APP_TYPE_W3C){
			return OmdlConstants::APP_TYPE_W3C;
		}
		return null;
	}

	public function getWidgetIdentifier() {
		return $this->widgetIdentifier;
	}

	public function setWidgetIdentifier($widgetIdentifier) {
		$this->widgetIdentifier = $widgetIdentifier;
	}

	public function getWidgetLink() {
		if(stristr( $this->widgetLink, '?format=') === FALSE) {		
			return $this->widgetLink . "?format=" . $this->widgetType;
		}
		else{
			return $this->widgetLink;
		}
	}

	public function setWidgetLink($widgetLink) {
		$this->widgetLink = $widgetLink;
	}

	public function getWidgetType() {
		return $this->widgetType;
	}

	public function setWidgetType($widgetType) {
		$this->widgetType = $widgetType;
	}
}
?>
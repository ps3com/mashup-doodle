<?php
//require_once("{$CFG->dirroot}/course/format/mashup/classes/MashupDatabaseHelper.php");

class OmdlInputAdapter {

	// Has a LEFT Column been detected
	public $LEFT_FOUND = false;
	// Has a CENTER Column been detected
	public $CENTER_FOUND = false;
	// Has a RIGHT Column been detected
	public $RIGHT_FOUND = false;
	// Default page name given in xml file
	private $name;
	// stores the layout code
	private $layoutCode;
	
	// region 1
	private $topLeft = array();
	private $middleLeft = array();
	private $bottomLeft = array();
	// region 2
	private $topCenter = array();
	private $middleCenter = array();
	private $bottomCenter = array();
	// region 3
	private $topRight = array();
	private $middleRight = array();
	private $bottomRight = array();
	// unknown region (possibly a 4th region)
	private $unknown = array();
	
	
	public function addToAppMap($widgetReference, $position){
		//TODO
	}
	
	public function getName() {
		return $this->name;
	}
	
	public function setName($name) {
		$this->name = $name;
	}
	
	public function getLayoutCode() {
		return $this->layoutCode;
	}
	
	public function setLayoutCode($layoutCode) {
		$this->layoutCode = $layoutCode;
	}

}
?>
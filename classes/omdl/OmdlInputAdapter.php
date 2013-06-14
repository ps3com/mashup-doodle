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
	
	public function getAllUrls(){
		$newList = array();
		$newList  = array_merge($this->getAllLeftUrls(), $this->getAllCenterUrls(), $this->getAllRightUrls(), $this->getAllUnknownUrls());
		return $newList;
	}
	
	public function getAllUnknownUrls(){
		return array_reverse($this->unknown);
	}
	
	public function getAllRightUrls(){
		$newList = array();
		$newList = array_merge(array_reverse($this->topRight), array_reverse($this->middleRight), array_reverse($this->bottomRight));
		return $newList;
	}
	
	public function getAllCenterUrls(){
		$newList = array();
		$newList = array_merge(array_reverse($this->topCenter), array_reverse($this->middleCenter), array_reverse($this->bottomCenter));
		return $newList;
	}
	
	public function getAllLeftUrls(){		
		$newList = array();
		$newList = array_merge(array_reverse($this->topLeft), array_reverse($this->middleLeft), array_reverse($this->bottomLeft));
		return $newList;
	}
	
	public function addToAppMap($widgetReference, $position){
		if(strpos($position, OmdlConstants::POSITION_LEFT)!== false){
            $LEFT_FOUND = true;
            if(strpos($position, OmdlConstants::POSITION_TOP)!== false){
            	array_push($this->topLeft, $widgetReference);
            }else if (strpos($position, OmdlConstants::POSITION_MIDDLE)!== false){
                array_push($this->middleLeft, $widgetReference);
            }else{// otherwise go to bottom
                array_push($this->bottomLeft, $widgetReference);
            }
        }
        else if(strpos($position, OmdlConstants::POSITION_CENTER)!== false){
            $CENTER_FOUND = true;
            if(strpos($position, OmdlConstants::POSITION_TOP)!== false){	
                array_push($this->topCenter, $widgetReference);
            }else if (strpos($position, OmdlConstants::POSITION_MIDDLE)!== false){
                array_push($this->middleCenter, $widgetReference);
            }else{// otherwise go to bottom
                array_push($this->bottomCenter, $widgetReference);
            }
        }
        else if(strpos($position, OmdlConstants::POSITION_RIGHT)!== false){
            $RIGHT_FOUND = true;
            if(strpos($position, OmdlConstants::POSITION_TOP)!== false){
            	array_push($this->topRight, $widgetReference);
            }else if (strpos($position, OmdlConstants::POSITION_MIDDLE)!== false){
                array_push($this->middleRight, $widgetReference);
            }else{// otherwise go to bottom
                array_push($this->bottomRight, $widgetReference);
            }
        }
        else{
            array_push($this->unknown, $widgetReference);
        }
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
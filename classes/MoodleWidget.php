<?php
require_once("{$CFG->dirroot}/course/format/mashup/classes/connectors/w3c/Widget.php");
require_once("{$CFG->dirroot}/course/format/mashup/classes/MashupDatabaseHelper.php");

class MoodleWidget {

	private $id;
	private $courseId;
	private $url;
	private $title;
	private $widgetType;
	private $dataRow;
	private $dataCol;
	private $dataSizeX;
	private $dataSizeY;
	private $pageId;
	

	function __construct($id, $courseId=null, $url=null, $title=null, $widgetType=null, $datarow=null, $datacol=null, $datasizex=null, $datasizey=null, $pageId=null) {
		$this->id = $id;
		$this->courseId = $courseId;
		$this->url = $url;
    	$this->title = $title;
    	$this->widgetType = $widgetType;
    	$this->dataRow = $datarow;
    	$this->dataCol = $datacol;
    	$this->dataSizeX = $datasizex;
    	$this->dataSizeY = $datasizey;
    	$this->pageId = $pageId;
	}
	
	public function serialize(){
		if (!isset($this->id)){
			$mashupDatabaseHelper = new MashupDatabaseHelper();
			$this->id = $mashupDatabaseHelper->addNewWidget($this->courseId, $this->url, $this->title, $this->widgetType,
					$this->dataRow, $this->dataCol, $this->dataSizeX, $this->dataSizeY, $this->pageId);
		}
		return $this->id;
	}
	
	public function reserialize(){
		if (isset($this->id)){
			$mashupDatabaseHelper = new MashupDatabaseHelper();
			$this->id = $mashupDatabaseHelper->updateWidget($this, $this->courseId);
		}
		return $this->id;
	}
	
	public function deserialize(){
		if (isset($this->id)){
			$mashupDatabaseHelper = new MashupDatabaseHelper();
			$persistedwidget = $mashupDatabaseHelper->getSingleWidget($this->id);
			$this->courseId = $persistedwidget->courseId;
			$this->url = $persistedwidget->url;
	    	$this->title = $persistedwidget->title;
	    	$this->widgetType = $persistedwidget->widgetType;
	    	$this->dataRow = $persistedwidget->dataRow;
	    	$this->dataCol = $persistedwidget->dataCol;
	    	$this->dataSizeX = $persistedwidget->dataSizeX;
	    	$this->dataSizeY = $persistedwidget->dataSizeY;
	    	$this->pageId = $persistedwidget->pageId;
		}
	}
	
	public function toJson() {
		$json = array();
		foreach($this as $key => $value) {
			$json[$key] = $value;
		}
		return $json;
	}

	public function getPageId(){
		return $this->pageId;
	}
	
	public function getId(){
		return $this->id;
	}
	
	public function getUrl() {
		return $this->url;
	}
	
	public function setUrl($url){
		$this->url = $url;
	}
	
	public function getTitle(){
		return $this->title;
	}
	
	public function getWidgetType(){
		return $this->widgetType;
	}

	public function getDataRow(){
		return $this->dataRow;
	}

	public function setDataRow($row){
		$this->dataRow = $row;
	}

	public function getDataCol(){
		return $this->dataCol;
	}
	
	public function setDataCol($col){
		$this->dataCol = $col;
	}

	public function getDataSizeX(){
		return $this->dataSizeX;
	}
	
	public function setDataSizeX($dataSizeX){
		$this->dataSizeX = $dataSizeX;
	}
	
	public function getDataSizeY(){
		return $this->dataSizeY;
	}
	
	public function setDataSizeY($dataSizeY){
		$this->dataSizeY = $dataSizeY;
	}
}
?>
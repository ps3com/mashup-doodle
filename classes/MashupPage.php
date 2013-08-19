<?php
require_once("{$CFG->dirroot}/course/format/mashup/classes/connectors/w3c/Widget.php");
require_once("{$CFG->dirroot}/course/format/mashup/classes/MashupDatabaseHelper.php");

class MashupPage {

	private $id;
	private $title;
	private $courseId;
	private $pageLayout;
	private $mashupDatabaseHelper;

	function __construct($id=null, $title=null, $courseId=null, $pageLayout=null) {
		$this->id = $id;	
		$this->title = $title;
		$this->courseId = $courseId;
		$this->pageLayout = $pageLayout;
		$this->mashupDatabaseHelper = new MashupDatabaseHelper();		
	}
	
	public function toJson() {
		$json = array();
    	foreach($this as $key => $value) {
        	$json[$key] = $value;
    	}
    	return $json; 
	}
	
	public function serialize(){
		if (!isset($this->id)){
			$this->id = $this->mashupDatabaseHelper->addNewPage($this->title, $this->courseId, $this->pageLayout);
		}
		return $this->id;	
	}
	
	public function reserialize(){
		if (isset($this->id)){
			$this->mashupDatabaseHelper->updatePage($this);			
		}
		return $this->id;
	}
	
	public function deserialize(){
		if (isset($this->id)){
			$persistedPage = $this->mashupDatabaseHelper->getSinglePage($this->id);
			$this->title = $persistedPage->page_name;
			$this->courseId = $persistedPage->course_id;
			$this->pageLayout = $persistedPage->page_layout;
		}
	}

	public function getId(){
		return $this->id;
	}
	
	public function getTitle(){
		return $this->title;
	}

	public function setTitle($title){
		$this->title = $title;
	}
	
	public function getPageLayout(){
		return $this->pageLayout;
	}
	
	public function setPageLayout($layout){
		$this->pageLayout = $layout;
	}
}
?>
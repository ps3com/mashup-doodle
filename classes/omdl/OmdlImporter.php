<?php
require_once("{$CFG->dirroot}/course/format/mashup/classes/MashupDatabaseHelper.php");
require_once("{$CFG->dirroot}/course/format/mashup/classes/omdl/OmdlModelUtils.php");
require_once("{$CFG->dirroot}/course/format/mashup/classes/omdl/OmdlWebUtils.php");
require_once("{$CFG->dirroot}/course/format/mashup/classes/omdl/OmdlInputAdapter.php");
require_once("{$CFG->dirroot}/course/format/mashup/classes/omdl/OmdlWidgetReference.php");

class OMDLImporter {

	private $courseId;
	private $mashupDatabaseHelper;
	private $omdlInputAdaptor;
	private $xmlDocument;
	private $omdlWebUtils;

	
	function __construct($courseId, $xmlDoc) {
		$this->courseId = $courseId;
		$this->xmlDocument = $xmlDoc;
		$this->mashupDatabaseHelper = new MashupDatabaseHelper();
		$this->omdlInputAdapter = new OmdlInputAdapter();
		$this->omdlWebUtils = new OmdlWebUtils();
	}
	
	public function fromXML(){
		$t="";
		$defaultNamespace;
		$titleText;
		$layoutText;
		// make sure root node is workspace
		if($this->xmlDocument->getName() != OmdlConstants::WORKSPACE){
			return "Error: Root node must be " . OmdlConstants::WORKSPACE;
		}
		// find the omdl namespace
		foreach ( $this->xmlDocument->getDocNameSpaces() as $key){			
			if(!strncmp($key, OmdlConstants::aNAMESPACE, strlen(OmdlConstants::aNAMESPACE))){
				$defaultNamespace=$key;	
			}
		}
		// namespace not found, so throw error
		if(!isset($defaultNamespace)){
			return "Error: xml document must have default namespace of " .  OmdlConstants::aNAMESPACE;
		}
		// register namespace for xpath queries
		$this->xmlDocument->registerXPathNamespace('omdl', $defaultNamespace);
		
		//get the title
		$title = $this->xmlDocument->xpath('//omdl:'.OmdlConstants::aTITLE);
		if(sizeof($title)>0){
			$titleText = $title[0];
		}
		$this->omdlInputAdapter->setName($title);
		
		//get the layout
		$layout = $this->xmlDocument->xpath('//omdl:'.OmdlConstants::LAYOUT);
		if(sizeof($layout)>0){
			$layoutText = $layout[0];
		}
		
		$t.=$titleText."\n";
		$t.=$layoutText."\n";
		
		// find the apps
		foreach($this->xmlDocument->xpath('//omdl:'.OmdlConstants::APP) as $app) {
			$t.="".$app['id'];
			$t.= " : ". $app->link['href'];
			$t.= " : ". $app->link['type'];
			$t.= " : ". $app->position;
			$t.="\n";
			$widgetRef = new OmdlWidgetReference($app['id'], $app->link['href'], $app->link['type']);
			$this->omdlInputAdapter->addToAppMap($widgetRef, $app->position);			
		}
		
		// store the string found in the xml file
		$this->omdlInputAdapter->setLayoutCode($layoutText);
		// update this string into a mashup layout
		$this->omdlInputAdapter->setLayoutCode(OmdlModelUtils::getMoodleLayoutForImport($this->omdlInputAdapter));
		
		////////////////////////////
		$pageName="test"; //todo get from input dialog
		$mashupPage = new MashupPage(null, $pageName, $this->courseId, $this->omdlInputAdapter->getLayoutCode());
		$pageId = $mashupPage->serialize();
		
		switch ($this->omdlInputAdapter->getLayoutCode()){
			case 1:
				$this->populateRegionWidgets($pageId, $this->omdlInputAdapter->getAllUrls(), 1);
				break;
			case 2:
				$this->populateRegionWidgets($pageId, $this->omdlInputAdapter->getAllLeftUrls(), 1);
				$this->populateRegionWidgets($pageId, $this->omdlInputAdapter->getAllRightUrls(), 2);
				break;
			case 3:
				$this->populateRegionWidgets($pageId, $this->omdlInputAdapter->getAllLeftUrls(), 1);
				$this->populateRegionWidgets($pageId, $this->omdlInputAdapter->getAllCenterUrls(), 2);
				$this->populateRegionWidgets($pageId, $this->omdlInputAdapter->getAllRightUrls(), 3);
				break;
			case 4:
				$this->populateRegionWidgets($pageId, $this->omdlInputAdapter->getAllLeftUrls(), 1);
				$this->populateRegionWidgets($pageId, $this->omdlInputAdapter->getAllCenterUrls(), 2);
				$this->populateRegionWidgets($pageId, $this->omdlInputAdapter->getAllRightUrls(), 3);
				$this->populateRegionWidgets($pageId, $this->omdlInputAdapter->getAllUnknownUrls(), 4);
				break;
			default:
				// there are no layouts with more than 4 regions at present
		}
	}
	
	private function populateRegionWidgets($pageId, $widgetRefArray, $column){
		$rowCount = 1;
		foreach($widgetRefArray as $widgetReference) {
			$moodleWidget = null;
			// if widget type is W3C....
			if($widgetReference->getWidgetTypeFromFormatType() == OmdlModelUtils::APP_TYPE_W3C){
				//if this is a W3C widget then we need to check if it is already imported in wookie
				if(!$this->omdlWebUtils->doesWidgetExistInWookie($widgetReference->getWidgetIdentifier())){
					//not installed so we will have to download it and install it to wookie
					$response = $this->omdlWebUtils->downloadAndInstallW3CWidget($widgetReference->getWidgetLink());
					if(isset($response)){
						$moodleWidget = new MoodleWidget(null, $this->courseId, $response['id'], $response->name, 1, $rowCount, $column, 1, 1, $pageId);
						$moodleWidget->serialize();
					}
				}
				else{
					// widget is already imported into wookie so create local record
					$moodleWidget = new MoodleWidget(null, $this->courseId, $widgetReference->getWidgetIdentifier(), $this->omdlWebUtils->getWidgetTitle($widgetReference->getWidgetIdentifier()), 1, $rowCount, $column, 1, 1, $pageId);
					$moodleWidget->serialize();
				}
			}
			else{
				// TODO another widget type - opensocial for example
			}	
			$rowCount++;
		}
	}
	
}
?>
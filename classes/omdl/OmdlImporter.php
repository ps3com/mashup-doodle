<?php
require_once("{$CFG->dirroot}/course/format/mashup/classes/MashupDatabaseHelper.php");
require_once("{$CFG->dirroot}/course/format/mashup/classes/omdl/OmdlModelUtils.php");
require_once("{$CFG->dirroot}/course/format/mashup/classes/omdl/OmdlInputAdapter.php");
require_once("{$CFG->dirroot}/course/format/mashup/classes/omdl/OmdlWidgetReference.php");

class OMDLImporter {

	private $courseId;
	private $mashupDatabaseHelper;
	private $omdlInputAdaptor;
	private $xmlDocument;

	
	function __construct($courseId, $xmlDoc) {
		$this->courseId = $courseId;
		$this->xmlDocument = $xmlDoc;
		$this->mashupDatabaseHelper = new MashupDatabaseHelper();
		$this->omdlInputAdapter = new OmdlInputAdapter();
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
		
		return $t; 
	}
}
?>
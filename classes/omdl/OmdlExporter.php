<?php
require_once("{$CFG->dirroot}/course/format/mashup/classes/MashupDatabaseHelper.php");
require_once("{$CFG->dirroot}/course/format/mashup/classes/MashupPage.php");
require_once("{$CFG->dirroot}/course/format/mashup/classes/omdl/OmdlModelUtils.php");

class OMDLExporter {

	private $courseId;
	private $pageId;
	private $mashupDatabaseHelper;
	private $mashupPage;
	private $widgets;

	function __construct($courseId, $pageId) {
		$this->courseId = $courseId;
		$this->pageId = $pageId;
		$this->mashupDatabaseHelper = new MashupDatabaseHelper();
		$this->getPage();
		$this->getWidgetsForPage();
	}
	
	private function getNumberOfColumnsInPage(){
		// using simple column layouts until the widget overlapping problem is resolved
		/*
		$columnsInPage;
		$layoutCode = $this->mashupPage->getPageLayout();
		if($layoutCode==1){
			$columnsInPage=1;
		}else if($layoutCode==2||$layoutCode==3){
			$columnsInPage=2;
		}else if($layoutCode==4||$layoutCode==5){
			$columnsInPage=3;
		}
		return $columnsInPage;
		*/
		return $this->mashupPage->getPageLayout();
	}
		
	private function getWidgetFromColumnAndRow($col, $row){
		$instances = $this->getWidgetsInColumn($col);
		return $instances[$row];
	}

	private function getWidgetsInColumn($column){
		$widgetsInColumn = array();		
		foreach ($this->widgets as $widget) {
			if($widget->getDataCol()==$column){				
				array_push($widgetsInColumn, $widget);
			}
		}
		return $widgetsInColumn;
	}
	
	private function countWidgetsInColumn($column){
		$counter=0;
		foreach ($this->widgets as $widget) {
			if($widget->getDataCol()==$column){
				$counter++;
			}
		}
		return $counter;
	}
	
	public function getWidgetType($widget){
		$widgetType = $widget->getWidgetType();
		if($widgetType==1){
			return OmdlModelUtils::MOODLE_APP_TYPE_W3C;
		}
		else if($widgetType==2){
			return OmdlModelUtils::MOODLE_APP_TYPE_OPENSOCIAL;
		}
		else{
			return OmdlModelUtils::MOODLE_APP_TYPE_UNKNOWN;
		}
	}
	
	public function toXml(){
		global $USER,$CFG;						
		$ns = OmdlModelUtils::aNAMESPACE;
		$document = new DOMDocument('1.0', 'UTF-8');
		// if the root node declares a ns the pretty formatter seems to not work.
		//$workspace = $document->createElementNS($ns, 'workspace', '');
		$workspace = $document->createElement(OmdlModelUtils::WORKSPACE);
		$workspace = $document->appendChild($workspace);
		$statusTag = $document->createElementNS($ns, OmdlModelUtils::aSTATUS, OmdlModelUtils::DEFAULT_STATUS);
		$statusTag->appendChild($document->createAttributeNS($ns, OmdlModelUtils::aDATE))->appendChild($document->createTextNode(date("Y-m-d\TH:i:sO")));
		
		$workspace->appendChild($statusTag);
		// TODO - identifier should point to a URI where the page can be found rather than this export xml format
		// improve page api so that it can be called via a url & use that instead
		$workspace->appendChild($document->createElementNS($ns, OmdlModelUtils::aIDENTIFIER, 
				"{$CFG->wwwroot}/course/format/mashup/classes/actions/exportOmdlAction.php?courseId=".$this->courseId."&amp;pageId=".$this->pageId));
		$workspace->appendChild($document->createElementNS($ns, OmdlModelUtils::aTITLE, $this->mashupPage->getTitle()));
		$workspace->appendChild($document->createElementNS($ns, OmdlModelUtils::aCREATOR, $USER->username));
		$workspace->appendChild($document->createElementNS($ns, OmdlModelUtils::aDATE, date("Y-m-d\TH:i:sO")));
		$workspace->appendChild($document->createElementNS($ns,  OmdlModelUtils::LAYOUT, OmdlModelUtils::getLayoutData($this->mashupPage->getPageLayout())));
		
		$columnsInPage =  $this->getNumberOfColumnsInPage();
		for ($i=0; $i<$columnsInPage; $i++){
			for ($j=0; $j<sizeof($this->getWidgetsInColumn($i+1)); $j++){
				$widgetInst = $this->getWidgetFromColumnAndRow($i+1, $j);				
				$appElement = $document->createElementNS($ns, OmdlModelUtils::APP);
				$appElement->appendChild($document->createAttributeNS($ns, OmdlModelUtils::ID_ATTRIBUTE))->appendChild($document->createTextNode($widgetInst->getUrl()));
				$workspace->appendChild($appElement);
					
				$appElement->appendChild($document->createElementNS($ns, OmdlModelUtils::TYPE_ATTRIBUTE, OmdlModelUtils::UNKNOWN_VALUE));
					
				$linkElement = $document->createElementNS($ns, OmdlModelUtils::LINK, '');
				if($widgetInst->getWidgetType()==1){
					$linkElement->appendChild($document->createAttributeNS($ns, OmdlModelUtils::HREF))->appendChild($document->createTextNode($CFG->mashup_wookie_url.'widgets/'.$widgetInst->getUrl()));
				}
				else{
					$linkElement->appendChild($document->createAttributeNS($ns, OmdlModelUtils::HREF))->appendChild($document->createTextNode($widgetInst->getUrl()));
				}
				$linkElement->appendChild($document->createAttributeNS($ns, OmdlModelUtils::TYPE_ATTRIBUTE))->appendChild($document->createTextNode($this->getWidgetType($widgetInst)));
				$linkElement->appendChild($document->createAttributeNS($ns, OmdlModelUtils::aREL))->appendChild($document->createTextNode(OmdlModelUtils::REL_TYPE));
				$appElement->appendChild($linkElement);
			
				//$positionString = $this->getPositionString(($i+1), $columnsInPage, ($j+1), sizeof($this->getWidgetsInColumn($i+1)));
				$positionString = OmdlModelUtils::getPositionString(($i+1), $columnsInPage, ($j+1), sizeof($this->getWidgetsInColumn($i+1)));
				$appElement->appendChild($document->createElementNS($ns, OmdlModelUtils::POSITION, $positionString));
			}
		}
		$document->formatOutput = true;
		$output = $document->saveXML();				
		return $output;
	}
	
	
	private function getPage(){
		if (isset($this->pageId)){
			$this->mashupPage = new MashupPage($this->pageId);
			$this->mashupPage->deserialize();
		}
		else{
			throw new Exception("pageId not set");
		}
	}
	
	private function getWidgetsForPage(){
		if (isset($this->pageId)){
			$this->widgets = $this->mashupDatabaseHelper->getWidgetsForPage($this->pageId);
		}
		else{
			throw new Exception("pageId not set");
		}
	}

}
?>
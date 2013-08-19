<?php
require_once("{$CFG->dirroot}/course/format/mashup/classes/MashupDatabaseHelper.php");

class MashupPageHelper {

	private $mashupDatabaseHelper;
	
	function __construct() {
		$this->mashupDatabaseHelper = new MashupDatabaseHelper();
	}
	
	public function getHighestRowInColumn($pageId, $column){
		$widgetsInColumn = $this->mashupDatabaseHelper->getWidgetsForPage($pageId, $column);
		$rowCount=0;
		foreach ($widgetsInColumn as $widgetInColumn){
			if($widgetInColumn->getDataRow() > $rowCount){
				$rowCount = $widgetInColumn->getDataRow();
			}
		}
		return $rowCount;
	}
	
	// Move widgets from the 'fromCol' to the 'toCol' given a pageId
	function moveWidgetsToNewColumn($pageId, $fromCol, $toCol){
		// get the number of widgets currently in the target column
		$numberOfWidgetsInColumnOne = $this->getHighestRowInColumn($pageId, $toCol);
		// get widgets in the column to be moved
		$widgetsInColumnTwo = $this->mashupDatabaseHelper->getWidgetsForPage($pageId, $fromCol);
		// move them all into the 'toCol' column
		foreach ($widgetsInColumnTwo as $widgetInColumnTwo){
			$numberOfWidgetsInColumnOne++;
			$widgetInColumnTwo->setDataCol(1);
			$widgetInColumnTwo->setDataRow($numberOfWidgetsInColumnOne);
			$widgetInColumnTwo->reserialize();
		}
	}

}
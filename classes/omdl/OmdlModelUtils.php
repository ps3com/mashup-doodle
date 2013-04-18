<?php
require_once("{$CFG->dirroot}/course/format/mashup/classes/omdl/OmdlConstants.php");

class OmdlModelUtils implements OmdlConstants {
	
	public static function getMoodleLayoutForImport($omdlInputAdapter){
		//todo
		return "Not implemented - todo";
	}
	
	public static function getLayoutData($layoutCode){
		switch ($layoutCode) {
			case 1:
				$layout="ONE COLUMN";
				break;
			case 2:
				$layout="TWO COLUMNS";
				break;
			case 3:
				//$layout="TWO COLUMNS WIDE NARROW";
				$layout="THREE COLUMNS";
				break;
			case 4:
				//$layout="THREE COLUMNS";
				$layout="FOUR COLUMNS";
				break;
			case 5:
				//$layout="THREE COLUMNS NARROW WIDE NARROW";
				break;
		}
		return $layout;
	}
	
	/**
	 * Used in a page export to OMDL, maps where a widget is found within
	 * a particular region, to a String value in OMDL, giving a hint to positioning
	 * @param region - current region
	 * @param totalRegions - total regions
	 * @param widget - current widget within a region
	 * @param totalWidget - total number of widgets in this region
	 * @return - a string containing an <app> (regionWidget) position in the UI.
	 */
	public static function getPositionString($region, $totalRegions, $widget, $totalWidget){		
		$vs = OmdlModelUtils::getVerticalStringForWidget($widget, $totalWidget);
		$hs = OmdlModelUtils::getColumnStringForWidget($region, $totalRegions);
		$both = $vs . " " . $hs;
		return trim($both);
	}
	
	/**
	 * Find the Vertical position of this widget in OMDL format
	 * @param thisWidgetIndex - index of this widget
	 * @param totalWidgets - total widgets in region
	 * @return - omdl string for vertical alignment
	 */
	public static function getVerticalStringForWidget($thisWidgetIndex, $totalWidgets){
		$count = ($totalWidgets + 3 - 1) / 3;
		if($totalWidgets<2){
			return "";
		}
		else if($thisWidgetIndex <= $count){
			return OmdlConstants::POSITION_TOP;			
		}
		else if($thisWidgetIndex <= $count*2){
			return OmdlConstants::POSITION_MIDDLE;
		}
		else{
			return OmdlConstants::POSITION_BOTTOM;
		}
	}
	
	/**
	 * Find the horizontal position of this widget in OMDL format
	 * @param currentRegion -  this region
	 * @param numberOfRegions - total page regions
	 * @return - omdl string for horizontal alignment
	 */
	public static function getColumnStringForWidget($currentRegion, $numberOfRegions){
		$columnStr = "";
		switch ($numberOfRegions){
			case 1: // one region - always return blank string ""
				break;
			case 2:
				// two regions
				switch ($currentRegion) {
					case 1: // Two regions current region one
						$columnStr = OmdlConstants::POSITION_LEFT;
						break;
					case 2: // Two regions current region two
						$columnStr = OmdlConstants::POSITION_RIGHT;
						break;
					default:
						// nothing
				}
				break;
			default:
				// three regions
				switch ($currentRegion) {
					case 1: // Three regions current region one
						$columnStr = OmdlConstants::POSITION_LEFT;
						break;
					case 2: // Three regions current region two
						$columnStr = OmdlConstants::POSITION_CENTER;
						break;
					case 3: // Three regions current region three
						$columnStr = OmdlConstants::POSITION_RIGHT;
						break;
					default:
						// nothing
				}
				break;
		}
		return $columnStr;
	}
	
}
?>
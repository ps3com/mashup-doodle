<?php
require_once("{$CFG->dirroot}/course/format/mashup/classes/MashupDatabaseConnector.php");
require_once("{$CFG->dirroot}/course/format/mashup/classes/MashupPage.php");
require_once("{$CFG->dirroot}/course/format/mashup/classes/MoodleWidget.php");

class MashupDatabaseHelper {
	
	private $mashupDatabaseConnector;	

	function __construct() {
		$this->mashupDatabaseConnector = MashupDatabaseConnector::GetInstance();
	}
	
	function get_error() {
		return $this->mashupDatabaseConnector->get_error();
	}
	
	function deleteWidget($dbkey){
		$sqldelete = "DELETE FROM `moodle_widget` WHERE `entity_id`= $dbkey";
		$result = $this->mashupDatabaseConnector->execute_sql($sqldelete, false );
		if ( $result == null ) {
			if ($this->mashupDatabaseConnector->get_error() != null ) {
				echo ( $this->mashupDatabaseConnector->get_error());
			}
		}
	}
	
	function updateWidget($gridsterWidget, $course){
		$sqlupdate = 'UPDATE `moodle_widget` SET wrow='.$gridsterWidget->row.',
												wcol='.$gridsterWidget->col.',
												size_x='.$gridsterWidget->sizex.',
												size_y='.$gridsterWidget->sizey.'
												WHERE entity_id ='.$gridsterWidget->id;
		$result = $this->mashupDatabaseConnector->execute_sql($sqlupdate, false );
		if ( $result == null ) {
			return false;
		}
		else{
			return true;
		}
	}
	
	function addNewWidget($courseid, $url, $title, $widgetType, $datarow, $datacol, $datasizex, $datasizey, $pageId){
		$sqlinsert = 'INSERT INTO `moodle_widget` (`course_id`, `url`, `title`, `widget_type`, `wrow`, `wcol`, `size_x`, `size_y`, `page_id`) VALUES ('.$courseid.', \''
				.$url.'\', \''.$title.'\', '.$widgetType.', '.$datarow.', '.$datacol.', '.$datasizex.', '.$datasizey.', '.$pageId.')';
				
		$result = $this->mashupDatabaseConnector->execute_sql($sqlinsert, false );
		if ( $result == null ) {
			if ($this->mashupDatabaseConnector->get_error() != null ) {
				echo ($this->mashupDatabaseConnector->get_error());
			}
		}
		return mysql_insert_id();
	}
	
	function addNewPage($pageName, $courseId, $pageLayout ){
		$sqlinsert = 'INSERT INTO `mashup_page` (`page_name`, `course_id`, `page_layout`) VALUES (\''
				.$pageName.'\', '.$courseId.', '.$pageLayout.')';
	
		$result = $this->mashupDatabaseConnector->execute_sql($sqlinsert, false );
		if ( $result == null ) {
			if ($this->mashupDatabaseConnector->get_error() != null ) {
				echo ($this->mashupDatabaseConnector->get_error());
			}
		}
		return mysql_insert_id();
	}
	
	function deletePage($dbkey){
		// first delete any widgets from this page
		$sqldelete = "DELETE FROM `moodle_widget` WHERE `page_id`= $dbkey";
		$result = $this->mashupDatabaseConnector->execute_sql($sqldelete, false );
		if ( $result == null ) {
			if ($this->mashupDatabaseConnector->get_error() != null ) {
				echo ( $this->mashupDatabaseConnector->get_error());
			}
		}
		//now delete the page
		$sqldelete = "DELETE FROM `mashup_page` WHERE `entity_id`= $dbkey";
		$result = $this->mashupDatabaseConnector->execute_sql($sqldelete, false );
		if ( $result == null ) {
			if ($this->mashupDatabaseConnector->get_error() != null ) {
				echo ( $this->mashupDatabaseConnector->get_error());
			}
		}
	
	}
	
	public function getPagesForCourse($courseId){
		$sqllookup = "SELECT mashup_page.entity_id,
		mashup_page.page_name,
		mashup_page.course_id,
		mashup_page.page_layout		
		from mashup_page
		where mashup_page.course_id = $courseId";
		$persistedPages = $this->mashupDatabaseConnector->execute_sql ($sqllookup);
		$pages = array();
		if (!isset($persistedPages)){
			echo $this->mashupDatabaseConnector->get_error();
		}
		else{
			// put these into widget objects
			foreach ($persistedPages as $persistedPage) {
				$mashupPage = new MashupPage($persistedPage->entity_id, $persistedPage->page_name, $persistedPage->course_id,  $persistedPage->page_layout);
				array_push($pages, $mashupPage);
			}
		}
		return $pages;
	}
	
	public function getSinglePage($pageId){
		$sqllookup = "SELECT mashup_page.entity_id,
		mashup_page.page_name,
		mashup_page.course_id,
		mashup_page.page_layout		
		from mashup_page
		where mashup_page.entity_id = $pageId";
		$result = $this->mashupDatabaseConnector->execute_sql ($sqllookup);
		return $result[0];
	}
	
	public function getLayoutForPage($pageId){
		$sqllookup = "SELECT mashup_page.entity_id,
		mashup_page.page_name,
		mashup_page.course_id,
		mashup_page.page_layout		
		from mashup_page
		where mashup_page.entity_id = $pageId";
		$pageLayout = $this->mashupDatabaseConnector->execute_sql ($sqllookup);
		if (!isset($pageLayout)){
			echo $this->mashupDatabaseConnector->get_error();
		}
		return $pageLayout[0]->page_layout;
	}
	
	public function getWidgetsForPage($pageId){
		$sqllookup = "SELECT moodle_widget.entity_id,
		moodle_widget.course_id,
		moodle_widget.url,
		moodle_widget.title,
		moodle_widget.widget_type,
		moodle_widget.wrow,
		moodle_widget.wcol,
		moodle_widget.size_x,
		moodle_widget.size_y,
		moodle_widget.page_id
		from moodle_widget
		where moodle_widget.page_id = $pageId order by wcol, wrow";
		$persistedWidgets = $this->mashupDatabaseConnector->execute_sql ($sqllookup);
		$widgets = array();
		if (!isset($persistedWidgets)){
			echo $this->mashupDatabaseConnector->get_error();
		}
		else{
			// put these into widget objects
			foreach ($persistedWidgets as $persistedWidget) {
				$MoodleWidget = new MoodleWidget($persistedWidget->entity_id, $persistedWidget->course_id, $persistedWidget->url, $persistedWidget->title, 
						$persistedWidget->widget_type, $persistedWidget->wrow, $persistedWidget->wcol, 
							$persistedWidget->size_x, $persistedWidget->size_y, $persistedWidget->page_id);
				array_push($widgets, $MoodleWidget);
			}
		}
		return $widgets;
	}
}
?>
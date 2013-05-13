<?php
require_once("{$CFG->dirroot}/course/format/mashup/classes/MashupDatabaseHelper.php");
require_once("{$CFG->dirroot}/course/format/mashup/classes/Gallery.php");
require_once("{$CFG->dirroot}/course/format/mashup/classes/InstanceGenerator.php");
require_once("{$CFG->dirroot}/course/format/mashup/includes/js_includes.php");

/**
 * 
 */
class Mashup {
	
	private $courseId;
	
	function __construct() {
		global $COURSE;
		$this->courseId = $COURSE->id;
	}

	public function initUi(){
		$markup="";
		$markup.=$this->getBrowseDialog();
		$markup.=$this->initJS();
		return $markup;
	}
	
	private function initJS(){
		// get permissions to prevent students etc to admin this course
		$canEdit;
		$context = get_context_instance(CONTEXT_COURSE, $this->courseId);
		if (has_capability('moodle/course:manageactivities', $context)) {
			$canEdit = 'true';
		}else{
			$canEdit = 'false';
		}		
		$markup='<div id="mashup-content"></div>';
		// call the js handlers
		$markup.='
		<script>
			$(function(){ //DOM Ready
				mashup_properties.canEdit='.$canEdit.';
				mashup_properties.courseId='.$this->courseId.';
				MashupEngine.init(mashup_properties);
			});
		</script>
		'.PHP_EOL; 
		
		return $markup;		
	}

	private function getBrowseDialog(){
		$responseText="";
		$responseText.='<div id="w3cBrowseForm" title="Browse widget store">'.PHP_EOL;
		$responseText.='<ul id="w3cwidgetsList" class="storeItems">'.PHP_EOL;
		// get the gallery
		$gallery = new Gallery();
		$responseText.=$gallery->showGallery();
		$responseText.='</ul>'.PHP_EOL;
		$responseText.='</div>'.PHP_EOL;
		return $responseText;
	}
	
}
?>
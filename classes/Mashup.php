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
		$markup.=$this->getAddPageDialog();
		$markup.=$this->getImportPageDialog();
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
	
	private function getImportPageDialog(){
		$markup='';
		$markup.='<div id="importPageDialog" class="dialog" title="Import Page" style="display:none;">'.PHP_EOL;
        $markup.='       <form method="post" id="pageFormImport" class="form-horizontal" enctype="multipart/form-data">'.PHP_EOL;
        $markup.='            <fieldset class="ui-helper-reset">'.PHP_EOL;
        $markup.='                <div class="control-group error">'.PHP_EOL;
        $markup.='                    <label id="pageFormErrorsTabbed2" class="control-label"></label>'.PHP_EOL;
        $markup.='                </div>'.PHP_EOL;
        $markup.='                <div class="control-group">'.PHP_EOL;
        $markup.='                    <label class="control-label" for="tab_titleTabbed2">Title</label>'.PHP_EOL;
        $markup.='                    <div class="controls">'.PHP_EOL;
        $markup.='                        <input id="tab_titleTabbed2" name="pageName" class="input-xlarge focused required" type="text" value="" />'.PHP_EOL;
        $markup.='                    </div>'.PHP_EOL;
        $markup.='                </div>'.PHP_EOL;
        $markup.='                <div class="control-group">'.PHP_EOL;
        $markup.='                    <label class="control-label" for="omdlFile">Browse for File</label>'.PHP_EOL;
        $markup.='                    <div class="controls">'.PHP_EOL;
        $markup.='                        <input id="omdlFile" name="omdlFile" class="input-xlarge focused required" type="file" value="" />'.PHP_EOL;
        $markup.='                    </div>'.PHP_EOL;
        $markup.='                </div>'.PHP_EOL;
        $markup.='                <div class="control-group">'.PHP_EOL;
        $markup.='                    <div class="controls"><iframe id="file_upload_frame" name="file_upload_frame" src="" style="width:0;height:0;border:0px solid black;"></iframe></div>'.PHP_EOL;
        $markup.='                </div>'.PHP_EOL;
        $markup.='            </fieldset>'.PHP_EOL;
        $markup.='        </form>'.PHP_EOL;
		$markup.='</div>'.PHP_EOL;
		
		return $markup;
	}

	private function getAddPageDialog(){
		//TODO get pagelayouts from DB
		$markup='';
		$markup.='<div id="addPageDialog" class="dialog" title="Add Page" style="display:none;">'.PHP_EOL;
		$markup.='	<form>'.PHP_EOL;
		$markup.='		<fieldset class="ui-helper-reset">'.PHP_EOL;
		$markup.='			<label for="tab_title">Title</label>'.PHP_EOL;
		$markup.='			<input type="text" name="tab_title" id="tab_title" value="" class="ui-widget-content ui-corner-all" />'.PHP_EOL;
		$markup.='			<label for="page_layout">Page layout</label>'.PHP_EOL;
		$markup.='			&nbsp;<select name="page_layout" id="page_layout" class="ui-widget-content ui-corner-all">'.PHP_EOL;
		// Due to general buggyness of gridsters datasize-x being more than one we will stick to simple columns layouts for now
		/*
		$markup.='				<option value="1">One column</option>'.PHP_EOL;
		$markup.='				<option value="2">Two columns</option>'.PHP_EOL;
		$markup.='				<option value="3">Two columns (wide/narrow)</option>'.PHP_EOL;
		$markup.='				<option value="4">Three columns</option>'.PHP_EOL;
		$markup.='				<option value="5">Three columns (narrow/wide/narrow)</option>'.PHP_EOL;
		*/
		// simple layouts
		$markup.='				<option value="1">One column</option>'.PHP_EOL;
		$markup.='				<option value="2">Two columns</option>'.PHP_EOL;
		$markup.='				<option value="3">Three columns</option>'.PHP_EOL;
		$markup.='				<option value="4">Four columns</option>'.PHP_EOL;
		// simple layouts
		
		$markup.='			</select>'.PHP_EOL;
		$markup.='		</fieldset>'.PHP_EOL;
		$markup.='	</form>'.PHP_EOL;
		$markup.='</div>'.PHP_EOL;
		// ADDS the delete page confirm
		//$markup.='<div id="confirmDeletePageDialog" title="Delete Page">'.PHP_EOL;
	///	$markup.='	<p><span class="ui-icon ui-icon-alert" style="float: left; margin: 0 7px 20px 0;"></span>This page will be permanently deleted and cannot be recovered. Are you sure?</p>'.PHP_EOL;
		//$markup.='</div>'.PHP_EOL;
		//
		
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
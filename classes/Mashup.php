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
		$markup.=$this->getMenuBar();
		$markup.=$this->getPages();
		return $markup;
	}
	
	private function getPages(){
		$markup='<div id="mashup-content"><div>';
		// call the js handlers
		$markup.='
		<script>
			$(function(){ //DOM Ready
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
	
	private function getMenuBar(){
		$responseText="";
		$responseText.='<div>'.PHP_EOL;
		$responseText.='<ul id="bar1" class="menubar ui-menubar ui-widget-header ui-helper-clearfix" role="menubar" style="background: #E4E2D6;">'.PHP_EOL;
		$responseText.='	<li class="ui-menubar-item" role="presentation">'.PHP_EOL;
		$responseText.='		<a href="http://view.jqueryui.com/menubar/demos/menubar/default.html#View" tabindex="-1" aria-haspopup="true" class="ui-button ui-widget ui-button-text-only ui-menubar-link" role="menuitem"><span class="ui-button-text">Options</span></a>'.PHP_EOL;
		$responseText.='		<ul id="ui-id-12" class="ui-menu ui-widget ui-widget-content ui-corner-all" role="menu" tabindex="0" style="display: none;" aria-hidden="true" aria-expanded="false">'.PHP_EOL;
        $responseText.='			<li class="ui-menu-item" role="presentation"><a href="#" id="import_page" class="ui-corner-all" tabindex="-1" role="menuitem">Import page</a></li>'.PHP_EOL;
		$responseText.='			<li class="ui-menu-item" role="presentation"><a href="#" id="export_page" class="ui-corner-all" tabindex="-1" role="menuitem">Export page</a></li>'.PHP_EOL;
		$responseText.='			<li class="ui-menu-item" role="presentation"><a href="#" id="add_page" class="ui-corner-all" tabindex="-1" role="menuitem">New page</a></li>'.PHP_EOL;
		$responseText.='			<li class="ui-menu-item" role="presentation"><a href="#" class="browseW3CWidgets ui-corner-all" tabindex="-1" role="menuitem">Browse Widgets</a></li>'.PHP_EOL;
		$responseText.='		</ul>'.PHP_EOL;
		$responseText.='	</li>'.PHP_EOL;
		$responseText.='</ul>'.PHP_EOL;
		$responseText.='</div>'.PHP_EOL;
		return $responseText;
	}	
}
?>
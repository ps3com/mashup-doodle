var MashupEngine = (function() {
	
	var courseId = null;
	var canEdit = null;  //TODO this is set but we need to hide bits when a student is viewing the mashup
	var htmlParent = null;
	var importOMDLPageUrl = null;
	var exportOMDLPageUrl = null;
	var getWidgetsForPageUrl = null;
	var addNewPageUrl = null;
	var getPagesUrl = null;
	var removePageUrl = null;
	var removeWidgetUrl = null;
	var newWidgetInstanceUrl = null;
	var updatePositionsUrl = null;

	var layout = (function() {
		
        var gridster = null;
        var currentPage;
        var currentLayout;
        
        function importOMDLPage(){
        	$pageFormImport = $("#pageFormImport");        	
            if ($.browser.msie == true) {
                alert("import is not supported in your browser");
            }
            else {
               // if ($pageFormImport.valid()) {
                    $pageFormImport.get(0).setAttribute('action', MashupEngine.importOMDLPageUrl + "?courseId="+MashupEngine.courseId);
                    document.getElementById('pageFormImport').onsubmit = function () {
                        document.getElementById('pageFormImport').target = 'file_upload_frame';
                        document.getElementById("file_upload_frame").onload = processFileUploadResult;
                    }
                    $pageFormImport.submit();
              //  }
            }
        }

        function processFileUploadResult() {
        	var ret = $('#file_upload_frame').contents().find('html').text();
            alert(ret);
        }
        
        function exportOMDLPage(){
			window.open(MashupEngine.exportOMDLPageUrl +"?courseId="+MashupEngine.courseId+"&pageId="+currentPage);
        }
        
        function getWidgetsForPage(){
			$.get(MashupEngine.getWidgetsForPageUrl, { pageId: currentPage, courseId: MashupEngine.courseId})
			.done(function(data) {
				var widgets = jQuery.parseJSON(data);
				generateWidgetsForPage(widgets, currentPage);
			})
			.fail(function(err) { 
				console.log(err);
				alert("Error retrieving widgets for this page");
			});	
        }
        
        function generateWrapperForSingleWidget(widget){
        	var layout = "";
       		layout+='<li id="widget-li-'+widget['id']+'" data-localident="'+widget['id']+'" data-row="'+widget['dataRow']+'" data-col="'+widget['dataCol']+'" data-sizex="'+widget['dataSizeX']+'" data-sizey="'+widget['dataSizeY']+'">';
       		layout+='    <div class="wrapper" id="widget-wrapper-'+widget['id']+'">';
       		layout+='        <div class="widgetmenubar" id="widget-menubar-'+widget['id']+'">';
       		layout+='            <div class="left" style="height:16px;width:16px;"><a href="#" class="min">&nbsp;</a><!--<img src="icons/arrow-stop-090.png"/>--></div>';
       		layout+='            <div class="right" style="height:16px;width:16px;" id="contextmenu-'+widget['id']+'"><img src="/course/format/mashup/images/calendar.png"/></div>';
       		layout+='            <div class="center" style="text-align:center;"><h2>'+widget['title']+'</h2></div>';
       		layout+='        </div>';
       		layout+='        <div class="widgetwrapper">';
       		layout+='            <iframe class="vis" src="'+widget['url']+'"></iframe>';
       		layout+='        </div>';
       		layout+='    </div>';
       		layout+='</li>';
       		return layout;
        }
        
        function generateWidgetsForPage(widgets, currentPage){
        	var gridsterLayout = '<div class="gridster" style="width:100%" data-mashup-cols="'+widgets[0].layout+'">';
        	widgets.shift(); //remove the layout code
        	gridsterLayout+='<ul>';
        	if(widgets.length>0){
	           	$.each(widgets, function() {      		
	           		gridsterLayout += generateWrapperForSingleWidget(this);
	        	});
        	}
        	else{
        		gridsterLayout+='<div id="noWidgetsFound-"'+currentPage+' style="align:center;width:100%"><h1><a href="#" class="browseW3CWidgets">Add widgets to this page</a></h1></div>';
        	}

        	gridsterLayout+='</ul>';
           	gridsterLayout+='</div>';
        	$('#page-'+currentPage).append(gridsterLayout);
        	
			//alert(data);
			//$('#page-'+currentPage).append(data);
			//alert("len:"+$('#page-'+currentPage+' ul li').size());
			//
			initGridster();
        }
        
        function addNewPage(){		
			var title = $( "#tab_title" ).val() || "Main";
			var pageLayout = $('select#page_layout option:selected').val() || 1;
			$.get(MashupEngine.addNewPageUrl, { pageName: title, pageLayout: pageLayout, courseId: MashupEngine.courseId})
			.done(function(data) {
				var page = jQuery.parseJSON(data);
				var label = page['title'];
				var id = "page-" + page['id'];
				var li =  getTabHtml(id, label);
				$("#pages").find( ".ui-tabs-nav" ).append( li );
				$("#pages").append( "<div id='" + id + "'></div>" );
				$("#pages").tabs( "refresh" );				
			})
			.fail(function(err) { 
				console.log(err);
				alert("Error adding new page:");
			});			
        }
        
        function getPagesForCourse(){
			$.get(MashupEngine.getPagesUrl, {courseId: MashupEngine.courseId})
			.done(function(data) {
				var pages = jQuery.parseJSON(data);
				if(pages != null){
					generatePageLayout(pages);
				}
				else{
					alert("Error retrieving page data. Data is null.");
				}
			})
			.fail(function(err) { 
				console.log(err);
				alert("Error retrieving page data. Ajax problem.");
			});	
        }
        
		function removePage(pageId){
			$.post(MashupEngine.removePageUrl, { courseId: MashupEngine.courseId, pageId: pageId})
			.done(function(data) {				
				console.log(data);
				$("#page-" + pageId ).remove();
				$("#pages").tabs( "refresh");
			})
			.fail(function(err) {
				console.log("Error deleting page");
				console.log(err);
			});
		}
                
		function removeWidgetFromPage(widgetId){
			$.post(MashupEngine.removeWidgetUrl, { courseId: MashupEngine.courseId, widgetId: $('#'+widgetId).attr('data-localident')})
			.done(function(data) {				
				gridster.remove_widget( $('#'+widgetId) );
				console.log(data);
				serializeGrid();
			})
			.fail(function(err) {
				console.log("Error deleting widget");
				console.log(err);
			});
		}
		
		function addNewWidgetToPage(widgetId, widgetTitle, widgetType){
			$.get(MashupEngine.newWidgetInstanceUrl, { url: widgetId, title: widgetTitle, widgetType: widgetType, courseId: MashupEngine.courseId, pageId:currentPage})
			.done(function(data) {
				var widgets = jQuery.parseJSON(data);
				var markup = generateWrapperForSingleWidget(widgets[0]);
				gridster.add_widget(markup, 1, 1, 1 ,1);
				serializeGrid();
				$("#w3cBrowseForm").dialog("close");
			})
			.fail(function(err) { 
				console.log(err);
				alert("Error adding new widget:");
				$("#w3cBrowseForm").dialog("close");
			});			
		}

		function serializeGrid(){
			$gridsterLayout = gridster.serialize(); // NEED TO COMPARE FIRST - ONLY UPDATE RECORDS NEEDED
			
			$gridsterLayout.forEach(function(widgat) {
			    console.log("@@ "+widgat.id + ":" + widgat.col + ":" + widgat.row+ ":" + widgat.sizex + ":" + widgat.sizey);
			});
			
			//console.log("overlapped:"+gridster.get_widgets_overlapped().length);
			//var available = gridster.next_position(1,1);
			//console.log("next:"+ available.row + " " + available.col);
			var jsonData = JSON.stringify($gridsterLayout);
			
			$.post(MashupEngine.updatePositionsUrl, { courseId: MashupEngine.courseId, dataEnv: jsonData})
			.done(function(data) {
				console.log("update positions done!\n"+data);
			})
			.fail(function(err) { 
				console.log(err);
				alert("Error updating widget positions");
			});		
		}
		
		function showFullscreen(originalId){
			var current = $('#'+originalId).zIndex();
			if(current>10){
				// minimize
				$('#'+originalId).zIndex(2);
			}else{
				$('#'+originalId).zIndex(12);
			}			
			$('#'+originalId).toggleClass("sDashboardWidgetContainerMaximized");
		}	
	
		function modifyWidgetDimension(originalId, action){			
			$initialSizeX = parseInt($('#'+originalId).attr('data-sizex'));
			$initialSizeY = parseInt($('#'+originalId).attr('data-sizey'));
			//alert("before:"+ $initialSizeX +":"+$initialSizeY);
			switch (action)
			{
			case 1: // inc height
			  gridster.resize_widget( $('#'+originalId), $initialSizeX, $initialSizeY + 1);
			  break;
			case 2://dec height
			  gridster.resize_widget( $('#'+originalId), $initialSizeX, $initialSizeY - 1);
			  break;
			case 3: // inc width
		      gridster.resize_widget( $('#'+originalId), $initialSizeX + 1, $initialSizeY);
			  break;
			case 4: // dec width
			  gridster.resize_widget( $('#'+originalId), $initialSizeX - 1, $initialSizeY);
			  break;
			} 
			$initialSizeX = parseInt($('#'+originalId).attr('data-sizex'));
			$initialSizeY = parseInt($('#'+originalId).attr('data-sizey'));
			//alert("after::"+ $initialSizeX +":"+$initialSizeY);
			serializeGrid();
		}
		
		function createContextMenus(){	
			$.contextMenu({
				selector: '.right',
				trigger: 'left',
				callback: function(key, options) {
					var tid = options.$trigger.attr("id");
					var wrapperId = tid.replace("contextmenu-","widget-li-");
					if(key=="fullscreen"){
						showFullscreen(wrapperId);		            	
					}
					else if(key=="delete"){
						removeWidgetFromPage(wrapperId);			            	
					}
					else if(key=="incheight"){
						modifyWidgetDimension(wrapperId,1);
					}
					else if(key=="decheight"){
						modifyWidgetDimension(wrapperId,2);
					}
					else if(key=="incwidth"){
						modifyWidgetDimension(wrapperId,3);
					}
					else if(key=="decwidth"){
						modifyWidgetDimension(wrapperId,4);
					}
					else if(key=="serialize"){
			            	serializeGrid();
					}
					else{
						alert("not delete");
					}    
				},
				items: {
					"fullscreen": {"name": "Full Screen", "icon": "edit"},
					//"delete": {"name": "Delete", "icon": "delete", disabled: true},
					"serialize": {"name": "Serialize"},
					"delete": {"name": "Delete", "icon": "delete"},
					"fold1": {
						"name": "Dimensions", 
						"items": {
							"incheight": {"name": "Increase height", "icon": "arrow-090-medium"},
							"decheight": {"name": "Decrease height", "icon": "arrow-270-medium"},
							"incwidth": {"name": "Increase width", "icon": "arrow-000-medium"},
							"decwidth": {"name": "Decrease width", "icon": "arrow-180-medium"},
						}
					}
				}
			});
		}
		
		function initBrowseForm(){
			$("#w3cBrowseForm").dialog({
        		autoOpen: false,
        		height: 300,
        		width: 350,
        		modal: true,
        		buttons: {
            		Cancel: function(){
                		$(this).dialog("close");
            		}
        		},
        		close: function(){}
    			});
			/*
			 $( "#dialog-confirm" ).dialog({
				 resizable: false,
				 autoOpen: false,
				 height:140,
				 modal: true,
				 buttons: {
					 "Delete all items": function() {
						 $( this ).dialog( "close" );
					 },
					 Cancel: function() {
						 $( this ).dialog( "close" );
					 }
				 }
			 });
			 */
		}
		
		function initMenuBar(){
			var menuHTML="";
			menuHTML+='<div>';
			menuHTML+='<ul id="bar1" class="menubar ui-menubar ui-widget-header ui-helper-clearfix" role="menubar" style="background: #E4E2D6;">';
			menuHTML+='	<li class="ui-menubar-item" role="presentation">';
			menuHTML+='		<a href="#" tabindex="-1" aria-haspopup="true" class="ui-button ui-widget ui-button-text-only ui-menubar-link" role="menuitem"><span class="ui-button-text">Options</span></a>';
			menuHTML+='		<ul id="ui-id-12" class="ui-menu ui-widget ui-widget-content ui-corner-all" role="menu" tabindex="0" style="display: none;" aria-hidden="true" aria-expanded="false">';
	        menuHTML+='			<li class="ui-menu-item" role="presentation"><a href="#" id="import_page" class="ui-corner-all" tabindex="-1" role="menuitem">Import page</a></li>';
			menuHTML+='			<li class="ui-menu-item" role="presentation"><a href="#" id="export_page" class="ui-corner-all" tabindex="-1" role="menuitem">Export page</a></li>';
			menuHTML+='			<li class="ui-menu-item" role="presentation"><a href="#" id="add_page" class="ui-corner-all" tabindex="-1" role="menuitem">New page</a></li>';
			menuHTML+='			<li class="ui-menu-item" role="presentation"><a href="#" class="browseW3CWidgets ui-corner-all" tabindex="-1" role="menuitem">Browse Widgets</a></li>';
			menuHTML+='		</ul>';
			menuHTML+='	</li>';
			menuHTML+='</ul>';
			menuHTML+='</div>';
			$('#'+MashupEngine.htmlParent).append(menuHTML);
			
			$("#bar1").menubar({
				position: {
					within: $("#demo-frame").add(window).first()
				}
			//,select: select
			});
	
			$(".menubar-icons").menubar({
				autoExpand: true,
				menuIcon: true,
				buttons: true,
				position: {
					within: $("#demo-frame").add(window).first()
				}
			//,select: select
			});
			
			$(".browseW3CWidgets").click(function() { // this is the browse widgets link in the main menu
				$("#widget_gallery").show();
				$("#w3cBrowseForm").dialog("open");
			});
			
			$("#export_page").click(function() { 
				exportOMDLPage();
			});
			
		}
		
        function generatePageLayout(pages){
        	$.each(pages, function() {     
        		$("#pages ul").append(getTabHtml('page-'+this['id'], this['title']));
        		$("#pages").append( "<div id='page-" + this['id'] + "'></div>" );
        	});
        	
        	// do the first page
        	pageId = pages[0]['id'];
        	//layout = pages[0]['pageLayout'];
        	currentPage = pageId;
        	getWidgetsForPage();
        	initPages();
        }

        function getTabHtml(id, label){
        	var tabTemplate = "<li><a href='#{href}'>#{label}</a> <span class='ui-icon ui-icon-close' role='presentation'>Remove page</span></li>";
        	li = $( tabTemplate.replace( /#\{href\}/g, "#" + id ).replace( /#\{label\}/g, label ) );
        	return li;
        }
		
		function initPages(){
			var tabs = $("#pages").tabs(					
					{selected: 0,
					    select: function(event, ui) {
					        $('.gridster').remove();
					        var pageId = ui.tab.attributes[0].nodeValue.replace("#page-","");
					        currentPage = pageId;
					        getWidgetsForPage();	       
					    }
					}		
			);
			
			// close icon: removing the tab on click
			tabs.delegate( "span.ui-icon-close", "click", function() {
				//$("#confirmDeletePageDialog").dialog("open"); //TODO CONFIRM DIALOG FOR PAGE DELETE
				var tabCount = $("#pages").tabs("length");
				// dont delete a page if its the only one
				if(tabCount>1){
					var panelId = $( this ).closest( "li" ).remove().attr( "aria-controls" );
					removePage(panelId.replace("page-",""));			
				}else{
					alert("You must have at least one page.");
				}
			});
			
			tabs.bind( "keyup", function( event ) {
				if ( event.altKey && event.keyCode === $.ui.keyCode.BACKSPACE ) {
					var panelId = tabs.find( ".ui-tabs-active" ).remove().attr( "aria-controls" );
					removePage(panelId.replace("page-",""));
				}
			});		
			
			//************** add page dialog **************************
			// modal dialog init: custom buttons and a "close" callback reseting the form inside
			var addPageDialog = $("#addPageDialog").dialog({
				autoOpen: false,
				modal: true,
				buttons: {
					Add: function() {
						addNewPage();
						$(this).dialog("close");
					},
					Cancel: function() {
						$(this).dialog("close");
					}
				},
				close: function() {
					form[0].reset();
				}
			});

			// addpage form: calls addnewpage function on submit and closes the dialog
			// needed when user presses enter rather than click the add button
			var form = addPageDialog.find("form").submit(function(event) {
				addNewPage();
				addPageDialog.dialog("close");
				event.preventDefault();
			});
			
			// addpage button: just opens the dialog
			$("#add_page").click(function() {
				addPageDialog.dialog("open");
			});
			//************** end add page dialog **************************
	
			//************** import page dialog **************************
			// modal dialog init: custom buttons and a "close" callback reseting the form inside
			var importPageDialog = $("#importPageDialog").dialog({
				autoOpen: false,
				modal: true,
				buttons: {
					Add: function() {
						importOMDLPage();
						$(this).dialog("close");
					},
					Cancel: function() {
						$(this).dialog("close");
					}
				},
				close: function() {
					$('#pageFormImport')[0].reset();
				}
			});

			// addpage button: just opens the dialog
			$("#import_page").click(function() {
				importPageDialog.dialog("open");
			});
			//************** end import page dialog **************************
		}
		
		function initGridster(){
			var cols = $(".gridster").attr('data-mashup-cols');
			var canvasWidth = $('.gridster').css('width').replace("px","");			
			var widgetWidth=canvasWidth/cols - 20;
			
			$(".gridster ul").gridster({
			widget_margins: [10, 10],
			widget_base_dimensions: [widgetWidth, 350],
			cols: cols,
			min_cols: cols,
			extra_cols: 0,
			max_size_x: cols,
			max_size_y: cols,
			avoid_overlapped_widgets: true,
			serialize_params: function($w, wgd) {
				return {
					id: wgd.el[0].dataset.localident,
					col: wgd.col,
					row: wgd.row,
					sizex: wgd.size_x,
					sizey: wgd.size_y
				};
			},
			avoid_overlapped_widgets: true,
		        draggable: {
		        	start: function(event, ui){
						$(".vis").hide();
		            },
		            stop: function(event, ui){
						$(".vis").show();
						serializeGrid();
		            }
				}
			});			
			gridster = $(".gridster ul").gridster().data('gridster');
			console.log(gridster.serialize());
			
			// listen to window resize events so we can resize the widget containers
			$(window).resize( function() {
				var canvasWidth = $('.gridster').css('width').replace("px","");
				var widgetWidth=canvasWidth/cols - 20;
				gridster.resize_widget_dimensions({widget_base_dimensions: [widgetWidth, 350], min_width: 200});
			});
		}
		
		function initParent(){
			var data= '<div style="height:100%;" id="pages"><ul></ul></div>';
			$('#'+MashupEngine.htmlParent).append(data);
		}

        function init(){
        	if(MashupEngine.canEdit){
        		initMenuBar();
        	}
        	initParent();
        	initBrowseForm();
        	getPagesForCourse();
			createContextMenus();
		}
        return {
            init: init,
            addNewWidgetToPage: addNewWidgetToPage,
            initGridster : initGridster
        };
    })();
	
	function init(args) {
        this.courseId = args.courseId;
        this.canEdit = args.canEdit;
        this.htmlParent = args.htmlParent;
        this.getWidgetsForPageUrl = args.getWidgetsForPageUrl;
        this.importOMDLPageUrl = args.importOMDLPageUrl;
        this.exportOMDLPageUrl = args.exportOMDLPageUrl;
        this.addNewPageUrl = args.addNewPageUrl;
        this.getPagesUrl = args.getPagesUrl;    
        this.removePageUrl = args.removePageUrl;
		this.removeWidgetUrl = args.removeWidgetUrl;
		this.newWidgetInstanceUrl = args.newWidgetInstanceUrl;
		this.updatePositionsUrl = args.updatePositionsUrl;
        layout.init();
    }

    // public  API
    return {
        init: init,
        addNewWidgetToPage : layout.addNewWidgetToPage,
        initGridster : layout.initGridster
    };
})();
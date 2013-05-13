<?php
require_once("{$CFG->dirroot}/course/format/mashup/classes/connectors/w3c/WookieConnectorService.php");

class Gallery {

    function init(){
    }

    function showGallery(){
    	global $USER,$CFG;
		$instanceID = "gallery_instance_key";
        $gallery = "<div id=\"widget_gallery\" class=\"widget-gallery\" style=\"display:none\">";
        $conn = new WookieConnectorService ($CFG->mashup_wookie_url, $CFG->mashup_wookie_key, $instanceID, $USER->id );

        $widgets = $conn->getAvailableWidgets();
        foreach ($widgets as $widget){
            $gallery = $gallery."
                <div class=\"wookie-widget\">
                      <div class=\"wookie-icon-area\"><img class=\"wookie-icon\" src=\"".$widget->getIcon()."\" width=\"75\" height=\"75\"/></div>
                      <div class=\"wookie-title\">".$widget->getTitle()."</div>
                      <div class=\"wookie-description\">".$widget->getDescription()."</div>
                      <div class=\"wookie-choose\"><input type=\"button\" class=\"wookie-button\" value=\"select widget\" id=\"".$widget->getIdentifier()."\"></div>
                </div>
            ";
        }
        $gallery = $gallery."</div><br/>";
        
        $gallery .='
        <script>
        $(".wookie-button").unbind("click")
        $(".wookie-button").click( function(e){
        		MashupEngine.addNewWidgetToPage($(this).attr("id"), $(this).parent().prevAll("div.wookie-title:first").text(), 1);
        });
        </script>
        ';
        return $gallery;
    }

}

// how to use
// $gallery = new Gallery();
// print($gallery->showGallery()); //prints it all out
?>

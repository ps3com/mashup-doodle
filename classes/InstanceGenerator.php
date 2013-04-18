<?php
require_once("{$CFG->dirroot}/course/format/mashup/framework/Widgeta.php");
require_once("{$CFG->dirroot}/course/format/mashup/classes/MoodleWidget.php");
require_once("{$CFG->dirroot}/course/format/mashup/framework/WookieConnectorService.php");

class InstanceGenerator {

	private $connection = null;
	private $instanceId = null;

	function __construct($instanceId) {
		global $USER,$CFG;
		$this->instanceId = $instanceId;
		$this->connection = new WookieConnectorService ($CFG->mashup_wookie_url, $CFG->mashup_wookie_key, $this->instanceId, $USER->id, $USER->username);
	}

	public function getInstanceMarkup($persistedWidget){
		global $USER;		
		$widgetInstance = $this->getWidget($this->connection, $persistedWidget->getUrl());
		
		//set username
		$this->setProperty($widgetInstance, "username", $USER->username, $this->connection);
		// Add participant
		//$this->addParticipant($widgetInstance, $conn);
		$layout="";
		$layout.=$this->generateMarkupForWidget($widgetInstance, $persistedWidget);
		return $layout;
	}

	private function generateMarkupForWidget($widgetInstance, $persistedWidget){
		$output="";
		$output.='<li id="widget-li-'.$persistedWidget->getId().'" data-localident="'.$persistedWidget->getId().'" data-row="'.$persistedWidget->getDataRow().'" data-col="'.$persistedWidget->getDataCol().'" data-sizex="'.$persistedWidget->getDataSizeX().'" data-sizey="'.$persistedWidget->getDataSizeY().'">'.PHP_EOL;
		$output.='    <div class="wrapper" id="widget-wrapper-'.$persistedWidget->getId().'">'.PHP_EOL;
		$output.='        <div class="widgetmenubar" id="widget-menubar-'.$persistedWidget->getId().'">'.PHP_EOL;
		$output.='            <div class="left" style="height:16px;width:16px;"><a href="#" class="min">&nbsp;</a><!--<img src="icons/arrow-stop-090.png"/>--></div>'.PHP_EOL;
		$output.='            <div class="right" style="height:16px;width:16px;" id="contextmenu-'.$persistedWidget->getId().'"><img src="/course/format/mashup/images/calendar.png"/></div>'.PHP_EOL;
		$output.='            <div class="center" style="text-align:center;"><h2>'.$persistedWidget->getTitle() .'</h2></div>'.PHP_EOL;
		$output.='        </div>'.PHP_EOL;
		$output.='        <div class="widgetwrapper">'.PHP_EOL;
		$output.='            <iframe class="vis" src="'.$widgetInstance->getUrl().'"></iframe>'.PHP_EOL;
		$output.='        </div>'.PHP_EOL;
		$output.='    </div>'.PHP_EOL;
		$output.='</li>'.PHP_EOL;
		return $output;
	}

	function getWidget($connection, $widgetIdentifier){
		//global $USER, $COURSE, $CFG;
		$widget = $connection->getOrCreateInstance ($widgetIdentifier);
		if ( $widget ) {
			/*
			 $this->config->widget_url =$widget->getURL();
			$this->config->widget_height = $widget->getHeight();
			$this->config->widget_width = $widget->getWidth();
			$this->config->widget_title = $widget->getTitle();
			$this->title = $this->config->widget_title;
			$this->instance_config_commit();
			$this->refresh_content();
			*/
		}
		return $widget;
	}

	/**
	 * Sets a Personal Property for a Widget instance
	 * NOTE that this should in future call the REST API using POST
	 * but this is currently tricky with Moodle's download_file_content function
	 */
	function setProperty($widgetInstance, $key,$value, $connection){

		global $USER, $COURSE,$CFG;
		$newProperty = new Property ( $key, $value );
		$r = $connection->setProperty ( $widgetInstance, $newProperty );
	}

	/**
	 * Adds a participant
	 * NOTE that this should in future call the REST API using POST
	 * but this is currently tricky with Moodle's download_file_content function
	 */
	function addParticipant($widgetinstance, $connection){

		global $USER, $COURSE, $CFG;

		if ($USER->picture != ""){
			$src = $CFG->httpswwwroot.'/user/pix.php/'.$USER->id.'/f1.jpg';
		} else {
			$src = $CFG->httpswwwroot.'/pix/u/f1.png';
		}
		$thisUser = new User ( $USER->id, $USER->username, $src );
		$connection->addParticipant($widgetinstance, $thisUser );
	}
}

?>
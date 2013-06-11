<?php
require_once("{$CFG->dirroot}/config.php");
require_once("{$CFG->dirroot}/course/format/mashup/classes/connectors/opensocial/util/BasicSecurityToken.php");
require_once("{$CFG->dirroot}/course/format/mashup/classes/connectors/opensocial/util/SecurityToken.php");

class SecurityTokenHandler {

	private $widget;
	private $courseId;
	private $userId;
	private $domain;
	

	function __construct($widget, $domain="default") {
		global $USER,$CFG,$COURSE;
		$this->widget = $widget;
		$this->courseId = $COURSE->id;
		$this->userId = $USER->id;
		$this->domain = $domain;
	}

	//staff:staff:4:default:http://gadgeturl.xml:4:default
	function getPlainSecurityToken(){
		$token = "";
		
		$token.= $this->courseId."a:"; // owner
		$token.= $this->userId."a:"; // viewer
		$token.= $this->widget->getId()."a:";// app id
		$token.= $this->domain."a:";// domain key
		$token.= urlencode($this->widget->getUrl()) . ":"; // app url
		$token.= $this->widget->getId()."a:";// mod id
		$token.= $this->domain; //container
		
		/*
		$token.="canonical:"; // owner
		$token.= "john.doe:"; // viewer
		$token.= "6457:";// app id
		$token.= "shindig:";// domain key
		$token.= urlencode($this->widget->getUrl()) . ":"; // app url
		//$token.= $this->widget->getUrl() . ":"; // app url
		$token.= "0:";// mod id
		$token.= $this->domain; //container
		*/
		
		//$token2 = "canonical:john.doe:2422:shindig:http%3A//aurl.com/something:0:default";
		//$token2 = 'canonical:john.doe:2422a:shindig:'.urlencode($this->widget->getUrl()).':0:default';
		return $token;
		//return urlencode($token2);
		//return 'canonical:john.doe:6457:shindig:http%3A//demo.ict-omelette.eu/container/sample-pubsub-2-publisher.xml:0:default';
	}
	
	// TODO Fix this method
	function getEncryptedSecurityToken(){
		$securityToken = BasicSecurityToken::createFromValues(
			$this->courseId.":", // owner
			$this->userId.":", // viewer
			$this->widget->getId().":",// app id
			$this->domain,// domain key
			urlencode($this->widget->getUrl()) . ":", // app url
			$this->widget->getId().":",// mod id
			$this->domain //container
		);		
		//var_dump($securityToken);
		return $securityToken->toSerialForm();
	}
	
}

?>
<?php 
libxml_use_internal_errors(true);
require_once("../../../../../config.php");
require_once("{$CFG->dirroot}/course/format/mashup/classes/MashupDatabaseHelper.php");
require_once("{$CFG->dirroot}/course/format/mashup/classes/omdl/OmdlImporter.php");
$courseId = required_param('courseId', PARAM_INT);
require_login($courseId);

$PAGE->set_url('/course/format/mashup/classes/actions/importOmdlAction.php', array('id' => $courseId));

// set permissions to prevent students etc to execute this
$course = $PAGE->course;
$context = get_context_instance(CONTEXT_COURSE, $course->id);
if (!has_capability('moodle/course:manageactivities', $context)) {
	echo "You do not have permission to complete this action";
}
else{

	$uploadErrors = array(
			0=>'There is no error, the file uploaded with success',
			1=>'The uploaded file exceeds the upload max filesize allowed. (50k)',
			2=>'The uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the HTML form',
			3=>'The uploaded file was only partially uploaded',
			4=>'No file was uploaded',
			6=>'Missing a temporary folder',
			7=>'Invalid File format',
			8=>'File must end with .xml suffix.'
	);
	
	
	if ($_FILES["omdlFile"]["error"] > 0){	
		echo "Error: " . $uploadErrors[$_FILES["omdlFile"]["error"]];
	}
	else{
		if ($_FILES["omdlFile"]["type"] != "text/xml"){
			echo "Error: " . $uploadErrors[7];
		}
		else{
			if($_FILES["omdlFile"]["size"] > 50000){
				echo "Error: " . $uploadErrors[1];
			}
			else{
				$allowedExts = array("xml");
				$filename = $_FILES["omdlFile"]["name"];
				$tmp = explode('.', $filename);
				$extension = end($tmp);
				if(!in_array($extension, $allowedExts)){
					echo "Error: " . $uploadErrors[8];
				}
				else{
					$upload = (object) $_FILES['omdlFile'];
					$doc = simplexml_load_file($upload->tmp_name);
					
					if (!$doc) {
						$return="Unable to parse xml document\n";
						foreach (libxml_get_errors() as $error){
							switch ($error->level) {
								case LIBXML_ERR_WARNING:
									$return .= "Warning $error->code: ";
									break;
								case LIBXML_ERR_ERROR:
									$return .= "Error $error->code: ";
									break;
								case LIBXML_ERR_FATAL:
									$return .= "Fatal Error $error->code: ";
									break;
							}
							$return .= trim($error->message) .
							"  Line: $error->line" .
							"  Column: $error->column\n";
						}
						
						libxml_clear_errors();
						echo $return;
					}
					else{
						$omdlImporter = new OMDLImporter($courseId, $doc);
						echo $omdlImporter->fromXML();
					}
				}
			}
		}
	}
}
?>
<?php
$settings->add(new admin_setting_configtext('mashup_wookie_url','Wookie Server URL',
                  '', "http://localhost:8080/wookie/", PARAM_TEXT));

$settings->add(new admin_setting_configtext('mashup_wookie_key', 'Wookie API Key',
                   '', "TEST", PARAM_TEXT));

$settings->add(new admin_setting_configtext('mashup_wookie_admin_username', 'Wookie Admin username',
		'', "java", PARAM_TEXT));

$settings->add(new admin_setting_configtext('mashup_wookie_admin_password', 'Wookie Admin password',
		'', "java", PARAM_TEXT));

$settings->add(new admin_setting_configtextarea('mashup_wookie_moodleparams','Moodle Params',
					get_string('moodleparamshelp','format_mashup'),'',PARAM_RAW));

?>

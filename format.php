<?php
require_once("{$CFG->dirroot}/course/format/mashup/classes/Mashup.php");
defined('MOODLE_INTERNAL') || die();
echo '<link rel="stylesheet" type="text/css" href="'.$CFG->wwwroot.'/course/format/mashup/script/gridster/jquery.gridster.css">'.PHP_EOL;
echo '<link rel="stylesheet" type="text/css" href="'.$CFG->wwwroot.'/course/format/mashup/script/jqueryUI/jquery-ui-1.9.2.custom.min.css">'.PHP_EOL;
echo '<link rel="stylesheet" href="'.$CFG->wwwroot.'/course/format/mashup/script/jqueryUI/jquery.ui.menubar.css">'.PHP_EOL;
echo '<link rel="stylesheet" href="'.$CFG->wwwroot.'/course/format/mashup/script/contextmenu/jquery.contextMenu.css">'.PHP_EOL;
echo '<link rel="stylesheet" type="text/css" href="'.$CFG->wwwroot.'/course/format/mashup/style/style.css">'.PHP_EOL;
echo '<link rel="stylesheet" href="http://code.jquery.com/ui/1.10.3/themes/smoothness/jquery-ui.css" />'.PHP_EOL;

$mashup = new Mashup();
print($mashup->initUi());
?>

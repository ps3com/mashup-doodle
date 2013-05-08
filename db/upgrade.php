<?php

defined('MOODLE_INTERNAL') || die();

function xmldb_format_mashup_upgrade($oldversion = 0) {
    global $DB;

    $dbman = $DB->get_manager();
        
    if ($oldversion < 2013051403) {
        // Define table mashup_page to be created
        $table = new xmldb_table('mashup_page');

        // Adding fields to table mashup_page
        $table->add_field('entity_id', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, XMLDB_SEQUENCE, null, null);
        $table->add_field('page_name', XMLDB_TYPE_CHAR, '45', null, null, null, 'Main', null);
        $table->add_field('course_id', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null, null);
        $table->add_field('page_layout', XMLDB_TYPE_INTEGER, '2', XMLDB_UNSIGNED, null, null, null, null);

        /// Adding keys to table mashup_page
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('entity_id'));
        
        // Launch create table for mashup_page
        $dbman->create_table($table);  
        upgrade_plugin_savepoint(true, '2013051403', 'format', 'mashup');
        
        //
        
        $table = new xmldb_table('mashup_widget');
        
        // Adding fields to table mashup_page
        $table->add_field('entity_id', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, XMLDB_SEQUENCE, null, null);
        $table->add_field('course_id', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null, null);
        $table->add_field('url', XMLDB_TYPE_CHAR, '200', null, XMLDB_NOTNULL, null, null, null);
        $table->add_field('title', XMLDB_TYPE_CHAR, '100', null, XMLDB_NOTNULL, null, null, null);
        $table->add_field('widget_type', XMLDB_TYPE_INTEGER, '3', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null, null);
        $table->add_field('wrow', XMLDB_TYPE_INTEGER, '3', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null, null);
        $table->add_field('wcol', XMLDB_TYPE_INTEGER, '3', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null, null);
        $table->add_field('size_x', XMLDB_TYPE_INTEGER, '3', XMLDB_UNSIGNED, null, null, null, null);
        $table->add_field('size_y', XMLDB_TYPE_INTEGER, '3', XMLDB_UNSIGNED, null, null, null, null);
        $table->add_field('page_id', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null, null);
        
        /// Adding keys to table mashup_page
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('entity_id'));
        
        // Launch create table for mashup_page
        $dbman->create_table($table);
        upgrade_plugin_savepoint(true, '2013051403', 'format', 'mashup');
    }
    
    return true;
}

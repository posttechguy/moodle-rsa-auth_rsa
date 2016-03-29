<?php
/**
 * Custom authentication for Renal Society of Australia project
 *
 * Upgrade script
 *
 * @package    auth_rsa
 * @author     Bevan Holman <bevan@pukunui.com>, Pukunui
 * @copyright  2015 onwards, Pukunui
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

function xmldb_auth_rsa_upgrade($oldversion) {
    global $DB;
    $dbman = $DB->get_manager();

    /// Add a new table mdl_auth_rsa_cpdlog to plugin
    if ($oldversion < 2015121506) {

        // Define table auth_rsa_cpdlog to be created.
        $table = new xmldb_table('auth_rsa_cpdlog');

        // Adding fields to table auth_rsa_cpdlog.
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('userid', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, '0');
        $table->add_field('firstname', XMLDB_TYPE_CHAR, '100', null, XMLDB_NOTNULL, null, null);
        $table->add_field('lastname', XMLDB_TYPE_CHAR, '100', null, XMLDB_NOTNULL, null, null);
        $table->add_field('email', XMLDB_TYPE_CHAR, '100', null, null, null, null);
        $table->add_field('courseid', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, '0');
        $table->add_field('cpdpoints', XMLDB_TYPE_NUMBER, '8, 2', null, XMLDB_NOTNULL, null, '0.0');
        $table->add_field('timestarted', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, '0');
        $table->add_field('timefinished', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, '0');
        $table->add_field('timemodified', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, '0');

        // Adding keys to table auth_rsa_cpdlog.
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));

        // Conditionally launch create table for auth_rsa_cpdlog.
        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }

        // Rsa savepoint reached.
        upgrade_plugin_savepoint(true, 2015121506, 'auth', 'rsa');
    }
    if ($oldversion < 2015121520) {

        // Define field hash to be added to auth_whia_domain.
        $table = new xmldb_table('auth_rsa_cpdlog');
        $field = new xmldb_field('hash', XMLDB_TYPE_CHAR, '100', null, XMLDB_NOTNULL, null, null, 'timemodified');

        // Conditionally launch add field cohortid.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Monitor savepoint reached.
        upgrade_plugin_savepoint(true, 2015121520, 'auth', 'rsa');
    }

    return true;
}
<?php
/**
 * Custom authentication for Water Corporation Water Education project
 *
 * Installation class definition
 *
 * @package    auth_rsa
 * @author     Bevan Holman <bevan@pukunui.com>, Pukunui
 * @copyright  2015 onwards, Pukunui
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace auth_rsa;

defined('MOODLE_INTERNAL') || die();

/**
 * Installation manager.
 *
 * @package    auth_rsa
 * @author     Bevan Holman <bevan@pukunui.com>, Pukunui
 * @copyright  2015 onwards, Pukunui
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class install {

    /**
     * Load the given file into the appropriate table.
     *
     * The tables only have an id and name field - this consistency is important!
     *
     * @uses $CFG
     * @uses $DB
     * @param string $filename
     * @param string $table
     * @return integer  number of files inserted.
     */
    public static function get_table_data($filename) {
        global $CFG, $DB;

        $fullpath = $CFG->dirroot.'/auth/rsa/db/'.$filename;
        $count = 0;
        if (file_exists($fullpath)) {

            if ($fh = fopen($fullpath, "r")) {
                $record = array();
                while (!feof($fh)) {
                    $record[] = trim(clean_param(fgets($fh, 256), PARAM_RAW));
                }
                fclose($fh);
            }
        }
        return $record;
    }

    /**
     * Create custom profile fields.
     * We do this by directly inserting the definition into the database.
     *
     */
    public static function create_profile_field($shortname, $description, $categoryid, $sortorder, $required) {
        global $DB, $OUTPUT;

        if ($DB->record_exists('user_info_field', array('shortname' => $shortname))) {
            echo $OUTPUT->notification(
                    get_string('install:customprofilefield:alreadyexists', 'auth_rsa', $shortname),
                    'notifyproblem'
            );
            return false;
        }
        echo $OUTPUT->notification(get_string('install:customprofilefield:name', 'auth_rsa'), $shortname);

        $param1 = array('dbsql'  => $sql,
                        'dbhost' => '',
                        'dbname' => '',
                        'dbuser' => '',
                        'dbpass' => '',
                        'dbtype' => 'mysqli',
                       );
        $record = array('shortname'         => $shortname,
                        'name'              => $shortname,
                        'datatype'          => 'menusql',
                        'description'       => $description,
                        'descriptionformat' => 1,
                        'categoryid'        => $categoryid,
                        'sortorder'         => $sortorder,
                        'required'          => $required,
                        'locked'            => 0,
                        'visible'           => 2,
                        'forceunique'       => 0,
                        'signup'            => 1,
                        'defaultdata'       => '',
                        'defaultdataformat' => 0,
                        'param1'            => $this->get_table_data($shortname.".txt"),
                       );
        return $DB->insert_record('user_info_field', (object)$record);
    }

    /**
     * Execute the installation process.
     */
    public static function execute() {
        global $CFG, $DB, $OUTPUT;

        echo $OUTPUT->notification(get_string('install:customprofilefield:data', 'auth_rsa'), 'notifymessage');

        // Load up the information tables.
        $yearleveldata = self::get_table_data('yearlevel.txt');
        $yearofbirthdata = self::get_table_data('yearofbirth.txt');
        $whereareyoufromdata = self::get_table_data('whereareyoufrom.txt');
        $typeofaccountdata = self::get_table_data('typeofaccount.txt');

        echo $OUTPUT->notification(get_string('install:customprofilefield:category', 'auth_rsa'), 'notifymessage');
        // Create a profile category.
        if (!($categoryid = $DB->get_field('user_info_category', 'id',
                array('name' => get_string('install:customprofilefield:category:student', 'auth_rsa'))))) {
            if (!($sortorder = $DB->get_field_sql('SELECT MAX(sortorder) FROM {user_info_category}', null))) {
                $sortorder = 0;
            }
            $category = array('name' => get_string('install:customprofilefield:category:student', 'auth_rsa'),
                              'sortorder' => ++$sortorder);
            $categorystudentid = $DB->insert_record('user_info_category', (object)$category);
        }
        // Add new profile fields.
        if (!empty($categorystudentid)) {
            // Create a custom text field for "yearlevel" the long way.
            $this->create_profile_field('yearlevel',
                                        get_string('customprofilefield:yearlevel:desc:default', 'auth_rsa'),
                                        $categorystudentid,
                                        $sortorder++,
                                        0);

            // Create a custom text field for "yearofbirth" the long way.
            $this->create_profile_field('yearofbirth',
                                        get_string('customprofilefield:yearofbirth:desc:default', 'auth_rsa'),
                                        $categorystudentid,
                                        $sortorder++,
                                        0);
        }
        if (!($categoryteacherid = $DB->get_field('user_info_category', 'id',
                array('name' => get_string('install:customprofilefield:category:teacher', 'auth_rsa'))))) {
            if (!($sortorder = $DB->get_field_sql('SELECT MAX(sortorder) FROM {user_info_category}', null))) {
                $sortorder = 0;
            }
            $category = array('name' => get_string('install:customprofilefield:category:teacher', 'auth_rsa'),
                              'sortorder' => ++$sortorder);
            $categoryteacherid = $DB->insert_record('user_info_category', (object)$category);
        }
        if (!empty($categoryteacherid)) {
            // Create a custom text field for "whereareyoufrom" the long way.
            $this->create_profile_field('whereareyoufrom',
                                        get_string('customprofilefield:whereareyoufrom:desc:default', 'auth_rsa'),
                                        $categoryteacherid,
                                        $sortorder++,
                                        0);
        }
        // Create a profile category.
        if (!($categorydefaultid = $DB->get_field('user_info_category', 'id',
                array('name' => get_string('install:customprofilefield:category:default', 'auth_rsa'))))) {
            if (!($sortorder = $DB->get_field_sql('SELECT MAX(sortorder) FROM {user_info_category}', null))) {
                $sortorder = 0;
            }
            $category = array('name' => get_string('install:customprofilefield:category:default', 'auth_rsa'),
                              'sortorder' => ++$sortorder);
            $categorydefaultid = $DB->insert_record('user_info_category', (object)$category);
        }
        if (!empty($categorydefaultid)) {
            // Create a custom text field for "typeofaccount" the long way.
            $this->create_profile_field('typeofaccount',
                                        get_string('customprofilefield:typeofaccount:desc:default', 'auth_rsa'),
                                        $categorydefaultid,
                                        $sortorder++,
                                        0);
        }

        // Add new roles

        if (!($sortorder = $DB->get_field_sql('SELECT MAX(sortorder) FROM {roles}', null))) {
            $sortorder = 0;
        }

        $newrole = new stdClass();
        $newrole->name          = 'Adult';
        $newrole->shortname     = 'adult';
        $newrole->description   = 'An adult student';
        $newrole->sortorder     = $sortorder++;
        $newrole->archetype     = 'student';

        $roleid = $DB->insert_record('role',  $newrole);

        $newrole->name          = 'Water Corp. Teacher';
        $newrole->shortname     = 'wceteacher';
        $newrole->description   = 'A teaching student';
        $newrole->sortorder     = $sortorder;
        $newrole->archetype     = 'student';

        $roleid = $DB->insert_record('role',  $newrole);
    }
}

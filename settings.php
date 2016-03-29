<?php
/**
 * Custom authentication for Water Corporation Water Efficiency project
 *
 * Administration settings
 *
 * @package    auth_rsa
 * @author     Bevan Holman <bevan@pukunui.com>, Pukunui
 * @copyright  2015 onwards, Pukunui
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die;

// Settings menu.
/*
$ADMIN->add('root', new admin_category('auth_rsa', get_string('pluginname', 'auth_rsa')));

$ADMIN->add('auth_rsa',
    new admin_externalpage('auth_rsa_student', get_string('settings:menu:addstudent', 'auth_rsa'),
    new moodle_url('/auth/rsa/student.php'), 'auth/watercorped:teacherconfig')
);
*/
        $settings->add(new admin_setting_configtext(
                'auth_rsa/oauthclientid',
                new lang_string('setting:oauthclientid', 'auth_rsa'),
                new lang_string('setting:oauthclientid:desc', 'auth_rsa'),
                'rsa',
                PARAM_RAW,
                100
                ));

        $settings->add(new admin_setting_configtext(
                'auth_rsa/oauthclientsecret',
                new lang_string('setting:oauthclientsecret', 'auth_rsa'),
                new lang_string('setting:oauthclientsecret:desc', 'auth_rsa'),
                'rsa_private',
                PARAM_RAW,
                100
                ));

        $settings->add(new admin_setting_configtext(
                'auth_rsa/oauthcpdlogtoken',
                new lang_string('setting:oauthcpdlogtoken', 'auth_rsa'),
                new lang_string('setting:oauthcpdlogtoken:desc', 'auth_rsa'),
                '',
                PARAM_RAW,
                500
                ));
        $settings->add(new admin_setting_configtext(
                'auth_rsa/oauthcpdlogtokenexpiry',
                new lang_string('setting:oauthcpdlogtokenexpiry', 'auth_rsa'),
                new lang_string('setting:oauthcpdlogtokenexpiry:desc', 'auth_rsa'),
                0,
                PARAM_INT,
                10
                ));

        $settings->add(new admin_setting_configtext(
                'auth_rsa/oauthcpdlogappname',
                new lang_string('setting:oauthcpdlogappname', 'auth_rsa'),
                new lang_string('setting:oauthcpdlogappname:desc', 'auth_rsa'),
                '',
                PARAM_RAW,
                100
                ));

        $settings->add(new admin_setting_configtext(
                'auth_rsa/oauthcpdlogtokenurl',
                new lang_string('setting:oauthcpdlogtokenurl', 'auth_rsa'),
                new lang_string('setting:oauthcpdlogtokenurl:desc', 'auth_rsa'),
                '',
                PARAM_RAW,
                100
                ));

        $settings->add(new admin_setting_configtext(
                'auth_rsa/oauthcpdlogcategoryid',
                new lang_string('setting:oauthcpdlogcategoryid', 'auth_rsa'),
                new lang_string('setting:oauthcpdlogcategoryid:desc', 'auth_rsa'),
                '',
                PARAM_RAW,
                10
                ));

        $settings->add(new admin_setting_configtext(
                'auth_rsa/oauthcpdlogactivityid',
                new lang_string('setting:oauthcpdlogactivityid', 'auth_rsa'),
                new lang_string('setting:oauthcpdlogactivityid:desc', 'auth_rsa'),
                '',
                PARAM_RAW,
                10
                ));

        $settings->add(new admin_setting_configtext(
                'auth_rsa/oauthcpdloglastrun',
                new lang_string('setting:oauthcpdloglastrun', 'auth_rsa'),
                new lang_string('setting:oauthcpdloglastrun:desc', 'auth_rsa'),
                0,
                PARAM_INT,
                10
                ));

        $settings->add(new admin_setting_configtext(
                'auth_rsa/lasttimestamp',
                new lang_string('setting:lasttimestamp', 'auth_rsa'),
                new lang_string('setting:lasttimestamp:desc', 'auth_rsa'),
                0,
                PARAM_INT,
                10
                ));

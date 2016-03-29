<?php
/**
 * Custom authentication for Renal Society of Australia project login/signup page
 *
 * @package    auth_rsa
 * @author     Bevan Holman <bevan@pukunui.com>, Pukunui
 * @copyright  2015 onwards, Pukunui
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once('../../config.php');
require_once('./locallib.php');
require_once('./auth.php');
require_once('./forms.php');
require_once($CFG->dirroot.'/user/lib.php');
require_once($CFG->dirroot.'/admin/tool/log/store/standard/classes/log/store.php');

$systemcontext = context_system::instance();
$strpluginname = get_string('pluginname', 'auth_rsa');
$returnurl = "/auth/rsa/enroluser.php";
$title = get_string('enroluser:metatag:heading', 'auth_rsa');

$PAGE->set_context($systemcontext);
$PAGE->set_title($title);
$PAGE->set_heading($title);
$PAGE->set_pagelayout('standard');
$PAGE->set_url($returnurl);

$username       = required_param('username', PARAM_ALPHANUM);
$timestamp      = required_param('timestamp', PARAM_INT);
$hash           = required_param('hash', PARAM_ALPHANUM);
$firstname      = required_param('firstname', PARAM_TEXT);
$lastname       = required_param('lastname', PARAM_TEXT);
$email          = required_param('email', PARAM_EMAIL);
$coursename     = optional_param('course', '', PARAM_ALPHANUM);

$config         = get_config('auth_rsa');

/*
http://nen.moodle.com.au/test/auth/rsa/enroluser.php?username=rsa125&firstname=Renal125&email=teststudent125@example.com&timestamp=1457407294&hash=454119e7ee8b04617d62e21ff9f2b610&lastname=Society&course=test101
*/

$userid         = 0;
$ueid           = 0;
$raid           = 0;
$out            = '';

$enrolrequest   = new auth_plugin_rsa;


$jsonoutput = array();

if ($timestamp > $config->lasttimestamp) {

    $from = get_admin();

// md5($username.$timestamp.RSA_PRE_SHAREDKEY) == $hash and
    if (  ($modifier = $DB->get_record('user', array('id' => $from->id))))
    {
        $user                   = null;
        $userdata               = new stdClass();
        $userdata->username     = $username;
        $userdata->firstname    = $firstname;
        $userdata->lastname     = $lastname;
        $userdata->email        = $email;
        $userdata->mnethostid   = 1;

        $password               = generate_password();

        if ($user = $DB->get_record('user', array('username' => $username)))
        {
            // Update old user with new user details.
            $userdata->id           = $user->id;
            user_update_user($userdata, false, false);
            $userid                 = $user->id;
        }
        else
        {
            // Insert new user details.
            $userdata->id           = 0;
            $userdata->password     = hash_internal_user_password($password);
            $userdata->confirm      = 1;
            $userid                 = user_create_user($userdata, false, false);
        }
        $user = $DB->get_record('user', array('id' => $userid));

        $auth = get_auth_plugin($user->auth);

        if (!($result = $auth->user_confirm($user->username, $user->secret))) {
            throw new coding_exception(get_string('user:confirm:fail', 'auth_rsa'));
        }
        $jsonoutput['userid'] = $userid;
//        $out .= html_writer::div('User ID: '.$userid);

        if ($userid and $course = $DB->get_record('course', array('shortname' => $coursename)))
        {
            $jsonoutput['courseid'] = $course->id;

    //        $out .= html_writer::div('Course ID: '.$course->id);

            $enrol = $DB->get_record('enrol', array('courseid' => $course->id, 'enrol' => 'manual'));

            $userenrolment                  = new stdClass();
            $userenrolment->enrolid         = $enrol->id;
            $userenrolment->userid          = $userid;
            $userenrolment->modifierid      = $modifier->id;
            $userenrolment->timestart       = time();
            $userenrolment->timeend         = 0;
            $userenrolment->timecreated     = time();
            $userenrolment->timemodified    = time();

            if (!($ue = $DB->get_record('user_enrolments', array('userid' => $userid, 'enrolid' => $enrol->id)))) {

                $jsonoutput['message'] = ($ueid = $DB->insert_record('user_enrolments', $userenrolment)) ? 'You are enrolled in '.$course->fullname : 'You were not enrolled in a course';

              //  $out .= html_writer::div(($ueid = $DB->insert_record('user_enrolments', $userenrolment)) ? 'You are enrolled in '.$course->fullname : 'You were not enrolled in a course');
            } else {
                $jsonoutput['message'] = 'You are enrolled in '.$course->fullname;
 //               $out .= html_writer::div('You are enrolled in '.$course->fullname);
                $ueid = $ue->id;
            }
            $coursecontext                  = context_course::instance($course->id);

            $roleassignment                 = new stdClass();
            $roleassignment->roleid         = 5;
            $roleassignment->contextid      = $coursecontext->id;
            $roleassignment->userid         = $userid;
            $roleassignment->component      = 'auth_rsa';
            $roleassignment->itemid         = 0;
            $roleassignment->timemodified   = time();
            $roleassignment->modifierid     = $modifier->id;
            $roleassignment->sortorder      = 0;

            if (!($ra = $DB->get_record('role_assignments', array('userid' => $userid, 'roleid' => 5)))) {
                $jsonoutput['role'] = ($raid = $DB->insert_record('role_assignments', $roleassignment)) ? 'You have the role of a student' : 'You have no role';
          //      $out .= html_writer::div(($raid = $DB->insert_record('role_assignments', $roleassignment)) ? 'You have the role of a student' : 'You have no role');
            } else {
                $jsonoutput['role'] = 'You have the role of a student';
           //     $out .= html_writer::div('You have the role of a student');
                $raid = $ra->id;
            }

            $logrecord1                     = new stdClass();
            $logrecord1->eventname          = '\\auth\\rsa\\user_enrolment_created';
            $logrecord1->component          = 'auth_rsa';
            $logrecord1->action             = 'created';
            $logrecord1->target             = 'user_enrolment';
            $logrecord1->objecttable        = 'user_enrolment';
            $logrecord1->objectid           = $ueid;
            $logrecord1->crud               = 'c';
            $logrecord1->edulevel           = '0';
            $logrecord1->contextid          = $coursecontext->id;
            $logrecord1->contextlevel       = $coursecontext->contextlevel;
            $logrecord1->contextinstanceid  = $coursecontext->instanceid;
            $logrecord1->userid             = $userid;
            $logrecord1->courseid           = $course->id;
            $logrecord1->relateduserid      = 0;
            $logrecord1->anonymous          = 0;
        //    $logrecord1->other              = "a:1:{s:5:\"enrol\";s:8:\"auth_rsa\";}";
            $logrecord1->other              = serialize($userenrolment);
            $logrecord1->timecreated        = time();
            $logrecord1->origin             = 'web';
            $logrecord1->ip                 = $_SERVER['REMOTE_ADDR'];
            $logrecord1->realuserid         = NULL;

            $logrecord2                     = new stdClass();
            $logrecord2->eventname          = '\\auth\\rsa\\role_assigned';
            $logrecord2->component          = 'auth_rsa';
            $logrecord2->action             = 'created';
            $logrecord2->target             = 'role';
            $logrecord2->objecttable        = 'role';
            $logrecord2->objectid           = $raid;
            $logrecord2->crud               = 'c';
            $logrecord2->edulevel           = '0';
            $logrecord2->contextid          = $coursecontext->id;
            $logrecord2->contextlevel       = $coursecontext->contextlevel;
            $logrecord2->contextinstanceid  = $coursecontext->instanceid;
            $logrecord2->userid             = $userid;
            $logrecord2->courseid           = $course->id;
            $logrecord2->relateduserid      = 0;
            $logrecord2->anonymous          = 0;
           // $logrecord2->other              = "a:3:{s:2:\"id\";i:".$raid.";s:9:\"component\";s:8:\"auth_rsa\";s:6:\"itemid\";i:0;}";
            $logrecord2->other              = serialize($roleassignment);
            $logrecord2->timecreated        = time();
            $logrecord2->origin             = 'web';
            $logrecord2->ip                 = $_SERVER['REMOTE_ADDR'];
            $logrecord2->realuserid         = NULL;

            $lastinsertid                   = $DB->insert_record('logstore_standard_log', $logrecord1);
            $lastinsertid                   = $DB->insert_record('logstore_standard_log', $logrecord2);
//print_object($logrecords);
        }
        else
        {
            $jsonoutput['message'] = get_string('db:nocourse', 'auth_rsa');
            $jsonoutput['status'] = 'failed';
          //  throw new coding_exception(get_string('db:nocourse', 'auth_rsa'));
        }

        if ($userid and $ueid and $raid)
        {
            $timestamp              = time();
            $hash                   = md5($userid.$course->id.$timestamp);

            $cpdlog                 = new stdClass();
            $cpdlog->userid         = $userid;
            $cpdlog->firstname      = $user->firstname;
            $cpdlog->lastname       = $user->lastname;
            $cpdlog->email          = $user->email;
            $cpdlog->courseid       = $course->id;
            $cpdlog->course         = $course->fullname;
            $cpdlog->cpdpoints      = $cpdpoints = 0;
            $cpdlog->timestarted    = $timestamp;
            $cpdlog->timefinished   = 0;
            $cpdlog->timemodified   = $timestamp;
            $cpdlog->hash           = $hash;

            if (!($cdplogid = $DB->insert_record('auth_rsa_cpdlog', $cpdlog)))
            {
                $jsonoutput['message'] = get_string('db:insert:error:cpdlog', 'auth_rsa');
                $jsonoutput['status'] = 'failed';
           //      throw new coding_exception(get_string('db:insert:error:cpdlog', 'auth_rsa'));
            } else {
                // Send emails to user and admin
  //              $user->id                       = 4;
//                $user->email                    = "bevan@pukunui.com";
    //print_object($from);

                $emailtext                      = new stdClass;
                $emailtext->coursefullname      = $course->fullname;
                $emailtext->firstname           = $user->firstname;
                $emailtext->lastname            = $user->lastname;
                $emailtext->username            = $user->username;
                $emailtext->password            = $password;
                $emailtext->supportname         = $from->firstname.' '.$from->lastname;
                $emailtext->supportemail        = $from->email;
                $emailtext->url                 = $CFG->wwwroot."/auth/rsa/nen.php?uid=".$userid.'&cid='.$course->id.'&time='.$timestamp.'&hash='.$hash;

                $emailsubject = get_string('enrolment:user:subject', 'auth_rsa', $emailtext);
                $emailmessage = get_string('enrolment:user:message', 'auth_rsa', $emailtext);

                if (email_to_user($user, $from, $emailsubject, $emailmessage)) {
                    $jsonoutput['useremail'] = "Emailed enrolment details to ".$user->email;
                   // $out .= html_writer::div("Emailed enrolment details to ".$user->email);
                }
                else
                {
                    $jsonoutput['useremail'] = "NOT sent enrolment details for ".$user->email;
    //                $out .= html_writer::div("NOT emailed enrolment details for ".$user->email);
                }

                $emailsubject = get_string('enrolment:admin:subject', 'auth_rsa', $emailtext);
                $emailmessage = get_string('enrolment:admin:message', 'auth_rsa', $emailtext);

//                $from->id                       = 4;
//                $from->email                    = "bevan@pukunui.com";

                if (email_to_user($from, $from, $emailsubject, $emailmessage)) {
                    $jsonoutput['adminemail'] = "Admin sent enrolment details to ".$user->email;
    //                $out .= html_writer::div("Admin emailed enrolment details to ".$user->email);
                }
                else
                {
                    $jsonoutput['adminemail'] = "Admin NOT sent enrolment details for ".$user->email;
    //                $out .= html_writer::div("Admin NOT emailed enrolment details for ".$user->email);
                }
            }
            $jsonoutput['status'] = 'success';
        } else {
            $jsonoutput['message'] = 'User not enrolled';
//            $out .= html_writer::div('User not enrolled');
        }
    }
    set_config('lasttimestamp', $timestamp, 'auth_rsa');
} else {
    $jsonoutput['message'] = 'Page timed out';
    $jsonoutput['status'] = 'failed';
 //   $out .= html_writer::div('Page timed out');
}
header('Content-Type: application/json; charset=UTF-8');
echo json_encode($jsonoutput);


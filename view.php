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

$systemcontext = context_system::instance();
$strpluginname = get_string('pluginname', 'auth_rsa');
// require_capability('auth/watercorped:studentconfig', $systemcontext);

$id = required_param('id', PARAM_INT);
$submitbutton = optional_param('submitbutton', '', PARAM_RAW);
$avatar = optional_param('avatar', '', PARAM_RAW);
$returnurl = "/auth/rsa/view.php?id=".$id;

$PAGE->set_url($returnurl);
$PAGE->set_context($systemcontext);
$PAGE->set_title(get_string('managestudents:updateuser:metatag:heading', 'auth_rsa'));
$PAGE->set_pagelayout('standard');
$PAGE->set_heading(get_string('managestudents:updateuser:metatag:heading', 'auth_rsa'));

$watercorped = new auth_plugin_watercorped;

$userform = $watercorped->updateuser_form();

// Form cancelled?
if ($userform->is_cancelled()) {
    redirect(new moodle_url($returnurl));
    exit;

// Form submitted?
} else if ($user = $userform->get_data()) {
    if ($id > 0 and !empty($submitbutton)) {

/*
        if (!($picturename = $DB->get_field_sql('SELECT MAX(picture) FROM {user}', null))) {
            $user->picture = 1;
        }
        $user->picture = intval($user->picture) + 1;

        $usercontext = context_user::instance($user->id);

         $fs = get_file_storage();
         $file_record = array(
                        'contextid' => $usercontext->id,
                        'component' => 'user',
                        'filearea' => 'pix/avatar',
                        'itemid' => 0,
                        'filepath' => '/',
                        'filename' => $picturename,
                        'timecreated' => time(),
                        'timemodified' => time());

         $fs->create_file_from_pathname($file_record, $CFG->avatar);
*/
        if ($watercorped->user_update_details($user)) {
            $strcontinue = get_string('db:user:update:success', 'auth_rsa');
        } else {
            $strcontinue = get_string('db:user:update:error', 'auth_rsa');
        }
    }
   //  $urltogo = new moodle_url('/my');

    redirect(new moodle_url($returnurl), $strcontinue);
    exit;
}

echo $OUTPUT->header();
$userform->display();
echo $OUTPUT->footer();

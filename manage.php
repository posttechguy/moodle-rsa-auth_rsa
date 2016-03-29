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
require_capability('auth/watercorped:teacherconfig', $systemcontext);

$id      = optional_param('id', 0, PARAM_INT);
$delete  = optional_param('delete', 0, PARAM_INT);
$confirm = optional_param('confirm', '', PARAM_ALPHANUM);

$returnurl = '/auth/rsa/manage.php';
$title = get_string('managestudents:mystudents:metatag:heading', 'auth_rsa');

$PAGE->set_context($systemcontext);
$PAGE->set_title($title);
$PAGE->set_heading($title);
$PAGE->set_pagelayout('standard');
$PAGE->set_url($returnurl);

$watercorped = new auth_plugin_watercorped;

$students = $watercorped->listofallstudents($USER->id);
/*
if ($classes->is_cancelled()) {
    redirect(new moodle_url($returnurl));
    exit;
// Form submitted?
} else if ($data = $classes->get_data()) {

}
*/
echo $OUTPUT->header();
echo $OUTPUT->box("This section lists the students currently in your class(es)<br /><br />
    Manage your students by:
    <ol>
        <li>Clicking on the student name to edit their profile,</li>
        <li>Click on the cross associated with their name to remove them from that class</li>
    </ol>
    ");
echo html_writer::table($students);
echo $OUTPUT->footer();

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

$returnurl = $CFG->wwwroot."/login/index.php";

$userid    = required_param('uid', PARAM_INT);
$courseid  = required_param('cid', PARAM_INT);
$timestamp = required_param('time', PARAM_INT);
$hash      = required_param('hash', PARAM_ALPHANUM);


if ($clickbait = $DB->get_record('auth_rsa_cpdlog', array('userid' => $userid, 'courseid' => $courseid)))
{
    if ($clickbait->hash == $hash)
    {
        if ($user = $DB->get_record('user', array('id' => $userid))) {
            complete_user_login($user);

            $clickbait->hash = '';
            $DB->update_record('auth_rsa_cpdlog', $clickbait);

            redirect($CFG->wwwroot."/course/view,php?id=".$courseid);
        }
    }
}
redirect($returnurl);

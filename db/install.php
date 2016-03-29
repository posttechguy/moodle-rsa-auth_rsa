<?php
/**
 * Custom authentication for Renal Society of Australia project
 *
 * Installation script
 *
 * @package    auth_rsa
 * @author     Bevan Holman <bevan@pukunui.com>, Pukunui
 * @copyright  2015 onwards, Pukunui
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die;

require_once($CFG->dirroot.'/auth/rsa/locallib.php');

function xmldb_auth_rsa_install() {
    \auth_rsa\install::execute();
}

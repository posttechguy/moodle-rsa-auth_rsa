<?php
/**
 * CPD Log Sync
 *
 * @package    auth_rsa
 * @author     Bevan Holman <bevan@pukunui.com>, Pukunui Technology
 * @copyright  2015 onwards, Pukunui
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace auth_rsa\task;

class cron_task extends \core\task\scheduled_task {

    /**
     * Get a descriptive name for this task (shown to admins).
     *
     * @return string
     */
    public function get_name() {
        return get_string('crontask', 'auth_rsa');
    }

    /**
     * Run auth_rsa_cpdlog cron.
     */
    public function execute() {
        global $CFG;
        require_once($CFG->dirroot . '/auth/rsa/locallib.php');
        auth_rsa_cpdlog();
    }

}

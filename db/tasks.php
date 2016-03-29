<?php
/**
 * CPD Log Sync
 *
 * Scheduled task definition
 *
 * @package    auth_rsa
 * @author     Bevan Holman <bevan@pukunui.com>, Pukunui Technology
 * @copyright  2015 onwards, Pukunui
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$tasks = array(
    array(
        'classname' => 'auth_rsa\task\cron_task',
        'blocking'  => 0,
        'minute'    => '*',
        'hour'      => '*',
        'day'       => '*',
        'dayofweek' => '*',
        'month'     => '*'
    )
);

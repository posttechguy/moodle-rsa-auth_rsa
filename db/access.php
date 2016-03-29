<?php
/**
 * Custom authentication for Renal Society of Australia project
 *
 * Capabilities definition
 *
 * @package    auth_rsa
 * @author     Bevan Holman <bevan@pukunui.com>, Pukunui
 * @copyright  2015 onwards, Pukunui
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
*/

defined('MOODLE_INTERNAL') || die;

$capabilities = array (
    'auth/rsa:enroluser' => array (
        'riskbitmask'  => RISK_CONFIG,
        'captype'      => 'write',
        'contextlevel' => CONTEXT_SYSTEM,
        'archetypes'   => array (
            'student' => CAP_ALLOW,
        )
    ),
    'auth/rsa:teacherconfig' => array (
        'riskbitmask'  => RISK_CONFIG,
        'captype'      => 'write',
        'contextlevel' => CONTEXT_SYSTEM,
        'archetypes'   => array (
            'manager' => CAP_ALLOW,
        )
    ),
);

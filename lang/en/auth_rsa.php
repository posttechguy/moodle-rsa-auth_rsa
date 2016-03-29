<?php
/**
 * Custom authentication for Renal Society of Australia project
 *
 * String definitions
 *
 * @package    auth_rsa
 * @author     Bevan Holman <bevan@pukunui.com>, Pukunui Technology
 * @copyright  2015 onwards, Pukunui
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die;

$string['db:nocourse'] = 'This course does not exists';
$string['enroluser:h1:enrolmentstatus'] = 'Enrolment Status';
$string['enroluser:metatag:heading'] = 'Enrolment Status';
$string['enrolment:admin:subject'] = '{$a->firstname} {$a->lastname} has enrolled in {$a->coursefullname}';
$string['enrolment:admin:message'] = 'The email is to inform you that {$a->firstname} {$a->lastname} has
enrolled into the following course: {$a->coursefullname}

To access the course click on the link below

{$a->url}

Please do not reply to this email.';
$string['enrolment:user:subject'] = 'Enrolled in: {$a->coursefullname}';
$string['enrolment:user:message'] = 'The email is to inform you that you have been enrolled into the
following course: {$a->coursefullname}

Your login details are:

Username: {$a->username}
Password: {$a->password}

To access the course click on the link below

{$a->url}

Please do not reply to this email.

Site Admin
{$a->supportname}
{$a->supportemail}';
$string['crontask'] = 'CPD Log Sync';
$string['pluginname'] = 'RSA Authentication';
$string['setting:lasttimestamp'] = 'Last time user enrolled';
$string['setting:lasttimestamp:desc'] = 'This setting controls the availability of /auth/rsa/enroluser.php.
If the timestamp in the request is older than last time user enrolled, the process will stop.';
$string['setting:oauthcpdlogactivityid'] = 'Activity ID';
$string['setting:oauthcpdlogactivityid:desc'] = 'Sent in the request when writing back to the CPDLog';
$string['setting:oauthcpdlogappname'] = 'CPDLog App Name';
$string['setting:oauthcpdlogappname:desc'] = 'Describes the application that gets the CPDLog token';
$string['setting:oauthcpdlogcategoryid'] = 'Category ID';
$string['setting:oauthcpdlogcategoryid:desc'] = 'Sent in the request when writing back to the CPDLog';
$string['setting:oauthcpdloglastrun'] = 'CPDLog App Name Last Run';
$string['setting:oauthcpdloglastrun:desc'] = 'The time the application was lat run';
$string['setting:oauthcpdlogtokenurl'] = 'CPDLog Get Token URL';
$string['setting:oauthcpdlogtokenurl:desc'] = 'The url that requests the CPDLog token and responses with a current token';
$string['setting:oauthcpdlogtoken'] = 'CPDLog Response Token';
$string['setting:oauthcpdlogtoken:desc'] = 'The current working token. DO NOT CHANGE.';
$string['setting:oauthcpdlogtokenexpiry'] = 'CPDLog Response Token Expiry';
$string['setting:oauthcpdlogtokenexpiry:desc'] = 'The current working token expiry.';
$string['setting:oauthclientid'] = 'CPDLog Request Client ID';
$string['setting:oauthclientid:desc'] = 'The client ID for the get token request';
$string['setting:oauthclientsecret'] = 'CPDLog Request Client Secret';
$string['setting:oauthclientsecret:desc'] = 'The client secret for the get token request';
$string['user:confirm:fail'] = 'User confirmation has failed';
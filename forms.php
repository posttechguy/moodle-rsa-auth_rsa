<?php
/**
 * Custom authentication for Water Corporation Water Efficiency project
 *
 * Form definitions
 *
 * @package    auth_rsa
 * @author     Bevan Holman <bevan@pukunui.com>, Pukunui
 * @copyright  2015 onwards, Pukunui
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die;

require_once($CFG->libdir.'/formslib.php');
require_once($CFG->dirroot.'/user/profile/lib.php');
/**
 * Form definition for Water Efficiency Login
 *
 * @package    auth_rsa
 * @reportor   Bevan Holman <bevan@pukunui.com>, Pukunui
 * @copyright  2015 onwards, Pukunui
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

class auth_rsa_login_form extends moodleform {
    /**
     * Define the form
     */
    public function definition() {
        global $DB;

        $mform =& $this->_form;

        $mform->addElement('header', '', get_string('schoollogin:fieldlist:login', 'auth_rsa'));

        $mform->addElement('text', 'username', get_string('formlabel:username', 'auth_rsa'), null);
        $mform->setType('username', PARAM_NOTAGS);
        $mform->addRule('username', get_string('formvalidation:username', 'auth_rsa'), 'required', null, 'server');
        $mform->setDefault('username', '');
        $mform->addElement('passwordunmask', 'password', get_string('formlabel:password', 'auth_rsa'), null);
        $mform->addRule('password', get_string('formvalidation:password', 'auth_rsa'), 'required', null, 'server');
        $this->add_action_buttons(false, get_string('button:login', 'auth_rsa'));

        $mform->closeHeaderBefore('password');
    }

    /**
     * Validate the form submission.
     *
     * @param array $data  submitted form data
     * @param array $files submitted form files
     * @return array
     */

    public function validation($data, $files) {

        global $CFG, $DB;

        $usernew = (object)$usernew;
        $usernew->email = $usernew->username = trim($usernew->email);

        $user = $DB->get_record('user', array('username' => $data->username));
        $err = array();

        if ($user) {
            if ($user->suspended) {
                // Show some error because we can not login suspended users.
                $err['suspended'] = get_string('error');
            }
            if ($user->deleted) {
                // Show some error because we can not login deleted users.
                $err['deleted'] = get_string('error');
            }
        }
        if (empty($data->username)) {
            $err['email'] = get_string('required');
        }
        if (empty($data->password)) {
            $err['password'] = get_string('required');
        }

        return (count($err) == 0) ? true : $err;
    }
}

/**
 * User sign-up form
 *
 * @package    auth_rsa
 * @author     Bevan Holman <bevan@pukunui.com>, Pukunui
 * @copyright  2015 onwards, Pukunui
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class auth_rsa_signup_form extends moodleform {
    /**
     * Define the form
     */

    public function definition() {
        global $DB;
        global $USER;
        global $CFG;

        $strrequired = get_string('required');

        $mform =& $this->_form;

        $mform->addElement('hidden', 'id', 0);
        $mform->setType('id', PARAM_INT);

        // Start the formal input elements.

        //---Type of account------------------------------------
        $mform->addElement('hidden', 'username', '');
        $mform->setType('username', PARAM_RAW);

        $mform->addElement('header', 'newaccount', get_string('managestudents:fieldlist:addstudentaccount', 'auth_rsa'));
        $mform->setExpanded('newaccount');

        // A list of the type of accounts.
        // To add another, first update the language file to add formtag:typeofaccount:newtypeofaccount and its description,
        // update the version file and do an upgrade.
        // Then add the new typeofaccount to the user profile fields under typeofaccount.
        // don't forget to ajust the access file
        // This process will allow for no disruption to the website.

        if ($typeofaccountdata = $DB->get_fieldset_select('user_info_field', 'param1', 'shortname = ?', array("typeofaccount"))) {

            $typeofaccountdata =  str_replace('%0A', ',', str_replace('+', ' ', urlencode($typeofaccountdata[0])));
            $accounts = explode(',', $typeofaccountdata);

            foreach ($accounts as $typeofaccount) {
                $mform->addElement('radio', 'profile_field_typeofaccount', '',
                        get_string('formtag:typeofaccount:'.$typeofaccount, 'auth_rsa'), $typeofaccount);
            }
        }
        $mform->setDefault('profile_field_typeofaccount', 'student');


        //---First & Last names---------------------------------
        $mform->addElement('text', 'firstname', get_string('formlabel:firstname', 'auth_rsa'), null);
        $mform->setType('firstname', PARAM_NOTAGS);
        $mform->addRule('firstname', get_string('formvalidation:firstname', 'auth_rsa'), 'required', null, 'server');
        $mform->setDefault('firstname', '');

        $mform->addElement('text', 'lastname', get_string('formlabel:lastname', 'auth_rsa'), null);
        $mform->setType('lastname', PARAM_NOTAGS);
        $mform->addRule('lastname', get_string('formvalidation:lastname', 'auth_rsa'), 'required', null, 'server');
        $mform->setDefault('lastname', '');


        //---Emails---------------------------------------------
        if (!empty($USER->id)) {
            $mform->addElement('text', 'email', get_string('formlabel:emailorguardianemail', 'auth_rsa'), null);
            $mform->setType('email', PARAM_NOTAGS);
            $mform->addRule('email', get_string('formvalidation:emailorguardianemail', 'auth_rsa'), 'required', null, 'server');
            $mform->setDefault('email', '');
        } else {
            $mform->addElement('text', 'email', get_string('formlabel:emailorguardianemail', 'auth_rsa'), null);
            $mform->setType('email', PARAM_NOTAGS);
            $mform->addRule('email', get_string('formvalidation:email', 'auth_rsa'), 'required', null, 'server');
            $mform->setDefault('email', '');

            $mform->addElement('text', 'confirmemail', get_string('formlabel:confirmemail', 'auth_rsa'), null);
            $mform->setType('confirmemail', PARAM_NOTAGS);
            $mform->addRule('confirmemail', get_string('formvalidation:confirmemail', 'auth_rsa'), 'required', null, 'server');
            $mform->setDefault('confirmemail', '');
        }


        //---Passwords------------------------------------------
        $mform->addElement('passwordunmask', 'password',
                    get_string('formlabel:password', 'auth_rsa'), ' maxlength="30" size="12" ');
   //     $mform->addRule('password', get_string('formvalidation:password', 'auth_rsa'), 'required', null, 'server');
        $mform->addHelpButton('password', 'updateuser:form:help:password', 'auth_rsa');

        $mform->addElement('passwordunmask', 'confirmpassword',
                    get_string('formlabel:confirmpassword', 'auth_rsa'), ' maxlength="30" size="12" ');
  //      $mform->addRule('confirmpassword', get_string('formvalidation:confirmpassword', 'auth_rsa'), 'required', null, 'server');
        $mform->addHelpButton('confirmpassword', 'updateuser:form:help:password', 'auth_rsa');

        if ($whereareyoufromdata = $DB->get_field_select('user_info_field', 'param1', 'shortname = ?', array("whereareyoufrom"))) {

            $whereareyoufromdata =  str_replace('%0A', ',', str_replace('+', ' ', urlencode($whereareyoufromdata)));
            $whereareyoufrom = explode(',', $whereareyoufromdata);
            $whereareyoufromarray = array("0" => get_string('form:select:choose', 'auth_rsa'));
            foreach ($whereareyoufrom as $key => $value) $whereareyoufromarray["$value"] = $value;

            $whereareyoufromselect = $mform->addElement('select', 'profile_field_whereareyoufrom',
                                    get_string('formlabel:whereareyoufrom', 'auth_rsa'), $whereareyoufromarray);

        //    $selectedwhereareyoufrom = empty($USER->profile['whereareyoufrom']) ? 0 : $USER->profile['whereareyoufrom'];
         //   $whereareyoufromselect->setSelected("$selectedwhereareyoufrom");
        }

        //---Student Information--------------------------------
        $mform->addElement('header', 'studentinformation', get_string('schoollogin:fieldlist:studentinformation', 'auth_rsa'));
        $mform->setExpanded('studentinformation');

        if ($yearlevelsdata = $DB->get_fieldset_select('user_info_field', 'param1', 'shortname = ?', array("yearlevel"))) {


            $yearlevelsdata =   str_replace('%2F', '/', str_replace('%0A', ',', str_replace('+', ' ', urlencode($yearlevelsdata[0]))));
            $yearlevels = explode(',', $yearlevelsdata);
            $yearlevelsarray = array("0" => get_string('form:select:choose', 'auth_rsa'));
            foreach ($yearlevels as $key => $value) $yearlevelsarray["$value"] = $value;

            $yearlevelselect = $mform->addElement('select', 'profile_field_yearlevel',
                                    get_string('formlabel:yearlevel', 'auth_rsa'), $yearlevelsarray);

         //   $selectedyearlevel = empty($USER->profile['yearlevel']) ? '0' : $USER->profile['yearlevel'];

         //   $selectedyearlevel =
         //   $yearlevelselect->setSelected("$selectedyearlevel");
        }
        if ($yearofbirthdata = $DB->get_field_select('user_info_field', 'param1', 'shortname = ?', array("yearofbirth"))) {

            $yearofbirthdata =   str_replace('%2F', '/', str_replace('%0A', ',', str_replace('+', ' ', urlencode($yearofbirthdata))));
            $yearofbirth = explode(',', $yearofbirthdata);
            $yearofbirtharray = array("0" => get_string('form:select:choose', 'auth_rsa'));
            foreach ($yearofbirth as $key => $value) $yearofbirtharray["$value"] = $value;

            $yearofbirthselect = $mform->addElement('select', 'profile_field_yearofbirth',
                                    get_string('formlabel:yearofbirth', 'auth_rsa'), $yearofbirtharray);

         //   $selectedyearofbirth = empty($USER->profile['yearofbirth']) ? intval(date("Y") - 6) : $USER->profile['yearofbirth'];
         //  $yearofbirthselect->setSelected("$selectedyearofbirth");
        }

        $mform->closeHeaderBefore('studentinformation');


        //---Teacher Information--------------------------------
        $mform->addElement('header', 'teacheradultinformation', get_string('schoollogin:fieldlist:teacheradultinformation', 'auth_rsa'));
        $mform->setExpanded('teacheradultinformation');

        $mform->addElement('text', 'institution', get_string('formlabel:schoolororgname', 'auth_rsa'), null);
        $mform->setType('institution', PARAM_NOTAGS);
        $mform->setDefault('institution', '');

        $mform->closeHeaderBefore('teacheradultinformation');

        $mform->addElement('advcheckbox', 'agreetandcs', '', get_string('formtag:tandcs', 'auth_rsa'),
                            array('group' => 1), array(0, 1));

        if ($this->signup_captcha_enabled()) {
            $mform->addElement('recaptcha', 'recaptcha_element',
                get_string('security_question', 'auth'), array('https' => $CFG->loginhttps));
            $mform->addHelpButton('recaptcha_element', 'recaptcha', 'auth');
            $mform->closeHeaderBefore('recaptcha_element');
        }

        $this->add_action_buttons(true, get_string('button:teacher:createstudentaccount', 'auth_rsa'));
        $mform->disabledIf('submit', 'agreetandcs');
    }

    /**
     * Validate the form submission.
     *
     * @param array $usernew queried from db
     * @param array $files submitted form files
     * @return array
     */

    public function validation($usernew, $files) {

        global $CFG, $DB;

        $usernew = (object)$usernew;
        $usernew->email = $usernew->username = trim($usernew->email);

        $user = $DB->get_record('user', array('id' => $usernew->id));

        $errors = parent::validation($usernew, $files);

        if (!$user and !empty($usernew->createpassword)) {
            if ($usernew->suspended) {
                // Show some error because we can not mail suspended users.
                $errors['suspended'] = get_string('error');
            }
        }

        if (empty($usernew->email)) {
            // Might be only whitespace.
            $errors['email'] = get_string('required');
        } else if (!$user or $user->email !== $usernew->email) {
            // Check new username does not exist.
            if (isset($usernew->username)) {
                if ($DB->record_exists('user', array('username' => $usernew->username, 'mnethostid' => $CFG->mnet_localhost_id))) {
                    $errors['username'] = get_string('usernameexists');
                }
            }
            // Check allowed characters.
            if ($usernew->username !== core_text::strtolower($usernew->username)) {
                $errors['username'] = get_string('usernamelowercase');
            } else {
                if ($usernew->username !== clean_param($usernew->username, PARAM_USERNAME)) {
                    $errors['username'] = get_string('invalidusername');
                }
            }
        }

        if (!$user or $user->email !== $usernew->username) {
            if (!validate_email($usernew->username)) {
                $errors['email'] = get_string('invalidemail');
            } else if ($DB->record_exists('user', array(
                    'email' => $usernew->email,
                    'mnethostid' => $CFG->mnet_localhost_id))) {
                $errors['email'] = get_string('emailexists');
            }
        }

        if (isset($usernew->confirmemail)) {
            if ($usernew->confirmemail !== $usernew->email) {
                $errors['confirmemail'] = get_string('emailnotthesame', 'auth_rsa');
            }
        }

        if (!empty($usernew->password)) {
            $errmsg = ''; // Prevent eclipse warning.
            if (!check_password_policy($usernew->password, $errmsg)) {
                $errors['password'] = $errmsg;
            }
        } else if (!$user) {
            $auth = get_auth_plugin($usernew->auth);
            if ($auth->is_internal()) {
                // Internal accounts require password!
                $errors['password'] = get_string('required');
            }
        }

        if (empty($USER->id)) {
            if (empty($usernew->password)) {
                $errors['password'] = get_string('formvalidation:password', 'auth_rsa');
            }
            if (empty($usernew->confirmpassword)) {
                $errors['confirmpassword'] = get_string('formvalidation:confirmpassword', 'auth_rsa');
            }
            if ($usernew->password != $usernew->confirmpassword) {
                $errors['password'] = get_string('formvalidation:notmatchedpasswords', 'auth_rsa');
            }
        }
/*
        if ($usernew->profile_field_typeofaccount == 'student' and empty($usernew->profile_field_yearlevel)) {
            $errors['yearlevel'] = get_string('formvalidation:yearlevel', 'auth_rsa');
        }
        if ($usernew->profile_field_typeofaccount == 'student' and empty($usernew->profile_field_yearofbirth)) {
            $errors['yearofbirth'] = get_string('formvalidation:yearofbirth', 'auth_rsa');
        }
        if (empty($usernew->profile_field_whereareyoufrom)) {
            $errors['whereareyoufrom'] = get_string('formvalidation:whereareyoufrom', 'auth_rsa');
        }
        if ($usernew->profile_field_typeofaccount != 'student' and empty($usernew->institution)) {
            $errors['schoolororg'] = get_string('formvalidation:schoolororg', 'auth_rsa');
        }
*/

        if ($usernew->profile_field_typeofaccount != 'student' and empty($usernew->institution)) {
            $errors['institution'] = get_string('formvalidation:schoolororg', 'auth_rsa');
        }
        if ($usernew->profile_field_typeofaccount == 'student' and (empty($usernew->profile_field_yearlevel) or $usernew->profile_field_yearlevel == 'N/A')) {
            $errors['profile_field_yearlevel'] = get_string('formvalidation:yearlevel:student', 'auth_rsa');
        }
        if ($usernew->profile_field_typeofaccount != 'student' and (!empty($usernew->profile_field_yearlevel) and $usernew->profile_field_yearlevel != 'N/A')) {
            $usernew->profile_field_yearlevel = 'N/A';
            $errors['profile_field_yearlevel'] = get_string('formvalidation:yearlevel:teacher', 'auth_rsa');
        }

        if ($usernew->profile_field_typeofaccount == 'student' and (empty($usernew->profile_field_yearofbirth) or $usernew->profile_field_yearofbirth == 'N/A')) {
            $errors['profile_field_yearofbirth'] = get_string('formvalidation:yearofbirth:student', 'auth_rsa');
        }
        if ($usernew->profile_field_typeofaccount != 'student' and (!empty($usernew->profile_field_yearofbirth) and $usernew->profile_field_yearofbirth != 'N/A')) {
            $usernew->profile_field_yearofbirth = 'N/A';
            $errors['profile_field_yearofbirth'] = get_string('formvalidation:yearofbirth:teacher', 'auth_rsa');
        }

        if (empty($usernew->profile_field_whereareyoufrom)) {
            $errors['profile_field_whereareyoufrom'] = get_string('formvalidation:whereareyoufrom', 'auth_rsa');
        }





        if ($this->signup_captcha_enabled()) {
            $recaptchaelement = $this->_form->getElement('recaptcha_element');
            if (!empty($this->_form->_submitValues['recaptcha_challenge_field'])) {
                $challengefield = $this->_form->_submitValues['recaptcha_challenge_field'];
                $responsefield = $this->_form->_submitValues['recaptcha_response_field'];
                if (true !== ($result = $recaptchaelement->verify($challengefield, $responsefield))) {
                    $errors['recaptcha'] = $result;
                }
            } else {
                $errors['recaptcha'] = get_string('missingrecaptchachallengefield');
            }
        }

        // Next the customisable profile fields.
        $errors += profile_validation($usernew, $files);

        if (empty($usernew->agreetandcs)) {
            $errors['agreetandcs'] = get_string('formvalidation:agreetandcs', 'auth_rsa');
        }



        return (count($errors) == 0) ? true : $errors;
    }
    /**
     * Returns whether or not the captcha element is enabled, and the admin settings fulfil its requirements.
     * @return bool
     */
    public function signup_captcha_enabled() {
        global $CFG;
        return !empty($CFG->recaptchapublickey) && !empty($CFG->recaptchaprivatekey) && get_config('auth/email', 'recaptcha');
    }
}

//--------------------------------------------------------------------------

/**
 * User edit details form
 *
 * @package    auth_rsa
 * @author     Bevan Holman <bevan@pukunui.com>, Pukunui
 * @copyright  2015 onwards, Pukunui
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class auth_rsa_updateuser_form extends moodleform {
    /**
     * Define the form
     */

    public function definition() {
        global $DB;
        global $USER;
        global $CFG;
        global $OUTPUT;
        global $PAGE;

        $strrequired = get_string('required');

        $mform =& $this->_form;

        $mform->addElement('hidden', 'id', $USER->id);
        $mform->setType('id', PARAM_INT);

        // Start the formal input elements.

        //---Type of account------------------------------------
        $mform->addElement('header', 'newaccount', empty($USER->id) ? get_string('schoollogin:fieldlist:newaccount', 'auth_rsa') :
            get_string('schoollogin:fieldlist:existingaccount', 'auth_rsa'));
        $mform->setExpanded('newaccount');


        //---First & Last names---------------------------------
        $mform->addElement('text', 'firstname', get_string('formlabel:firstname', 'auth_rsa'), null);
        $mform->setType('firstname', PARAM_NOTAGS);
        $mform->addRule('firstname', get_string('formvalidation:firstname', 'auth_rsa'), 'required', null, 'server');
        $mform->setDefault('firstname', $USER->firstname);

        $mform->addElement('text', 'lastname', get_string('formlabel:lastname', 'auth_rsa'), null);
        $mform->setType('lastname', PARAM_NOTAGS);
        $mform->addRule('lastname', get_string('formvalidation:lastname', 'auth_rsa'), 'required', null, 'server');
        $mform->setDefault('lastname', $USER->lastname);


        //---Emails---------------------------------------------
        $mform->addElement('text', 'email', get_string('formlabel:emailorguardianemail', 'auth_rsa'), null);
        $mform->setType('email', PARAM_NOTAGS);
        $mform->addRule('email', get_string('formvalidation:emailorguardianemail', 'auth_rsa'), 'required', null, 'server');
        $mform->setDefault('email', $USER->email);


        //---Passwords------------------------------------------
        $mform->addElement('passwordunmask', 'password',
                    get_string('formlabel:password', 'auth_rsa'), ' maxlength="30" size="12" ');
   //     $mform->addRule('password', get_string('formvalidation:password', 'auth_rsa'), 'required', null, 'server');
        $mform->addHelpButton('password', 'updateuser:form:help:password', 'auth_rsa');

        $mform->addElement('passwordunmask', 'confirmpassword',
                    get_string('formlabel:confirmpassword', 'auth_rsa'), ' maxlength="30" size="12" ');
  //      $mform->addRule('confirmpassword', get_string('formvalidation:confirmpassword', 'auth_rsa'), 'required', null, 'server');
        $mform->addHelpButton('confirmpassword', 'updateuser:form:help:password', 'auth_rsa');

        if ($whereareyoufromdata = $DB->get_field_select('user_info_field', 'param1', 'shortname = ?', array("whereareyoufrom"))) {

            $whereareyoufromdata =  str_replace('%0A', ',', str_replace('+', ' ', urlencode($whereareyoufromdata)));
            $whereareyoufrom = explode(',', $whereareyoufromdata);
            $whereareyoufromarray = array("0" => get_string('form:select:choose', 'auth_rsa'));
            foreach ($whereareyoufrom as $key => $value) $whereareyoufromarray["$value"] = $value;

            $whereareyoufromselect = $mform->addElement('select', 'profile_field_whereareyoufrom',
                                    get_string('formlabel:whereareyoufrom', 'auth_rsa'), $whereareyoufromarray);

            $selectedwhereareyoufrom = empty($USER->profile['whereareyoufrom']) ? 'Perth' : $USER->profile['whereareyoufrom'];
            $whereareyoufromselect->setSelected("$selectedwhereareyoufrom");
        }

        //---Student Information--------------------------------
        $mform->addElement('header', 'studentinformation', get_string('schoollogin:fieldlist:studentinformation', 'auth_rsa'));
        $mform->setExpanded('studentinformation');

        if ($yearlevelsdata = $DB->get_fieldset_select('user_info_field', 'param1', 'shortname = ?', array("yearlevel"))) {

            $yearlevelsdata =   str_replace('%2F', '/', str_replace('%0A', ',', str_replace('+', ' ', urlencode($yearlevelsdata[0]))));
            $yearlevels = explode(',', $yearlevelsdata);
            $yearlevelsarray = array("0" => get_string('form:select:choose', 'auth_rsa'));
            foreach ($yearlevels as $key => $value) $yearlevelsarray["$value"] = $value;

            $yearlevelselect = $mform->addElement('select', 'profile_field_yearlevel',
                                    get_string('formlabel:yearlevel', 'auth_rsa'), $yearlevelsarray);

            $selectedyearlevel = empty($USER->profile['yearlevel']) ? 'N/A' : $USER->profile['yearlevel'];
            $yearlevelselect->setSelected("$selectedyearlevel");
        }

        if ($yearofbirthdata = $DB->get_field_select('user_info_field', 'param1', 'shortname = ?', array("yearofbirth"))) {

            $yearofbirthdata =  str_replace('%2F', '/', str_replace('%0A', ',', str_replace('+', ' ', urlencode($yearofbirthdata))));
            $yearofbirth = explode(',', $yearofbirthdata);
            $yearofbirtharray = array("0" => get_string('form:select:choose', 'auth_rsa'));
            foreach ($yearofbirth as $key => $value) $yearofbirtharray["$value"] = $value;

            $yearofbirthselect = $mform->addElement('select', 'profile_field_yearofbirth',
                                    get_string('formlabel:yearofbirth', 'auth_rsa'), $yearofbirtharray);

            $selectedyearofbirth = empty($USER->profile['yearofbirth']) ? 'N/A' : $USER->profile['yearofbirth'];
            $yearofbirthselect->setSelected("$selectedyearofbirth");
        }
        $mform->closeHeaderBefore('studentinformation');


        //---Teacher Information--------------------------------
        $mform->addElement('header', 'teacheradultinformation', get_string('schoollogin:fieldlist:teacheradultinformation', 'auth_rsa'));
        $mform->setExpanded('teacheradultinformation');

        $mform->addElement('text', 'institution', get_string('formlabel:schoolororgname', 'auth_rsa'), null);
        $mform->setType('institution', PARAM_NOTAGS);
        $mform->setDefault('institution', $USER->institution);
        /*
// print_object($USER);
        $mform->addElement('hidden', 'avatarimage', empty($USER->profile['avatarimage']) ? '' : $USER->profile['avatarimage']);
        $mform->setType('avatarimage', PARAM_RAW);

        $PAGE->requires->yui_module('moodle-auth_rsa-avatarchooser',
                                    'M.auth_rsa',
                                    array(array('avatar' => $mform->getAttribute('avatarimage'))));

        ?>
        <div id="id_avatar_pic" class="span8 pull-right">
            <?php
            $avatar = new user_picture($USER);
            $avatar->courseid = 0;
            $avatar->link = false;
            $avatar->size = 100;
            $avatar->alttext = true;
            echo $OUTPUT->render($avatar);
             ?>
        </div>
        <?php
*/
        $mform->closeHeaderBefore('teacheradultinformation');

        if ($this->signup_captcha_enabled()) {
            $mform->addElement('recaptcha', 'recaptcha_element',
                get_string('security_question', 'auth'), array('https' => $CFG->loginhttps));
            $mform->addHelpButton('recaptcha_element', 'recaptcha', 'auth');
            $mform->closeHeaderBefore('recaptcha_element');
        }

        $this->add_action_buttons(true, get_string('button:updatedetails', 'auth_rsa'));
    }


    /**
     * Extend the form definition after data has been parsed.
     */
    public function definition_after_data() {
        global $USER, $CFG, $DB, $OUTPUT;

        $mform = $this->_form;
        if ($userid = $mform->getElementValue('id')) {
            $user = $DB->get_record('user', array('id' => $userid));
        } else {
            $user = false;
        }

        // Next the customisable profile fields.
        profile_definition_after_data($mform, $userid);
    }


    /**
     * Validate the form submission.
     *
     * @param array $usernew queried from db
     * @param array $files submitted form files
     * @return array
     */

    public function validation($usernew, $files) {

        global $CFG, $DB, $USER;

        $usernew = (object)$usernew;
        $usernew->email = $usernew->username = trim($usernew->email);

        $user = $DB->get_record('user', array('id' => $usernew->id));

        $errors = parent::validation($usernew, $files);

        if (!$user and !empty($usernew->createpassword)) {
            if ($usernew->suspended) {
                // Show some error because we can not mail suspended users.
                $errors['suspended'] = get_string('error');
            }
        }

        if (empty($usernew->email)) {
            // Might be only whitespace.
            $errors['email'] = get_string('required');
        } else if (!$user or $user->email !== $usernew->email) {
            // Check new username does not exist.
            if (isset($usernew->username)) {
                if ($DB->record_exists('user', array('username' => $usernew->username, 'mnethostid' => $CFG->mnet_localhost_id))) {
                    $errors['username'] = get_string('usernameexists');
                }
            }
            // Check allowed characters.
            if ($usernew->username !== core_text::strtolower($usernew->username)) {
                $errors['username'] = get_string('usernamelowercase');
            } else {
                if ($usernew->username !== clean_param($usernew->username, PARAM_USERNAME)) {
                    $errors['username'] = get_string('invalidusername');
                }
            }
        }

        if (!$user or $user->email !== $usernew->username) {
            if (!validate_email($usernew->username)) {
                $errors['email'] = get_string('invalidemail');
            } else if ($DB->record_exists('user', array(
                    'email' => $usernew->email,
                    'mnethostid' => $CFG->mnet_localhost_id))) {
                $errors['email'] = get_string('emailexists');
            }
        }

        if (!validate_email($usernew->email)) {
            $errors['email'] = get_string('formvalidation:invalidemail', 'auth_rsa');
        }
        if ($usernew->password != $usernew->confirmpassword) {
            $errors['password'] = get_string('formvalidation:notmatchedpasswords', 'auth_rsa');
            $errors['confirmpassword'] = get_string('formvalidation:notmatchedpasswords', 'auth_rsa');
        }
        if ($USER->profile['typeofaccount'] != 'student' and empty($usernew->institution)) {
            $errors['institution'] = get_string('formvalidation:schoolororg', 'auth_rsa');
        }
        if ($USER->profile['typeofaccount'] == 'student' and (empty($usernew->profile_field_yearlevel) or $usernew->profile_field_yearlevel == 'N/A')) {
            $errors['profile_field_yearlevel'] = get_string('formvalidation:yearlevel:student', 'auth_rsa');
        }
        if ($USER->profile['typeofaccount'] != 'student' and (!empty($usernew->profile_field_yearlevel) and $usernew->profile_field_yearlevel != 'N/A')) {
            $usernew->profile_field_yearlevel = 'N/A';
            $errors['profile_field_yearlevel'] = get_string('formvalidation:yearlevel:teacher', 'auth_rsa');
        }

        if ($USER->profile['typeofaccount'] == 'student' and (empty($usernew->profile_field_yearofbirth) or $usernew->profile_field_yearofbirth == 'N/A')) {
            $errors['profile_field_yearofbirth'] = get_string('formvalidation:yearofbirth:student', 'auth_rsa');
        }
        if ($USER->profile['typeofaccount'] != 'student' and (!empty($usernew->profile_field_yearofbirth) and $usernew->profile_field_yearofbirth != 'N/A')) {
            $usernew->profile_field_yearofbirth = 'N/A';
            $errors['profile_field_yearofbirth'] = get_string('formvalidation:yearofbirth:teacher', 'auth_rsa');
        }

        if (empty($USER->profile['whereareyoufrom'])) {
            $errors['profile_field_whereareyoufrom'] = get_string('formvalidation:whereareyoufrom', 'auth_rsa');
        }


        if ($this->signup_captcha_enabled()) {
            $recaptchaelement = $this->_form->getElement('recaptcha_element');
            if (!empty($this->_form->_submitValues['recaptcha_challenge_field'])) {
                $challengefield = $this->_form->_submitValues['recaptcha_challenge_field'];
                $responsefield = $this->_form->_submitValues['recaptcha_response_field'];
                if (true !== ($result = $recaptchaelement->verify($challengefield, $responsefield))) {
                    $errors['recaptcha'] = $result;
                }
            } else {
                $errors['recaptcha'] = get_string('missingrecaptchachallengefield');
            }
        }

        // Next the customisable profile fields.
        $errors += profile_validation($usernew, $files);

        return (count($errors) == 0) ? true : $errors;
    }
    /**
     * Returns whether or not the captcha element is enabled, and the admin settings fulfil its requirements.
     * @return bool
     */
    public function signup_captcha_enabled() {
        global $CFG;
        return !empty($CFG->recaptchapublickey) && !empty($CFG->recaptchaprivatekey) && get_config('auth/email', 'recaptcha');
    }
}


/**
 * Management - classes section
 *
 * @package    auth_rsa
 * @author     Bevan Holman <bevan@pukunui.com>, Pukunui
 * @copyright  2015 onwards, Pukunui
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class auth_rsa_manage_classes_section extends moodleform {

    /**
     * Return set of link to manage classes add/view/report
     * This is used from /auth/rsa/view.php
     */

    public function definition() {

        global $CFG;

        $mform =& $this->_form;

        $mform->addElement('header', 'classes', get_string('fieldlist:classes', 'auth_rsa'));
        $mform->setExpanded('classes');

        $mform->addElement('static', 'fieldlistclasses', get_string('managestudents:text:fieldlist:classes', 'auth_rsa'), '');

        $buttonarray = array();
        $buttonarray[] = &$mform->createElement('submit', 'addclassbutton', get_string('button:addclass', 'auth_rsa'), array('clickedbutton' => 'addclassbutton'));
        $buttonarray[] = &$mform->createElement('submit', 'viewclassesbutton', get_string('button:viewclasses', 'auth_rsa'));
        $buttonarray[] = &$mform->createElement('submit', 'classreportsbutton', get_string('button:classreports', 'auth_rsa'));
        $mform->addGroup($buttonarray, 'buttonar', '', array(' '), false);
        $mform->closeHeaderBefore('buttonar');
    }
    public function validation($usernew, $files) {
        global $CFG;
    }
}
class auth_rsa_classform extends moodleform {
    /**
     * Define the form
     */
    public function definition() {
        global $DB;

        $mform =& $this->_form;
        $id = $this->_customdata;
        $strrequired = get_string('required');

        $mform->addElement('hidden', 'id');
        $mform->setType('id', PARAM_INT);
        $mform->setDefault('id', $id);

        if (!empty($id) and ($class = $DB->get_field('auth_rsa_class', 'name', array('id' => $id))) ) {
            $mform->addElement('static', 'currentname', get_string('formlabel:currentclassname', 'auth_rsa'), $class);

            $mform->addElement('hidden', 'classname');
            $mform->setType('classname', PARAM_RAW);
            $mform->setDefault('classname', $class);

            $strname   = get_string('formlabel:newclass', 'auth_rsa');
            $strsubmit = get_string('button:savechanges', 'auth_rsa');
        } else {
            $strname   = get_string('formlabel:class', 'auth_rsa');
            $strsubmit = get_string('button:addclass', 'auth_rsa');
        }

        $mform->addElement('text', 'name', $strname, array('size' => 20));
        $mform->setType('name', PARAM_RAW);
        $mform->addRule('name', $strrequired, 'required', null, 'client');

        $buttonarray = array();
        $buttonarray[] =& $mform->createElement('submit', 'submitbutton', $strsubmit);
        $buttonarray[] =& $mform->createElement('submit', 'cancel', get_string('cancel'));
        $mform->addGroup($buttonarray, 'buttonar', '', array(' '), false);
    }
    public function validation($usernew, $files) {
        global $CFG;
        return true;
    }
}
/**
 * Management - classes section
 *
 * @package    auth_rsa
 * @author     Bevan Holman <bevan@pukunui.com>, Pukunui
 * @copyright  2015 onwards, Pukunui
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class auth_rsa_manage_students_classes_section extends moodleform {

    /**
     * Return set of link to manage classes add/view/report
     * This is used from /auth/rsa/view.php
     */

    public function definition() {

        global $CFG;

        $mform =& $this->_form;

        $buttonarray = array();
        $buttonarray[] = &$mform->createElement('submit', 'addstudentbutton', get_string('button:addstudent', 'auth_rsa'), array('clickedbutton' => 'addclassbutton'));
        $buttonarray[] = &$mform->createElement('submit', 'addgroupofstudentsbutton', get_string('button:addstudentgroup', 'auth_rsa'));
        $mform->addGroup($buttonarray, 'buttonar', '', array(' '), false);
        $mform->closeHeaderBefore('buttonar');
    }
    public function validation($usernew, $files) {
        global $CFG;
    }
}

/**
 * Management - delete a student from a class using a drop down of classes they are in
 *
 * @package    auth_rsa
 * @author     Bevan Holman <bevan@pukunui.com>, Pukunui
 * @copyright  2015 onwards, Pukunui
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class auth_rsa_delete_student_from_class extends moodleform {

    /**
     * Return set of link to manage classes add/view/report
     * This is used from /auth/rsa/view.php
     */

    public function definition() {

        global $CFG;
        global $DB;

        $mform =& $this->_form;
        $deleteclasslist = $this->_customdata['deleteclasslist'];
        $classinfo = $this->_customdata['classinfo'];

        $mform->addElement('hidden', 'delete', $classinfo->studentuserid);
        $mform->setType('delete', PARAM_INT);
/*
        $mform->addElement('hidden', 'confirm', md5('delete'.$classinfo->studentuserid));
        $mform->setType('confirm', PARAM_RAW);
*/
        if ($deleteclasslist) {
            foreach ($deleteclasslist as $key => $value) $deleteclasslistarray["$key"] = $value->classname;

            $mform->addElement('static', 'question', '', get_string('managestudents:text:chooseclass', 'auth_rsa', $classinfo->name));
            $mform->addElement('select', 'classid', get_string('formlabel:class', 'auth_rsa'), $deleteclasslistarray);
        }
        $this->add_action_buttons(true, get_string('button:continue', 'auth_rsa'));
    }
    public function validation($usernew, $files) {
        global $CFG;
    }
}
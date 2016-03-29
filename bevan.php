<?php
require_once('../../config.php');
require_once('./locallib.php');


if ($_SERVER['REMOTE_ADDR'] == '203.59.120.7')
{
    @error_reporting(E_ALL | E_STRICT);   // NOT FOR PRODUCTION SERVERS!
    @ini_set('display_errors', '1');         // NOT FOR PRODUCTION SERVERS!
    $CFG->debug = (E_ALL | E_STRICT);   // === DEBUG_DEVELOPER - NOT FOR PRODUCTION SERVERS!
    $CFG->debugdisplay = 1;

}

auth_rsa_cpdlog();


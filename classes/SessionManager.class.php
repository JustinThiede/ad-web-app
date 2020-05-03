<?php
/**
* Manages sessions
*
*
* PHP version 7.4
*
*
* @package ad-web-app
* @author Original Author <justin.thiede@visions.ch>
* @copyright visions.ch GmbH
* @license http://creativecommons.org/licenses/by-nc-sa/3.0/
*/

class SessionManager
{
    function __construct(){
        // Start session and set new session id
        if (!isset($_SESSION['STARTED'])) {
            session_start();
            session_regenerate_id(true);
            $_SESSION['STARTED'] = true;
        }
    }

    // Creates and deletes sessions needed for user
    public function userSessions()
    {
        // Kill session after 30min of inactivity
        if (isset($_SESSION['LAST_ACTIVITY']) && (time() - $_SESSION['LAST_ACTIVITY'] >= 1800)) {
            $this->killSessions();
        }

        $_SESSION['LAST_ACTIVITY'] = time();

        // Change session ID every 30min
        if (!isset($_SESSION['LAST_ID_CHANGE'])) {
            $_SESSION['LAST_ID_CHANGE'] = time();
        } else if (time() - $_SESSION['LAST_ID_CHANGE'] >= 1800) {
            session_regenerate_id(true);
            $_SESSION['LAST_ID_CHANGE'] = time();
        }
    }

    public function killSessions()
    {
        unset($_SESSION);
        session_destroy();
        setcookie("PHPSESSID", "", 1);
    }
}

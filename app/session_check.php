<?php
/**
* Checks if user is loged in
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

session_start();

if (isset($_SESSION['USER'])) {
	echo json_encode('true');
} else {
	echo json_encode('false');
}

<?php
/**
 * the core loads up every class
 * and translates the request into a
 * MVC-suitable format
 */


//Config with global used constants
include 'config.php';

// Autoloader for all classes
function my_autoloader($class) {
    require_once('classes/' . $class . '.class.php');
}

spl_autoload_register('my_autoloader');

$sessionManager = new SessionManager();

/**
 * Routing
 * translate the URL-params
 * delivered by our .htaccess into
 * page and actions,
 * if nothing given use user as default
 */
$request    = explode('/', (empty($_REQUEST['args']) ? 'user' : $_REQUEST['args']));
$page 	    = $request[0];
$action     = $request[1]; // define rest as action
$postAction = isset($_POST['action']) ? $_POST['action'] : ''; // Logout is sent over post

if (isset($_SESSION['USER']) && $postAction != 'logout') {
	$sessionManager->userSessions();

	/**
	 * MVC
	 * call MVC-class and proccess
	 * our page-var there
	 */
	new MVC($page);

	/**
	 * Controller
	 * call MVC-defined controller
	 * and process our action-var there
	 */
	new Controller($action);
} else {
	new MVC('login');
	new Controller($postAction);
}

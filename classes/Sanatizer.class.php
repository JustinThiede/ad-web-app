<?php
/**
* Sanitizer to clean strings
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

class Sanitizer
{

    function __construct(){}

    // Strip out potentially dangerous inputs
    public function strip($userInput)
    {
         return htmlspecialchars(strip_tags($userInput),ENT_QUOTES);
    }
}

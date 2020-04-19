<?php
/*
* Build MVC-Call based on given
* parameters based on Request
*/

class MVC
{
    public $page;

    function __construct($page)
    {
        /* Load View */
        require_once('pages/view/view.php');

        /* Load Model */
        require_once('pages/model/' . $page . '.php');

        /* Load Controller */
        require_once('pages/controller/' . $page . '.php');
    }
}

<?php
/**
 * View
 * globally used class for all pages
 * to include view-files
 */

class View
{
    /**
     * Get View
     * use page-paramter to navigate into folder
     * use action-paramter to call correct file
     * use data-paramter to send vars
     */
    function getview($page, $action=null, $data=null)
    {
        require_once('pages/view/' . $page . '/' . $action . '.php');
    }
}

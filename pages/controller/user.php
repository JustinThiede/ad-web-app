<?php declare(strict_types=1);
/**
* Calls user login and pw reset functions
*
*
* PHP version 7.3
*
*
* @package ad-web-app
* @author Original Author <justin.inw@hotmail.com>
* @copyright BZTF
* @license http://creativecommons.org/licenses/by-nc-sa/3.0/
*/

class Controller
{
    protected View   $view;
    protected Model  $model;
    protected string $action;

    public function __construct($action)
    {
        $this->view         = new View();
        $this->model        = new Model();
        $this->action       = !empty($action) ? $action : 'index'; // if no action given, use index as default

        $this->callpage();
    }

    /**
     *
     * Call Page
     * use action-parameters to call
     * the according view
     *
     * @return void
     */
    protected function callpage(): void
    {
        switch($this->action) {
            default:
                $this->index();
        }
    }

    /**
     *
     * Index view
     *
     * @return void
     */
    protected function index(): void
    {
        $this->view->getview('user', 'index');
    }
}

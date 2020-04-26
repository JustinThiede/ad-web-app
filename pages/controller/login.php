<?php declare(strict_types=1);
/**
* Calls user login and pw reset functions
*
*
* PHP version 7.4
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

        // Set class variables to post variables
        foreach ($_POST as $key => $value) {
            $this->{$key} = $value ?? '';
        }

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
            case 'login':
                $this->auth();
            break;

            case 'logout':
                $this->logout();
            break;

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
        $this->view->getview('login', 'index');
    }

   /**
    *
    * Authenticate users
    * Call view based on success or fail of login
    *
    * @return void
    */
    protected function auth(): void
    {
        if ($this->model->checkLogin($this->email, $this->pw)) {
            header('Location: /user/index');
        } else {
            $this->view->getview('login', 'index', 'Bitte überprüfen Sie das Passwort und die Email.');
        }
    }

    /**
     *
     * Logout user and kill all sessions
     *
     * @return void
     */
    protected function logout(): void
    {
        $this->model->killSession();
        header('Location: /');
    }
}

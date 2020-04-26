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
    protected View      $view;
    protected Model     $model;
    protected Sanitizer $sanitizer;
    protected string    $action;

    public function __construct($action)
    {
        $this->view      = new View();
        $this->model     = new Model();
        $this->sanitizer = new Sanitizer();
        $this->action    = !empty($action) ? $action : 'index'; // if no action given, use index as default

        // Set class variables to post variables
        foreach ($_POST as $key => $value) {
            $this->{$key} = !empty($value) ? $this->sanitizer->strip($value) : '';
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
            case 'add':
                $this->add();
            break;

            case 'update':
                $this->update();
            break;

            case 'delete':
                $this->delete();
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
        $users = $this->model->getUsers();
        $this->view->getview('user', 'index', $users);
    }

    protected function add(): void
    {
        if (empty($_POST)) {
            $this->view->getview('user', 'add');
        } else if (empty($this->edit)) {
            $pwSame    = $this->model->samePw($this->pw, $this->pwConfirm);
            $pwComplex = $this->model->pwComplexity($this->pw);

            if (!$pwSame) {
                $this->view->getview('user', 'add', 'Die Passwörter müssen gleich sein.');
            }

            if (!$pwComplex) {
                $this->view->getview('user', 'add', 'Das Passwort muss mindestens 8 Zeichen, Grossbuchstaben, Kleinbuchstaben und entweder ein Spezialzeichen oder eine Zahl enthalten.');
            }

            

        } else {

        }



    }

    // Call delete or edit pages
    protected function update()
    {
        if (!empty($this->edit)) {
            $this->view->getview('user', 'add', $user);
        } elseif (!empty($this->delete)) {
            $this->view->getview('user', 'delete', [
                'dn' => $this->dn,
                'cn' => $this->cn
            ]);
        }
    }

    // Delete user
    protected function delete()
    {
        $userDeleted = $this->model->deleteUser($this->dn);

        if ($userDeleted) {
            $this->view->getview('user', 'delete', [
                'success' => 'Benutzer gelöscht!'
            ]);
        } else {
            $this->view->getview('user', 'delete', [
                'error' => 'Der Benutzer konnte nicht gelöscht werden! Wenden Sie sich an den Administrator.'
            ]);
        }
    }
}

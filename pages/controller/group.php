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
        $groups = $this->model->getGroups();
        $this->view->getview('group', 'index', $groups);
    }

    /**
     *
     * Add users
     *
     * @return void
     */
    protected function add(): void
    {
        if (empty($_POST)) {
            $this->view->getview('user', 'add');
        } else if (empty($this->edit)) {
            $pwSame    = $this->model->samePw($this->pw, $this->pwConfirm);
            $pwComplex = $this->model->pwComplexity($this->pw);
            $exists    = $this->model->checkExist($this->loginName);

            if (!$pwSame) {
                $this->view->getview('user', 'add', 'Die Passwörter müssen gleich sein.');
                return;
            }

            if (!$pwComplex) {
                $this->view->getview('user', 'add', 'Das Passwort muss mindestens 8 Zeichen, Grossbuchstaben, Kleinbuchstaben und entweder ein Spezialzeichen oder eine Zahl enthalten.');
                return;
            }

            if ($exists) {
                $this->view->getview('user', 'add', 'Der Benutzer existiert bereits.');
                return;
            }

            $created = $this->model->createUser($this->firstName, $this->lastName, $this->loginName, $this->pw);

            if (!$created) {
                $this->view->getview('user', 'add', 'Der Benutzer konnte nicht hinzugefügt werden.');
            } else {
                $this->view->getview('user', 'add', 'Der Benutzer wurde erfolgreich hinzugefügt.');
            }
        } else {
            if (isset($this->changePw)) {
                $pwSame    = $this->model->samePw($this->pw, $this->pwConfirm);
                $pwComplex = $this->model->pwComplexity($this->pw);

                if (!$pwSame) {
                    $this->view->getview('user', 'add', 'Die Passwörter müssen gleich sein.');
                    return;
                }

                if (!$pwComplex) {
                    $this->view->getview('user', 'add', 'Das Passwort muss mindestens 8 Zeichen, Grossbuchstaben, Kleinbuchstaben und entweder ein Spezialzeichen oder eine Zahl enthalten.');
                    return;
                }
            } else {
                $this->pw = '';
            }

            $updatedUser = $this->model->updateUser($this->edit, $this->memberOf, $this->firstName, $this->lastName, $this->loginName, $this->pw);

            if (!$updatedUser) {
                $this->view->getview('user', 'add', 'Der Benutzer konnte nicht geändert werden.');
            } else {
                $this->view->getview('user', 'add', 'Der Benutzer wurde erfolgreich geändert.');
            }
        }


        return;
    }

    /**
     *
     * Call delete or edit pages
     *
     * @return void
     */
    protected function update(): void
    {
        if (!empty($this->edit)) {
            $user = $this->model->getGroup($this->cn);
            $this->view->getview('group', 'add', $user);
        } elseif (!empty($this->delete)) {
            $this->view->getview('group', 'delete', [
                'dn' => $this->dn,
                'cn' => $this->cn
            ]);
        }
    }

    /**
     *
     * Delete groups
     *
     * @return void
     */
    protected function delete(): void
    {
        $groupDeleted = $this->model->deleteGroup($this->dn);

        if ($groupDeleted) {
            $this->view->getview('group', 'delete', [
                'success' => 'Gruppe gelöscht!'
            ]);
        } else {
            $this->view->getview('group', 'delete', [
                'error' => 'Die Gruppe konnte nicht gelöscht werden! Wenden Sie sich an den Administrator.'
            ]);
        }
    }
}

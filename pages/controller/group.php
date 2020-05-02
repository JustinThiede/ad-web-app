<?php declare(strict_types=1);
/**
 * Calls group add, edit, select, delete functions
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
     * Add group
     *
     * @return void
     */
    protected function add(): void
    {
        if (empty($_POST)) {
            $this->view->getview('group', 'add');
        } else if (empty($this->edit)) {
            $exists    = $this->model->checkExist($this->cn);

            if ($exists) {
                $this->view->getview('group', 'add', 'Die Gruppe existiert bereits.');
                return;
            }

            $created = $this->model->createGroup($this->cn, $this->groupType);

            if (!$created) {
                $this->view->getview('group', 'add', 'Die Gruppe konnte nicht hinzugefügt werden.');
            } else {
                $this->view->getview('group', 'add', 'Die Gruppe wurde erfolgreich hinzugefügt.');
            }
        } else {
            $updateGroup = $this->model->updateGroup($this->edit, $this->cn, $this->groupType);

            if (!$updateGroup) {
                $this->view->getview('group', 'add', 'Die Gruppe konnte nicht geändert werden.');
            } else {
                $this->view->getview('group', 'add', 'Die Gruppe wurde erfolgreich geändert.');
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
            $group = $this->model->getGroup($this->cn);
            $this->view->getview('group', 'add', $group);
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

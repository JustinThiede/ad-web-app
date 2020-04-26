<?php declare(strict_types=1);
/**
* Checks and sets user athentifications
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

class Model
{
    protected MiniOrm        $db;
    protected SessionManager $sessionManager;
    protected LDAP           $ldap;

    public function __construct()
    {
        $this->db             = new MiniOrm(CONF_DB_HOST, CONF_DB_DB, CONF_DB_USER, CONF_DB_PW, CONF_DB_CHARSET);
        $this->sessionManager = new SessionManager();
        $this->ldap           = new LDAP();
    }

    public function getUsers() {
        return $this->ldap->searchUsers();
    }
}

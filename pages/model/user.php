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

    public function getUsers()
    {
        return $this->ldap->searchUsers();
    }

    public function deleteUser(string $dn)
    {
        return $this->ldap->deleteObject($dn);
    }

    public function createUser(string $firstName, string $lastName, string $loginName, string $pw)
    {
        return $this->ldap->createObject($firstName, $lastName, $loginName, $pw);
    }

    public function checkExist(string $loginName)
    {
        if (!$this->ldap->objectExists($loginName)) {
            return false;
        }

        return true;
    }

    public function samePw(string $pw, string $pwConfirm)
    {
        if ($pw != $pwConfirm) {
            return false;
        }

        return true;
    }

    public function pwComplexity(string $pw)
    {
        $uppercase    = preg_match('@[A-Z]@', $pw);
        $lowercase    = preg_match('@[a-z]@', $pw);
        $specialChars = preg_match('@[^\w]@', $pw);
        $number       = preg_match('@[0-9]@', $pw);

        if (!$uppercase || !$lowercase || (!$specialChars && !$number) || strlen($pw) < 8) {
            return false;
        }

        return true;
    }
}

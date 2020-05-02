<?php declare(strict_types=1);
/**
 * Group get, add, edit, delete and validation functions
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

    /**
     *
     * Gets LDAP groups
     *
     * @return array
     */
    public function getGroups(): array
    {
        return $this->ldap->searchGroups();
    }

    /**
     *
     * Gets specific LDAP group
     *
     * @param $cn the CN of the group thats beeing searched
     * @return array
     */
    public function getGroup($cn): array
    {
        return $this->ldap->searchGroup($cn);
    }

    /**
     *
     * Deletes LDAP groups
     *
     * @param  string $dn the dn of the object
     * @return bool
     */
    public function deleteGroup(string $dn):bool
    {
        return $this->ldap->deleteObject($dn);
    }

    /**
     *
     * Creates LDAP group
     *
     * @param  string $cn CN of group
     * @param  string $groupType Type of the group
     * @return bool
     */
    public function createGroup(string $cn, string $groupType): bool
    {
        return $this->ldap->createGroup($cn, $groupType);
    }

    /**
     *
     * Updates LDAP user
     *
     * @param  string $dn The DN of the user
     * @param  string $memberOf The groups the user is a member of
     * @param  string $firstName firstname of the user
     * @param  string $lastName lastname of the user
     * @param  string $loginName the loginname of the user
     * @param  string $pw the password of the user
     * @return bool
     */
    public function updateUser(string $dn, string $memberOf, string $firstName, string $lastName, string $loginName, string $pw): bool
    {
        return $this->ldap->updateObject($dn, $memberOf, $firstName, $lastName, $loginName, $pw);
    }

    /**
     *
     * Checks if the object already exists
     *
     * @param  string $cn The CN of the group
     * @return bool
     */
    public function checkExist(string $cn): bool
    {
        return $this->ldap->objectExists($cn);
    }
}

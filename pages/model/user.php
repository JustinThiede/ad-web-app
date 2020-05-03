<?php declare(strict_types=1);
/**
 * User get, add, edit, delete and validation functions
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
     * Gets LDAP users
     *
     * @return array
     */
    public function getUsers(): array
    {
        return $this->ldap->searchUsers();
    }

    /**
     *
     * Gets specific LDAP user
     *
     * @return array
     */
    public function getUser($cn): array
    {
        return $this->ldap->searchUser($cn);
    }

    /**
     *
     * Deletes LDAP users
     *
     * @param  string $dn the dn of the object
     * @return bool
     */
    public function deleteUser(string $dn):bool
    {
        return $this->ldap->deleteObject($dn);
    }

    /**
     *
     * Creates LDAP user
     *
     * @param  string $firstName firstname of the user
     * @param  string $lastName lastname of the user
     * @param  string $loginName the loginname of the user
     * @param  array $memberOf the groups the user belongs to
     * @param  string $pw the password of the user
     * @return bool
     */
    public function createUser(string $firstName, string $lastName, string $loginName, array $memberOf, string $pw): bool
    {
        return $this->ldap->createUser($firstName, $lastName, $loginName, $memberOf, $pw);
    }

    /**
     *
     * Updates LDAP user
     *
     * @param  string $dn The DN of the user
     * @param  string $firstName firstname of the user
     * @param  string $lastName lastname of the user
     * @param  string $loginName the loginname of the user
     * @param  array  $memberOf The groups the user belongs to
     * @param  string $pw the password of the user
     * @return bool
     */
    public function updateUser(string $dn, string $firstName, string $lastName, string $loginName, array $memberOf, string $pw): bool
    {
        return $this->ldap->updateUser($dn, $firstName, $lastName, $loginName, $memberOf, $pw);
    }

    /**
     *
     * Checks if the object already exists
     *
     * @param  string $cn The CN of the user
     * @return bool
     */
    public function checkExist(string $cn): bool
    {
        return $this->ldap->objectExists($cn);
    }

    /**
     *
     * Checks that user inputted passwords are both the same
     *
     * @param  string $pw first entered password
     * @param  string $pwConfirm second entered password
     * @return bool
     */
    public function samePw(string $pw, string $pwConfirm): bool
    {
        if ($pw !== $pwConfirm) {
            return false;
        }

        return true;
    }

    /**
     *
     * Checks that password has lowercase, uppercase and
     * special chars or numbers in it. Also it makes shure that there are
     * at least 8 characters
     *
     * @param  string $pw first entered password
     * @return bool
     */
    public function pwComplexity(string $pw): bool
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

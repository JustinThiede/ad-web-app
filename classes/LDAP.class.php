<?php declare(strict_types=1);
/**
* Connection and interaction with LDAP server
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

class LDAP
{
    private $con  = '';
    private $bind = '';

    function __construct(){
        $this->con  = ldap_connect(CONF_LDAP_HOST, CONF_LDAP_PORT);
        $this->bind = ldap_bind($this->con, CONF_LDAP_USER, CONF_LDAP_PW);

        if (!$this->con) {
            exit('Could not connect to Server');
        }

        if (!$this->bind) {
            exit('Could not authenticate user on LDAP');
        }
    }

    /**
     *
     * Searches for users in the LDAP server
     *
     * @return array
     */
    public function searchUsers(): array
    {
        $base_dn   = "CN=Users, DC=smirnyag, DC=ch";
        $filter    = "(&(objectClass=person)(!(cn=ad-web)))"; // Only people and exclude the user that the webapp uses
        $attr      = array("DN","OU","CN","DC","memberof", "userprincipalname", "givenname", "sn");
        $sr        = ldap_search($this->con, $base_dn, $filter, $attr);
        $searchRes = ldap_get_entries($this->con, $sr);
        $users     = [];

        foreach ($searchRes as $key => $res) {
            // Fields that are overall info about the domain dont have int keys
            if (gettype($key) != 'integer') {
                continue;
            }

            if (isset($res['memberof']['count'])) {
               unset($res['memberof']['count']); // Get rid of count field for implode of groups
            }

            $users[] = [
                'cn'        => $res['cn'][0] ?? '',
                'dn'        => $res['dn'] ?? '',
                'loginName' => $res['userprincipalname'][0] ?? '',
                'firstName' => $res['givenname'][0] ?? '',
                'lastName'  => $res['sn'][0] ?? '',
                'memberOf'  => !empty($res['memberof']) ? implode(',', $res['memberof']) : ''
            ]; // Array for further processing
        };

        return $users;
    }

    /**
     *
     * Create new object in LDAP server
     *
     * @return bool
     */
    public function createObject(string $firstName, string $lastName, string $loginName, string $pw): bool
    {
        $cn    = $firstName . ' ' . $lastName;
        $dn    = 'CN=' . $cn . ', CN=Users, DC=smirnyag, DC=ch';
        $uniPw = iconv('UTF-8', 'UTF-16LE', '"' . $pw . '"'); // Only Unicode encoded passwords are excepted

        $ldaprecord['cn']                 = $cn;
        $ldaprecord['givenName']          = $firstName;
        $ldaprecord['sn']                 = $lastName;
        $ldaprecord['userprincipalname']  = $loginName . '@' . CONF_LDAP_DOMAIN;
        $ldaprecord['sAMAccountName']     = $loginName;
        $ldaprecord['unicodepwd']         = $uniPw;
        $ldaprecord['objectclass'][0]     = 'top';
        $ldaprecord['objectclass'][1]     = 'person';
        $ldaprecord['objectclass'][2]     = 'organizationalPerson';
        $ldaprecord['objectclass'][3]     = 'user';
        $ldaprecord["UserAccountControl"] = '512';

        return ldap_add($this->con, $dn, $ldaprecord;
    }


    public function addMember($cn, $group): void
    {
        $groupName           = 'CN=' . $group . ', CN=Users, DC=smirnyag, DC=ch';
        $groupInfo['member'] = 'CN=' . $cn . ', CN=Users, DC=smirnyag, DC=ch'; // User's DN is added to group's 'member' array

        ldap_mod_add($this->con, $groupName, $groupInfo);
    }

    /**
     *
     * Check if object alredy exists
     *
     * @return bool
     */
    public function objectExists(string $loginName): bool
    {
        $base_dn   = "CN=Users, DC=smirnyag, DC=ch";
        $filter    = '(sAMAccountName=' . $loginName . ')';
        $sr        = ldap_search($this->con, $base_dn, $filter);
        $searchRes = ldap_get_entries($this->con, $sr);

        if ($searchRes['count'] == 0) {
            return false;
        }

        return true;
    }

    /**
     *
     * Delete object
     *
     * @return bool
     */
    public function deleteObject(string $dn): bool
    {
        return ldap_delete($this->con, $dn);
    }
}

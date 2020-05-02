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
        ldap_set_option($this->con, LDAP_OPT_PROTOCOL_VERSION, 3); // User version 3 of LDAP
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
     * @return array $users All found users
     */
    public function searchUsers(): array
    {
        $base_dn   = 'CN=Users, DC=smirnyag, DC=ch';
        $filter    = '(&(objectClass=person)(!(cn=ad-web)))'; // Only people and exclude the user that the webapp uses
        $attr      = array('DN','OU','CN','DC','memberof', 'userprincipalname', 'givenname', 'sn');
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
     * Searches for specific user in the LDAP server
     *
     * @param string $cn The users CN
     * @return array $user Found user
     */
    public function searchUser(string $cn): array
    {
        $base_dn   = 'CN=Users, DC=smirnyag, DC=ch';
        $filter    = '(CN=' . $cn . ')'; // Only people and exclude the user that the webapp uses
        $attr      = array('DN', 'memberof', 'givenname', 'sn', 'samaccountname');
        $sr        = ldap_search($this->con, $base_dn, $filter, $attr);
        $searchRes = ldap_get_entries($this->con, $sr)[0]; // Only one result is returned

        $user      = [
            'firstName' => $searchRes['givenname'][0],
            'lastName'  => $searchRes['sn'][0],
            'loginName' => $searchRes['samaccountname'][0],
            'dn'        => $searchRes['dn'],
            'memberOf'  => $searchRes['memberof'][0]
        ];

        return $user;
    }

    /**
     *
     * Create new object in LDAP server
     *
     * @param  string $firstName firstname of the user
     * @param  string $lastName lastname of the user
     * @param  string $loginName the loginname of the user
     * @param  string $pw the password of the user
     * @return bool
     */
    public function createObject(string $firstName, string $lastName, string $loginName, string $pw): bool
    {
        $cn                               = $firstName . ' ' . $lastName;
        $dn                               = 'CN=' . $cn . ', CN=Users, DC=smirnyag, DC=ch';
        $uniPw                            = iconv('UTF-8', 'UTF-16LE', '"' . $pw . '"'); // Only Unicode encoded passwords are excepted
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
        $ldaprecord['UserAccountControl'] = '512';

        return ldap_add($this->con, $dn, $ldaprecord);
    }

    /**
     *
     * Create new object in LDAP server
     *
     * @param  string $dn The DN of the user
     * @param  string $memberOf The groups the user is a member of
     * @param  string $firstName firstname of the user
     * @param  string $lastName lastname of the user
     * @param  string $loginName the loginname of the user
     * @param  string $pw the password of the user
     * @return bool
     */
    public function updateObject(string $dn, string $memberOf, string $firstName, string $lastName, string $loginName, string $pw): bool
    {
        $newRdn                          = 'cn=' . $firstName . ' ' . $lastName; // Set new cn for user
        $ldaprecord['givenName']         = $firstName;
        $ldaprecord['sn']                = $lastName;
        $ldaprecord['userprincipalname'] = $loginName . '@' . CONF_LDAP_DOMAIN;
        $ldaprecord['sAMAccountName']    = $loginName;

        // User change doesnt require a new password
        if (!empty($pw)) {
            $uniPw                    = iconv('UTF-8', 'UTF-16LE', '"' . $pw . '"'); // Only Unicode encoded passwords are excepted
            $ldaprecord['unicodepwd'] = $uniPw;
        }

        if (!ldap_mod_replace($this->con, $dn, $ldaprecord)) {
            return false;
        }

        // Change CN needs to be after replace otherwise DN is wrong
        if (!ldap_rename($this->con, $dn, $newRdn, 'CN=Users, DC=smirnyag, DC=ch', TRUE)) {
            return false;
        }

        return true;
    }

    /**
     *
     * Create new object in LDAP server
     *
     * @param  string $cn The CN of the object beeing added to group
     * @param  string $group The group thats beeing added to
     * @return void
     */
    public function addMember(strign $cn, string $group): void
    {
        $groupName           = 'CN=' . $group . ', CN=Users, DC=smirnyag, DC=ch';
        $groupInfo['member'] = 'CN=' . $cn . ', CN=Users, DC=smirnyag, DC=ch'; // User's DN is added to group's 'member' array

        ldap_mod_add($this->con, $groupName, $groupInfo);
    }

    /**
     *
     * Check if object alredy exists
     *
     * @param  string $loginName The loginname to search for
     * @return bool
     */
    public function objectExists(string $loginName): bool
    {
        $base_dn   = 'CN=Users, DC=smirnyag, DC=ch';
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
     * @param  string $dn The object to delete
     * @return bool
     */
    public function deleteObject(string $dn): bool
    {
        return ldap_delete($this->con, $dn);
    }
}

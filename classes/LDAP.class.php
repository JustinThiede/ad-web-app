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

            $users[] = [
                'cn'        => $res['cn'][0] ?? '',
                'dn'        => $res['dn'] ?? '',
                'loginName' => $res['userprincipalname'][0] ?? '',
                'firstName' => $res['givenname'][0] ?? '',
                'lastName'  => $res['sn'][0] ?? '',
                'memberOf'  => !empty($res['memberof']) ? $this->formatMember($res['memberof']) : ''
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
        $filter    = '(CN=' . $cn . ')';
        $attr      = array('DN', 'memberof', 'givenname', 'sn', 'samaccountname');
        $sr        = ldap_search($this->con, $base_dn, $filter, $attr);
        $searchRes = ldap_get_entries($this->con, $sr)[0]; // Only one result is returned

        $user      = [
            'firstName' => $searchRes['givenname'][0],
            'lastName'  => $searchRes['sn'][0],
            'loginName' => $searchRes['samaccountname'][0],
            'dn'        => $searchRes['dn'],
            'memberOf'  => !empty($searchRes['memberof']) ? $this->formatMember($searchRes['memberof']) : ''
        ];

        return $user;
    }

    /**
     *
     * Searches for groups in the LDAP server
     *
     * @return array $groups All found groups
     */
    public function searchGroups(): array
    {
        $base_dn   = 'CN=Users, DC=smirnyag, DC=ch';
        $filter    = '(objectClass=group)'; // Only groups
        $attr      = array('DN','CN','memberof', 'member');
        $sr        = ldap_search($this->con, $base_dn, $filter);
        $searchRes = ldap_get_entries($this->con, $sr);
        $users     = [];

        foreach ($searchRes as $key => $res) {
            // Fields that are overall info about the domain dont have int keys
            if (gettype($key) != 'integer') {
                continue;
            }

            $groups[] = [
                'cn'       => $res['cn'][0] ?? '',
                'dn'       => $res['dn'] ?? '',
                'memberOf' => !empty($res['memberof']) ? $this->formatMember($res['memberof']) : '',
                'member'   => !empty($res['member']) ? $this->formatMember($res['member']) : ''
            ]; // Array for further processing
        };

        return $groups;
    }

    /**
     *
     * Searches for specific group in the LDAP server
     *
     * @param string $cn The users CN
     * @return array $group Found group
     */
    public function searchGroup(string $cn): array
    {
        $base_dn   = 'CN=Users, DC=smirnyag, DC=ch';
        $filter    = '(CN=' . $cn . ')';
        $sr        = ldap_search($this->con, $base_dn, $filter);
        $searchRes = ldap_get_entries($this->con, $sr)[0]; // Only one result is returned

        $group     = [
            'cn'        => $cn,
            'dn'        => $searchRes['dn'],
            'groupType' => $searchRes['grouptype'][0] == 2 ? 2 : 1,
            'memberOf'  => !empty($searchRes['memberof']) ? $this->formatMember($searchRes['memberof']) : ''
        ];

        return $group;
    }

    /**
     *
     * Create new user in LDAP server
     *
     * @param  string $firstName firstname of the user
     * @param  string $lastName lastname of the user
     * @param  string $loginName the loginname of the user
     * @param  array  $memberOf the groups the user belongs to
     * @param  string $pw the password of the user
     * @return bool
     */
    public function createUser(string $firstName, string $lastName, string $loginName, array $memberOf, string $pw): bool
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

        return $this->saveCreates($dn, $cn, $ldaprecord, $memberOf);
    }

    /**
     *
     * Create new group in LDAP server
     *
     * @param  string $cn CN of the group
     * @param  string $groupType The type of the group
     * @param  array  $memberOf The groups the group belongs to
     * @return bool
     */
    public function createGroup(string $cn, string $groupType, array $memberOf): bool
    {
        $dn                           = 'CN=' . $cn . ', CN=Users, DC=smirnyag, DC=ch';
        $ldaprecord['cn']             = $cn;
        $ldaprecord['objectClass'][0] = 'top';
        $ldaprecord['objectClass'][1] = 'group';
        $ldaprecord["sAMAccountName"] = $cn;

        /*
         * For unknowen reasons only distribution group can be defined
         * if left empty security group works
         * if secuirty group is defined an error is returned
         */
        if ($groupType == 2) {
            $ldaprecord['groupType'] = $groupType;
        }

        return $this->saveCreates($dn, $cn, $ldaprecord, $memberOf);
    }

    /**
     *
     * Save created objects
     *
     * @param  string $dn The DN of the object
     * @param  string $cn The CN of the object
     * @param  array  $ldaprecord The attributes of the object
     * @param  array  $memberOf The groups the object belongs to
     * @return bool
     */
    private function saveCreates(string $dn, string $cn, array $ldaprecord, array $memberOf): bool
    {
        if (!ldap_add($this->con, $dn, $ldaprecord)) {
            return false;
        }

        empty($memberOf) ?: $this->modifyMember($cn, $memberOf, 'add'); // Add member to object

        return true;
    }

    /**
     *
     * Update user on LDAP server
     *
     * @param  string $dn The DN of the user
     * @param  string $firstName firstname of the user
     * @param  string $lastName lastname of the user
     * @param  string $loginName the loginname of the user
     * @param  array  $memberOf The groups the user belongs to
     * @param  array  $removeMember The groups the user is beeing removed from
     * @param  string $pw the password of the user
     * @return bool
     */
    public function updateUser(string $dn, string $firstName, string $lastName, string $loginName, array $memberOf, array $removeMember, string $pw): bool
    {
        $cn                              = $firstName . ' ' . $lastName;
        $newRdn                          = 'cn=' . $cn; // Set new cn for user
        $ldaprecord['givenName']         = $firstName;
        $ldaprecord['sn']                = $lastName;
        $ldaprecord['userprincipalname'] = $loginName . '@' . CONF_LDAP_DOMAIN;
        $ldaprecord['sAMAccountName']    = $loginName;

        // User change doesnt require a new password
        if (!empty($pw)) {
            $uniPw                    = iconv('UTF-8', 'UTF-16LE', '"' . $pw . '"'); // Only Unicode encoded passwords are excepted
            $ldaprecord['unicodepwd'] = $uniPw;
        }

        return $this->saveUpdates($dn, $cn, $newRdn, $ldaprecord, $memberOf, $removeMember);
    }

    /**
     *
     * Update group on LDAP server
     *
     * @param  string $dn The DN of the group
     * @param  string $cn The CN of the group
     * @param  string $groupType The type of the group
     * @param  array  $memberOf The groups the group belongs to
     * @param  array  $removeMember The groups the group is beeing removed from
     * @return bool
     */
    public function updateGroup(string $dn, string $cn, string $groupType, array $memberOf, array $removeMember): bool
    {
        $newRdn                          = 'CN=' . $cn; // Set new CN for user
        $ldaprecord['sAMAccountName']    = $cn;

        if ($groupType == 2) {
            $ldaprecord['groupType'] = $groupType;
        }

        return $this->saveUpdates($dn, $cn, $newRdn, $ldaprecord, $memberOf, $removeMember);
    }

    /**
     *
     * Save changes made to object
     *
     * @param  string $dn The DN of the object
     * @param  string $cn The CN of the object
     * @param  string $newRdn The new CN of the object
     * @param  array  $ldaprecord The attributes of the object
     * @param  array  $memberOf The group the object belongs to
     * @param  array  $removeMember The groups the group is beeing removed from
     * @return bool
     */
    private function saveUpdates(string $dn, string $cn, string $newRdn, array $ldaprecord, array $memberOf, array $removeMember): bool
    {
        if (!ldap_mod_replace($this->con, $dn, $ldaprecord)) {
            return false;
        }

        // Change CN needs to be after replace otherwise DN is wrong
        if (!ldap_rename($this->con, $dn, $newRdn, 'CN=Users, DC=smirnyag, DC=ch', TRUE)) {
            return false;
        }

        empty($removeMember) ?: $this->modifyMember($cn, $removeMember, 'remove'); // First remove then add incase user added back a group that he was removed from
        empty($memberOf) ?: $this->modifyMember($cn, $memberOf, 'add'); // Add member to group

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

    /**
     *
     * Add or remove members from a group
     *
     * @param  string $cn The CN of the object beeing added or removed from the group
     * @param  string $groups The groups that are beeing modified
     * @param  string $action Remove or add to group
     * @return void
     */
    private function modifyMember(string $cn, array $groups, string $action): void
    {
        $groupInfo['member'] = 'CN=' . $cn . ', CN=Users, DC=smirnyag, DC=ch'; // User's DN is added to group's 'member' array

        foreach ($groups as $group) {
            $groupName = 'CN=' . $group . ', CN=Users, DC=smirnyag, DC=ch';

            switch ($action) {
                case 'add':
                    ldap_mod_add($this->con, $groupName, $groupInfo);
                    break;

                case 'remove':
                    ldap_mod_del($this->con, $groupName, $groupInfo);
                    break;
            }
        }
    }

    /**
     *
     * Outputs the users and groups as their name instead of DN
     *
     * @param  array $groups The found groups DN
     * @return string $memberCn All user and group names
     */
    private function formatMember(array $groups): string
    {
        $memberCn = '';

        if (isset($groups['count'])) {
           unset($groups['count']); // Get rid of count field for implode of groups
        }

        foreach ($groups as $member) {
            $memberCn .= str_replace('CN=', '', explode(',', $member)[0]) . ';<br>';
        }

        return $memberCn;
    }

    /**
     *
     * Check if object alredy exists
     *
     * @param  string $loginName The loginname to search for
     * @return bool
     */
    public function objectExists(string $cn): bool
    {
        $base_dn   = 'CN=Users, DC=smirnyag, DC=ch';
        $filter    = '(CN=' . $cn . ')';
        $sr        = ldap_search($this->con, $base_dn, $filter);
        $searchRes = ldap_get_entries($this->con, $sr);

        if ($searchRes['count'] == 0) {
            return false;
        }

        return true;
    }
}

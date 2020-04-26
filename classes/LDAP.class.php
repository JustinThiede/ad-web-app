<?php
/**
*
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

    // Strip out potentially dangerous inputs
    public function searchUsers()
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
               unset($res['memberof']['count']);
            }

            $users[] = [
                'cn'        => $res['cn'][0] ?? '',
                'dn'        => $res['dn'] ?? '',
                'loginName' => $res['userprincipalname'][0] ?? '',
                'firstName' => $res['givenname'][0] ?? '',
                'lastName'  => $res['sn'][0] ?? '',
                'memberOf'  => !empty($res['memberof']) ? implode(',', $res['memberof']) : ''
            ];
        };

        return $users;
    }

    public function deleteObject(string $dn) {
        return ldap_delete($this->con, $dn);
    }
}

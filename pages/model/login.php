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

    public function __construct()
    {
        $this->db             = new MiniOrm(CONF_DB_HOST, CONF_DB_DB, CONF_DB_USER, CONF_DB_PW, CONF_DB_CHARSET);
        $this->sessionManager = new SessionManager();
    }

    /**
     *
     * Compare user inputted login credentials with the saved credentials in the DB
     *
     * @param string $email User inputted email adress
     * @param string $pw User inputted password
     * @return bool
     */
    public function checkLogin(string $email, string $pw): bool
    {
        $users = $this->db->selectAll('user');

        foreach ($users as $user) {
            if ($email == $user['email']) {
                // Verify password with salted hash which consists of pw and last login
                if (password_verify($pw . $user['last_login'], $user['pw'])) {
                    $this->updateUser($user['user_id'], $pw);
                    $_SESSION['USER'] = $user['user_id']; // Set user session
                    return true;
                }
            }
        }

        return false;
    }

    /**
     *
     * Update password and last login in DB
     *
     * @param int    $userId ID of the user
     * @param string $pw password of the user
     * @return void
     */
    protected function updateUser(int $userId, string $pw): void
    {
        $now   = time();
        $pw    = password_hash($pw . $now, PASSWORD_BCRYPT);
        $query = 'UPDATE `user` SET `pw` = :pw, `last_login` = :now WHERE `user_id` = :userId';
        $this->db->prepareQuery($query, $pw, $now, $userId);
    }

    /**
     *
     * Kill all session data
     *
     * @return void
     */
    public function killSession(): void
    {
        $this->sessionManager->killSessions();
    }
}

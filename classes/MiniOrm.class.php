<?php declare(strict_types=1);
/**
* A mini ORM for quick pdo use
*
*
* PHP version 7.4
*
*
* @package   ad-web-app
* @author    Original Author <justin.thiede@visions.ch>
* @copyright visions.ch GmbH
* @license   http://creativecommons.org/licenses/by-nc-sa/3.0/
*/

class MiniOrm
{
    protected PDO $pdo;
    protected PDOStatement $stmt;

    /**
     *
     * Connect to database
     *
     * @param string $host hostname of db
     * @param string $db db name
     * @param string $user db user
     * @param string $pass db password
     * @param string $charset db charset
     */
    public function __construct(string $host, string $db, string $user, string $pass, string $charset)
    {
        $dsn = 'mysql:host=' . $host . ';dbname=' . $db . ';charset=' . $charset;
        $options = [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ];

        try {
            $this->pdo = new PDO($dsn, $user, $pass, $options);
        } catch (\PDOException $e) {
            throw new \PDOException($e->getMessage(), (int)$e->getCode());
        }
    }

    /**
     *
     * Executes querys
     * First argument is the query the rest binds
     * If there is only one argument then there are no binds
     *
     * @param $arguments multiple agruments with query and binds
     * @return bool
     */
    public function prepareQuery(...$arguments): bool
    {
        if (count($arguments) > 1) {
            foreach ($arguments as $key => &$argument) {
                if ($key == 0) {
                    $this->stmt = $this->pdo->prepare($argument);
                } else {
                    $this->stmt->bindParam($key, $argument);
                }
            }
            unset($argument);
        } else {
            $this->stmt = $this->pdo->prepare($arguments[0]);
        }

        return $this->executeQuery();
    }

    /**
     *
     * Fetches all results of a query
     *
     * @return array
     */
    public function getData(): array
    {
        return $this->stmt->fetchAll();
    }

    /**
     *
     * Fetches a column
     *
     * @return string
     */
    public function getColumn(): string
    {
        $column = $this->stmt->fetchColumn();

        if (!$column) {
            return "No matching data found";
        }

        return $column;
    }

    /**
     *
     * Select all of a table
     *
     * @param string $table table to select all from
     * @return array
     */
    public function selectAll(string $table): array
    {
        return $this->pdo->query('select * from ' . $table)->fetchAll();
    }

    /**
     *
     * Execute a query
     *
     * @return bool
     */
    protected function executeQuery(): bool
    {
        try {
            $this->stmt->execute();
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }
}

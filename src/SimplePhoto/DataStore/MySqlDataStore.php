<?php namespace SimplePhoto\DataStore;

/**
 * @author Morrison Laju <morrelinko@gmail.com>
 */
class MySqlDataStore extends PdoConnection implements DataStoreInterface
{
    /**
     * Creates a MySql Connection
     *
     * @param array $parameters
     *      - [String] host: eg. localhost
     *      - [String] database: eg. my_app
     *      - [String] username: eg. root
     *      - [String] password: eg. 123456
     *      - [String] charset:  (Optional)
     *
     * @return \PDO
     */
    public function createConnection($parameters)
    {
        $connection = new \PDO(
            "mysql:host={$parameters['host']};dbname={$parameters['database']}",
            $parameters["username"],
            $parameters["password"]
        );

        if (isset($parameters["charset"])) {
            $connection->exec("SET CHARACTER SET {$parameters['charset']}");
        }

        return $connection;
    }
}

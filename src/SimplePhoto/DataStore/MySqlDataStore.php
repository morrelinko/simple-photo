<?php

/*
 * This file is part of the SimplePhoto package.
 *
 * (c) Laju Morrison <morrelinko@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace SimplePhoto\DataStore;

/**
 * @author Laju Morrison <morrelinko@gmail.com>
 */
class MySqlDataStore extends PdoConnection implements DataStoreInterface
{
    /**
     * Creates a MySql Connection
     *
     * @param array $parameters Connection parameters
     * <pre>
     * host: eg. localhost
     * database: eg. my_app
     * username: eg. root
     * password: eg. 123456
     * charset:  (Optional)
     * </pre>
     *
     * @return \PDO
     */
    public function createConnection($parameters)
    {
        $connection = new \PDO(
            'mysql:host=' . $parameters['host'] . ';dbname=' . $parameters['database'],
            $parameters['username'],
            $parameters['password']
        );

        if (isset($parameters['charset'])) {
            $connection->exec('SET CHARACTER SET ' . $parameters['charset']);
        }

        return $connection;
    }
}

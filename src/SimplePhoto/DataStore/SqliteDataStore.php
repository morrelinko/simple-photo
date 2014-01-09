<?php namespace SimplePhoto\DataStore;

use SimplePhoto\Utils\FileUtils;

/**
 * @author Morrison Laju <morrelinko@gmail.com>
 */
class SqliteDataStore extends PdoConnection implements DataStoreInterface
{
    /**
     * Creates an SQLiteConnection
     *
     * @param array $parameters
     *      - [String] database: eg. my_app.db
     *
     * @return \PDO
     */
    public function createConnection($parameters)
    {
        $path = FileUtils::normalizePath($parameters['database']);
        $connection = new \PDO("sqlite:{$path}");

        return $connection;
    }
}
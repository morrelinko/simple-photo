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

use SimplePhoto\Utils\FileUtils;

/**
 * @author Laju Morrison <morrelinko@gmail.com>
 */
class SqliteDataStore extends PdoConnection implements DataStoreInterface
{
    /**
     * Creates an SQLiteConnection
     *
     * @param array $parameters Connection parameters
     * <pre>
     * database: eg my_app.db
     * </pre>
     *
     * @return \PDO
     */
    public function createConnection($parameters)
    {
        $path = FileUtils::normalizePath($parameters['database']);
        $connection = new \PDO('sqlite:' . $path);

        return $connection;
    }
}

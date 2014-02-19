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
abstract class PdoConnection
{
    /**
     * @var \PDO
     */
    protected $db;

    /**
     * @var array
     */
    protected $options;

    /**
     * Constructor
     *
     * @param array|\PDO $connection
     * @param array $options
     *
     * @throws \InvalidArgumentException
     */
    public function __construct($connection, array $options = array())
    {
        $this->options = array_merge(array(
            'photo_table' => 'photos'
        ), $options);

        if (!$connection instanceof \PDO) {
            if (!is_array($connection)) {
                throw new \InvalidArgumentException(sprintf(
                    'First argument passed to %s must be a
                    configuration array or an instance of \PDO',
                    __CLASS__
                ));
            }

            $connection = array_merge(array(
                'username' => null,
                'password' => null
            ), $connection);

            $connection = $this->createConnection($connection);
        }

        $this->db = $connection;
        $this->db->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
    }

    /**
     * @param array $connection
     *
     * @return mixed
     */
    abstract public function createConnection($connection);

    /**
     * @return \PDO
     */
    public function getConnection()
    {
        return $this->db;
    }

    /**
     * {@inheritDoc}
     */
    public function addPhoto(array $values)
    {
        $values['createdAt'] = date("Y-m-d H:i:s");
        $values['updatedAt'] = $values['createdAt'];

        $sql = '
            INSERT INTO %s (
                storage_name, file_name, file_extension, file_size, file_path, file_mime, created_at, updated_at
            )
            VALUES (
                :storageName, :fileName, :fileExtension, :fileSize, :filePath, :fileMime, :createdAt, :updatedAt
            )
        ';

        $statement = $this->db->prepare(
            sprintf($sql, $this->options['photo_table'])
        );

        $statement->execute($values);

        return $this->db->lastInsertId();
    }

    /**
     * {@inheritDoc}
     */
    public function getPhoto($photoId)
    {
        $sql = '
            SELECT
                id, storage_name, file_name, file_path,
                file_extension, file_size, file_mime, created_at, updated_at
            FROM %s
            WHERE id = :photoId
        ';

        $statement = $this->db->prepare(
            sprintf($sql, $this->options['photo_table'])
        );

        $statement->execute(compact('photoId'));

        if ($photo = $statement->fetch(\PDO::FETCH_ASSOC)) {
            return $photo;
        }

        return array();
    }

    /**
     * {@inheritDoc}
     */
    public function getPhotos(array $photoIds)
    {
        $ids = str_repeat('?, ', count($photoIds)) . '?';
        $sql = '
            SELECT
                id, storage_name, file_name, file_path,
                file_extension, file_size, file_mime, created_at, updated_at
            FROM %s
            WHERE id IN (' . $ids . ')
        ';

        $statement = $this->db->prepare(
            sprintf($sql, $this->options['photo_table'])
        );

        $statement->execute($photoIds);

        if ($photos = $statement->fetchAll(\PDO::FETCH_ASSOC)) {
            return $photos;
        }

        return array();
    }

    /**
     * {@inheritDoc}
     */
    public function deletePhoto($photoId)
    {
        $sql = '
            DELETE FROM %s
            WHERE id = :photoId
        ';

        // Delete from database
        $statement = $this->db->prepare(
            sprintf($sql, $this->options['photo_table'])
        );

        return $statement->execute(compact('photoId'));
    }
}

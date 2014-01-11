<?php namespace SimplePhoto\DataStore;

/**
 * @author Morrison Laju <morrelinko@gmail.com>
 */
abstract class PdoConnection
{
    protected $db;

    protected $options;

    public function __construct($connection, array $options = array())
    {
        $this->options = array_merge(array(
            "photo_table" => "photos"
        ), $options);

        if (!$connection instanceof \PDO) {
            if (!is_array($connection)) {
                throw new \InvalidArgumentException(sprintf(
                    "First argument passed to %s must be a configuration array or an instance of \PDO",
                    __CLASS__
                ));
            }

            $connection = array_merge(array(
                "username" => null,
                "password" => null
            ), $connection);

            $connection = $this->createConnection($connection);
        }

        $this->db = $connection;
        $this->db->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
    }

    abstract public function createConnection($connection);

    /**
     * @return \PDO
     */
    public function getConnection()
    {
        return $this->db;
    }

    /**
     * {@inheritDocs}
     */
    public function addPhoto(array $values)
    {
        $statement = $this->db->prepare(sprintf("
            INSERT INTO %s (
                storage_name, file_path, file_mime
            )
            VALUES (
                :storageName, :filePath, :fileMime
            )
        ", $this->options["photo_table"]));

        $statement->execute($values);

        return $this->db->lastInsertId();
    }

    /**
     * {@inheritDocs}
     */
    public function getPhoto($photoId)
    {
        $statement = $this->db->prepare(sprintf("
            SELECT photo_id, storage_name, file_path, file_mime, created_at, updated_at
            FROM %s
            WHERE photo_id = :photoId
        ", $this->options["photo_table"]));

        $statement->execute(compact("photoId"));

        if ($photo = $statement->fetch(\PDO::FETCH_ASSOC)) {
            return $photo;
        }

        return array();
    }

    /**
     * {@inheritDocs}
     */
    public function deletePhoto($photoId)
    {
        // Delete from database
        $statement = $this->db->prepare(sprintf("
            DELETE FROM %s
            WHERE photo_id = :photoId
        ", $this->options["photo_table"]));

        return $statement->execute(compact("photoId"));
    }
}
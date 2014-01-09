<?php

require_once "../../autoload.php";

// Setup (this must have been done somewhere in your script so that it can be reused throughout your app)
$storageManager = new \SimplePhoto\StorageManager();
$storageManager->add("local", new \SimplePhoto\Storage\LocalStorage(__DIR__, "./files/photos"));

$dataStore = new \SimplePhoto\DataStore\SqliteDataStore(array(
    "database" => "sample_app.db"
));

$dataStore->getConnection()->exec("
    CREATE TABLE IF NOT EXISTS photos (
        photo_id INTEGER PRIMARY KEY,
        storage_name TEXT NOT NULL,
        file_path TEXT NOT NULL,
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
        updated_at DATETIME DEFAULT CURRENT_TIMESTAMP
    );
");

$simplePhoto = new \SimplePhoto\SimplePhoto($storageManager, $dataStore);

// Upload
if (isset($_POST["upload"])) {
    $photoId = $simplePhoto->uploadFromPhpFileUpload($_FILES["image"]);
}

$statement = $dataStore->getConnection()->prepare("SELECT * FROM photos");
$statement->execute();

foreach ($statement->fetchAll(PDO::FETCH_ASSOC) as $photo) {
    var_dump($simplePhoto->getPhoto($photo["photo_id"]));
}

?>

<form method="post" enctype="multipart/form-data">
    <input type="file" name="image">
    <button name="upload">Upload Image</button>
</form>
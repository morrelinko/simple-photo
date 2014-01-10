<?php

require_once "../../autoload.php";

// (Basic) Setup (this must have been done somewhere in your script so that it can be reused throughout your app)
$storageManager = new \SimplePhoto\StorageManager();
$storageManager->add("local", new \SimplePhoto\Storage\LocalStorage(__DIR__, "./files/photos"));

// (Advance) Adding fallback storage for getting default photos that do not exists
// $storageManager->add(\SimplePhoto\StorageManager::FALLBACK_STORAGE, new \SimplePhoto\Storage\LocalStorage(__DIR__, "./file/defaults"));
// Or simply with
$storageManager->setFallback(new \SimplePhoto\Storage\LocalStorage(__DIR__, "./files/defaults"));

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
if (isset($_POST["upload"]) && isset($_FILES["image"])) {
    $photoId = $simplePhoto->uploadFromPhpFileUpload($_FILES["image"]);
}

$statement = $dataStore->getConnection()->prepare("SELECT * FROM photos");
$statement->execute();

foreach ($statement->fetchAll(PDO::FETCH_ASSOC) as $photo) {
    var_dump($simplePhoto->getPhoto($photo["photo_id"]));
}

// Photo that does not exists
// var_dump($simplePhoto->getPhoto(1000, array("default" => "my_photo.png")));

// Get a photo Resized
$resize = $simplePhoto->getPhoto(1, array(
    "transform" => array(
        "size" => array(200, 200)
    )
));

var_dump($resize);
?>

<form method="post" enctype="multipart/form-data">
    <input type="file" name="image">
    <button name="upload">Upload Image</button>
</form>
<?php

require_once __DIR__ . '/../../vendor/autoload.php';

// 1. Storage Manager
$storageManager = new \SimplePhoto\StorageManager();

// Create local storage
$localStorage = new \SimplePhoto\Storage\LocalStorage(array(
    'root' => __DIR__,
    'path' => './files/photos'
));

// Create remote host storage
$remoteStorage = new \SimplePhoto\Storage\RemoteHostStorage(array(
        'path' => 'photos',
        'host' => '127.0.0.1',
        'port' => 21,
        'username' => 'morrelinko',
        'password' => '123456',
        'root' => '/',
        'url' => 'http://localhost/project/packages'
    )
);

// Add local storage to storage manager
$storageManager->add('local', $localStorage);

// Add remote storage to storage manager
$storageManager->add('static_host', $remoteStorage);

// Set fallback storage that loads default photos for invalid/not found photos
$storageManager->setFallback(
    new \SimplePhoto\Storage\LocalStorage(array(
        'root' => __DIR__,
        'path' => './files/defaults'
    ))
);

// 2. Data Store
$dataStore = new \SimplePhoto\DataStore\SqliteDataStore(array(
    'database' => 'sample_app.db'
));

// Not Required..
$dataStore->getConnection()->exec("
    CREATE TABLE IF NOT EXISTS photo (
        photo_id INTEGER PRIMARY KEY,
        storage_name TEXT NOT NULL,
        file_name TEXT NOT NULL,
        file_extension TEXT NOT NULL,
        file_size TEXT NOT NULL DEFAULT 0,
        file_path TEXT NOT NULL,
        file_mime TEXT NOT NULL,
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
        updated_at DATETIME DEFAULT CURRENT_TIMESTAMP
    );
");

// Create an instance of simple photo and return it..
return new \SimplePhoto\SimplePhoto($storageManager, $dataStore);

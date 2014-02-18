<?php

require_once __DIR__ . '/../../vendor/autoload.php';

$memoryStorage = new \SimplePhoto\Storage\MemoryStorage();
$memoryDataStore = new \SimplePhoto\DataStore\MemoryDataStore();

$storageManager = new \SimplePhoto\StorageManager();
$storageManager->add('memory', $memoryStorage);
$storageManager->setDefault('memory');

$simplePhoto = new \SimplePhoto\SimplePhoto($storageManager, $memoryDataStore);

// Upload
$id = $simplePhoto->upload(new \SimplePhoto\Source\UrlSource('http://localhost/cdn/images/728X90.jpg'));

var_dump($simplePhoto->get($id));
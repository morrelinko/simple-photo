<?php

/** @var $simplePhoto SimplePhoto\SimplePhoto */
$simplePhoto = require 'setup.php';

/** @var $dataStore SimplePhoto\DataStore\SqliteDataStore */
$dataStore = $simplePhoto->getDataStore();

$statement = $dataStore->getConnection()->prepare('SELECT * FROM photo');
$statement->execute();

// List all photos in the Data Store
foreach ($statement->fetchAll(PDO::FETCH_ASSOC) as $photo) {
    var_dump($simplePhoto->get($photo['photo_id']));
}

// Get a photo, resize + rotate it...
$resizePhoto = $simplePhoto->get(1, array(
    'transform' => array(
        'resize' => array(100, 100),
        'rotate' => array(180)
    )
));

if ($resizePhoto) {
    echo '<img src="' . $resizePhoto->url() . '" />';
}

// var_dump($resizePhoto);
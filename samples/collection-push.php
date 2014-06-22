<?php

use SimplePhoto\PhotoResult;

/** @var $simplePhoto SimplePhoto\SimplePhoto */
$simplePhoto = require 'setup.php';

// 1. Photo Collections

$photos = $simplePhoto->collection(array(3, 2, 1, 4, 5), array(
    'fallback' => 'not_found.png'
));

// Get photo for id #3
var_dump($photos->get(0));

// Get photo for id #2
var_dump($photos->get(1));

// Get photo for id #5
var_dump($photos->get(4));

// Filter the $photos collection and return a new collection containing only
// photos that are stored in the 'local' storage
$localPhotos = $photos->filter(function (PhotoResult $photo) {
    return $photo->storage() == 'local';
});

// var_dump($localPhotos);

// 2. Pushing Photo Result into an existing array

// Sample Data of a user with photo_id (profile photo) and cover_photo_id (profile cover photo)
$data = array(
    'user_id' => 4,
    'username' => 'morrelinko',
    'photo_id' => 1,
    'cover_photo_id' => 2
);

$simplePhoto->push($data, array('photo_id', 'cover_photo_id'));

// Uncomment next line to see output
// var_dump($data);

// Sample Data 2
$data = array(
    'user_id' => 4,
    'username' => 'morrelinko',
    'photo_id' => 1,
);

// By convention, simple photo looks for 'photo_id' in the array if none is specified
$simplePhoto->push($data);

// var_dump($data);

// Sample Data (Array List)
$list = array(
    array(
        'user_id' => 4,
        'username' => 'morrelinko',
        'photo_id' => 1,
    ),
    array(
        'user_id' => 4,
        'username' => 'morrelinko',
        'photo_id' => 1,
    ),
    array(
        'user_id' => 4,
        'username' => 'morrelinko',
        'photo_id' => 1,
    )
);

// $simplePhoto->push($list, array('photo_id'));
// var_dump($data);

// Manually pushing specific data
$data = array(
    'user_id' => 4,
    'username' => 'morrelinko',
    'photo_id' => 1,
    'cover_photo_id' => 2
);

$simplePhoto->push($data, array('photo_id', 'cover_photo_id'), function (&$item, PhotoResult $photo, $key, $find) {
    if ($find == 'photo_id') {
        // Adds an element to the array 'photo_url' => 'http://xxxxxxxxxxxx'
        $item['photo_url'] = $photo->url();
    } else if ($find == 'cover_photo_id') {
        // Adds an element to the array 'cover_photo_url' => 'http://xxxxxxxxxxxx'
        $item['cover_photo_url'] = $photo->url();
    }
});

// $data then becomes
$data = array(
    'user_id' => 4,
    'username' => 'morrelinko',
    'photo_id' => 1,
    'cover_photo_id' => 2,
    'photo_url' => 'http://xxxxxxxxxxxx',
    'cover_photo_url' => 'http://xxxxxxxxxxxx'
);


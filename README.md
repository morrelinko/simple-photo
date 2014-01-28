SimplePhoto
---------------------
Photo uploading and management made easy

[![Build Status](https://travis-ci.org/morrelinko/simple-photo.png?branch=master)](https://travis-ci.org/morrelinko/simple-photo)

## Installation

Through Composer

```json
{
    "require": {
        "morrelinko/simple-photo": "2.*"
    }
}
```

## Uploading Photo

```php
$photoId = $simplePhoto->uploadFromPhpFileUpload($_FILES["image"]);
// Or
$photoId = $simplePhoto->uploadFromFilePath("/path/to/photo.png");
```

## Retrieving Photo

```php
$photo = $simplePhoto->get($photoId);

$photo->url();
$photo->path();
$photo->fileMime();
$photo->fileExtension();
```

## Setup

SimplePhoto requires a...
1. Storage Manager: For Storing & Managing registered storage adapters.
2. Data Store: (Such as database) For persisting photo data.

```php
use SimplePhoto\Storage\LocalStorage;
use SimplePhoto\StorageManager;
use SimplePhoto\DataStore\SqliteDataStore;
use SimplePhoto\SimplePhoto;

// Create a local storage adapter
$localStorage = new LocalStorage('/path/to/project/root/', 'photos');

// Create a storage manager
$storageManager = new StorageManager();

// Adds one or more registered storage adapters
$storageManager->add('local', $localStorage);

// Create Data Store
$dataStore = new SqliteDataStore(['database' => 'photo_app.db']);

// Create Our Simple Photo Object
$simplePhoto = new SimplePhoto($storageManager, $dataStore);
```

## Get photos (+Transformation)

If you want to get a re-sized photo, use the "transform" options of the second argument

```php
$photo = $simplePhoto->get($photoId, [
	'transform' => [
		'size' => [200, 200]
	]
]);
```

All transformation options available...
```php
[
    'size' => [100, 100]
    'rotate' => [45]
]
```

## Collection of photos

```php
$photos = $simplePhoto->collection([2, 23, 15]);

$photos->get(0); // gets photo '2'
$photos->get(1); // gets photo '23'

```

PhotoCollection come with a handful of methods for manipulating its items

```php
// Creates a collection of photos
$photos = $simplePhoto->collection([2, 23, 15, 34, 21, 1, 64, 324]);

// Gets all as array
$allPhotos = $photos->all();

// Uses filter() method.
// This example creates a new photo collection containing only photos in 'local' storage
$localPhotos = $photos->filter(function($photo) {
    return $photo->storage() == 'local';
});

var_dump($localPhotos);
```

## Push (in english 'push photo result into')

```php
// Probably gotten from a db
$users = [
    ['user_id' => 1, 'name' => 'John Doe', 'photo_id' => 2],
    ['user_id' => 2, 'name' => 'Mary Alice', 'photo_id' => 5]
];

$simplePhoto->push($users, array('photo_id'));

var_dump($users);

// Sample Output:
[
    ['user_id' => 1, 'name' => 'John Doe', 'photo_id' => 2, 'photo' => (Object SimplePhoto\PhotoResult)],
    ['user_id' => 2, 'name' => 'Mary Alice', 'photo_id' => 5, 'photo' => (Object SimplePhoto\PhotoResult)]
];

```

If you would like complete control on what is pushed to the array from the photo result,

you specify a callback as third argument to `push()`

```php

$simplePhoto->push($users, array('photo_id'), function(&$item, $photo, $index, $name) {
    $item['photo_url'] = $photo->url();
});

var_dump($users);

// Sample Output:
[
    ['user_id' => 1, 'name' => 'John Doe', 'photo_id' => 2, 'photo_url' => 'http://example.com/files/2014/xxxxx.png'],
    ['user_id' => 2, 'name' => 'Mary Alice', 'photo_id' => 5, 'photo_url' => 'http://example.com/files/2014/xxxxx.png']
];

```

## Credits

This code is principally developed and maintained by [Laju Morrison] (https://github.com/morrelinko)

## Licence

The MIT License (MIT). Please see [License File](https://github.com/morrelinko/simple-photo/blob/master/LICENSE) for more information.

Supported by http://contactlyapp.com
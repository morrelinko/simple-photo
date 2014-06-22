SimplePhoto
---------------------

Handling photos in your web application has never been so *simple*.

[![Build Status](https://travis-ci.org/morrelinko/simple-photo.png?branch=master)](https://travis-ci.org/morrelinko/simple-photo)

## Installation

Through [Composer](http://getcomposer.org)

```json
{
    "require": {
        "morrelinko/simple-photo": "0.*"
    }
}
```

Create the database using the schema below for the data store you will be using

* Sqlite DataStore: [Sql Query](https://github.com/morrelinko/simple-photo/blob/develop/schema/sqlite.sql)
* MySql DataStore: [Sql Query](https://github.com/morrelinko/simple-photo/blob/develop/schema/mysql.sql)

## Uploading Photo

```php
$photoId = $simplePhoto->uploadFromPhpFileUpload($_FILES["image"]);
// Or
$photoId = $simplePhoto->uploadFromFilePath("/path/to/photo.png");
```

With support for accepting uploads from different sources.

```php
$photoId = $simplePhoto->upload(new YourUploadSource($imageData));
```

The two upload methods shown above actually are aliases/shortcuts for doing this

```php
$photoId = $simplePhoto->upload(new PhpFileUploadSource($_FILES["image"]));
// Or
$photoId = $simplePhoto->upload(new FilePathSource("/path/to/photo.png"));
```

## Retrieving Photo

```php
$photo = $simplePhoto->get($photoId);

$photo->id();
$photo->url();
$photo->path();
$photo->fileMime();
$photo->storage();
$photo->fileSize();
$photo->fileExtension();
$photo->filePath();
$photo->createdAt();
...
```

## Setup

SimplePhoto requires...

* Storage Manager: For Storing & Managing registered storage adapters.
* Data Store: Database for persisting information about a photo.

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

The default transformation options available...

```php
[
    'size' => [$width, $height]
    'rotate' => [$angle, ($background)]
]
```

[You could implement your own transformer and add more transformation options](http://simplephoto.morrelinko.com/docs/transformer)

Arguments in parenthesis are optional


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

## Push

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

## Supported Photo Sources

* [FilePath Source](https://github.com/morrelinko/simple-photo/blob/develop/src/Source/FilePathSource.php)
* [PhpFileUpload Source](https://github.com/morrelinko/simple-photo/blob/develop/src/Source/PhpFileUploadSource.php)
* [Url Source](https://github.com/morrelinko/simple-photo/blob/develop/src/Source/UrlSource.php)
* [SymfonyFileUploadSource](https://github.com/morrelinko/simple-photo/blob/develop/src/Source/SymfonyFileUploadSource.php)

## Supported Data Stores

* [MySql Data Store](https://github.com/morrelinko/simple-photo/blob/develop/src/DataStore/MySqlDataStore.php)
* [Sqlite Data Store](https://github.com/morrelinko/simple-photo/blob/develop/src/DataStore/SqliteDataStore.php)
* [Memory Data Store](https://github.com/morrelinko/simple-photo/blob/develop/src/DataStore/MemoryDataStore.php)

## Supported Storage

* [Local Storage](https://github.com/morrelinko/simple-photo/blob/develop/src/Storage/LocalStorage.php)
* [Remote Host Storage](https://github.com/morrelinko/simple-photo/blob/develop/src/Storage/RemoteHostStorage.php)
* [Memory Storage](https://github.com/morrelinko/simple-photo/blob/develop/src/Storage/MemoryStorage.php)
* [AwsS3 Storage](https://github.com/morrelinko/simple-photo/blob/develop/src/Storage/AwsS3Storage.php)

## TODO

* Add MongoDB Data Store

## Credits

This code is principally developed and maintained by [Laju Morrison] (https://github.com/morrelinko)

## Licence

The MIT License (MIT). Please see [License File](https://github.com/morrelinko/simple-photo/blob/master/LICENSE) for more information.

Supported by http://contactlyapp.com
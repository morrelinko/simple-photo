SimplePhoto
---------------------
Photo uploading and management made easy

[![Build Status](https://travis-ci.org/morrelinko/simple-photo.png?branch=master)](https://travis-ci.org/morrelinko/simple-photo)

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

## Retrieving photos (+Transformation)

If you want to get a re-sized photo, use the "transform" options of the second argument

```php
$photo = $simplePhoto->get($photoId, [
	"transform" => [
		"size" => [200, 200]
	]
]);
```

Other transformation options will be made available...

## Credits

This code is principally developed and maintained by [Laju Morrison] (https://github.com/morrelinko)

## Licence

The MIT License (MIT). Please see [License File](https://github.com/morrelinko/simple-photo/blob/master/LICENSE) for more information.

Supported by http://contactlyapp.com
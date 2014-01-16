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
	$simplePhoto->getPhoto($photoId);
```

## Setup

```php
	// Coming soon
```

## Retrieving photos (+Transformation)

If you want to get a re-sized photo, use the "transform" options of the second argument

```php
	$photo = $simplePhoto->get($photoId, array(
		"transform" => array(
			"size" => array(200, 200)
		)
	));
```

## Licence

The MIT License (MIT). Please see [License File](https://github.com/morrelinko/simple-photo/blob/master/LICENSE) for more information.

Supported by http://contactlyapp.com
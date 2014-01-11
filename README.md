SimplePhoto
---------------------
Photo uploading and management made easy

## Uploading Photo
	<?php
	$photoId = $simplePhoto->uploadFromPhpFileUpload($_FILES["image"]);
	// Or
	$photoId = $simplePhoto->uploadFromFilePath("/path/to/photo.png");

## Retrieving Photo
	<?php
	$simplePhoto->getPhoto($photoId);

## Setup

	<?php
	// Coming soon

## Retrieving photos (+Transformation)

If you want to get a re-sized photo, use the "transform" options of the second argument

	<?php
	$photo = $simplePhoto->get($photoId, array(
		"transform" => array(
			"size" => array(200, 200)
		)
	));

## Licence

Faker is released under the MIT Licence. See the bundled LICENSE file for details.

== Supported by http://contactlyapp.com ==
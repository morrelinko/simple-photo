<?php

use SimplePhoto\Source\UrlSource;

/** @var $simplePhoto SimplePhoto\SimplePhoto */
$simplePhoto = require 'setup.php';

if (isset($_POST['image'])) {
    if ($photoId = $simplePhoto->upload(new UrlSource($_POST['image']))) {
        var_dump($simplePhoto->get($photoId));
    } else {
        echo "Error uploading image";
    }
}

?>

<form method="post">
    <label>
        <input type="text" name="image">
    </label>

    <br />
    <button name="upload">Upload Image</button>

</form>
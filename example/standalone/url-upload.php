<?php

use SimplePhoto\Source\UrlSource;

/** @var $simplePhoto SimplePhoto\SimplePhoto */
$simplePhoto = require 'setup.php';

if (isset($_POST['image'])) {
    $photoId = $simplePhoto->upload(new UrlSource($_POST['image']));

    var_dump($simplePhoto->get($photoId));
}

?>

<form method="post">
    <label>
        <input type="text" name="image">
    </label>

    <br />
    <button name="upload">Upload Image</button>

</form>
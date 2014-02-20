<?php

/**
 * Upload a photo allowing you to select which storage you would like to save the image
 */

$simplePhoto = require 'setup.php';

// Test File Upload (From php file upload)
if (isset($_POST['upload']) && isset($_FILES['image'])) {

    $photoId = $simplePhoto->uploadFromPhpFileUpload($_FILES['image'], array(
        'storageName' => isset($_POST['storage']) ? $_POST['storage'] : 'local',
        'transform' => array(
            'size' => array(100, 100) // Resize photo to 100x100 [Note: the 100x100 photo becomes the original photo]
        )
    ));

    echo '<img src="' . $simplePhoto->get($photoId)->url() . '" />';
}

?>

<form method="post" enctype="multipart/form-data">
    <input type="file" name="image">

    <br />
    Select Storage: <br />
    <label for="storage">
        <select name="storage">
            <?php foreach ($storageManager->getAll() as $name => $storage): ?>
                <option value="<?php echo $name; ?>">
                    <?php echo $name; ?>
                </option>
            <?php endforeach; ?>
        </select>
    </label>

    <button name="upload">Upload Image</button>

</form>
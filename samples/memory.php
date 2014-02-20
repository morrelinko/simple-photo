<?php

require_once __DIR__ . '/../vendor/autoload.php';

$memoryStorage = new \SimplePhoto\Storage\MemoryStorage();
$memoryDataStore = new \SimplePhoto\DataStore\MemoryDataStore();

$storageManager = new \SimplePhoto\StorageManager();
$storageManager->add('memory', $memoryStorage);
$storageManager->setDefault('memory');

$simplePhoto = new \SimplePhoto\SimplePhoto($storageManager, $memoryDataStore);

// Upload
//
$id = $simplePhoto->upload(new \SimplePhoto\Source\UrlSource(
    'http://localhost/cdn/images/funny/tumblr_msb21x6CsJ1rrqglzo1_500.gif'
));

var_dump($id);

$photo = $simplePhoto->get($id);
var_dump($photo);
?>

<?php if ($photo): ?>
    <img src="<?php echo $photo->url(); ?>" />
<?php endif; ?>
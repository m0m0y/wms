<?php

header("Content-type: application/json");

$now = new DateTime();
$cleaned = $purged = $saved = 0;
$files = glob("../order_files/*.{jpg,png,gif,JPG,jpeg}", GLOB_BRACE);

for ($i = 0; $i < count($files); $i++){
    $date = date('Y-m-d h:i:s', filemtime($files[$i]));
    $dateToTest = new DateTime($date);
    if ($dateToTest->diff($now)->days > 30) {
        unlink($files[$i]);
        $cleaned++;
        $purged += filesize($files[$i]);
        clearstatcache();
    }
}

if ($cleaned) {
    $byte = floor(log($purged) / log(1024));
    $sizes = array('B', 'KB', 'MB', 'GB', 'TB', 'PB', 'EB', 'ZB', 'YB');
    $saved = sprintf('%.02F', $purged / pow(1024, $byte)) * 1 . ' ' . $sizes[$byte];
}

$response = array(
    "Total image deleted" => $cleaned,
    "Total space saved" => $saved
);

echo json_encode($response);

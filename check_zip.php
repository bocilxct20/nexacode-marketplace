<?php
$zip = new ZipArchive;
$res = $zip->open('packages/flux-pro-2.11.1.zip');
if ($res === TRUE) {
    echo "Files in zip:\n";
    for($i = 0; $i < $zip->numFiles; $i++) {
        echo $zip->getNameIndex($i) . "\n";
    }
    $zip->close();
} else {
    echo "Failed to open zip, error code: " . $res . "\n";
}

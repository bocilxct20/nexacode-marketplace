<?php
$path = 'C:/Users/dani/Documents/nexacode/marketplace v2/packages/flux-pro-2.11.1.zip';
$zip = new ZipArchive();
if ($zip->open($path) === TRUE) {
    echo "Files in zip ($path):\n";
    for($i = 0; $i < $zip->numFiles; $i++) {
        echo $zip->getNameIndex($i) . "\n";
    }
    $zip->close();
} else {
    echo "Failed to open zip $path\n";
}

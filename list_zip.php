<?php
$zipFile = 'packages/flux-pro-2.11.1.zip';
if (!file_exists($zipFile)) {
    die("File not found: $zipFile\n");
}
$zip = new ZipArchive;
if ($zip->open($zipFile) === TRUE) {
    echo "Files in $zipFile:\n";
    for($i = 0; $i < $zip->numFiles; $i++) {
        echo $zip->getNameIndex($i) . "\n";
    }
    $zip->close();
} else {
    echo "Failed to open zip file\n";
}

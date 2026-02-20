<?php
$zip = new ZipArchive;
if ($zip->open('packages/flux-pro-2.11.1.zip') === TRUE) {
    echo $zip->getFromName('composer.json');
    $zip->close();
}

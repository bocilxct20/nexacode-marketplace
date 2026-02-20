<?php
$zip = new ZipArchive;
if ($zip->open('packages/flux-pro-2.11.1.zip') === TRUE) {
    file_put_contents('zip_composer.json', $zip->getFromName('composer.json'));
    $zip->close();
}

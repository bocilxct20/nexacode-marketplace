<?php
$zip = new ZipArchive;
$res = $zip->open('packages/flux-pro-2.11.1.zip');
if ($res === TRUE) {
    echo $zip->getFromName('composer.json');
    $zip->close();
}

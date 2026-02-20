<?php
$zip = new ZipArchive;
$res = $zip->open('packages/flux-pro-2.11.1.zip');
if ($res === TRUE) {
    file_put_contents('temp_flux_composer.json', $zip->getFromName('composer.json'));
    $zip->close();
    echo "Extracted to temp_flux_composer.json";
}

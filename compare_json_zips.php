<?php
function getComposerJson($path) {
    if (!file_exists($path)) return "File not found: $path\n";
    $zip = new ZipArchive();
    if ($zip->open($path) === TRUE) {
        $json = $zip->getFromName('composer.json');
        $zip->close();
        return $json;
    }
    return "Failed to open $path\n";
}

echo "COMPOSER.JSON from marketplace v2:\n";
echo getComposerJson('C:/Users/dani/Documents/nexacode/marketplace v2/packages/flux-pro-2.11.1.zip');
echo "\n\nCOMPOSER.JSON from our new zip:\n";
echo getComposerJson('c:/Users/dani/Documents/nexacode/marketplace/packages/flux-pro-2.11.1.zip');

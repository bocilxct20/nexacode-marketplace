<?php
function listZip($path) {
    if (!file_exists($path)) return "File not found: $path\n";
    $zip = new ZipArchive();
    if ($zip->open($path) === TRUE) {
        $out = "Files in $path:\n";
        for($i = 0; $i < min(20, $zip->numFiles); $i++) {
            $out .= $zip->getNameIndex($i) . "\n";
        }
        if ($zip->numFiles > 20) $out .= "... (" . ($zip->numFiles - 20) . " more files)\n";
        $zip->close();
        return $out;
    }
    return "Failed to open $path\n";
}

echo listZip('C:/Users/dani/Documents/nexacode/marketplace v2/packages/flux-pro-2.11.1.zip');
echo "\n";
echo listZip('c:/Users/dani/Documents/nexacode/marketplace/packages/flux-pro-2.11.1.zip');

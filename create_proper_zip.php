<?php
$sourceDir = 'C:/Users/dani/Documents/Album Ruang Rindu/FluxPro - v2.11.1/flux-pro/';
$destFile = 'c:/Users/dani/Documents/nexacode/marketplace/packages/flux-pro-2.11.1.zip';

if (!is_dir('packages')) mkdir('packages');

$zip = new ZipArchive();
if ($zip->open($destFile, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== TRUE) {
    die("Failed to create zip\n");
}

$files = [
    'composer.json',
    'LICENSE.md',
];

foreach ($files as $file) {
    if (file_exists($sourceDir . $file)) {
        $zip->addFile($sourceDir . $file, $file);
    }
}

function addDirToZip($dir, $baseDir, $zip) {
    $files = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator($dir),
        RecursiveIteratorIterator::LEAVES_ONLY
    );

    foreach ($files as $name => $file) {
        if (!$file->isDir()) {
            $filePath = $file->getRealPath();
            $relativePath = substr($filePath, strlen($baseDir));
            $relativePath = str_replace('\\', '/', $relativePath); // Normalize to forward slashes
            $zip->addFile($filePath, $relativePath);
        }
    }
}

addDirToZip($sourceDir . 'src', $sourceDir, $zip);
addDirToZip($sourceDir . 'dist', $sourceDir, $zip);
addDirToZip($sourceDir . 'stubs', $sourceDir, $zip);

$zip->close();
echo "Zip created successfully at $destFile\n";

function listDetailedZip($path) {
    if (!file_exists($path)) return "File not found: $path\n";
    $zip = new ZipArchive();
    if ($zip->open($path) === TRUE) {
        $out = "Details for $path (Total files: " . $zip->numFiles . "):\n";
        $hasSrc = false;
        $hasDist = false;
        $hasStubs = false;
        for($i = 0; $i < $zip->numFiles; $i++) {
            $name = $zip->getNameIndex($i);
            if (strpos($name, 'src/') === 0) $hasSrc = true;
            if (strpos($name, 'dist/') === 0) $hasDist = true;
            if (strpos($name, 'stubs/') === 0) $hasStubs = true;
        }
        $out .= "Has src: " . ($hasSrc ? 'Yes' : 'No') . "\n";
        $out .= "Has dist: " . ($hasDist ? 'Yes' : 'No') . "\n";
        $out .= "Has stubs: " . ($hasStubs ? 'Yes' : 'No') . "\n";
        $zip->close();
        return $out;
    }
    return "Failed to open $path\n";
}

echo listDetailedZip('C:/Users/dani/Documents/nexacode/marketplace v2/packages/flux-pro-2.11.1.zip');
echo listDetailedZip($destFile);

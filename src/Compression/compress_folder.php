<?php
// require_once '../helpers.php';
// require_once 'scan_directory.php';

/**
 * Compress a folder
 * 
 * @param string $folder_path
 */
function compress_folder(string $folder_path = '.git')
{

  // $zipPath       = __DIR__ .  "/$folder_path" . '.zip'; // OLD 
  $zipPath = getcwd() . "/$folder_path" . '.zip'; // In order to be able to use it globally
  // inspect($zipPath); // TEST:

  $folderToZip = $folder_path;

  // Check if the folder exists 
  $folderToZip = realpath($folder_path);
  // inspect($folderToZip);

  if ($folderToZip === false || !is_dir($folderToZip)) {
    echo "❌ The folder '$folder_path' does not exist or is not a valid directory.\n";
    return;
  }

  // ─────────────────────────────────────────
  // STEP 1: Zip the folder
  // ─────────────────────────────────────────
  $zip = new ZipArchive();

  if ($zip->open($zipPath, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true) {
    die("Cannot create zip file \nExiting...");
  }

  $files = new RecursiveIteratorIterator(
    new RecursiveDirectoryIterator($folderToZip, FilesystemIterator::SKIP_DOTS)
  );

  foreach ($files as $file) {
    if (!$file->isDir()) {
      $localPath = substr($file->getPathname(), strlen($folderToZip) + 1);
      $zip->addFile($file->getPathname(), $localPath);
    }
  }
  $zip->close();

  echo "✅ Folder compressed to: $zipPath\n";
  return $zipPath;
}

// TEST: 
// compress_folder();

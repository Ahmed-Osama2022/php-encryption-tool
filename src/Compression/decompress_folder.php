<?php
// require_once 'helpers.php';

/**
 * Decompress a zip file
 * 
 * @param string $zip_path
 * 
 * @return string|false
 */
function decompress_folder(string $zip_path): string|false
{
  if (!file_exists($zip_path)) {
    echo "❌ Zip file not found: '$zip_path'.\n";
    return false;
  }

  $zip = new ZipArchive();
  if ($zip->open($zip_path) !== true) {
    echo "❌ Cannot open zip file: '$zip_path'.\n";
    return false;
  }

  $extractPath = preg_replace('/\.zip$/', '', $zip_path);

  if (!is_dir($extractPath)) {
    mkdir($extractPath, 0755, true);
  }

  $zip->extractTo($extractPath);
  $zip->close();
  unlink($zip_path);

  echo "✅ Extracted to: $extractPath\n";
  return $extractPath;
}

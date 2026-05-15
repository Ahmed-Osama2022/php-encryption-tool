<?php

/**
 * Inspect a variable
 * 
 * @param mixed $data The variable to inspect
 * 
 * @return void
 */
function inspect($data)
{
  var_dump($data);
  die();
}

/**
 * Choose an encrypted file
 * 
 * @return string|false
 */
function choose_enc_file(): string|false
{
  // $files = glob(__DIR__ . '/*.enc');
  $files = glob(getcwd() . '/*.enc'); // To use it globally

  if (empty($files)) {
    echo "❌ No encrypted files found in current directory.\n";
    echo "👋 Exiting...\n";
    return false;
  }

  echo "\nAvailable encrypted files:\n";
  foreach ($files as $i => $file) {
    echo ($i + 1) . ". " . basename($file) . "\n";
  }

  echo "Choose a file: ";
  $choice = (int) trim(fgets(STDIN));

  if ($choice < 1 || $choice > count($files)) {
    echo "❌ Invalid choice.\n";
    return false;
  }

  return $files[$choice - 1];
}
/**
 * Ask the user if they want to delete the original file
 * 
 * @param string $file_path The path to the file
 * 
 * @return void
 */
function ask_delete(string $file_path): void
{
  echo "❓ Do you want to delete the original '$file_path'? (Y/y for Yes, N/n for No, default is No): ";
  $choice = strtolower(trim(fgets(STDIN)));

  if ($choice === 'y') {
    try {
      if (is_dir($file_path)) {
        $files = new RecursiveIteratorIterator(
          new RecursiveDirectoryIterator($file_path, FilesystemIterator::SKIP_DOTS),
          RecursiveIteratorIterator::CHILD_FIRST
        );
        foreach ($files as $file) {
          $file->isDir() ? rmdir($file->getPathname()) : unlink($file->getPathname());
        }
        rmdir($file_path);
      } else {
        unlink($file_path);
      }
      echo "🗑️ Deleted: $file_path\n";
    } catch (Exception $e) {
      echo "⚠️ Could not delete '$file_path': " . $e->getMessage() . "\n";
    }
  } else {
    echo "✅ Kept original: $file_path\n";
  }
}

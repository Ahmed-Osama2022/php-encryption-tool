#!/usr/bin/env php
<?php
// ini_set('memory_limit', '-1');

require_once 'vendor/autoload.php';
// require_once 'helpers.php';
// require_once 'choose_folder.php';
// require_once 'compress_folder.php';
// require_once 'decompress_folder.php';
// require_once 'encrypt_folder.php';
// require_once 'decrypt_folder.php';
// require_once 'ask_delete.php';

// define('ROOT_DIR', __DIR__);
// inspect(ROOT_DIR);
// The main app function 
function app(): void
{
  echo "\n=== 👋 Welcome to folder Encrypt/Decrypt Tool 👋 ===\n";
  echo "1. 🔒🔐 Encrypt a folder 🔐🔒\n";
  echo "2. 🔓 Decrypt a folder 🔓\n";
  echo "3. 👋 Exit\n";
  echo "Choose an option (1, 2, 3): ";
  $choice = trim(fgets(STDIN));

  if ($choice == '1') {
    echo "\n=========================\n";
    echo "🔒🔐 === Encryption === 🔐🔒\n";
    echo "=========================\n";

    $folder    = choose_folder();
    echo "📂 Selected folder: ($folder)\n";
    echo "Enter passphrase: ";
    $keyphrase = trim(fgets(STDIN));

    $zipPath = compress_folder($folder);
    if ($zipPath !== false) {
      encrypt_folder($zipPath, $keyphrase);
      // ask_delete($folder); // delete original folder?
      ask_delete(getcwd() . DIRECTORY_SEPARATOR . $folder); // delete original folder? (Better approach)
    }
  } elseif ($choice == '2') {
    echo "\n=========================\n";
    echo "🔓 === Decryption === 🔓\n";
    echo "=========================\n";

    $encFile = choose_enc_file();
    if ($encFile === false) {
      return;
    }

    echo "🔐 Selected file: ($encFile)\n";
    echo "Enter passphrase: ";
    $keyphrase = trim(fgets(STDIN));

    $zipPath = decrypt_folder($encFile, $keyphrase);
    if ($zipPath !== false) {
      decompress_folder($zipPath);
      ask_delete($encFile); // delete the .enc file?
      // ask_delete(getcwd() . DIRECTORY_SEPARATOR . $encFile); // delete the .enc file?
    }
  } elseif ($choice == '3') {
    echo "👋 Exiting...\n";
    exit();
  } else {
    echo "❌ Invalid choice. Please try again.\n";
    sleep(1);
    app();
  }
}

app();

// die();

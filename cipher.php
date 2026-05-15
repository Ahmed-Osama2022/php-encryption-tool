
<?php

/**
 * Let's define the main 3 functions
 * 1- Encrption core (Algorithm and all the stuff related) 
 * 2- main() || app()
 * 3- encrypt_folder()
 * 4- decrypt_folder()
 * 5- ask_delete()
 */

function choose_folder()
{
  // 1- Get the working directory
  $current_folder = getcwd();

  // echo $current_folder;
  // die();

  // 2- Scan the current directory for folders
  $files = scandir($current_folder);
  $files = array_diff($files, array('..', '.'));
  // $files = scandir($dircurrent_folder, SCANDIR_SORT_DESCENDING);

  // 3- Check if the directory contains a direcotoires
  $directories = array_filter($files, function ($file) {
    return is_dir($file);
  });


  if (empty($directories)) {
    echo "❌ No folders found in the current directory. ❌\n";
    echo "\n=========================================================== \n";
    echo "==================== Restart the App ====================== \n";
    echo "=========================================================== \n";
    sleep(1);
    app();
  }

  // 4- List the directories

  echo "\n📂 Available folders: ";

  echo "Choose a folder:\n";
  foreach ($directories as $key => $directory) {
    echo $key + 1 . ". " . $directory . "\n";
  }

  // print_r($files);
  // die();
}


// The main app function 
function app()
{
  // 1- Let the user choose what did he want to do? (Encrypt | Decrypt | Exit)
  echo "\n=== 👋 Welcome to folder Encrypt/Decrypt Tool 👋 ===\n";
  // echo "Choose what you want to do:\n";
  echo "1. Encrypt a folder\n";
  echo "2. Decrypt a folder\n";
  echo "3. Exit\n";
  echo "Choose an option (1, 2, 3): \n";
  $choice = fgets(STDIN);


  if ($choice == '1') {
    $folder = choose_folder();
    echo "Enter passphrase: ";

    $keyphrase = fgets(STDIN);
    // encrypt_folder(folder, keyphrase);
  } elseif ($choice == '2') {
    $folder = choose_folder();
    echo "Enter passphrase: ";

    // $keyphrase = fgets(STDIN);
    // decrypt_folder(folder, keyphrase);
  } elseif ($choice == '3') {
    echo "👋 Exiting...\n";
    exit();
  } else {
    echo "❌ Invalid choice. Please try again. ❌\n";
    sleep(1);
    app();
  }
}

app();

die();
// Get the user input
echo "Enter your folder name you want to compress it: ";
$input = fgets(STDIN);


// $zipPath       = '/tmp/archive.zip';
// $zipPath       = '/tmp/archive.zip';
$encryptedPath = '/tmp/archive.enc';
$restoredPath  = '/tmp/restored.zip';
$folderToZip   = '/path/to/your/folder';

// ─────────────────────────────────────────
// STEP 1: Zip the folder
// ─────────────────────────────────────────
$zip = new ZipArchive();
$zip->open($zipPath, ZipArchive::CREATE | ZipArchive::OVERWRITE);

$files = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($folderToZip));
foreach ($files as $file) {
  if (!$file->isDir()) {
    $zip->addFile($file->getPathname(), $file->getFilename());
  }
}
$zip->close();

// ─────────────────────────────────────────
// STEP 2: Encrypt the zip
// ─────────────────────────────────────────
$key = random_bytes(32);
$iv  = random_bytes(16);

$plaintext  = file_get_contents($zipPath);
$ciphertext = openssl_encrypt($plaintext, 'AES-256-CBC', $key, OPENSSL_RAW_DATA, $iv);

file_put_contents($encryptedPath, $iv . $ciphertext);
unlink($zipPath); // remove unencrypted zip

// ─────────────────────────────────────────
// STEP 3: Decrypt
// ─────────────────────────────────────────
$raw        = file_get_contents($encryptedPath);
$iv         = substr($raw, 0, 16);
$ciphertext = substr($raw, 16);

$plaintext = openssl_decrypt($ciphertext, 'AES-256-CBC', $key, OPENSSL_RAW_DATA, $iv);

file_put_contents($restoredPath, $plaintext);

?>
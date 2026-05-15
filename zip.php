<?php
require_once 'helpers.php';


function compress_folder(?string $folder_path = '.gjflkkjdskjdkls')
{


  $zipPath       = __DIR__ .  "/$folder_path" . '.zip';
  // inspect($zipPath);
  // $zipPath       = '/tmp/archive.zip';
  $encryptedPath = '/tmp/archive.enc';
  $restoredPath  = '/tmp/restored.zip';
  // $folderToZip   = '/path/to/your/folder';

  $folderToZip = $folder_path;

  // ─────────────────────────────────────────
  // STEP 1: Zip the folder
  // ─────────────────────────────────────────
  $zip = new ZipArchive();
  // $zip->open($zipPath, ZipArchive::CREATE | ZipArchive::OVERWRITE);


  if ($zip->open($zipPath, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true) {
    die("Cannot create zip file");
  }

  // $files = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($folderToZip));
  // foreach ($files as $file) {
  //   if (!$file->isDir()) {
  //     $zip->addFile($file->getPathname(), $file->getFilename());
  //   }
  // }
  // $zip->close();

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



  // // ─────────────────────────────────────────
  // // STEP 2: Encrypt the zip
  // // ─────────────────────────────────────────
  // $key = random_bytes(32);
  // $iv  = random_bytes(16);

  // $plaintext  = file_get_contents($zipPath);
  // $ciphertext = openssl_encrypt($plaintext, 'AES-256-CBC', $key, OPENSSL_RAW_DATA, $iv);

  // file_put_contents($encryptedPath, $iv . $ciphertext);
  // unlink($zipPath); // remove unencrypted zip

  // // ─────────────────────────────────────────
  // // STEP 3: Decrypt
  // // ─────────────────────────────────────────
  // $raw        = file_get_contents($encryptedPath);
  // $iv         = substr($raw, 0, 16);
  // $ciphertext = substr($raw, 16);

  // $plaintext = openssl_decrypt($ciphertext, 'AES-256-CBC', $key, OPENSSL_RAW_DATA, $iv);

  // file_put_contents($restoredPath, $plaintext);
}


// TEST: 
compress_folder();

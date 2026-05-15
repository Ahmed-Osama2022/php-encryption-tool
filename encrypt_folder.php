<?php
require_once 'helpers.php';




function encrypt_folder(string $folder_path, string $passphrase)
{

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

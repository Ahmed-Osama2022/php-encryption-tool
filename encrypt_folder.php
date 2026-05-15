<?php
require_once 'helpers.php';

function encrypt_folder(string $zip_path, string $passphrase): string|false
{
  if (!file_exists($zip_path)) {
    echo "❌ Zip file not found: '$zip_path'.\n";
    return false;
  }

  $encryptedPath = preg_replace('/\.zip$/', '.enc', $zip_path);

  $salt       = random_bytes(16);
  $key        = hash_pbkdf2('sha256', $passphrase, $salt, 100_000, 32, true);
  $iv         = random_bytes(16);

  $plaintext  = file_get_contents($zip_path);
  $ciphertext = openssl_encrypt($plaintext, 'AES-256-CBC', $key, OPENSSL_RAW_DATA, $iv);

  // File layout: [16 bytes salt][16 bytes IV][ciphertext]
  file_put_contents($encryptedPath, $salt . $iv . $ciphertext);
  unlink($zip_path);

  echo "✅ Encrypted to: $encryptedPath\n";
  return $encryptedPath;
}

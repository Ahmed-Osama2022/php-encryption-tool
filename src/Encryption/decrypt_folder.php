<?php
// require_once 'helpers.php';

function decrypt_folder(string $enc_path, string $passphrase): string|false
{
  if (!file_exists($enc_path)) {
    echo "❌ Encrypted file not found: '$enc_path'.\n";
    return false;
  }

  $raw        = file_get_contents($enc_path);
  $salt       = substr($raw, 0, 16);
  $iv         = substr($raw, 16, 16);
  $ciphertext = substr($raw, 32);

  $key       = hash_pbkdf2('sha256', $passphrase, $salt, 100_000, 32, true);
  $plaintext = openssl_decrypt($ciphertext, 'AES-256-CBC', $key, OPENSSL_RAW_DATA, $iv);

  if ($plaintext === false) {
    echo "❌ Decryption failed. Wrong passphrase?\n";
    return false;
  }

  $zipPath = preg_replace('/\.enc$/', '.zip', $enc_path);
  file_put_contents($zipPath, $plaintext);

  echo "✅ Decrypted zip at: $zipPath\n";
  return $zipPath;
}

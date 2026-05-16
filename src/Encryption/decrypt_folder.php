<?php
// require_once 'helpers.php';

/**
 * Decrypt a zip file
 * 
 * @param string $enc_path
 * @param string $passphrase
 * 
 * @return string|false
 */
function decrypt_folder(string $enc_path, string $passphrase): string|false
{
  if (!file_exists($enc_path)) {
    echo "❌ Encrypted file not found: '$enc_path'.\n";
    return false;
  }

  $in = fopen($enc_path, 'rb');

  // Read header: [16 bytes salt][16 bytes IV]
  $salt = fread($in, 16);
  $iv   = fread($in, 16);

  $key = hash_pbkdf2('sha256', $passphrase, $salt, 100_000, 32, true);

  $zipPath = preg_replace('/\.enc$/', '.zip', $enc_path);
  $out     = fopen($zipPath, 'wb');

  while (!feof($in)) {
    $chunk     = fread($in, 8192);
    $plaintext = openssl_decrypt($chunk, 'AES-256-CTR', $key, OPENSSL_RAW_DATA, $iv);

    if ($plaintext === false) {
      fclose($in);
      fclose($out);
      unlink($zipPath);
      echo "❌ Decryption failed. Wrong passphrase?\n";
      return false;
    }

    fwrite($out, $plaintext);
    $iv = increment_iv($iv);
  }

  fclose($in);
  fclose($out);

  echo "✅ Decrypted zip at: $zipPath\n";
  return $zipPath;
}

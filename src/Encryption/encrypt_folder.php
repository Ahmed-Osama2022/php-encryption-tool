<?php
// require_once 'helpers.php';

/**
 * Encrypt a zip file
 * 
 * @param string $zip_path
 * @param string $passphrase
 * 
 * @return string|false
 */
function encrypt_folder(string $zip_path, string $passphrase): string|false
{
  if (!file_exists($zip_path)) {
    echo "❌ Zip file not found: '$zip_path'.\n";
    return false;
  }

  $encryptedPath = preg_replace('/\.zip$/', '.enc', $zip_path);

  $salt = random_bytes(16);
  $key  = hash_pbkdf2('sha256', $passphrase, $salt, 100_000, 32, true);
  $iv   = random_bytes(16);

  $in  = fopen($zip_path, 'rb');
  $out = fopen($encryptedPath, 'wb');

  // Write header: [16 bytes salt][16 bytes IV]
  fwrite($out, $salt . $iv);

  while (!feof($in)) {
    $chunk      = fread($in, 8192); // 8KB at a time
    $ciphertext = openssl_encrypt($chunk, 'AES-256-CTR', $key, OPENSSL_RAW_DATA, $iv);
    fwrite($out, $ciphertext);

    // Increment IV to keep CTR mode correct across chunks
    $iv = increment_iv($iv);
  }

  fclose($in);
  fclose($out);
  unlink($zip_path);

  echo "✅ Encrypted to: $encryptedPath\n";
  return $encryptedPath;
}

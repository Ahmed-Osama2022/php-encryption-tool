<?php
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

  // Header: [16 bytes salt][16 bytes IV]
  fwrite($out, $salt . $iv);

  $hmacCtx = hash_init('sha256', HASH_HMAC, $key); // incremental HMAC

  while (!feof($in)) {
    $chunk      = fread($in, 8192);
    $ciphertext = openssl_encrypt($chunk, 'AES-256-CTR', $key, OPENSSL_RAW_DATA, $iv);
    fwrite($out, $ciphertext);
    hash_update($hmacCtx, $ciphertext); // feed ciphertext into HMAC
    $iv = increment_iv($iv);
  }

  // Append 32-byte HMAC at the end
  $hmac = hash_final($hmacCtx, true);
  fwrite($out, $hmac);

  fclose($in);
  fclose($out);
  unlink($zip_path);

  echo "✅ Encrypted to: $encryptedPath\n";
  return $encryptedPath;
}

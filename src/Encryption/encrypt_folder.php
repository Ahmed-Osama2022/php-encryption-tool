<?php
function encrypt_folder(string $zip_path, string $passphrase): string|false
{
  if (!file_exists($zip_path)) {
    echo "❌ Zip file not found: '$zip_path'.\n";
    return false;
  }

  $encryptedPath = preg_replace('/\.zip$/', '.enc', $zip_path);

  // Argon2id — memory-hard, GPU-resistant
  $salt = random_bytes(16);
  $key  = sodium_crypto_pwhash(
    32,
    $passphrase,
    $salt,
    2,          // SODIUM_CRYPTO_PWHASH_OPSLIMIT_INTERACTIVE
    67108864,   // SODIUM_CRYPTO_PWHASH_MEMLIMIT_INTERACTIVE — 64MB
    2           // SODIUM_CRYPTO_PWHASH_ALG_ARGON2ID13
  );

  $iv  = random_bytes(16);
  $in  = fopen($zip_path, 'rb');
  $out = fopen($encryptedPath, 'wb');

  // Header: [16 bytes salt][16 bytes IV]
  fwrite($out, $salt . $iv);

  // Incremental HMAC — no need to load full ciphertext into memory
  $hmacCtx = hash_init('sha256', HASH_HMAC, $key);

  while (!feof($in)) {
    $chunk      = fread($in, 8192);
    $ciphertext = openssl_encrypt($chunk, 'AES-256-CTR', $key, OPENSSL_RAW_DATA, $iv);
    fwrite($out, $ciphertext);
    hash_update($hmacCtx, $ciphertext);
    $iv = increment_iv($iv);
  }

  // Append 32-byte HMAC at end of file
  fwrite($out, hash_final($hmacCtx, true));

  fclose($in);
  fclose($out);

  // Zero out key from memory
  sodium_memzero($key);

  unlink($zip_path);
  echo "✅ Encrypted to: $encryptedPath\n";
  return $encryptedPath;
}

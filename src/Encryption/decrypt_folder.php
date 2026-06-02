<?php
function decrypt_folder(string $enc_path, string $passphrase): string|false
{
  if (!file_exists($enc_path)) {
    echo "❌ Encrypted file not found: '$enc_path'.\n";
    return false;
  }

  $fileSize   = filesize($enc_path);
  $headerSize = SODIUM_CRYPTO_PWHASH_SALTBYTES + 16; // 32 bytes: salt + IV
  $hmacSize   = 32;
  $cipherSize = $fileSize - $headerSize - $hmacSize;

  if ($cipherSize <= 0) {
    echo "❌ File too small to be valid.\n";
    return false;
  }

  $in   = fopen($enc_path, 'rb');
  $salt = fread($in, SODIUM_CRYPTO_PWHASH_SALTBYTES);
  $iv   = fread($in, 16);

  $key = sodium_crypto_pwhash(
    32,
    $passphrase,
    $salt,
    SODIUM_CRYPTO_PWHASH_OPSLIMIT_INTERACTIVE,
    SODIUM_CRYPTO_PWHASH_MEMLIMIT_INTERACTIVE,
    SODIUM_CRYPTO_PWHASH_ALG_ARGON2ID13
  );

  // --- Pass 1: verify HMAC over ciphertext (streaming) ---
  $hmacCtx   = hash_init('sha256', HASH_HMAC, $key);
  $remaining = $cipherSize;

  while ($remaining > 0) {
    $chunk = fread($in, min(8192, $remaining));
    hash_update($hmacCtx, $chunk);
    $remaining -= strlen($chunk);
  }

  $computedHmac = hash_final($hmacCtx, true);
  $storedHmac   = fread($in, 32); // last 32 bytes

  if (!hash_equals($storedHmac, $computedHmac)) {
    fclose($in);
    sodium_memzero($key);
    echo "❌ Wrong passphrase or corrupted file.\n";
    return false;
  }

  // --- Pass 2: decrypt (HMAC verified — safe to proceed) ---
  fseek($in, $headerSize);
  $remaining = $cipherSize;

  $zipPath = preg_replace('/\.enc$/', '.zip', $enc_path);
  $out     = fopen($zipPath, 'wb');

  while ($remaining > 0) {
    $chunk     = fread($in, min(8192, $remaining));
    $plaintext = openssl_decrypt($chunk, 'AES-256-CTR', $key, OPENSSL_RAW_DATA, $iv);
    fwrite($out, $plaintext);
    $iv = increment_iv($iv);
    $remaining -= strlen($chunk);
  }

  fclose($in);
  fclose($out);
  sodium_memzero($key);

  echo "✅ Decrypted zip at: $zipPath\n";
  return $zipPath;
}

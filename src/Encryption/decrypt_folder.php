<?php
function decrypt_folder(string $enc_path, string $passphrase): string|false
{
  if (!file_exists($enc_path)) {
    echo "❌ Encrypted file not found: '$enc_path'.\n";
    return false;
  }

  $fileSize = filesize($enc_path);

  // Layout: [16 salt][16 IV][ciphertext][32 HMAC]
  $headerSize   = 16 + 16;       // 32 bytes
  $hmacSize     = 32;
  $cipherSize   = $fileSize - $headerSize - $hmacSize;

  if ($cipherSize <= 0) {
    echo "❌ File too small to be valid.\n";
    return false;
  }

  $in = fopen($enc_path, 'rb');

  $salt = fread($in, 16);
  $iv   = fread($in, 16);

  $key = hash_pbkdf2('sha256', $passphrase, $salt, 100_000, 32, true);

  // Verify HMAC by streaming ciphertext (memory efficient)
  $hmacCtx   = hash_init('sha256', HASH_HMAC, $key);
  $remaining = $cipherSize;

  while ($remaining > 0) {
    $chunkSize = min(8192, $remaining);
    $chunk     = fread($in, $chunkSize);
    hash_update($hmacCtx, $chunk);
    $remaining -= $chunkSize;
  }

  $computedHmac = hash_final($hmacCtx, true);
  $storedHmac   = fread($in, 32); // last 32 bytes

  if (!hash_equals($storedHmac, $computedHmac)) {
    fclose($in);
    echo "❌ Wrong passphrase or corrupted file.\n";
    return false;
  }

  // HMAC verified — now decrypt
  fseek($in, $headerSize); // rewind to start of ciphertext
  $remaining = $cipherSize;

  $zipPath = preg_replace('/\.enc$/', '.zip', $enc_path);
  $out     = fopen($zipPath, 'wb');

  while ($remaining > 0) {
    $chunkSize = min(8192, $remaining);
    $chunk     = fread($in, $chunkSize);
    $plaintext = openssl_decrypt($chunk, 'AES-256-CTR', $key, OPENSSL_RAW_DATA, $iv);
    fwrite($out, $plaintext);
    $iv = increment_iv($iv);
    $remaining -= $chunkSize;
  }

  fclose($in);
  fclose($out);

  echo "✅ Decrypted zip at: $zipPath\n";
  return $zipPath;
}

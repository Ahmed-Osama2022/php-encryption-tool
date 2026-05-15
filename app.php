#!/usr/bin/env php
<?php
require_once 'helpers.php';
require_once 'choose_folder.php';
require_once 'encrypt_folder.php';
require_once 'decrypt_folder.php';
require_once 'ask_delete.php';

/**
 * Let's define the main 3 functions
 * 1- Encrption core (Algorithm and all the stuff related) 
 * 2- main() || app()
 * 3- encrypt_folder()
 * 4- decrypt_folder()
 * 5- ask_delete()
 */
// The main app function 
function app()
{
  // 1- Let the user choose what did he want to do? (Encrypt | Decrypt | Exit)
  echo "\n=== 👋 Welcome to folder Encrypt/Decrypt Tool 👋 ===\n";
  // echo "Choose what you want to do:\n";
  echo "1. 🔒 Encrypt a folder 🔒\n";
  echo "2. 🔓 Decrypt a folder 🔓\n";
  echo "3. 👋 Exit 👋\n";
  echo "Choose an option (1, 2, 3): \n";
  $choice = trim(fgets(STDIN));

  if ($choice == '1') {
    echo "=========================\n";
    echo "🔒 === Encryption === 🔒\n";
    echo "=========================";
    $folder = choose_folder();
    // inspect($folder);

    echo "📂 Selected folder: " . "($folder)\n";
    echo "Enter passphrase: ";
    $keyphrase = trim(fgets(STDIN));
    // inspect($keyphrase); //TEST: 
    encrypt_folder($folder, $keyphrase);
  } elseif ($choice == '2') {
    echo "=========================\n";
    echo "🔓 === Decryption === 🔓\n";
    echo "=========================";
    $folder = choose_folder();
    echo "📂 Selected folder: " . "($folder)\n";
    echo "Enter passphrase: ";

    $keyphrase = trim(fgets(STDIN));
    decrypt_folder($folder, $keyphrase);
  } elseif ($choice == '3') {
    echo "👋 Exiting...\n";
    exit();
  } else {
    echo "❌ Invalid choice. Please try again. ❌\n";
    sleep(1);
    app();
  }
}

app();

die();

// Get the user input
// echo "Enter your folder name you want to compress it: ";
// $input = fgets(STDIN);




?>
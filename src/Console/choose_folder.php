<?php
// require_once 'helpers.php';
// require_once 'scan_directory.php';
// 
function choose_folder()
{
  // 1- Get the working directory
  $directories = scan_directory();

  if (empty($directories)) {
    echo "❌ No folders found in the current directory. ❌\n";
    echo "\n=========================================================== \n";
    echo "==================== Restart the App ====================== \n";
    echo "=========================================================== \n";
    sleep(1);
    app();
  }

  // 4- List the directories
  echo "\n📂 Available folders: \n";
  echo "Choose a folder:\n";
  foreach ($directories as $key => $directory) {
    echo $key + 1 . ") " . $directory . "\n";
  }
  // inspect($directories);

  // 5- Get the user input
  // $choice = fgets(STDIN); // BUG:
  $choice = (int) trim(fgets(STDIN));

  if ($choice < 1 || $choice > count($directories)) {
    echo "❌ Invalid choice. Please try again. ❌\n";
    sleep(1);
    return choose_folder();
  }

  $folder = $directories[$choice - 1];

  return $folder;
}

<?php
// require_once '../helpers.php';

function scan_directory()
{
  // 1- Get the working directory
  $current_folder = getcwd(); // To be based on your current terminal session (Helpful if wnated to use globally)
  // || OR || 
  // $current_folder = ROOT_DIR; // 
  // inspect($current_folder);

  // 2- Scan the current directory for folders
  $files = scandir($current_folder);
  $files = array_diff($files, array('..', '.'));
  // $files = scandir($dircurrent_folder, SCANDIR_SORT_DESCENDING);

  $directories = [];
  // 3- Check if the directory contains a direcotoires
  // BUG: (When compile to .phar file)
  // foreach ($files as $file) {
  //   if (is_dir($file)) {
  //     array_push($directories, $file);
  //   }
  // }

  foreach ($files as $file) {
    if (is_dir($current_folder . DIRECTORY_SEPARATOR . $file)) {
      array_push($directories, $file);
    }
  }

  return $directories;
}

// TEST:
// print_r(scan_directory());

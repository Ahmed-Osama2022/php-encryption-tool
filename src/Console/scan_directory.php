<?php
require_once '../helpers.php';

function scan_directory()
{
  // 1- Get the working directory
  // $current_folder = getcwd(); // BUG:
  $current_folder = __DIR__;

  // 2- Scan the current directory for folders
  $files = scandir($current_folder);
  $files = array_diff($files, array('..', '.'));
  // $files = scandir($dircurrent_folder, SCANDIR_SORT_DESCENDING);

  $directories = [];
  // 3- Check if the directory contains a direcotoires
  foreach ($files as $file) {
    if (is_dir($file)) {
      array_push($directories, $file);
    }
  }
  return $directories;
}

// TEST:
// print_r(scan_directory());

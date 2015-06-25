<?php

  /* 
   * GD Resize Crontask
   * Used on Slash for generating wallpaper thumbnails
   * by Andrew Brown
   * 
   * Last modified: 01/05/11
   */

  set_time_limit(0); // Override execution time limit

  include("settings.php"); // Import config $cf array
  thumb_directory("Wallpapers", "Thumbnails", $cf['preview_height'], $cf['preview_width']);

  // Directory listing on $input_dir, form thumbnails on each file and save in $output_dir
  function thumb_directory($input_dir, $output_dir, $height, $width)
  {
    if ($fhandle = opendir($input_dir))
    {
      while (false !== ($file = readdir($fhandle)))
      {
        make_thumbnail($file, $input_dir, $output_dir, $height, $width, true);
      }
    }
  }
  
  function make_thumbnail($filename, $input_dir, $output_dir, $height, $width, $recursive = true)
  {
    // Recurse into subfolders
    if ($recursive && $filename != "." && $filename != ".." && is_dir("$input_dir/$filename"))
    {
      thumb_directory("$input_dir/$filename", "$output_dir/$filename", $height, $width);
      return;
    }
  
    $expanded  = explode(".", $filename);
    $extension = $expanded[1];
    echo "Thumbnailing $input_dir/$filename to $output_dir/$filename..";
    
    // Create image
    switch ($extension)
    {
      case "jpg":
      case "jpeg":
        $img = imagecreatefromjpeg($input_dir . "/" . $filename);
        break;
      case "png":
        $img = imagecreatefrompng($input_dir . "/" . $filename);
        break;
      case "gif":
        $img = imagecreatefromgif($input_dir . "/" . $filename);
        break;
      default:
        // Ignore everything else
        echo "Skipping.\n";
      	return false;
    }
    
    // Calculate new dimensions
    $old_height = imageSY($img);
    $old_width  = imageSX($img);
    
    if ($old_width > $old_height)
    {
      // Landscape
      $new_height = $old_height * ($height / $old_width);
      $new_width  = $width;
    }
    elseif ($old_width < $old_height)
    {
      // Portrait
      $new_height = $height;
      $new_width  = $old_width * ($width / $old_height);
    }
    
    // Resize
    $thumb = ImageCreateTrueColor($new_width, $new_height);
    imagecopyresampled($thumb, $img, 0, 0, 0, 0, $new_width, $new_height, $old_width, $old_height);
    
    // Save to filesystem
    if (!file_exists($output_dir))
    {
      mkdir($output_dir);
    }
    
    switch ($extension)
    {
      case "jpg":
      case "jpeg":
        imagejpeg($thumb, $output_dir . "/thumb_" . $filename);
        break;
      case "png":
        imagepng($thumb, $output_dir . "/thumb_" . $filename);
        break;
      case "gif":
        imagegif($thumb, $output_dir . "/thumb_" . $filename);
        break;
    }

    // Clean up
    echo "Done!\n";
    imagedestroy($img);
    imagedestroy($thumb);
    
  }
?>
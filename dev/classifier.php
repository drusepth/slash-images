<?php

	// Classify images by dominant color
	// by Andrew Brown
	
	if (!isset($_GET['file']))
	{
		echo "<h2>Add ?file=path/to/file to get started!</h2>";
		die;
	}

	// Limit constants
	define("LOWER", 0);
	define("UPPER", 1);

	// Define color ranges in terms of min/max for each rgb
	// This will allow more specific colors to be easily added later
	$colors = array(
		// Color => array("R" => array(MIN, MAX),
		"Red"    => array("R" => array(50, 255),
					      "G" => array(0, 50),
					      "B" => array(0, 50)),

		"Green"  => array("R" => array(0, 50),
						  "G" => array(30, 255),
						  "B" => array(0, 50)),

		"Blue"   => array("R" => array(0, 50),
						  "G" => array(0, 50),
						  "B" => array(50, 255)),

		"White"  => array("R" => array(0, 25),
					      "G" => array(0, 25),
					      "B" => array(0, 25)),

		"LGrey"  => array("R" => array(25, 75),
						  "G" => array(25, 75),
						  "B" => array(25, 75)),

		"Purple" => array("R" => array(64, 128),
						  "G" => array(0, 64),
						  "B" => array(128, 255))
	);
	
	// Load image as bitmap
	$img = get_image($_GET['file']);
	
	// Read dimensions
	$height = imagesy($img);
	$width  = imagesx($img);
	
	// Display image and dimensions
	echo "<div><img src='", $_GET['file'], "' /></div>\n";
	echo "<div>Dimensions: ", $width, "x", $height, "</div>\n";

	// Pixel color data
	$cscores = array();
	$pcount  = $height * $width;

	// Score pixels
	for ($y = 0; $y < $height; $y++)
	{
		for ($x = 0; $x < $width; $x++)
		{
			$rgb = get_pixel($img, $x, $y);
			
			$r = ($rgb >> 16) & 0xFF;
			$g = ($rgb >>  8) & 0xFF;
			$b = ($rgb) & 0xFF;
			
			$pcolors = score_color($r, $g, $b, $colors);

			foreach ($pcolors as $pcolor)
			{
				$cscores[$pcolor]++;
			}
		}
	}
	
	// Quicksort potential colors descending for aesthetics
	asort($cscores);
	$cscores = array_reverse($cscores, true);
	
	// Display color guesses
	echo "<ul>\n";
	foreach ($cscores as $color => $freq)
	{
		echo "<li><strong>", $color, "</strong>: ";
		echo $freq, " matches (", round($freq / $pcount * 100, 3), "% certainty)</li>\n";
	}
	echo "</ul>";

	// Return pixel data at $x, $y
	function get_pixel($img, $x, $y)
	{
		$rgb = imagecolorat($img, $x, $y);

		return $rgb;
	}
	
	// Check $r$g$b against each color bound in $colors, return all
	// colors that match
	function score_color($r, $g, $b, $colors)
	{
		$possible_colors = array();

		foreach ($colors as $color => $RGB)
		{
			// If we're within all color ranges, add this as a potential color
			if (true
				&& $r >= $RGB["R"][LOWER] && $r <= $RGB["R"][UPPER]
				&& $g >= $RGB["G"][LOWER] && $g <= $RGB["G"][UPPER]
				&& $b >= $RGB["B"][LOWER] && $b <= $RGB["B"][UPPER]
			)
			{
				array_push($possible_colors, $color);
			}
		}
		
		return $possible_colors;
	}
	
	// Create image from file or url
	function get_image($path)
	{
		$extension = substr($path, strrpos($path, '.') + 1);
		$img = null;
		
		// todo handle invalid urls rather than throwing fatal errors
	
		switch ($extension)
		{
			case "jpg":
			case "jpeg":
				$img = imagecreatefromjpeg($path);
				break;
			case "gif":
				$img = imagecreatefromgif($path);
				break;
			case "png":
				$img = imagecreatefrompng($path);
				break;
			case "bmp":
				$img = imagecreatefrombmp($path);
				break;
		}
		
		return $img;
	}

?>
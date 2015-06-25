<?php
	
	$cf['title'] = 'Slash';
	
	$cf['content_directory'] = 'Wallpapers'; # Without trailing slash
	$cf['thumbs_directory'] = 'Thumbnails';
	
	$cf['preview_height'] = 240;
	$cf['preview_width'] = 240;
	
	function debug ($text) 
	{
		$cf['debug_mode'] = 'off';
		if ($cf['debug_mode'] == 'on') {
			echo $text . "\n";
		}
	}
	
?>
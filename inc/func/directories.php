<?php
/**
 * Recursively delete a directory
 *
 * @param string $path Intial path to delete
 */
function removeDir($path) {
	$normal_files = glob($path . "*");
	$hidden_files = glob($path . "\.?*");
	$all_files = array_merge($normal_files, $hidden_files);

	foreach ($all_files as $file) {
		/* Skip pseudo links to current and parent dirs (./ and ../). */
		if (preg_match("/(\.|\.\.)$/", $file))
		{
				continue;
		}

		if (is_file($file) === TRUE) {
			/* Remove each file in this Directory */
			unlink($file);
			echo _gettext('Removed File').': '. $file . "<br />";
		}
		else if (is_dir($file) === TRUE) {
			/* If this Directory contains a Subdirectory, run this Function on it */
			removeDir($file);
		}
	}
	/* Remove Directory once Files have been removed (If Exists) */
	if (is_dir($path) === TRUE) {
		rmdir($path);
		echo '<br />'. _gettext('Removed Directory').': ' . $path . "<br /><br />";
	}
}

/**
 * Remove a board
 *
 * @param string $dir Directory to remove
 * @return boolean Result
 */
function removeBoard($dir){
	global $tc_db;

	if(!isset($GLOBALS['remerror'])) {
		$GLOBALS['remerror'] = false;
	}

	if($handle = opendir(KU_BOARDSDIR . $dir)){ /* If the folder exploration is sucsessful, continue */
		while (false !== ($file = readdir($handle))){ /* As long as storing the next file to $file is successful, continue */
			$path = $dir . '/' . $file;

			if(is_file(KU_BOARDSDIR . $path)){
				if(!unlink(KU_BOARDSDIR . $path)){
					echo '<u><font color="red">'.sprintf(_gettext('"%s" could not be deleted. This may be due to a permissions problem.</u><br />Directory cannot be deleted until all files are deleted.'), $path).'</font><br />';
					$GLOBALS['remerror'] = true;
					return false;
				}
			} else
				if(is_dir(KU_BOARDSDIR . $path) && substr($file, 0, 1) != '.'){
					removeBoard($path);
					@rmdir(KU_BOARDSDIR . $path);
				}
		}
		closedir($handle); /* Close the folder exploration */
	}

	if(!$GLOBALS['remerror']) /* If no errors occured, delete the now empty directory */
		if(!rmdir(KU_BOARDSDIR . $dir)){
			echo '<strong><font color="red">'.sprintf(_gettext('Could not remove directory "%s". This may be due to a permissions problem.'),$dir).'</font></strong><br />'.$GLOBALS['remerror'];
			return false;
		} else
			return true;

	return false;
}

/*
------------ lixlpixel recursive PHP functions -------------
recursive_directory_size( directory, human readable format )
expects path to directory and optional TRUE / FALSE
PHP has to have the rights to read the directory you specify
and all files and folders inside the directory to count size
if you choose to get human readable format,
the function returns the filesize in bytes, KB and MB
------------------------------------------------------------
to use this function to get the filesize in bytes, write:
recursive_directory_size('path/to/directory/to/count');
to use this function to get the size in a nice format, write:
recursive_directory_size('path/to/directory/to/count',TRUE);
*/

/**
 * Find the size of a directory, including any subdirectories
 *
 * @param string $directory Directory
 * @param boolean $format Format
 * @return array Size/number of files
 */
function recursive_directory_size($directory, $format=FALSE)
{
	$size = 0;
	$files = 0;

	/* If the path has a slash at the end we remove it here */
	if(substr($directory,-1) == '/')
	{
		$directory = substr($directory,0,-1);
	}

	/* If the path is not valid or is not a directory ... */
	if(!file_exists($directory) || !is_dir($directory) || !is_readable($directory))
	{
		/* ... We return -1 and exit the function */
		return -1;
	}
	/* We open the directory */
	if($handle = opendir($directory))
	{
		/* And scan through the items inside */
		while(($file = readdir($handle)) !== false)
		{
			/* We build the new path */
			$path = $directory.'/'.$file;

			/* If the filepointer is not the current directory or the parent directory */
			if($file != '.' && $file != '..')
			{
				/* If the new path is a file */
				if(is_file($path))
				{
					/* We add the filesize to the total size */
					$size += filesize($path);
					$files++;

				/* If the new path is a directory */
				}elseif(is_dir($path))
				{
					/* If $format is set to true, follow the directory and recalculate from there */
					if ($format) {
						/* We call this function with the new path */
						$handlesize = recursive_directory_size($path);

						if (is_int($handlesize)) {
							/* If the function returns more than zero */
							if($handlesize >= 0)
							{
								/* We add the result to the total size */
								$size += $handlesize;

							/* Else we return -1 and exit the function */
							}else{
								return -1;
							}
						/* Else we return -1 and exit the function */
						}else{
							return -1;
						}
					}
				}
			}
		}
		/* Close the directory */
		closedir($handle);
	}
	/* Return the total filesize in bytes */
	return array($size,$files);
}
?>
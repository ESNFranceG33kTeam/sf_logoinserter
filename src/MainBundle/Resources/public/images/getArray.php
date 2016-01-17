<?php

$path = "logos";
$dir = opendir($path); 
echo "array(";
$cpt=0;
while($file = readdir($dir)) {
	
	$extension = pathinfo($file, PATHINFO_EXTENSION);
	if($file != '.' && $file != '..' && !is_dir($path.$file))
	{
		$cpt++;
		echo "'$file' =>'" . $file. "',<br>";
	}
}
closedir($dir);
echo ");";

?>
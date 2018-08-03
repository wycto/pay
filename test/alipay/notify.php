<?php
$g = $_GET;
$p = $_POST;

$myfile = fopen("newfile.txt", "w") or die("Unable to open file!");

$g = implode(" ",$g);
$txt = $g . "Bill Gates\n";
fwrite($myfile, $txt);

$p = implode(" ",$p);
$txt = $p . "Steve Jobs\n";
fwrite($myfile, $txt);

fclose($myfile);

?>
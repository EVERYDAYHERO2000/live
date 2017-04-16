<?php


$comments = 'data:text/csv;charset=utf-8,fdfdfdfe erre';
$commentsSize = iconv_strlen($comments);

echo $comments;
echo '<br>';
echo $commentsSize;
/*
$filename = "php/description.txt";
$handle = fopen($filename, "r");
$contents = fread($handle, filesize($filename));
echo filesize($filename);
fclose($handle);
*/

?>
<?php

include 'php/global.php';

$path = $_GET["path"];


$folderPath =  preg_match('/\/+[0-9\-_]+\/+/', $path, $matches);
$descriptionPath = $matches[0].'description.txt';

$description = readDisk($downloadURL.'?public_key=https://yadi.sk/d/Xbi2Ydwq3GideD&path='.$descriptionPath);

$description = json_decode( file_get_contents( json_decode($description) -> {'href'} ) );



foreach ( $description as $f ){
  
   if ($f -> {'path'} !== ''){
    $posts = readDisk($downloadURL.'?public_key='.$publicKey.'&path='.$f -> {'path'});
    
    //echo $f -> {'path'};
    //echo '<br>'; 
     
    $f -> {'href'} = json_decode($posts) -> {'href'};
   }
}

print_r( json_encode($description) );



?>
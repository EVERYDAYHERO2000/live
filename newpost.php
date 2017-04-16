<?php

include 'php/global.php';

  $login = $_POST["login"];
  $comment = $_POST["comment"];
  
  $date = date('m-d-Y_His');

  $folder = $_POST["path"];
  $folderPath =  preg_match('/\/+[0-9\-_]+\/+/', $folder, $matches);
  $folder = $matches[0];

  if ($_FILES) {
    $path = $_FILES["images"]["tmp_name"][0];
    $filesize = $_FILES["images"]["size"][0];
    $filename = $_FILES["images"]["name"][0];

    $newPath = $folder.$filename;
    
    uploadFile( $uploadURL.'?path=disk:/'.$projectFolderName.$folder.$filename, $path, $filesize );
    
  } else {
    $path = '';
    $filesize = '';
    $filename = ''; 
    $newPath = '';
  }


$description = readDisk($downloadURL.'?public_key='.$publicKey.'&path='.$folder.'description.txt');

$description = json_decode( file_get_contents( json_decode($description) -> {'href'} ) );

$commentsHeader = 'data:text/csv;charset=utf-8,';
$comments = json_decode('{ "date" : "'.$date.'", "path" : "'.$newPath.'", "user" : "'.$login.'", "comment" : "'.$comment.'" }');

$description[] = $comments;

$comments = json_encode($description, JSON_UNESCAPED_UNICODE);

$commentsSize = strlen($comments);

echo $projectFolderName.$folder.'description.txt';
uploadFile( $uploadURL.'?path=disk:/'.$projectFolderName.$folder.'description.txt&overwrite=true', $commentsHeader.$comments, $commentsSize);

//ob_get_clean();
//$newURL = 'disk:/'.$projectFolderName.$folder;

//updateFolder('https://cloud-api.yandex.net/v1/disk/resources/move?from='.$newURL.'&path='.$newURL.'&overwrite=true&public_key=https://yadi.sk/d/Xbi2Ydwq3GideD');

echo $comments;



/*foreach ( $description as $f ){
    $posts = readDisk('https://cloud-api.yandex.net/v1/disk/public/resources/download?public_key='.$publicKey.'&path='.$f -> {'path'});
    $f -> {'href'} = json_decode($posts) -> {'href'};
}

print_r( json_encode($description) );
*/


?>
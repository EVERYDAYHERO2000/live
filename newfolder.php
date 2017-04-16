<?php

  include 'php/global.php';

  $path = $_FILES["images"]["tmp_name"][0];
  $filesize = $_FILES["images"]["size"][0];
  $filename = $_FILES["images"]["name"][0];
  $login = $_POST["login"];
  

  $folderName = date('m-d-Y_His');

  createFolder( 'https://cloud-api.yandex.net/v1/disk/resources/?path=disk:/'.$projectFolderName.'/'.$folderName );

  uploadFile( $uploadURL.'?path=disk:/'.$projectFolderName.'/'.$folderName.'/'.$filename, $path, $filesize );

  $newFilePath = 'disk:/'.$projectFolderName.'/'.$folderName.'/'.$filename;
  $commentsHeader = 'data:text/csv;charset=utf-8,';
  $path = '/'.$folderName.'/'.$filename;
  $comments = '[{ "date" : "'.$folderName.'", "path" : "'.$path.'", "user" : "'.$login.'", "comment" : "" }]';
  $commentsSize = strlen($comments);
  
  uploadFile( $uploadURL.'?path=disk:/'.$projectFolderName.'/'.$folderName.'/description.txt', $commentsHeader.$comments, $commentsSize);

  ob_get_clean();

  echo $path;
    
?>
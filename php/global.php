<?php

header('Content-Type: text/html; charset=utf-8');
include 'user.php';

$headers = array();
$headers[] = 'Authorization: OAuth '.$OAuth;
$headers[] = 'Content-Type: application/json';

$readURL = 'https://cloud-api.yandex.net/v1/disk/public/resources'; //чтение
$downloadURL = 'https://cloud-api.yandex.net/v1/disk/public/resources/download'; //скачать
$uploadURL = 'https://cloud-api.yandex.net/v1/disk/resources/upload'; //загрузить


//получить содержимое папки
function readDisk( $url ) {
  
  global $headers;
  
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $url);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
  curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
  
  $server_output = curl_exec ($ch);
  
  if (curl_exec($ch) === false){
    echo 'Ошибка curl: ' . curl_error($ch);
  }
  
  curl_close ($ch);
  return $server_output;
}


//собрать содержимое папок
function buildData( $d ){

  global $publicKey;
  global $readURL;    
  
  $data = '';

  foreach ( json_decode($d) -> {'_embedded'} -> {'items'} as $f ){
    $path = $f -> {'path'};

    if ( $f -> {'type'} === 'dir' ){
      $posts = readDisk( $readURL.'?public_key='.$publicKey.'&path='.$path.'&preview_size=L&limit=2&fields=name,path,_embedded.items.media_type,_embedded.items.mime_type,_embedded.items.name,_embedded.items.path,_embedded.items.preview,_embedded.items.type&sort=modified');

      $data = $data.$posts.',';
    }
  }

  return '['.substr($data, 0, -1).']';
}


//создать директорию
function createFolder( $url ){

  global $headers;
    
  $ch = curl_init();
  curl_setopt( $ch, CURLOPT_URL, $url );
  curl_setopt($ch, CURLOPT_PUT, true);
  curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
  curl_exec ($ch);
  curl_close ($ch);
}


//загрузить файл
function uploadFile( $url, $path, $filesize ){
  
  global $headers;
  
  $ch = curl_init();
  curl_setopt( $ch, CURLOPT_URL, $url);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
  curl_setopt($ch, CURLOPT_HTTPGET, true);
  curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
  $server_output = curl_exec ($ch);
  curl_close ($ch);

  print_r (json_decode($server_output));
  $url = json_decode($server_output) -> {'href'};

  $fp = fopen($path, 'r');

  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $url);
  curl_setopt($ch, CURLOPT_INFILE, $fp);
  curl_setopt($ch, CURLOPT_INFILESIZE, $filesize);
  curl_setopt($ch, CURLOPT_PUT, true);
  curl_setopt($ch, CURLOPT_UPLOAD, true);
  curl_exec($ch);
  
  curl_close($ch);
}

function updateFolder( $url ){
  global $headers;
  
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $url);
  curl_setopt($ch, CURLOPT_POST, true);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
  curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
  
  $server_output = curl_exec ($ch);
  
  if (curl_exec($ch) === false){
    echo 'Ошибка curl: ' . curl_error($ch);
  }
  
  curl_close ($ch);
  return $server_output;
}


?>
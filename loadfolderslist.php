
<?php

include 'php/global.php';



$folders = readDisk( $readURL.'?public_key='.$publicKey.'&preview_size=M&limit=1000000&sort=modified' );

echo buildData( $folders );
//echo $folders;

?>
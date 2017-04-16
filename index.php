<html>

  <head>
    <title>Board</title>
    <meta charset="utf-8">
    <link rel="stylesheet" href="css/main.css" />
    
    <link href="https://fonts.googleapis.com/css?family=PT+Mono" rel="stylesheet">
    <script src="https://yastatic.net/jquery/3.1.1/jquery.min.js"></script>
    <script src="js/jquery.scrollto.min.js"></script>
    <script src="js/main.js"></script>
    
    
    
  </head>

  <body>

    <div id="main">

      <form method="post" enctype="multipart/form-data" action="upload.php"> 
        <input class="files" type="file" name="images" id="images" />
        <div class="status"></div>
        <button type="submit" id="upload">Загрузить</button>
      </form>

    </div>
    <div class="albom">
    </div>
    <div class="close"></div>


  </body>

</html>
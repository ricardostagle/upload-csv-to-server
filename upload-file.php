<!DOCTYPE html>
<html>
<head>
  <title>Actulizacion de cunetas por una contrasenias generica</title>
  <link rel="stylesheet" href="https://jqueryvalidation.org/files/demo/site-demos.css">
</head>
<body>
  <form enctype="multipart/form-data" action="upload-file.php" method="POST">
    <p>Actulizacion de cunetas por una contrasenias generica</p>
    <label for="file" class="btn">Selecciona un archivo</label><br />
    <input id="file" type="file" name="uploaded_file" accept=".csv" style="visibility:hidden;" ></input>
    <br />
    <button>Subir</button>
  </form>

</body>
</html>
<?PHP
  if(!empty($_FILES['uploaded_file']))
  {
    $path = "uploads/";
    $path = $path . basename( $_FILES['uploaded_file']['name']);
    if(!empty(basename( $_FILES['uploaded_file']['name'])) && basename( $_FILES['uploaded_file']['name']) == "uploads/"){
      unlink($path);
    }
    
    if(move_uploaded_file($_FILES['uploaded_file']['tmp_name'], $path)) {
      
      $filename=basename( $_FILES['uploaded_file']['name']);
      echo "<br>El archivo '". $filename ."' ha sido cargado.<br>".PHP_EOL;

      if(substr($filename, -3) == 'csv'){
        
        echo "<br>Comienza a ejecutar php...".PHP_EOL;

        //run_request($filename); 

      }else{
        echo "<br>La extencion del archivo debe de ser csv.<br>".PHP_EOL;
      }
     
    } else{
      echo "<br>Hubo un error cargando el archivo, por favor vuelva a intentar.<br>".PHP_EOL;
    }
  }

?>
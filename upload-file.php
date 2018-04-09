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

  function run_request($path){

    $host = "localhost";
    $user = "user";
    $pass = "password";
    $db = "moodle";
    $prefix = 'mdl_';

    $correos_cuentas = array();

    echo "¿Estas seguro de realizar el cambio de contraeñas?  Escribe 'si' para continuar: ";
    $handle = fopen ("php://stdin","r");
    $line = fgets($handle);
    if(trim($line) != 'si'){
        echo "Cancelar\n";
        exit;
    }
    echo "\n";
    echo "Comenzando...\n";
    echo 'Abriendo archivo ' . $path .".\n";

    if (($gestor = fopen($path, "r")) === FALSE) {
      exit;
    }

    $fila = 1;

    if (($gestor = fopen($archivo, "r")) !== FALSE) {
      echo "Abro archivo...\n";
      $mysqli = new mysqli($host, $user, $pass, $db);

      /* comprueba la conexión */
      if (mysqli_connect_error()) {
          printf("Conección fallida: %s\n", mysqli_connect_error());
          exit();
      }else{
        //selección de la base de datos con la que vamos a trabajar 
        //mysql_select_db($db); 
      }

      /* devuelve el nombre de la base de datos actualmente seleccionada */
      if ($result = $mysqli->query("SELECT DATABASE()")) {
          $row = $result->fetch_row();
          printf("La base de datos por defecto es: %s.\n", $row[0]);
          $result->close();
      }

      /* cambia de test bd a world bd */
      //$mysqli->select_db("world");

      while (($datos = fgetcsv($gestor, 100, ",")) !== FALSE) {
        $numero = count($datos);
        //echo "$numero de campo en la línea $fila\n";
        echo "$fila || ";
        $fila++;
        $sqlselect = $mysqli->query("SELECT password FROM ".$prefix."user WHERE username = 'docente.upd.bd'");
        if($sqlselect->num_rows>0){
          while($row=$sqlselect->fetch_assoc()){
            $password = $row["password"];
          }
        }else{
          die('No se pudo seleccionar la base de datos:'.mysql_error());
        }
        for ($c=0; $c < $numero; $c++) {
          $correo = "";
          $correo = $datos[1];
          $correo = rtrim($correo,";");
          if($c==0){
            $sqluser = $mysqli->query("SELECT * FROM ".$prefix."user WHERE username LIKE '%" . strtoupper ($datos[$c])."%'");
            if ($sqluser->num_rows>0) {
              $sqlupdate = "UPDATE ".$prefix."user SET password = '".$password."' WHERE username = '" . strtoupper ($datos[$c])."'";
              $mysqli->query($sqlupdate);
              echo strtoupper ($datos[$c]) . " || Usuario actualizado || por cuenta || " . $correo;
            } else {
              $query_email="SELECT * FROM ".$prefix."user WHERE email = '" . $correo ."'";
            $sql_usuario_correo = $mysqli->query($query_email);
              if ($sql_usuario_correo->num_rows>0) {
                $sql_update_correo = "UPDATE ".$prefix."user SET password = '".$password."' WHERE email = '" . $correo ."'";
                $mysqli->query($sql_update_correo);
                echo strtoupper ($datos[$c]) . " || Usuario actualizado || por correo || " . $correo;
              } else {
                array_push($correos_cuentas, '"'.$datos[$c].'","'.$correo.'"');
                echo strtoupper($datos[$c])." || Cuenta no encontrada || Error || ".$correo;
              }
            }
          }
        }
        echo "\n";
      }
      foreach($correos_cuentas as $info_cuenta){
        echo $info_cuenta."\n";
      }
      echo "Actualizacion terminada.\n";
      $mysqli->close();
      fclose($gestor);
    }
  }


  if(!empty($_FILES['uploaded_file'])){
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
        run_request($path); 
      }else{
        echo "<br>La extencion del archivo debe de ser csv.<br>".PHP_EOL;
      }
    } else{
      echo "<br>Hubo un error cargando el archivo, por favor vuelva a intentar.<br>".PHP_EOL;
    }
  }

?>
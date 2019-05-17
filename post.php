<?php
include "config.php";
include "utils.php";


$dbConn =  connect($db);

/*
  listar todos los posts o solo uno
 */
if ($_SERVER['REQUEST_METHOD'] == 'GET')
{
    if (isset($_GET['cedula']))
    {
      //Mostrar un post
      $sql = $dbConn->prepare("SELECT * FROM users where cedula=:cedula");
      $sql->bindValue(':cedula', $_GET['cedula']);
      $sql->execute();
      header("HTTP/1.1 200 OK");
      echo json_encode(  $sql->fetch(PDO::FETCH_ASSOC)  );
      exit();
	  }
    else {
      //Mostrar lista de post
      $sql = $dbConn->prepare("SELECT * FROM users");
      $sql->execute();
      $sql->setFetchMode(PDO::FETCH_ASSOC);
      header("HTTP/1.1 200 OK");
      echo json_encode( $sql->fetchAll()  );
      exit();
	}
}

// Crear un nuevo post
if ($_SERVER['REQUEST_METHOD'] == 'POST')
{
    $input = $_POST;
    $sql = "INSERT INTO users
          (cedula, nombre, apellidos, edad, sueldo, direccion)
          VALUES
          (:cedula, :nombre, :apellidos, :edad,:sueldo,:direccion)";
    $statement = $dbConn->prepare($sql);
    bindAllValues($statement, $input);
    $statement->execute();
    $cedulaId = $dbConn->lastInsertId();
    if($postId)
    {
      $input['cedula'] = $cedulaId;
      header("HTTP/1.1 200 OK");
      echo json_encode($input);
      exit();
	 }
}

//Borrar
if ($_SERVER['REQUEST_METHOD'] == 'DELETE')
{
	$id = $_GET['cedula'];
  $statement = $dbConn->prepare("DELETE FROM users where cedula=:cedula");
  $statement->bindValue(':cedula', $cedula);
  $statement->execute();
	header("HTTP/1.1 200 OK");
	exit();
}

//Actualizar
if ($_SERVER['REQUEST_METHOD'] == 'PUT')
{
    $input = $_GET;
    $cedulaId = $input['cedula'];
    $fields = getParams($input);

    $sql = "
          UPDATE users
          SET $fields
          WHERE id='$cedulaId'
           ";

    $statement = $dbConn->prepare($sql);
    bindAllValues($statement, $input);

    $statement->execute();
    header("HTTP/1.1 200 OK");
    exit();
}


//En caso de que ninguna de las opciones anteriores se haya ejecutado
header("HTTP/1.1 400 Bad Request");

?>

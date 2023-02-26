<?php
session_start();
include('../includes/connection.php');

switch ($_POST['accion']) {
   case 'crear_usuario':
      $perfiles = array_slice($_POST, 4);
      if ($_POST['nombre'] == '' || $_POST['nombre'] == '' || $_POST['password'] == '' || count($perfiles) == 0) {
         echo '<!DOCTYPE html>
         <html lang="en">
         
         <head>
            <meta charset="UTF-8">
            <meta http-equiv="X-UA-Compatible" content="IE=edge">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
         
            <link rel="stylesheet" href="../assets/css/bootstrap.min.css">
            <script src="../assets/js/bootstrap.bundle.min.js"></script>
         
            <title>Dashboard</title>
         </head>
         
         <body>
            <div class="container text-center d-flex align-items-center vh-100 justify-content-center">
               <div class="alert alert-primary d-flex flex-column align-items-center justify-content-center h-25" role="alert">
                  <div class="d-flex align-items-center">
                     <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="me-2 bi bi-info-circle-fill" viewBox="0 0 16 16">
                        <path d="M8 16A8 8 0 1 0 8 0a8 8 0 0 0 0 16zm.93-9.412-1 4.705c-.07.34.029.533.304.533.194 0 .487-.07.686-.246l-.088.416c-.287.346-.92.598-1.465.598-.703 0-1.002-.422-.808-1.319l.738-3.468c.064-.293.006-.399-.287-.47l-.451-.081.082-.381 2.29-.287zM8 5.5a1 1 0 1 1 0-2 1 1 0 0 1 0 2z"/>
                     </svg>
                     <div>
                        Datos incompletos
                     </div>
                  </div>
                  <div>
                     <a class="btn btn-primary" href="formulario.php">Volver</a>
                  </div>
               </div>
            </div>
         </body>
         
         </html>';
      } else {
         $result = $pdo->prepare("INSERT INTO t100_usuarios (nombre, username, password, estado, id_compania_fk) VALUES (:nombre, :username, :password, :estado, :id_compania_fk)");
         $result->execute([
            'nombre' => $_POST['nombre'],
            'username' => $_POST['usuario'],
            'password' => $_POST['password'],
            'estado' => 'activo',
            'id_compania_fk' => $_SESSION['usuario']['id_compania_fk'],
         ]);

         $idUser = $pdo->lastInsertId();

         $values = '';
         foreach ($perfiles as $perfil) {
            $values .= "(" . $idUser . ", " . $perfil . "),";
         }

         $values = substr($values, 0, strlen($values) - 1);

         $insertPefiles = $pdo->prepare('INSERT INTO t103_usuarios_perfiles (id_usuario, id_perfil) VALUES ' . $values);
         $insertPefiles->execute();

         $pdo = null;

         header("Location: index.php");
      }

      break;
   case 'modificar_usuario':
      $perfiles = array_slice($_POST, 5);

      $result = $pdo->prepare('UPDATE t100_usuarios SET nombre = :nombre, username = :username, password = :password WHERE id_usuario = :id_usuario');
      $result->execute([
         'nombre' => $_POST['nombre'],
         'username' => $_POST['usuario'],
         'password' => $_POST['password'],
         'id_usuario' => $_POST['id_usuario']
      ]);

      $perfilesDB = $pdo->prepare('SELECT id_perfil FROM t103_usuarios_perfiles WHERE id_usuario = :id_usuario');
      $perfilesDB->execute(['id_usuario' => $_POST['id_usuario']]);
      $perfilesDB->setFetchMode(PDO::FETCH_ASSOC);
      $perfilesDB = $perfilesDB->fetchAll();

      $perfilesDBAux = array();
      foreach ($perfilesDB as $perfile) {
         array_push($perfilesDBAux, $perfile['id_perfil']);
      }

      foreach ($perfilesDBAux as $perfile) {
         if (!in_array($perfile, $perfiles)) {
            $deletePerfil = $pdo->prepare('DELETE FROM t103_usuarios_perfiles WHERE id_perfil = :id_perfil AND id_usuario = :id_usuario');
            $deletePerfil->execute([
               'id_perfil' => $perfile,
               'id_usuario' => $_POST['id_usuario'],
            ]);
         }
      }

      foreach ($perfiles as $perfile) {
         if (!in_array($perfile, $perfilesDBAux)) {
            $insertPerfil = $pdo->prepare('INSERT INTO t103_usuarios_perfiles (id_usuario, id_perfil) VALUES (:id_usuario, :id_perfil)');
            $insertPerfil->execute([
               'id_usuario' => $_POST['id_usuario'],
               'id_perfil' => $perfile
            ]);
         }
      }

      $pdo = null;

      header("Location: index.php");
      break;
   case 'eliminar_usuario':
      $result = $pdo->prepare('UPDATE t100_usuarios SET estado = "inactivo" WHERE id_usuario = :id_usuario');
      $result->execute([
         'id_usuario' => $_POST['id_usuario'],
      ]);
      $pdo = null;

      header("Location: index.php");
      break;

   default:
      # code...
      break;
}

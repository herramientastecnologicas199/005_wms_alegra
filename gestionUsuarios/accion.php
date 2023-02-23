<?php
session_start();
include('../includes/connection.php');

switch ($_POST['accion']) {
   case 'crear_usuario':
      $perfiles = array_slice($_POST, 4);
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

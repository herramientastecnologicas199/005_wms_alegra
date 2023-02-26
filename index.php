<!DOCTYPE html>
<html lang="en">

<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">

   <link rel="stylesheet" href="assets/css/bootstrap.min.css">
   <script src="assets/js/bootstrap.bundle.min.js"></script>

   <title>Login</title>
</head>

<body>
</body>
<div class="vh-100 d-flex bg-body-tertiary">
   <div class="container text-center d-flex align-items-center justify-content-center">
      <form method="post" class="shadow rounded-5 p-3 px-5 w-50 bg-white">
         <h2>LOGIN</h2>
         <div class="mb-3 text-start form-floating">
            <input id="input-usuario" name="input_usuario" class="form-control" type="text" placeholder="Usuario" />
            <label for="input-usuario">Usuario</label>
         </div>
         <div class="mb-3 text-start form-floating">
            <input id="input-password" name="input_password" class="form-control" type="password" placeholder="Contraseña" />
            <label for="input-password">Contraseña</label>
         </div>
         <button type="submit" class="btn btn-primary btn-lg">Login</button>
      </form>
   </div>
</div>

</html>

<?php

include_once('includes/connection.php');
session_start();
session_destroy();

if (!empty($_POST)) {
   $result = $pdo->prepare('SELECT id_usuario, nombre, username, password, id_compania_fk FROM t100_usuarios WHERE username = :username AND password = :password');
   $result->execute([
      'username' => $_POST['input_usuario'],
      'password' => $_POST['input_password'],
   ]);
   $result->setFetchMode(PDO::FETCH_ASSOC);
   $result = $result->fetchAll();

   $pdo = null;

   if (count($result) > 0) {
      session_start();
      $_SESSION['usuario'] = $result[0];
      header("Location: dashboard/index.php");
   } else {
      echo "No son correctas las credenciales.";
   }
}
?>
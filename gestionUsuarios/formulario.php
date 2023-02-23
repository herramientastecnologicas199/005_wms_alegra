<?php
include_once('../includes/connection.php');
?>

<!DOCTYPE html>
<html lang="en">

<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">

   <link rel="stylesheet" href="../assets/css/bootstrap.min.css">
   <script src="../assets/js/bootstrap.bundle.min.js"></script>

   <title>Formulario Usuario</title>
</head>

<body>
   <div class="vh-100 d-flex flex-column bg-white justify-content-start align-items-center">
      <?php
      include_once('../includes/navbar.php');
      ?>
      <div class="mt-3 d-flex h-100 w-100 align-items-center justify-content-center">
         <form action="accion.php" method="post" class="shadow py-3 px-5 rounded w-50 bg-body-tertiary">
            <?php
            $listaPerfiles = $pdo->prepare('SELECT * FROM t101_perfiles WHERE id_compania_fk = :id_compania_fk');
            $listaPerfiles->execute([':id_compania_fk' => $_SESSION['usuario']['id_compania_fk']]);
            $listaPerfiles->setFetchMode(PDO::FETCH_ASSOC);
            $listaPerfiles = $listaPerfiles->fetchAll();

            if (isset($_GET['id_usuario'])) {
               $user = $pdo->prepare('SELECT * FROM t100_usuarios WHERE id_usuario = :id_usuario');
               $user->execute([
                  'id_usuario' => $_GET['id_usuario'],
               ]);
               $user->setFetchMode(PDO::FETCH_ASSOC);
               $user = $user->fetchAll();
               $user = $user[0];

               $perfilesUser = $pdo->prepare('SELECT * FROM t103_usuarios_perfiles WHERE id_usuario = :id_usuario');
               $perfilesUser->execute(['id_usuario' => $_GET['id_usuario']]);
               $perfilesUser->setFetchMode(PDO::FETCH_ASSOC);
               $perfilesUser = $perfilesUser->fetchAll();

               $perfilesUserAux = array();
               foreach ($perfilesUser as $perfile) {
                  array_push($perfilesUserAux, $perfile['id_perfil']);
               }

               echo "<input type='hidden' name='accion' value='modificar_usuario' />";
               echo "<input type='hidden' name='id_usuario' value='" . $_GET['id_usuario'] . "' />";
               echo "
               <div class='text-center'>
                  <h3 class='text-uppercase'>Formulario Usuario</h3>
               </div>
               <div class='row g-2 mb-3'>
               <div class='form-floating col'>
                  <input value='" . $user['nombre'] . "' type='text' class='form-control form-control-sm' name='nombre' id='nombre' placeholder='Nombre'>
                  <label for='nombre'>Nombre</label>
               </div>
               <div class='form-floating col'>
                  <input value='" . $user['username'] . "' type='text' class='form-control form-control-sm' name='usuario' id='usuario' placeholder='Usuario'>
                  <label for='Usuario'>Usuario</label>
               </div>
               <div class='form-floating col'>
                  <input value='" . $user['password'] . "' type='text' class='form-control form-control-sm' name='password' id='password' placeholder='Contraseña'>
                  <label for='password'>Contraseña</label>
               </div>
               </div>";

               echo '<div class="mb-2">
               <p>
                  <button class="btn btn-info btn-sm" type="button" data-bs-toggle="collapse" data-bs-target="#collapseExample" aria-expanded="false" aria-controls="collapseExample">
                     Permisos
                  </button>
               </p>
               <div class="collapse" id="collapseExample">
                  <div class="card card-body">
                     <div class="container text-center overflow-y-auto" style="height: 200px;">
                        <div class="row row-cols-3 ">';
               foreach ($listaPerfiles as $perfil) {
                  echo '<div class="col">
                           <div class="form-check">
                              <input class="form-check-input" type="checkbox" value="' . $perfil['id_perfil'] . '" name="perfil_' . $perfil['id_perfil'] . '" id="flexCheckChecked"';
                  if (in_array($perfil['id_perfil'], $perfilesUserAux)) {
                     echo 'checked';
                  }
                  echo '>
                              <label class="form-check-label" for="flexCheckChecked">
                                 ' . $perfil['perfil'] . '
                              </label>
                           </div>
                        </div>';
               }

               echo '</div>
                     </div>
                  </div>
               </div>
            </div>';
            } else {
               echo "<input type='hidden' name='accion' value='crear_usuario' />";
               echo "<div class='text-center mb-2'>
                  <h3 class='text-uppercase'>Formulario Usuario</h3>
               </div>
               <div class='row g-2 mb-3'>
               <div class='form-floating col'>
                  <input type='text' class='form-control form-control-sm' name='nombre' id='nombre' placeholder='Nombre'>
                  <label for='nombre'>Nombre</label>
               </div>
               <div class='form-floating col'>
                  <input type='text' class='form-control form-control-sm' name='usuario' id='usuario' placeholder='Usuario'>
                  <label for='Usuario'>Usuario</label>
               </div>
               <div class='form-floating col'>
                  <input type='password' class='form-control form-control-sm' name='password' id='password' placeholder='Contraseña'>
                  <label for='password'>Contraseña</label>
               </div>
               </div>";
               echo '<div class="mb-2">
               <p>
                  <button class="btn btn-info btn-sm" type="button" data-bs-toggle="collapse" data-bs-target="#collapseExample" aria-expanded="false" aria-controls="collapseExample">
                     Permisos
                  </button>
               </p>
               <div class="collapse" id="collapseExample">
                  <div class="card card-body">
                     <div class="container text-center overflow-y-auto" style="height: 200px;">
                        <div class="row row-cols-3 ">';
               foreach ($listaPerfiles as $key => $perfil) {
                  echo '<div class="col">
                           <div class="form-check">
                              <input class="form-check-input" type="checkbox" value="' . $perfil['id_perfil'] . '" id="flexCheckChecked" name="perfil_' . $perfil['id_perfil'] . '">
                              <label class="form-check-label" for="flexCheckChecked">
                                 ' . $perfil['perfil'] . '
                              </label>
                           </div>
                        </div>';
               }
               echo '</div>
                     </div>
                  </div>
               </div>
            </div>';
            }
            ?>
            <hr />
            <div class="text-center">
               <button type="submit" class="btn btn-primary">Guardar</button>
            </div>
         </form>
      </div>
   </div>
</body>

</html>
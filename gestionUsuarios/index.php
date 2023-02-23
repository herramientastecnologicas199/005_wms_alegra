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

   <title>Gestión Usuarios</title>
</head>

<body>
   <?php
   include_once('../includes/navbar.php');
   ?>
   <div class="container text-center">
      <h1><span class="badge bg-secondary shadow">GESTIÓN USUARIOS</span></h1>
      <div class="mb-3 text-start">
         <a class="btn btn-primary align-items-center" href="formulario.php">
            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-plus-square me-1" viewBox="0 0 16 16">
               <path d="M14 1a1 1 0 0 1 1 1v12a1 1 0 0 1-1 1H2a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1h12zM2 0a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V2a2 2 0 0 0-2-2H2z" />
               <path d="M8 4a.5.5 0 0 1 .5.5v3h3a.5.5 0 0 1 0 1h-3v3a.5.5 0 0 1-1 0v-3h-3a.5.5 0 0 1 0-1h3v-3A.5.5 0 0 1 8 4z" />
            </svg>
            Añadir
         </a>
      </div>
      <div class="table-responsive">
         <table class="table table-bordered table-striped">
            <thead>
               <tr>
                  <th>Nombre</th>
                  <th>Usuarios</th>
                  <th>Estado</th>
                  <th colspan="2">Acción</th>
               </tr>
            </thead>
            <tbody>
               <?php
               $result = $pdo->prepare('SELECT * FROM t100_usuarios WHERE id_compania_fk = :id_compania_fk AND estado = "activo"');
               $result->execute([
                  'id_compania_fk' => $_SESSION['usuario']['id_compania_fk'],
               ]);
               $result->setFetchMode(PDO::FETCH_ASSOC);
               $result = $result->fetchAll();
               foreach ($result as $value) {
                  echo "<tr>";
                  echo '<td>' . $value['nombre'] . '</td>';
                  echo '<td>' . $value['username'] . '</td>';
                  echo '<td>' . $value['estado'] . '</td>';
                  echo '<td><a class="btn btn-warning btn-sm" href="formulario.php?id_usuario=' . $value['id_usuario'] . '"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-pencil-square" viewBox="0 0 16 16">
                     <path d="M15.502 1.94a.5.5 0 0 1 0 .706L14.459 3.69l-2-2L13.502.646a.5.5 0 0 1 .707 0l1.293 1.293zm-1.75 2.456-2-2L4.939 9.21a.5.5 0 0 0-.121.196l-.805 2.414a.25.25 0 0 0 .316.316l2.414-.805a.5.5 0 0 0 .196-.12l6.813-6.814z"/>
                     <path fill-rule="evenodd" d="M1 13.5A1.5 1.5 0 0 0 2.5 15h11a1.5 1.5 0 0 0 1.5-1.5v-6a.5.5 0 0 0-1 0v6a.5.5 0 0 1-.5.5h-11a.5.5 0 0 1-.5-.5v-11a.5.5 0 0 1 .5-.5H9a.5.5 0 0 0 0-1H2.5A1.5 1.5 0 0 0 1 2.5v11z"/>
                     </svg></a></td>';
                  echo '<td>
                  <form method="post" action="accion.php">
                     <input type="hidden" name="accion" value="eliminar_usuario" />
                     <input type="hidden" name="id_usuario" value="' . $value['id_usuario'] . '" />
                     <button type="submit" class="btn btn-danger btn-sm"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-trash" viewBox="0 0 16 16">
                     <path d="M5.5 5.5A.5.5 0 0 1 6 6v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5zm2.5 0a.5.5 0 0 1 .5.5v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5zm3 .5a.5.5 0 0 0-1 0v6a.5.5 0 0 0 1 0V6z"/>
                     <path fill-rule="evenodd" d="M14.5 3a1 1 0 0 1-1 1H13v9a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V4h-.5a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1H6a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1h3.5a1 1 0 0 1 1 1v1zM4.118 4 4 4.059V13a1 1 0 0 0 1 1h6a1 1 0 0 0 1-1V4.059L11.882 4H4.118zM2.5 3V2h11v1h-11z"/>
                     </svg></button>
                  </form>
                  </td>';
                  echo "</tr>";
               }
               ?>
            </tbody>
         </table>
      </div>
   </div>
</body>

</html>
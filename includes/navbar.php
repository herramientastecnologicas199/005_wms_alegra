<?php
session_start();
if (empty($_SESSION) || $_SESSION === null) {
   header("Location: ../index.php");
}

include_once('../includes/connection.php');

$result = $pdo->prepare('SELECT t1.id, t3.* FROM ((t103_usuarios_perfiles AS t1 INNER JOIN t104_perfiles_permisos AS t2 ON t1.id_perfil = t2.id_perfil) INNER JOIN t102_permisos AS t3 ON t2.id_permiso = t3.id_permiso) WHERE t1.id_usuario = :id_usuario');
$result->execute(['id_usuario' => $_SESSION['usuario']['id_usuario']]);
$result->setFetchMode(PDO::FETCH_ASSOC);
$result = $result->fetchAll();

$rutasPermisos = array();
foreach ($result as $permiso) {
   array_push($rutasPermisos, $permiso['ruta_pagina']);
}

$rutaActual = $_SERVER["REQUEST_URI"];
for ($i = strlen($rutaActual) - 1; $i > 0; $i--) {
   if ($rutaActual[$i] == '/') {
      $posicionSlash = $i;
      $rutaActual = substr($rutaActual, 0, $i);
      break;
   }
}

// $validacion = in_array($rutaActual, $rutasPermisos);
// if (!$validacion) {
//    header("Location: ../dashboard/index.php");
// }
?>

<nav class="navbar navbar-expand-lg bg-body-tertiary w-100">
   <div class="container-fluid">
      <a class="navbar-brand" href="/wms/dashboard/index.php">WMS</a>
      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
         <span class="navbar-toggler-icon"></span>
      </button>
      <div class="collapse navbar-collapse" id="navbarSupportedContent">
         <ul class="navbar-nav me-auto mb-2 mb-lg-0">
            <li class="nav-item dropdown">
               <a id="dropdown1" class="nav-link dropdown-toggle" role="button" data-bs-toggle="dropdown" data-bs-auto-close="outside" aria-expanded="false">
                  Menu 1
               </a>
               <ul class="dropdown-menu" aria-labelledby="dropdown1">
                  <li>
                     <a class="<?php if (in_array('/wms/dashboard', $rutasPermisos)) {
                                    echo 'dropdown-item';
                                 } else {
                                    echo 'dropdown-item disabled';
                                 }  ?>" href="/wms/dashboard/index.php">Dashboard</a>
                  </li>
                  <li>
                     <a class="<?php if (in_array('/wms/gestionUsuarios', $rutasPermisos)) {
                                    echo 'dropdown-item';
                                 } else {
                                    echo 'dropdown-item disabled';
                                 }  ?>" href="/wms/gestionUsuarios/index.php">Gestión Usuarios</a>
                  </li>
                  <li>
                     <a class="<?php if (in_array('/wms/pagina3', $rutasPermisos)) {
                                    echo 'dropdown-item';
                                 } else {
                                    echo 'dropdown-item disabled';
                                 }  ?>" href="/wms/pagina3">Pagina 3</a>
                  </li>
                  <li>
                     <a class="<?php if (in_array('/wms/pagina4', $rutasPermisos)) {
                                    echo 'dropdown-item';
                                 } else {
                                    echo 'dropdown-item disabled';
                                 }  ?>" href="/wms/pagina4">Pagina 4</a>
                  </li>
                  <li class="dropend">
                     <a id="dropdown2" class="dropdown-item dropdown-toggle" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        Menu 1.1
                     </a>
                     <ul class="dropdown-menu" aria-labelledby="dropdown2">
                        <li><a class="<?php if (in_array('/wms/pagina6', $rutasPermisos)) {
                                          echo 'dropdown-item';
                                       } else {
                                          echo 'dropdown-item disabled';
                                       }  ?>" href="/wms/pagina6">Pagina 6</a>
                        </li>
                        <li><a class="<?php if (in_array('/wms/pagina7', $rutasPermisos)) {
                                          echo 'dropdown-item';
                                       } else {
                                          echo 'dropdown-item disabled';
                                       }  ?>" href="/wms/pagina7">Pagina 7</a>
                        </li>
                        <li><a class="<?php if (in_array('/wms/pagina8', $rutasPermisos)) {
                                          echo 'dropdown-item';
                                       } else {
                                          echo 'dropdown-item disabled';
                                       }  ?>" href="/wms/pagina8">Pagina 8</a>
                        </li>
                     </ul>
                  </li>
                  <li>
                     <a class="<?php if (in_array('/wms/pagina5', $rutasPermisos)) {
                                    echo 'dropdown-item';
                                 } else {
                                    echo 'dropdown-item disabled';
                                 }  ?>" href="/wms/pagina5">Pagina 5</a>
                  </li>

               </ul>
            </li>
         </ul>
         <div class="ms-auto d-flex">
            <div class="rounded shadow-lg p-1 bg-dark text-white"><?php echo $_SESSION['usuario']['nombre']; ?></div>
            <div class="ms-1">
               <a href="../index.php" class="btn btn-sm btn-primary">
                  <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-box-arrow-right" viewBox="0 0 16 16">
                     <path fill-rule="evenodd" d="M10 12.5a.5.5 0 0 1-.5.5h-8a.5.5 0 0 1-.5-.5v-9a.5.5 0 0 1 .5-.5h8a.5.5 0 0 1 .5.5v2a.5.5 0 0 0 1 0v-2A1.5 1.5 0 0 0 9.5 2h-8A1.5 1.5 0 0 0 0 3.5v9A1.5 1.5 0 0 0 1.5 14h8a1.5 1.5 0 0 0 1.5-1.5v-2a.5.5 0 0 0-1 0v2z" />
                     <path fill-rule="evenodd" d="M15.854 8.354a.5.5 0 0 0 0-.708l-3-3a.5.5 0 0 0-.708.708L14.293 7.5H5.5a.5.5 0 0 0 0 1h8.793l-2.147 2.146a.5.5 0 0 0 .708.708l3-3z" />
                  </svg>
               </a>
            </div>
         </div>
      </div>
   </div>
</nav>
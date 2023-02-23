<?php

try {
   $host = "localhost";
   $dbname = "001_wms";
   $user = "root";
   $password = "";
   $port = 3306;

   $connection = "mysql:host=" . $host . ";port=" . $port . ";dbname=" . $dbname;
   $pdo = new PDO($connection, $user, $password);
   $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
   print_r('Error connection: ' . $e->getMessage());
}

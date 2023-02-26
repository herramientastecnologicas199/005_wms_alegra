<?php
	session_start();
	if (empty($_SESSION["objUsuario"])) {
		echo '<META HTTP-EQUIV="REFRESH" CONTENT="0;URL=../index.php">';
		exit();	
	}
?>
<!DOCTYPE html>
<html>
<head>
	<title>Recoger</title>
	<link rel="stylesheet" type="text/css" href="../resources/estilos.css">
	<link href="../resources/bootstrap.min.css" rel="stylesheet" >
	<script src="../resources/bootstrap.bundle.min.js"></script>
	<meta name="viewport" content="width=device-width,height=device-height,initial-scale=1,minimum-scale=1.0 maximum-scale=1.0" /> 

</head>
<body>
	<div class="header">
		<a href="../gestion/inicio.php" class='btn btn-secondary'>Inicio</a>
		&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
		<a href="depositar.php" class='btn btn-secondary'>Depositar</a>
	</div>
	<h3>Recoger</h3>
	<br>
	<div class="centrar">
		<form>
			<label>Lea Ubicación</label>
			<br>
			<input type="text" name="txtCodigoBarrasUbicacion" required="true" autofocus>
			<br><br>
			<input type="submit" value="Siguiente" class='btn btn-secondary'>
		</form>
	</div>
	<?php
		include_once("../gestion/conexion.php");

		$consultarDocumentos="select * from WMS_Buga_Transferencia_Temporal where estado_transferencia = '0' and DATE_FORMAT(fecha_transferencia, '%Y-%m-%d %H:%i') < DATE_FORMAT(NOW(), '%Y-%m-%d %H:%i')";

		$resultDocumentos=mysqli_query($conexion, $consultarDocumentos);
		if (mysqli_num_rows($resultDocumentos)>0) {
			while ($objDocumento = mysqli_fetch_assoc($resultDocumentos)) {
				$update="update WMS_Buga_Transferencia_Temporal set estado_transferencia = '1' where id = ".$objDocumento["id"];
				mysqli_query($conexion, $update);
			}
		}

		mysqli_close($conexion);
		
		if (!empty($_GET["txtCodigoBarrasUbicacion"])) {

      		$consultarUbicacion="SELECT [f155_id]
	      		FROM [t155_mc_ubicacion_auxiliares]
	      		where f155_rowid_bodega=1
	      		and f155_ind_estado=1
	      		and f155_id='".$_GET["txtCodigoBarrasUbicacion"]."'";

	      	$resultUbicacion=sqlsrv_query($conexionServer, $consultarUbicacion, array(), array("Scrollable"=>SQLSRV_CURSOR_KEYSET));

	      	if (sqlsrv_num_rows($resultUbicacion)>0) {
				echo '<META HTTP-EQUIV="REFRESH" CONTENT="0;URL=recogerCodigoBarrasItem.php?txtCodigoBarrasUbicacion='.$_GET["txtCodigoBarrasUbicacion"].'">';
				exit();	
	      	}else{
	      		echo "<h2>La ubicación ingresada no existe</h2>";
	      	}

      		sqlsrv_close($conexionServer);
		}
	?>
</body>
</html>
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
	</div>
	<h3>Recoger</h3>
	<br>
	<div class="centrar">
		<form action="recogerCantidadItem.php">
			<input type="hidden" name="txtCodigoBarrasUbicacion" value="<?php echo $_GET["txtCodigoBarrasUbicacion"]?>">
			<input type="hidden" name="txtCodigoBarrasItem" value="<?php echo $_GET["txtCodigoBarrasItem"]?>">
			<input type="hidden" name="txtItem" value="<?php echo $_GET["txtItem"]?>">
			<label>Lotes del Item</label>
			<br><br>
			<select name="comboLote" required="true" autofocus>
				<?php
					$arrayOpciones=explode(";", $_GET["txtLotes"]);
					$opciones="";
					for ($i=0; $i < sizeof($arrayOpciones)-1; $i++) { 
						$opciones.="<option>".$arrayOpciones[$i]."</option>";
					}
					echo $opciones;
				?>
			</select>
			<br><br>
			<input type="submit" value="Siguiente" class='btn btn-secondary'>
		</form>
	</div>
</body>
</html>
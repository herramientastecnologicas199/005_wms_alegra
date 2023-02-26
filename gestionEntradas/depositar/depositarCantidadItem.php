<?php
	session_start();
	if (empty($_SESSION["objUsuario"])) {
		echo '<META HTTP-EQUIV="REFRESH" CONTENT="0;URL=../index.php">';
		exit();	
	}
?><!DOCTYPE html>
<html>
<head>
	<title>Cantidad Recogida del Item</title>
	<link rel="stylesheet" type="text/css" href="../resources/estilos.css">
	<link href="../resources/bootstrap.min.css" rel="stylesheet" >
	<script src="../resources/bootstrap.bundle.min.js"></script>
	<meta name="viewport" content="width=device-width,height=device-height,initial-scale=1,minimum-scale=1.0 maximum-scale=1.0" /> 

</head>
<body>
	<div class="header">
		<a href="../gestion/inicio.php" class='btn btn-secondary'>Inicio</a>
	</div>
	
	<h3>Cantidad Recogida del Item</h3>

	<?php
		include_once("../gestion/conexion.php");

		if(!isset($_GET["comboLote"])){
			$_GET["comboLote"]="";
		}

		if(!isset($_GET["txtLote"])){
			$_GET["txtLote"]="";
		}
		
		$consultarItemsTerminal="select * from WMS_Buga_producto_terminal where item = ".$_GET["txtItem"]." and lote_item like '".$_GET["comboLote"]."' and estado_ingreso = '0' and usuario_ingreso = ".$_SESSION["objUsuario"]["id_usuario"]." order by cantidad_ingresada ASC";

		//echo $consultarItemsTerminal;

		$resultItems=mysqli_query($conexion, $consultarItemsTerminal);
		$tabla="";
		$formulario="";
		$cantidadRecogida=0;

		if (mysqli_num_rows($resultItems)>0) {

			while ($objI = mysqli_fetch_assoc($resultItems)) {

				$consultarCantidadDepositada="select SUM(cantidad_depositada) as cantidad_depositada from WMS_Buga_Terminal_Ubicacion_Salida where id_producto_terminal_fk = ".$objI["id_producto"];
							//echo $consultarCantidadDepositada."<br>";
				$resultCantidadDepositada=mysqli_query($conexion, $consultarCantidadDepositada);

				$cantidadDepositada=0;

				if (mysqli_num_rows($resultCantidadDepositada)>0) {
					while ($objC = mysqli_fetch_assoc($resultCantidadDepositada)) {
						$cantidadDepositada=$objC["cantidad_depositada"];
					}
				}

				$cantidadRecogida+=number_format($objI["cantidad_ingresada"]-$cantidadDepositada,2,".","");

				$tabla.="<tr>
				<td>".$objI["item"]."</td>
				<td>".$objI["descripcion_item"]."</td>
				<td>".$objI["unidad_medida_item"]."</td>
				<td>".$objI["lote_item"]."</td>
				<td>".number_format($objI["cantidad_ingresada"]-$cantidadDepositada,2,".","")."</td>
				</tr>";
			}

			$mensaje="";

			if (!empty($_GET["txtCantidad"])) {

				if ($_GET["txtCantidad"] > 0) {

					if (number_format($_GET["txtCantidad"],2,".","") > number_format($_GET["txtCantidadExistencia"],2,".","")) {
						$mensaje="<h2>La cantidad debe de ser menor o igual a ".$_GET["txtCantidadExistencia"]."</h2>";
					}else{
						echo '<META HTTP-EQUIV="REFRESH" CONTENT="0;URL=accion.php?accion='.$_GET["accion"].'&txtCodigoBarrasItem='.$_GET["txtCodigoBarrasItem"].'&txtItem='.$_GET["txtItem"].'&txtCodigoBarrasUbicacion='.$_GET["txtCodigoBarrasUbicacion"].'&txtLote='.$_GET["comboLote"].'&txtCantidad='.$_GET["txtCantidad"].'">';
						exit();	
					}
				}else{
					$mensaje="<h2>La cantidad debe de ser mayor a 0</h2>";
				}
			}else{
				$mensaje="<h2>La cantidad es obligatoria</h2>";
			}

			$formulario='<div class="centrar">
				<form>
					<input type="hidden" name="txtCodigoBarrasItem" value="'.$_GET["txtCodigoBarrasItem"].'">
					<input type="hidden" name="txtItem" value="'.$_GET["txtItem"].'">
					<input type="hidden" name="txtCodigoBarrasUbicacion" value="'.$_GET["txtCodigoBarrasUbicacion"].'">
					<input type="hidden" name="comboLote" value="'.(!empty($_GET["comboLote"]) ? $_GET["comboLote"] : $_GET["txtLote"]).'">
					<input type="hidden" name="txtCantidadExistencia" value="'.$cantidadRecogida.'">
					<input type="hidden" name="accion" value="registrarTraslado">
					<br>
					<input type="number" step="0.01" name="txtCantidad" required="true" min="0" max="'.$cantidadRecogida.'" autofocus>
					<br><br>
					<input type="submit" value="Siguiente" class="btn btn-secondary">
				</form>
			</div>
			'.$mensaje.'
			<br>

			<table>
				<tr>
					<th>Item</th>
					<th>Descripción</th>
					<th>Unidad Medida</th>
					<th>Lote</th>
					<th>Cantidad Recogida</th>
				</tr>';

			echo $formulario.$tabla."</table>";
		}

		mysqli_close($conexion);
	?>

</body>
</html>
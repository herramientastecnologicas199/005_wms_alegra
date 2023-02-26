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
		//print_r($_GET);
		
		include_once("../gestion/conexion.php");

		$consultarItem="SELECT t120_mc_items.f120_id as item, 
			t120_mc_items.f120_descripcion as Nombre_Item, 
			f120_referencia as Referencia_Item, 
			f131_id, 
			f120_id_unidad_inventario as Unidad_inventario_Item
			FROM t131_mc_items_barras
			INNER JOIN t121_mc_items_extensiones ON t131_mc_items_barras.f131_rowid_item_ext = t121_mc_items_extensiones.f121_rowid 
			AND t131_mc_items_barras.f131_id_cia = t121_mc_items_extensiones.f121_id_cia 
			AND t131_mc_items_barras.f131_id = t121_mc_items_extensiones.f121_id_barras_principal
			INNER JOIN t120_mc_items ON t121_mc_items_extensiones.f121_rowid_item = t120_mc_items.f120_rowid
			where t131_mc_items_barras.f131_id='".$_GET["txtCodigoBarrasItem"]."'";

		$resultItem=sqlsrv_query($conexionServer, $consultarItem, array(), array("Scrollable"=>SQLSRV_CURSOR_KEYSET));

		if (sqlsrv_num_rows($resultItem)>0) {
			$objItem="";
			while ($objI = sqlsrv_fetch_array($resultItem, SQLSRV_FETCH_ASSOC)) {
				$objItem=$objI;
			}
		}

		$consultarItemsTerminal="select * from WMS_Buga_Movimiento_Porcelana where item_hijo = ".$_GET["txtItem"]." and estado_movimiento = '0' and usuario_registro = ".$_SESSION["objUsuario"]["id_usuario"]." ORDER BY id_movimiento ASC";

		//echo $consultarItemsTerminal;

		$resultItems=mysqli_query($conexion, $consultarItemsTerminal);
		$tabla="";
		$formulario="";
		$cantidadRecogida=0;

		if (mysqli_num_rows($resultItems)>0) {

			while ($objI = mysqli_fetch_assoc($resultItems)) {

				$consultarCantidadDepositada="select SUM(cantidad_depositada) as cantidad_depositada from WMS_Buga_Movimiento_Porcelana_Salida where id_movimiento_fk = ".$objI["id_movimiento"];
				//echo $consultarCantidadDepositada."<br>";
				$resultCantidadDepositada=mysqli_query($conexion, $consultarCantidadDepositada);

				$cantidadDepositada=0;

				if (mysqli_num_rows($resultCantidadDepositada)>0) {
					while ($objC = mysqli_fetch_assoc($resultCantidadDepositada)) {
						$cantidadDepositada=$objC["cantidad_depositada"];
					}
				}

				$cantidadRecogida+=$objI["cantidad_movimiento"]-$cantidadDepositada;

				$tabla.="<tr>
				<td>".$objI["documento_movimiento"]."</td>
				<td>".$objI["item_hijo"]."</td>
				<td>".$objItem["Nombre_Item"]."</td>
				<td>".$objItem["Referencia_Item"]."</td>
				<td>".$objItem["Unidad_inventario_Item"]."</td>
				<td>".number_format($objI["cantidad_movimiento"]-$cantidadDepositada,2)."</td>
				</tr>";
			}

			$mensaje="";

			if (!empty($_GET["txtCantidad"])) {

				if ($_GET["txtCantidad"] > 0) {

					if ($_GET["txtCantidad"] > $_GET["txtCantidadExistencia"]) {
						$mensaje="<h2>La cantidad debe de ser menor o igual a ".$_GET["txtCantidadExistencia"]."</h2>";
					}else{
						//print_r($_GET);
						echo '<META HTTP-EQUIV="REFRESH" CONTENT="0;URL=accionPorcelana.php?accion=registrarTrasladoMovimiento&idMovimiento='.$_GET["idMovimiento"].'&txtItem='.$_GET["txtItem"].'&txtCodigoBarrasUbicacion='.$_GET["txtCodigoBarrasUbicacion"].'&txtCantidad='.$_GET["txtCantidad"].'">';
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
					<input type="hidden" name="idMovimiento" value="'.$_GET["idMovimiento"].'">
					<input type="hidden" name="txtCodigoBarrasItem" value="'.$_GET["txtCodigoBarrasItem"].'">
					<input type="hidden" name="txtItem" value="'.$_GET["txtItem"].'">
					<input type="hidden" name="txtCodigoBarrasUbicacion" value="'.$_GET["txtCodigoBarrasUbicacion"].'">
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
					<th>Documento</th>
					<th>Item</th>
					<th>Descripción</th>
					<th>Referencia</th>
					<th>Unidad Medida</th>
					<th>Cantidad Recogida</th>
				</tr>';

			echo $formulario.$tabla."</table>";
		}

		mysqli_close($conexion);
	?>

</body>
</html>
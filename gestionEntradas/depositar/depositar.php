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
	<title>Depositar</title>
	<link rel="stylesheet" type="text/css" href="../resources/estilos.css">
	<link href="../resources/bootstrap.min.css" rel="stylesheet" >
	<script src="../resources/bootstrap.bundle.min.js"></script>
	<meta name="viewport" content="width=device-width,height=device-height,initial-scale=1,minimum-scale=1.0 maximum-scale=1.0" /> 
</head>
<body>
	<?php

		include_once("../gestion/conexion.php");
	?>
	<div class="header">
		<a href="../gestion/inicio.php" class='btn btn-secondary'>Inicio</a>
	</div>
	<h3>Depositar</h3>
	<br>
	<div class="centrar">
		<form>
			<label>Lea Código de Barras del Item</label>
			<br>
			<input type="text" name="txtCodigoBarrasItem" required="true" autofocus>
			<br><br>
			<input type="submit" value="Siguiente" class='btn btn-secondary'>
		</form>
	</div>
	<br><br>

	<?php
		if (!empty($_GET["txtCodigoBarrasItem"])) {
			$consultarItem="SELECT t120_mc_items.f120_id, 
			t120_mc_items.f120_descripcion, 
			t131_mc_items_barras.f131_id
			FROM t131_mc_items_barras
			INNER JOIN t121_mc_items_extensiones ON t131_mc_items_barras.f131_rowid_item_ext = t121_mc_items_extensiones.f121_rowid 
			AND t131_mc_items_barras.f131_id_cia = t121_mc_items_extensiones.f121_id_cia 
			AND t131_mc_items_barras.f131_id = t121_mc_items_extensiones.f121_id_barras_principal
			INNER JOIN t120_mc_items ON t121_mc_items_extensiones.f121_rowid_item = t120_mc_items.f120_rowid
			where t131_mc_items_barras.f131_id='".$_GET["txtCodigoBarrasItem"]."'";

			$resultItem=sqlsrv_query($conexionServer, $consultarItem, array(), array("Scrollable"=>SQLSRV_CURSOR_KEYSET));

			if (sqlsrv_num_rows($resultItem)>0) {
				$objItem="";
				
				while ($obj = sqlsrv_fetch_array($resultItem, SQLSRV_FETCH_ASSOC)) {
					$objItem = $obj;
				}

				$consultarExistenciaTerminal="select id_producto from WMS_Buga_producto_terminal where item = ".$objItem["f120_id"]." and estado_ingreso = '0' and usuario_ingreso = ".$_SESSION["objUsuario"]["id_usuario"];
				$resultExistenciaTerminal=mysqli_query($conexion, $consultarExistenciaTerminal);

				if (mysqli_num_rows($resultExistenciaTerminal)>0) {
					echo '<META HTTP-EQUIV="REFRESH" CONTENT="0;URL=depositarCodigoBarrasUbicacion.php?txtCodigoBarrasItem='.$_GET["txtCodigoBarrasItem"].'">';
					exit();	
				}else{
					echo "<h2>El item no está registrado en su terminal</h2>";
				}
			}else{
				echo "<h2>El código de barras no pertenece a un item</h2>";
			}
			
		}
	?>

	<table>
		<tr>
			<th>Item</th>
			<th>Descripción</th>
			<th>Referencia</th>
			<th>Código Barras</th>
			<th>Unidad Medida</th>
			<th>Lote</th>
			<th>Cantidad Recogida</th>
		</tr>

		<?php
			
			$consultarItemsTerminal="select * from WMS_Buga_producto_terminal where estado_ingreso = '0' and usuario_ingreso = ".$_SESSION["objUsuario"]["id_usuario"];

			$resultItems=mysqli_query($conexion, $consultarItemsTerminal);
			$tabla="";

			if (mysqli_num_rows($resultItems)>0) {
				while ($objI = mysqli_fetch_assoc($resultItems)) {

					$consultarCantidadDepositada="select cantidad_depositada from WMS_Buga_Terminal_Ubicacion_Salida where id_producto_terminal_fk = ".$objI["id_producto"];
					//echo $consultarCantidadDepositada."<br>";
					$resultCantidadDepositada=mysqli_query($conexion, $consultarCantidadDepositada);

					$cantidadDepositada=0;

					if (mysqli_num_rows($resultCantidadDepositada)>0) {
						while ($objC = mysqli_fetch_assoc($resultCantidadDepositada)) {
							$cantidadDepositada+=$objC["cantidad_depositada"];
						}
					}

		            $consultarCodigoBarras="SELECT t120_mc_items.f120_id, 
		            t120_mc_items.f120_descripcion, 
		            t131_mc_items_barras.f131_id
		            FROM t131_mc_items_barras
		            INNER JOIN t121_mc_items_extensiones ON t131_mc_items_barras.f131_rowid_item_ext = t121_mc_items_extensiones.f121_rowid 
		            AND t131_mc_items_barras.f131_id_cia = t121_mc_items_extensiones.f121_id_cia 
		            AND t131_mc_items_barras.f131_id = t121_mc_items_extensiones.f121_id_barras_principal
		            INNER JOIN t120_mc_items ON t121_mc_items_extensiones.f121_rowid_item = t120_mc_items.f120_rowid
		            where t120_mc_items.f120_id='".$objI["item"]."'";
		            $resultCodigoBarras=sqlsrv_query($conexionServer, $consultarCodigoBarras, array(), array("Scrollable"=>SQLSRV_CURSOR_KEYSET));
		            $codigo="";

		            if (sqlsrv_num_rows($resultCodigoBarras)>0) { 
		                while ($objC = sqlsrv_fetch_array($resultCodigoBarras, SQLSRV_FETCH_ASSOC)) {
		                    $codigo=$objC["f131_id"];
		                }
		            }

					$tabla.="<tr>
						<td>".$objI["item"]."</td>
						<td>".$objI["descripcion_item"]."</td>
						<td>".$objI["referencia_item"]."</td>
						<td>".$codigo."</td>
						<td>".$objI["unidad_medida_item"]."</td>
						<td>".$objI["lote_item"]."</td>
						<td>".number_format($objI["cantidad_ingresada"]-$cantidadDepositada,4)."</td>
					</tr>";
				}
			}else{
				$tabla="<tr> <td colspan='7'>No se encontraron registros</td> </tr>";
			}

			echo $tabla;
			
		?>
	</table>

	<?php

		mysqli_close($conexion);
		sqlsrv_close($conexionServer);
	?>
</body>
</html>
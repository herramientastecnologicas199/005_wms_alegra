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
	<title>Código de Barras Ubicación</title>
	<link rel="stylesheet" type="text/css" href="../resources/estilos.css">
	<link href="../resources/bootstrap.min.css" rel="stylesheet" >
	<script src="../resources/bootstrap.bundle.min.js"></script>
	<meta name="viewport" content="width=device-width,height=device-height,initial-scale=1,minimum-scale=1.0 maximum-scale=1.0" /> 

</head>
<body>
	<div class="header">
		<a href="../gestion/inicio.php" class='btn btn-secondary'>Inicio</a>
	</div>
	<h3>Código de Barras Ubicación</h3>
	<br>
		<?php
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

				$formulario='<div class="centrar">
						<form>
							<input type="hidden" name="idMovimiento" value="'.$_GET["idMovimiento"].'">
							<input type="hidden" name="txtCodigoBarrasItem" value="'.$_GET["txtCodigoBarrasItem"].'">
							<input type="hidden" name="txtItem" value="'.$objItem["item"].'">
							<label>Lea ubicación</label>
							<br>
							<input type="text" name="txtCodigoBarrasUbicacion" required="true" autofocus>
							<br><br>
							<input type="submit" value="Siguiente" class="btn btn-secondary">
						</form>
					</div>
					<br><br>';

				$tabla="
				<table>
					<tr>
						<th>Item</th>
						<th>Descripción Item</th>
						<th>Referencia Item</th>
						<th>Unidad de Medida</th>
						<th>Ubicación</th>
					</tr>";

				$consultarInfoItem="select * from WMS_Buga_Saldo_Porcelana where item_saldo = ".$objItem["item"]." and cantidad_saldo > 0";

				//echo $consultarInfoItem;

				$resultInfoItem=mysqli_query($conexion, $consultarInfoItem);

				if (mysqli_num_rows($resultInfoItem)>0) {

					while ($objInfo = mysqli_fetch_assoc($resultInfoItem)) {

						$tabla.="<tr>
							<td>".$objInfo["item_saldo"]."</td>
							<td>".$objItem["Nombre_Item"]."</td>
							<td>".$objItem["Referencia_Item"]."</td>
							<td>".$objItem["Unidad_inventario_Item"]."</td>
							<td>".$objInfo["ubicacion_saldo"]."</td>
						</tr>";
					}

					

				}else{
					$tabla.="<tr> <td colspan='5'>Actualmente ese item no tiene cantidades disponibles en una ubicación, por favor ingrese la ubicación donde quiere depositarlo</td> </tr>";
				}
			}else{
				$tabla.="<tr> <td colspan='5'>No se encontraron registros 1</td> </tr>";
			}
			$tabla.="</table>";

			$mensaje="";

			if (!empty($_GET["txtCodigoBarrasUbicacion"])) {
				$consultarUbicacion="SELECT [f155_id]
					FROM [t155_mc_ubicacion_auxiliares]
					where f155_rowid_bodega=1
					and f155_ind_estado=1
					and f155_id='".$_GET["txtCodigoBarrasUbicacion"]."'";

				$resultUbicacion=sqlsrv_query($conexionServer, $consultarUbicacion, array(), array("Scrollable"=>SQLSRV_CURSOR_KEYSET));

				if (sqlsrv_num_rows($resultUbicacion)>0) {
					echo '<META HTTP-EQUIV="REFRESH" CONTENT="0;URL=depositarPorcelanaCantidadItem.php?idMovimiento='.$_GET["idMovimiento"].'&txtCodigoBarrasItem='.$_GET["txtCodigoBarrasItem"].'&txtItem='.$_GET["txtItem"].'&txtCodigoBarrasUbicacion='.$_GET["txtCodigoBarrasUbicacion"].'">';
					exit();	
				}else{
					$mensaje= "<h2>La ubicación es incorrecta</h2>";
				}
			}


			echo $formulario.$mensaje.$tabla;
		?>

	<?php
		mysqli_close($conexion);

		sqlsrv_close($conexionServer);
	?>
</body>
</html>
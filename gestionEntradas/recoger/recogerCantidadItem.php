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

	<script type="text/javascript">     
            function inhabilitarBoton(){
                document.getElementById("btnEnvio").style.display="none";
            }
    </script>

</head>
<body>
	<div class="header">
		<a href="../gestion/inicio.php" class='btn btn-secondary'>Inicio</a>
	</div>
	<h3>Recoger</h3>
	<br>
	<?php
		include_once("../gestion/conexion.php");
		$tabla="";

		if(!isset($_GET["comboLote"])){
			$_GET["comboLote"]="";
		}

		if(!isset($_GET["txtLote"])){
			$_GET["txtLote"]="";
		}

		$consultarInfoItem="SELECT top(20) bi_t400.f120_id AS Item,
				c120.f120_descripcion AS Nombre_Item,
				c120.f120_referencia AS Referencia_Item,
				c120.f120_id_unidad_inventario AS Unidad_inventario_Item,
				c120.desc_cri_mayor_item_2 AS linea,
				c120.desc_cri_mayor_item_3 AS sublinea,
				c120.desc_cri_mayor_item_4 AS marca,
				c120.desc_cri_mayor_item_5 AS responsable_detal,
				bi_t400.f_id_unidad_inventario AS Unidad_de_Medida,
				bi_t400.f_id_bodega AS Bodega,
				bi_t400.f_id_ubicacion_aux AS Ubicacion,
				bi_t400.f_id_lote AS Lote,
				c403.f403_fecha_vcto AS Fecha_vcto_lote_Lote,
				SUM(bi_t400.f_cant_disponible_1) AS Cantidad_disponible_1,
				SUM(bi_t400.f_cant_comprometida_1) AS Cantidad_comprometida_1,
				SUM(bi_t400.f_cant_pendiente_salir_1) AS Cantidad_pendiente_salir_1
				FROM
					(select      f403_id_cia,
					f403_id,
					f120_id f403_id_item,
					f121_rowid,        
					case when f403_ind_estado = 1 then 'Activo' else 'Inactivo' end f403_estado,
					f403_fecha_creacion,
					f403_fecha_vcto,
					f403_lote_prov,        
					f403_fabricante,
					f403_num_lote_fabricante,
					f403_fecha_manufactura,
					f403_notas
					from t403_cm_lotes
					inner join t121_mc_items_extensiones on f121_rowid = f403_rowid_item_ext
					inner join t120_mc_items on f120_rowid = f121_rowid_item) c403 RIGHT OUTER JOIN ((select  f120_id_cia,
					f120_id,
					f121_rowid,         
		            f120_referencia,
		            f120_descripcion,
		            f120_descripcion_corta,
		            f121_fecha_creacion fecha_creacion,
		            Case f121_ind_estado
		                  when 0 then 'Inactivo'
		                  when 1 then 'Activo'
		                  when 2 then 'Bloqueado'
		            end estado,                       
		            case f120_ind_tipo_item
		                  when 1 then 'Inventario'
		                  when 2 then 'Servicio'
		                  when 3 then 'Kits'
		                  when 4 then 'Fantasma'
		            end f120_tipo_item,
		            case when f120_ind_compra = 1 then 'Si' else 'No' end f120_item_para_compra,
		            case when f120_ind_venta = 1 then 'Si' else 'No' end f120_item_para_venta,
		            case when f120_ind_manufactura = 1 then 'Si' else 'No' end f120_item_para_manufactura,        
		            case when f120_ind_lote <> 0 then 'Si' else 'No' end f120_maneja_lote,       
		            case when f120_ind_serial <> 0 then 'Si' else 'No' end f120_maneja_serial,
		            f120_id_unidad_inventario,
		            f120_id_unidad_adicional,
		            f120_id_unidad_orden,
		            f120_id_unidad_empaque,            
		            f120_id_extension1,
		            f117_descripcion,
		            f120_id_extension2,
		            f119_descripcion,           
		            t122_inv.f122_peso  peso,
		            t122_inv.f122_volumen  volumen,            
		            t122_adic.f122_factor  fact_adic,
		            t122_ord.f122_factor  fact_ord,
		            t122_emp.f122_factor   fact_emp,
		            t125.id_cri_mayor_item_1              id_cri_mayor_item_1,
		            t125.desc_cri_mayor_item_1        desc_cri_mayor_item_1,    
		            t125.id_cri_mayor_item_2            id_cri_mayor_item_2,
		            t125.desc_cri_mayor_item_2      desc_cri_mayor_item_2,      
		            t125.id_cri_mayor_item_3            id_cri_mayor_item_3,
		            t125.desc_cri_mayor_item_3      desc_cri_mayor_item_3,     
		            t125.id_cri_mayor_item_4            id_cri_mayor_item_4,
		            t125.desc_cri_mayor_item_4      desc_cri_mayor_item_4,     
		            t125.id_cri_mayor_item_5            id_cri_mayor_item_5,
		            t125.desc_cri_mayor_item_5  desc_cri_mayor_item_5,                              
		            f120_notas       
		            from t120_mc_items t120
		            left join t121_mc_items_extensiones on t120.f120_rowid = f121_rowid_item
		            left join t117_mc_extensiones1_detalle on f117_id_cia = f121_id_cia and f121_id_extension1 = f117_id_extension1 AND f121_id_ext1_detalle = f117_id
		            LEFT JOIN t119_mc_extensiones2_detalle ON f119_id_cia = f121_id_cia AND f121_id_extension2 = f119_id_extension2 AND f121_id_ext2_detalle = f119_id
		            left join t122_mc_items_unidades t122_inv on t120.f120_rowid = t122_inv.f122_rowid_item and t120.f120_id_cia = t122_inv.f122_id_cia  and t120.f120_id_unidad_inventario = t122_inv.f122_id_unidad                                                 
		            left join t122_mc_items_unidades t122_adic on t120.f120_rowid = t122_adic.f122_rowid_item and t120.f120_id_cia = t122_adic.f122_id_cia and t120.f120_id_unidad_adicional = t122_adic.f122_id_unidad
		            left join t122_mc_items_unidades t122_ord on t120.f120_rowid = t122_ord.f122_rowid_item and t120.f120_id_cia = t122_ord.f122_id_cia and t120.f120_id_unidad_orden = t122_ord.f122_id_unidad
		            left join t122_mc_items_unidades t122_emp on t120.f120_rowid = t122_emp.f122_rowid_item and t120.f120_id_cia = t122_emp.f122_id_cia and t120. f120_id_unidad_empaque = t122_emp.f122_id_unidad                                                                                  
		            inner JOIN BI_T125 t125 on T125.rowid_item_ext = f121_rowid
		            INNER join t680_in_biable on  T125.parametro_biable = f680_id AND T125.parametro_biable = '1')c120 RIGHT OUTER JOIN bi_t400 INNER JOIN BI_T125
		            ON BI_T125.rowid_item_ext = bi_t400.f_rowid_item_ext
		            AND BI_T125.parametro_biable = '1' ON ( ( c120.f120_id = bi_t400.f120_id ) AND ( c120.f120_id_cia = bi_t400.f_id_cia ) AND ( c120.f121_rowid = bi_t400.f_rowid_item_ext ) )
		            ) 
		        ON ( ( c403.f403_id = bi_t400.f_id_lote ) AND ( c403.f403_id_cia = bi_t400.f_id_cia ) AND ( c403.f121_rowid = bi_t400.f_rowid_item_lote ) )
		        WHERE bi_t400.f_parametro_biable = '1'
		        AND ( bi_t400.f_id_bodega = '00101' )
		        AND ( bi_t400.f_id_ubicacion_aux = '".$_GET["txtCodigoBarrasUbicacion"]."' )
		        AND bi_t400.f120_id = '".$_GET["txtItem"]."'
		        AND bi_t400.f_id_lote = '".(!empty($_GET["comboLote"]) ? $_GET["comboLote"] : $_GET["txtLote"])."'
		        AND bi_t400.f_cant_disponible_1 > 0
				GROUP BY bi_t400.f120_id,
				c120.f120_descripcion,
				c120.f120_referencia,
				c120.f120_id_unidad_inventario,
				c120.desc_cri_mayor_item_2,
				c120.desc_cri_mayor_item_3,
				c120.desc_cri_mayor_item_4,
				c120.desc_cri_mayor_item_5,
				bi_t400.f_id_unidad_inventario,
				bi_t400.f_id_bodega,
				bi_t400.f_id_ubicacion_aux,
				bi_t400.f_id_lote,
				c403.f403_fecha_vcto";

			//echo $consultarInfoItem;
			$resultInfoItem=sqlsrv_query($conexionServer, $consultarInfoItem, array(), array("Scrollable"=>SQLSRV_CURSOR_KEYSET));
			$objItem="";

			if (sqlsrv_num_rows($resultInfoItem)>0) {
	
				$contador=0;
				while ($objI = sqlsrv_fetch_array($resultInfoItem, SQLSRV_FETCH_ASSOC)) {

					$consultarItemTerminal="select id_producto, sum(cantidad_ingresada) as cantidad_ingresada from WMS_Buga_producto_terminal where estado_ingreso= '0' and item = '".$objI["Item"]."' and lote_item = '".trim($objI["Lote"])."' and ubicacion_origen like '".$_GET["txtCodigoBarrasUbicacion"]."' group by id_producto";
					//echo $consultarItemTerminal."<br>";
					
					$resultItemTerminal=mysqli_query($conexion, $consultarItemTerminal);
					$cantidadTerminal=0;
					$cantidadTerminalSalidas=0;

					if (mysqli_num_rows($resultItemTerminal)>0) {
						while ($objT = mysqli_fetch_assoc($resultItemTerminal)) {
							$cantidadTerminal = $objT["cantidad_ingresada"];

							$consultarSalidas="select sum(cantidad_depositada) as cantidad_depositada from WMS_Buga_Terminal_Ubicacion_Salida where id_producto_terminal_fk = ".$objT["id_producto"];

							$resultSalida=mysqli_query($conexion, $consultarSalidas);

							if (mysqli_num_rows($resultSalida)>0) {
								while ($objS = mysqli_fetch_assoc($resultSalida)) {
									if (!empty($objS["cantidad_depositada"])) {
										$cantidadTerminalSalidas = $objS["cantidad_depositada"];
									}
								}
							}
						}
					}

					//echo "<br>cantidadTerminal: ".$cantidadTerminal." cantidadTerminalSalidas: ".$cantidadTerminalSalidas."<br>";

					$cantidadTerminal-=$cantidadTerminalSalidas;

					$consultarItemDocumento="select sum(cantidad_transferencia) as cantidad_transferencia from WMS_Buga_Transferencia_Temporal where item_transferencia = ".$objI["Item"]." and lote_transferencia like '".trim($objI["Lote"])."' and estado_transferencia = '0' and codigo_barras_ubicacion like '".$_GET["txtCodigoBarrasItem"]."'";

					$resultItemDocumento=mysqli_query($conexion, $consultarItemDocumento);
					$cantidadDocumento=0;

					if (mysqli_num_rows($resultItemDocumento)>0) {
						while ($objD = mysqli_fetch_assoc($resultItemDocumento)) {
							$cantidadDocumento=$objD["cantidad_transferencia"];
						}
					}

					//echo "<h2>".$objI["Item"]."</h2>Cant Ext: ".$objI["Cantidad_disponible_1"]." Cant Ter: ".$cantidadTerminal." cantidadDocumento: ".$cantidadDocumento;

					$objI["Cantidad_disponible_1"]-=$cantidadTerminal;
					$objI["Cantidad_disponible_1"]-=$cantidadDocumento;

					$tabla.="<tr>
					<td>".$objI["Item"]."</td>
					<td>".$objI["Nombre_Item"]."</td>
					<td>".$objI["Referencia_Item"]."</td>
					<td>".$objI["Unidad_inventario_Item"]."</td>
					<td>".$objI["Lote"]."</td>
					<td>".number_format($objI["Cantidad_disponible_1"],2)."</td>
					</tr>";

					$objItem=$objI;
				}

				$mensaje="";
				
				//print_r($_GET);

				if (!empty($_GET["txtCantidad"])) {
					if ($_GET["txtCantidad"] > 0) {
						if ($_GET["txtCantidad"] > $_GET["txtCantidadDisponible"]) {
							$mensaje="<h2>La cantidad debe de ser menor o igual a ".$_GET["txtCantidadDisponible"]."</h2>";
						}else{
							echo '<META HTTP-EQUIV="REFRESH" CONTENT="0;URL=accion.php?accion='.$_GET["accion"].'&txtCodigoBarrasItem='.$_GET["txtCodigoBarrasItem"].'&txtItem='.$_GET["txtItem"].'&txtCodigoBarrasUbicacion='.$_GET["txtCodigoBarrasUbicacion"].'&txtLote='.$_GET["txtLote"].'&txtDescripcion='.$_GET["txtDescripcion"].'&txtUnidadMedida='.$_GET["txtUnidadMedida"].'&txtReferencia='.$_GET["txtReferencia"].'&txtCantidad='.$_GET["txtCantidad"].'&modoEntrada=1&cantidadDisponible='.$_GET["txtCantidadDisponible"].'">';
							exit();	
						}
					}else{
						$mensaje="<h2>La cantidad debe de ser mayor a 0</h2>";
					}
				}else{
					$mensaje="<h2>La cantidad es obligatoria</h2>";
				}

				$descripcionItem = str_replace("#", "", $objItem["Nombre_Item"]);

				$formulario='<div class="centrar">
					<form>
						<input type="hidden" name="txtCodigoBarrasItem" value="'.$_GET["txtCodigoBarrasItem"].'">
						<input type="hidden" name="txtItem" value="'.$_GET["txtItem"].'">
						<input type="hidden" name="txtCodigoBarrasUbicacion" value="'.$_GET["txtCodigoBarrasUbicacion"].'">
						<input type="hidden" name="txtLote" value="'.(!empty($_GET["comboLote"]) ? $_GET["comboLote"] : $_GET["txtLote"]).'">
						<input type="hidden" name="txtDescripcion" value="'.$descripcionItem.'">
						<input type="hidden" name="txtUnidadMedida" value="'.$objItem["Unidad_inventario_Item"].'">
						<input type="hidden" name="txtReferencia" value="'.$objItem["Referencia_Item"].'">
						<input type="hidden" name="txtCantidadDisponible" value="'.$objItem["Cantidad_disponible_1"].'">
						<input type="hidden" name="accion" value="registrarRecoge">
						<label>Cantidad Recoger Item</label>
						<br><br>
						<input type="number" step="0.01" name="txtCantidad" required="true" min="0" max="'.$objItem["Cantidad_disponible_1"].'" autofocus>
						<br><br>
						<input type="submit" value="Aceptar" class="btn btn-secondary" id="btnEnvio" onclick="inhabilitarBoton()">
					</form>
				</div>
				'.$mensaje.'
				<br>

				<table>
					<tr>
						<th>Item</th>
						<th>Descrición</th>
						<th>Referencia</th>
						<th>Unidad Medida</th>
						<th>Lote</th>
						<th>Cantidad disponible</th>
					</tr>';

			echo $formulario.$tabla."</table>";
		}else{
			echo "";
		}

		mysqli_close($conexion);
		sqlsrv_close($conexionServer);
	?>

</body>
</html>
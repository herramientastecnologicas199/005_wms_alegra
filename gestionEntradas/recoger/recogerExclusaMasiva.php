<?php
	include_once("../gestion/navBar.php");
?>
<!DOCTYPE html>
<html>
<head>
	<title>Lista de Entradas de Compras</title>
	<link rel="stylesheet" type="text/css" href="../resources/estilos.css">
	<meta name="viewport" content="width=device-width,height=device-height,initial-scale=1,minimum-scale=1.0 maximum-scale=1.0" /> 

	<script type="text/javascript">
		function validarCantidad(contador, item, cantidaddisponible){
			var cantidad = document.getElementById("txtCantidad"+contador).value;
			cantidad = parseFloat(cantidad).toFixed(2);
			if (!isNaN(cantidad) && cantidad > 0) {

				if (cantidaddisponible < cantidad) {
					document.getElementById("mensajes").innerHTML="<h2>La cantidad ingresada debe de ser menor o igual a "+cantidaddisponible+"</h2>";
					event.preventDefault ? event.preventDefault() : (event.returnValue = false);
				}else{
					document.getElementById("recoge"+contador).href+="&cantidadIngresada="+cantidad;
					document.getElementById("recoge"+contador).removeAttribute("disabled");
					return true;
				}

			}else{
				document.getElementById("mensajes").innerHTML="<h2>Debe de ingresar una cantidad mayor que cero en el item "+item+"</h2>";
				event.preventDefault ? event.preventDefault() : (event.returnValue = false);
			}
		}
	</script>
</head>
<body>
	<?php
		include_once("../gestion/conexion.php");
	?>

	<h3>Lista de Entradas de Compras</h3>

	<?php
		if (!empty($_GET["accion"])) {

			if (!empty($_SESSION["objUsuario"])) {

				$parametros="";
				$contador=0;
				$mensaje="";

				for ($i=0; $i < $_GET["txtLimite"]; $i++) { 
					
					if ($_GET["txtCantidad".$i] !== "" && $_GET["txtUbicacion".$i] !== "") {

						$consultarUbicacion="SELECT [f155_id]
							FROM [t155_mc_ubicacion_auxiliares]
							where f155_rowid_bodega=1
							and f155_ind_estado=1
							and f155_id='".$_GET["txtUbicacion".$i]."'";

						$resultUbicacion=sqlsrv_query($conexionServer, $consultarUbicacion, array(), array("Scrollable"=>SQLSRV_CURSOR_KEYSET));

						if (sqlsrv_num_rows($resultUbicacion) > 0) {
							$consultarCodigoBarrasItem="SELECT t120_mc_items.f120_id, 
					            t120_mc_items.f120_descripcion, 
					            t131_mc_items_barras.f131_id
					            FROM t131_mc_items_barras
					            INNER JOIN t121_mc_items_extensiones ON t131_mc_items_barras.f131_rowid_item_ext = t121_mc_items_extensiones.f121_rowid 
					            AND t131_mc_items_barras.f131_id_cia = t121_mc_items_extensiones.f121_id_cia 
					            AND t131_mc_items_barras.f131_id = t121_mc_items_extensiones.f121_id_barras_principal
					            INNER JOIN t120_mc_items ON t121_mc_items_extensiones.f121_rowid_item = t120_mc_items.f120_rowid
					            where t120_mc_items.f120_id='".$_GET["txtItem".$i]."'";

					        $resultCodigosBarrasItem=sqlsrv_query($conexionServer, $consultarCodigoBarrasItem, array(), array("Scrollable"=>SQLSRV_CURSOR_KEYSET));

					        if (sqlsrv_num_rows($resultCodigosBarrasItem) > 0) {
					        	$objItem="";

					        	while ($obj = sqlsrv_fetch_array($resultCodigosBarrasItem, SQLSRV_FETCH_ASSOC)) {
					        		$objItem = $obj;
					        	}

					        	$parametros.="&txtItem".$contador."=".$_GET["txtItem".$i]."&txtCodigoBarrasItem".$contador."=".$objItem["f131_id"]."&txtUbicacion".$contador."=".$_GET["txtUbicacion".$i]."&txtLote".$contador."=".trim($_GET["txtLote".$i])."&txtCantidad".$contador."=".$_GET["txtCantidad".$i]."&txtUnidadMedida".$contador."=".$_GET["txtUnidadMedida".$i];
					        	$contador++;
					        }else{
					        	$mensaje.="<h2>No se encontró el código de barras del item: ".$_GET["txtItem".$i]."</h2>";
					        }	
						}else{
							$mensaje.="<h2>No se encontró el código de barras de la ubicación: ".$_GET["txtUbicacion".$i]." item: ".$_GET["txtItem".$i]."</h2>";
						}
					}
				}

				if (!empty($mensaje)) {
					echo $mensaje;
				}else if(!empty($parametros)){
					$parametros.="&txtLimite=".$contador;
					echo "<script>window.location.href='accion.php?accion=registrarTrasladoMasivo".$parametros."';</script>";
	    			exit();
				}else{
					echo "No se encontraron parametros";
				}


				sqlsrv_close($conexionServer);
			}else{
				echo "<h1>Debe de iniciar sesión</h1>";
			}
		}
	?>

	<form>
		<input type="hidden" name="accion" value="registrarTrasladoMasivo">
		<div style="float: right; margin: 0px 20px">
			<input type="submit" value="Registrar" class='btn btn-secondary'>
		</div>
		<br>
		<br>
		<table>
			<tr>
				<th>It.</th>
				<th>Descripción</th>
				<th>Ref.</th>
				<th>UM.</th>
				<th>L.</th>
				<th>C. E.</th>
				<th>Cant.</th>
				<th>Ubicación</th>
			</tr>
			<?php
				$consultarDocumentos="select * from WMS_Buga_Transferencia_Temporal where estado_transferencia = '0' and DATE_FORMAT(fecha_transferencia, '%Y-%m-%d %H:%i') < DATE_FORMAT(NOW(), '%Y-%m-%d %H:%i')";

				//echo $consultarDocumentos;
		
				$resultDocumentos=mysqli_query($conexion, $consultarDocumentos);
				if (mysqli_num_rows($resultDocumentos)>0) {
					while ($objDocumento = mysqli_fetch_assoc($resultDocumentos)) {
						$update="update WMS_Buga_Transferencia_Temporal set estado_transferencia = '1' where id = ".$objDocumento["id"];
						mysqli_query($conexion, $update);
					}
				}


				$tabla="";
				$condiciones="";


				if (!empty($_GET["txtItem"])) {

					$consultarItem="SELECT t120_mc_items.f120_id, 
						t120_mc_items.f120_descripcion, 
						t131_mc_items_barras.f131_id
						FROM t131_mc_items_barras
						INNER JOIN t121_mc_items_extensiones ON t131_mc_items_barras.f131_rowid_item_ext = t121_mc_items_extensiones.f121_rowid 
						AND t131_mc_items_barras.f131_id_cia = t121_mc_items_extensiones.f121_id_cia 
						AND t131_mc_items_barras.f131_id = t121_mc_items_extensiones.f121_id_barras_principal
						INNER JOIN t120_mc_items ON t121_mc_items_extensiones.f121_rowid_item = t120_mc_items.f120_rowid
						where t131_mc_items_barras.f131_id='".$_GET["txtItem"]."'";

					$resultItem=sqlsrv_query($conexionServer, $consultarItem, array(), array("Scrollable"=>SQLSRV_CURSOR_KEYSET));

					if (sqlsrv_num_rows($resultItem)>0) {
						$item="";

						while ($obj = sqlsrv_fetch_array($resultItem, SQLSRV_FETCH_ASSOC)) {
							$item=$obj["f120_id"];
						}
						$condiciones=" (bi_t400.f120_id = '".$_GET["txtItem"]."' or bi_t400.f120_id = '".$item."') and ";
					}else{
						$condiciones=" bi_t400.f120_id = '".$_GET["txtItem"]."' and ";
					}

				}

				$consultarItemsEntradas="
					SELECT TOP(30) bi_t400.f120_id AS Item,
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
			        WHERE ".$condiciones." bi_t400.f_parametro_biable = '1'
			        AND ( bi_t400.f_id_bodega = '00101' )
			        AND ( bi_t400.f_id_ubicacion_aux = 'ENTRADAS' )
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

				//echo $consultarItemsEntradas;
				$resultItemsEntradas=sqlsrv_query($conexionServer, $consultarItemsEntradas, array(), array("Scrollable"=>SQLSRV_CURSOR_KEYSET));

				if (sqlsrv_num_rows($resultItemsEntradas)>0) {
					$contador=0;
					while ($objI = sqlsrv_fetch_array($resultItemsEntradas, SQLSRV_FETCH_ASSOC)) {

						$consultarItemTerminal="select id_producto, sum(cantidad_ingresada) as cantidad_ingresada from WMS_Buga_producto_terminal where estado_ingreso= '0' and item = '".$objI["Item"]."' and lote_item = '".trim($objI["Lote"])."' and ubicacion_origen like '999' group by id_producto";
						
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

						$cantidadTerminal-=$cantidadTerminalSalidas;

						$consultarItemDocumento="select sum(cantidad_transferencia) as cantidad_transferencia from WMS_Buga_Transferencia_Temporal where item_transferencia = ".$objI["Item"]." and lote_transferencia like '".trim($objI["Lote"])."' and codigo_barras_ubicacion like '999' and estado_transferencia = '0'";

						$resultItemDocumento=mysqli_query($conexion, $consultarItemDocumento);
						$cantidadDocumento=0;

						if (mysqli_num_rows($resultItemDocumento)>0) {
							while ($objD = mysqli_fetch_assoc($resultItemDocumento)) {
								$cantidadDocumento=$objD["cantidad_transferencia"];
							}
						}

						$objI["Nombre_Item"]=str_replace("#", "", $objI["Nombre_Item"]);

						$objI["Cantidad_disponible_1"]-=$cantidadTerminal;
						$objI["Cantidad_disponible_1"]-=$cantidadDocumento;

						$tabla.="<tr>
							<td>".$objI["Item"]."</td>
							<td>".$objI["Nombre_Item"]."</td>
							<td>".$objI["Referencia_Item"]."</td>
							<td>".$objI["Unidad_inventario_Item"]."</td>
							<td>".$objI["Lote"]."</td>
							<td>".$objI["Cantidad_disponible_1"]."</td>
							<td>
								<input type='number' value='".(!empty($_GET["txtCantidad".$contador]) ? $_GET["txtCantidad".$contador] : $objI["Cantidad_disponible_1"])."' name='txtCantidad".$contador."' min='0' max='".$objI["Cantidad_disponible_1"]."' step='any' class='txtCantidad'>
							</td>
							<td>
								<input type='hidden' name='txtItem".$contador."' value='".$objI["Item"]."'>
								<input type='hidden' name='txtUnidadMedida".$contador."' value='".$objI["Unidad_inventario_Item"]."'>
								<input type='hidden' name='txtLote".$contador."' value='".$objI["Lote"]."'>
								<input type='text' name='txtUbicacion".$contador."' value='".(!empty($_GET["txtUbicacion".$contador]) ? $_GET["txtUbicacion".$contador] : "")."'>
							</td>
						</tr>";
						$contador++;
					}
					$tabla.="<input type='hidden' name='txtLimite' value='".$contador."'>";
				}else{
					$tabla="<tr> <td colspan='8'>No se encontraron registros</td> </tr>";
				}
				echo $tabla;
			?>
		</table>

		<?php
			mysqli_close($conexion);
			sqlsrv_close($conexionServer);
		?>
	</form>

	
</body>
</html>
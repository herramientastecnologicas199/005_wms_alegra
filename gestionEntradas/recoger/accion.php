<?php
	include_once("../gestion/navBar.php");

	include_once("../gestion/conexion.php");

    include_once("generarPlanoTransferencia.php");
    include_once("generarPlanoTransferenciaMasiva.php");

    
    $mensaje="";

	switch ($_GET["accion"]) {
		case 'registrarTraslado':

		//print_r($_GET);
		//echo "<br><br>";

			$consultarCantidadRecogida="select t1.id_producto, t1.cantidad_ingresada, t1.unidad_medida_item, t1.ubicacion_origen, ifnull(SUM(cantidad_depositada),0) as cantidad_depositada from WMS_Buga_producto_terminal as t1 left join WMS_Buga_Terminal_Ubicacion_Salida as t2 on t1.id_producto = t2.id_producto_terminal_fk where item = ".$_GET["txtItem"]." and t1.lote_item like '".$_GET["txtLote"]."' and estado_ingreso like '0' and usuario_ingreso = ".$_SESSION["objUsuario"]["id_usuario"]." group by id_producto order by cantidad_ingresada ASC";
			
			//echo $consultarCantidadRecogida;

			$resultCantidadRecogida=mysqli_query($conexion, $consultarCantidadRecogida);

			if (mysqli_num_rows($resultCantidadRecogida)>0) {
				while ($objItem = mysqli_fetch_assoc($resultCantidadRecogida)) {
					//print_r($objItem);
					//echo "<br><br>";

					$cantidadItem=0;
					if ($_GET["txtCantidad"] > 0) {
						$cantidadItem=$objItem["cantidad_ingresada"]-$objItem["cantidad_depositada"];

						if ($cantidadItem > $_GET["txtCantidad"]) {
							$cantidadItem=$_GET["txtCantidad"];
						}

						$insert="insert into WMS_Buga_Transferencia_Temporal (item_transferencia, codigo_barras_item_transferencia, codigo_barras_ubicacion, lote_transferencia, cantidad_transferencia, fecha_transferencia) values (".$_GET["txtItem"].", '".$_GET["txtCodigoBarrasItem"]."', '".$_GET["txtCodigoBarrasUbicacion"]."', '".$_GET["txtLote"]."', ".$cantidadItem.", '".date("Y-m-d H:i:s")."')";
						
						//echo "insert: ".$insert."<br><br>";

						$result=mysqli_query($conexion, $insert);

						if ($result) {
							$insertarUbicacionSalida="insert into WMS_Buga_Terminal_Ubicacion_Salida (id_producto_terminal_fk, ubicacion_destino, lote_item, cantidad_depositada, fecha_registro_depositar) values(".$objItem["id_producto"].", '".$_GET["txtCodigoBarrasUbicacion"]."', '".$_GET["txtLote"]."', ".$cantidadItem.", '".date("Y-m-d H:i:s")."')";

							//echo "insertarUbicacionSalida: ".$insertarUbicacionSalida."<br><br>";

							$resultUbicacionSalida=mysqli_query($conexion, $insertarUbicacionSalida);

							if ($resultUbicacionSalida) {
								
								$consultarCantidadesDepositadas="select SUM(cantidad_depositada) as cantidad_depositada from WMS_Buga_Terminal_Ubicacion_Salida where id_producto_terminal_fk = ".$objItem["id_producto"];

								//echo "consultarCantidadesDepositadas: ".$consultarCantidadesDepositadas."<br>";

								$resultCantidadesDepositadas=mysqli_query($conexion, $consultarCantidadesDepositadas);

								if (mysqli_num_rows($resultCantidadesDepositadas)>0) {
									while ($objCantidadDepositada = mysqli_fetch_assoc($resultCantidadesDepositadas)) {
										if (number_format($objCantidadDepositada["cantidad_depositada"],2) == number_format($objItem["cantidad_ingresada"],2)) {

											$modificarProductoTerminal="update WMS_Buga_producto_terminal set estado_ingreso = '1' where id_producto = ".$objItem["id_producto"];

											//echo "modificarProductoTerminal: ".$modificarProductoTerminal."<br><br>";

											$resultUpdate=mysqli_query($conexion, $modificarProductoTerminal);

											if (!$resultUpdate) {
												$mensaje="<h1>Error al modificar el estado del item en la terminal</h1>";
											}
										}
									}
								}
								
								generarPlanoTransferencia($conexionCalidad, $_GET["txtItem"], $objItem["ubicacion_origen"], $_GET["txtLote"], $objItem["unidad_medida_item"], $cantidadItem, $_GET["txtCodigoBarrasUbicacion"]);

								$_GET["txtCantidad"]-=$cantidadItem;

								$mensaje="<h1>Registro Exitoso</h1>";
							}else{
								$mensaje="<h1>Error al registrar la ubicación de salida</h1>";	
							}
							
						}else{
							$mensaje="<h1>No se pudo registrar la información</h1>";
						}


					}
				}
			}

			/*$resultCantidadRecogida=mysqli_query($conexion, $consultarCantidadRecogida);

			if (mysqli_num_rows($resultCantidadRecogida)>0) {
				$objProductoTerminal="";
				while ($objC = mysqli_fetch_assoc($resultCantidadRecogida)) {
					if ($objC["cantidad_depositada"]=="") {
						$objC["cantidad_depositada"]=0;
					}
					$objProductoTerminal = $objC;
				}

				if (!empty($objProductoTerminal["id_producto"])) {
					
					if (number_format($objProductoTerminal["cantidad_ingresada"] - $objProductoTerminal["cantidad_depositada"],2) < number_format($_GET["txtCantidad"],2)) {
						$mensaje="<h1>Error, no puede ingresar una cantidad mayor a la que recogió, cantidad pendiente por depositar: ".number_format($objProductoTerminal["cantidad_ingresada"] - $objProductoTerminal["cantidad_depositada"],2)."</h1>";
					}else{
						$insert="insert into WMS_Buga_Transferencia_Temporal (item_transferencia, codigo_barras_item_transferencia, codigo_barras_ubicacion, lote_transferencia, cantidad_transferencia, fecha_transferencia) values (".$_GET["txtItem"].", '".$_GET["txtCodigoBarrasItem"]."', '".$_GET["txtCodigoBarrasUbicacion"]."', '".$_GET["txtLote"]."', ".$_GET["txtCantidad"].", '".date("Y-m-d H:i:s")."')";
						$result=mysqli_query($conexion, $insert);

						if ($result) {
							$insertarUbicacionSalida="insert into WMS_Buga_Terminal_Ubicacion_Salida (id_producto_terminal_fk, ubicacion_destino, lote_item, cantidad_depositada) values(".$objProductoTerminal["id_producto"].", '".$_GET["txtCodigoBarrasUbicacion"]."', '".$_GET["txtLote"]."', ".$_GET["txtCantidad"].")";
							$resultUbicacionSalida=mysqli_query($conexion, $insertarUbicacionSalida);

							if ($resultUbicacionSalida) {
								
								$cantidadDepositada=number_format(($objProductoTerminal["cantidad_depositada"] + $_GET["txtCantidad"]),4);
								$cantidadIngresada=number_format($objProductoTerminal["cantidad_ingresada"],4);

								if ($cantidadDepositada === $cantidadIngresada) {
							
									$modificarProductoTerminal="update WMS_Buga_producto_terminal set estado_ingreso = '1' where id_producto = ".$objProductoTerminal["id_producto"];
									$resultUpdate=mysqli_query($conexion, $modificarProductoTerminal);

									if (!$resultUpdate) {
										$mensaje="<h1>Error al modificar el estado del item en la terminal</h1>";
									}
								}
								
								generarPlanoTransferencia($_GET["txtItem"], $objProductoTerminal["ubicacion_origen"], $_GET["txtLote"], $objProductoTerminal["unidad_medida_item"], $_GET["txtCantidad"], $_GET["txtCodigoBarrasUbicacion"]);
								$mensaje="<h1>Registro Exitoso</h1>";
							}else{
								$mensaje="<h1>Error al registrar la ubicación de salida</h1>";	
							}
							
						}else{
							$mensaje="<h1>No se pudo registrar la información</h1>";
						}
					}
				}else{
					$mensaje="<h1>No se encontró la información del item en la terminal</h1>";	
				}
				
			}else{
				$mensaje="<h1>No se encontró la información del item en la terminal</h1>";
			}*/
			break;
		
		case 'registrarRecoge':

			$consultarDuplicado="select id_producto, SUM(cantidad_ingresada) as cantidad_ingresada from WMS_Buga_producto_terminal where item = ".$_GET["txtItem"]." and unidad_medida_item = '".$_GET["txtUnidadMedida"]."' and lote_item like '".trim($_GET["txtLote"])."' and ubicacion_origen='".$_GET["txtCodigoBarrasUbicacion"]."' and usuario_ingreso = ".$_SESSION["objUsuario"]["id_usuario"]." and estado_ingreso like '0' HAVING SUM( cantidad_ingresada ) > 0";


			//echo $consultarDuplicado;

			$resultDuplicado = mysqli_query($conexion, $consultarDuplicado);

            if (mysqli_num_rows($resultDuplicado)>0) {
                $objItem="";

                while ($obj = mysqli_fetch_assoc($resultDuplicado)) {
            	    $objItem=$obj;
                }

                if ($objItem["cantidad_ingresada"] <= $_GET["cantidadDisponible"]) {

                	$updateCantidad="update WMS_Buga_producto_terminal set cantidad_ingresada = ".number_format($objItem["cantidad_ingresada"]+$_GET["txtCantidad"],2)." where id_producto = ".$objItem["id_producto"];
 	
	                //echo $updateCantidad."<br>";

	                $resultUpdate=mysqli_query($conexion, $updateCantidad);
	                if (!$resultUpdate) {
	                    $mensaje.= "No se actualizo el registro ".$objItem["id_producto"];
	                }
                }
                                   
            }else{

            	if ($_GET["txtCantidad"] <= $_GET["cantidadDisponible"]) {
            		$cantidad = number_format($_GET["txtCantidad"],2);

	            	$cantidad = str_replace(",", "", $cantidad);

					$insertItemTerminal="insert into WMS_Buga_producto_terminal (item, descripcion_item, referencia_item, unidad_medida_item, lote_item, cantidad_ingresada, ubicacion_origen, modo_entrada, usuario_ingreso, estado_ingreso) values ('".$_GET["txtItem"]."', '".utf8_decode($_GET["txtDescripcion"])."', '".$_GET["txtReferencia"]."', '".$_GET["txtUnidadMedida"]."', '".trim($_GET["txtLote"])."', '".$cantidad."', '".$_GET["txtCodigoBarrasUbicacion"]."', '".$_GET["modoEntrada"]."', ".$_SESSION["objUsuario"]["id_usuario"].", '0')";

					//echo $insertItemTerminal;

					$resultinsert=mysqli_query($conexion, $insertItemTerminal);
					if (!$resultinsert) {
						$mensaje.= "Error al registra la información";
					}
            	}
            	
			}

			if (empty($mensaje)) {

				if ($_GET["modoEntrada"]==0) {
					echo '<META HTTP-EQUIV="REFRESH" CONTENT="0;URL=recogerExclusa.php">';
					exit();	
				}else{
					echo '<META HTTP-EQUIV="REFRESH" CONTENT="0;URL=recoger.php">';
					exit();	
				}
			}

			break;

		case 'registrarTrasladoMasivo':
			$arrayItem=[];
			$mensaje="";

			for ($i=0; $i < $_GET["txtLimite"]; $i++) { 

				$objItemTerminal="";

				$consultarDuplicado="select * from WMS_Buga_producto_terminal where item = ".$_GET["txtItem".$i]." and unidad_medida_item like '".$_GET["txtUnidadMedida".$i]."' and lote_item like '".trim($_GET["txtLote".$i])."' and ubicacion_origen='ENTRADAS' and usuario_ingreso = ".$_SESSION["objUsuario"]["id_usuario"]." and estado_ingreso like '0'";

				$resultDuplicado = mysqli_query($conexion, $consultarDuplicado);

				if (mysqli_num_rows($resultDuplicado)>0) {
					$objItem="";

					while ($obj = mysqli_fetch_assoc($resultDuplicado)) {
						$objItem=$obj;
						$objItemTerminal=$obj;
					}

					$updateCantidad="update WMS_Buga_producto_terminal set cantidad_ingresada = ".number_format($objItem["cantidad_ingresada"]+$_GET["txtCantidad".$i],2)." where id_producto = ".$objItem["id_producto"];

					$resultUpdate=mysqli_query($conexion, $updateCantidad);

					if ($resultUpdate) {

					}else{
						$mensaje.="<h2>Error al registrar el item ".$_GET["txtItem".$i]." en la terminal</h2>";
					}

				}else{
					$insertItemTerminal="insert into WMS_Buga_producto_terminal (item, descripcion_item, referencia_item, unidad_medida_item, lote_item, cantidad_ingresada, ubicacion_origen, modo_entrada, usuario_ingreso, estado_ingreso) values ('".$_GET["txtItem".$i]."', '', '', '".$_GET["txtUnidadMedida".$i]."', '".trim($_GET["txtLote".$i])."', ".$_GET["txtCantidad".$i].", 'ENTRADAS', '0', ".$_SESSION["objUsuario"]["id_usuario"].", '0')";

					$resultinsert=mysqli_query($conexion, $insertItemTerminal);

					if ($resultinsert) {
						
						$consultarRegistro="select * from WMS_Buga_producto_terminal where item = ".$_GET["txtItem".$i]." and lote_item like '".trim($_GET["txtLote".$i])."' and unidad_medida_item like '".$_GET["txtUnidadMedida".$i]."' and cantidad_ingresada = ".$_GET["txtCantidad".$i]." and ubicacion_origen like 'ENTRADAS' and modo_entrada like '0' and estado_ingreso like '0' and usuario_ingreso = ".$_SESSION["objUsuario"]["id_usuario"]." order by id_producto DESC";

						$resultConsulta=mysqli_query($conexion, $consultarRegistro);

						if (mysqli_num_rows($resultConsulta)>0) {
							while ($obj = mysqli_fetch_assoc($resultConsulta)) {
								$objItemTerminal=$obj;
							}
						}

					}else{
						$mensaje.="<h2>Error al registrar el item ".$_GET["txtItem".$i]." en la terminal</h2>";
					}
				}

				if (!empty($objItemTerminal["id_producto"])) {
					$insert="insert into WMS_Buga_Transferencia_Temporal (item_transferencia, codigo_barras_item_transferencia, codigo_barras_ubicacion, lote_transferencia, cantidad_transferencia, fecha_transferencia) values (".$_GET["txtItem".$i].", '".$_GET["txtCodigoBarrasItem".$i]."', '".$_GET["txtUbicacion".$i]."', '".trim($_GET["txtLote".$i])."', ".$_GET["txtCantidad".$i].", '".date("Y-m-d H:i:s")."')";
					$result=mysqli_query($conexion, $insert);

					if ($result) {
						$insertarUbicacionSalida="insert into WMS_Buga_Terminal_Ubicacion_Salida (id_producto_terminal_fk, ubicacion_destino, lote_item, cantidad_depositada, fecha_registro_depositar) values(".$objItemTerminal["id_producto"].", '".$_GET["txtUbicacion".$i]."', '".trim($_GET["txtLote".$i])."', ".$_GET["txtCantidad".$i].", '".date("Y-m-d H:i:s")."')";
						$resultUbicacionSalida=mysqli_query($conexion, $insertarUbicacionSalida);

						if ($resultUbicacionSalida) {
							
							$cantidadDepositada=number_format(( $_GET["txtCantidad".$i]),4);
							$cantidadIngresada=number_format($objItemTerminal["cantidad_ingresada"],4);

							if (number_format($cantidadDepositada,2) == number_format($cantidadIngresada,2)) {
								
								$modificarProductoTerminal="update WMS_Buga_producto_terminal set estado_ingreso = '1' where id_producto = ".$objItemTerminal["id_producto"];
								$resultUpdate=mysqli_query($conexion, $modificarProductoTerminal);

								if (!$resultUpdate) {
									$mensaje.="<h1>Error al modificar el estado del item ".$_GET["txtItem".$i]." en la terminal</h1>";
								}
							}
									
							$arrayItem[]= array('item'=>$_GET["txtItem".$i], 'ubicacionOrigen'=>$objItemTerminal["ubicacion_origen"], 'lote'=>trim($_GET["txtLote".$i]), 'unidadMedida'=>$objItemTerminal["unidad_medida_item"], 'cantidad'=>$_GET["txtCantidad".$i], 'ubicacionDestino'=>$_GET["txtUbicacion".$i]);
							$mensaje.="<h1>Registro Exitoso del item ".$_GET["txtItem".$i]."</h1>";
						}else{
							$mensaje.="<h1>Error al registrar la ubicación de salida</h1>";	
						}
								
					}else{
						$mensaje.="<h1>No se pudo registrar la información</h1>";
					}
				}

			}

			if (sizeof($arrayItem)>0) {
				generarPlanoTransferenciaMasiva($conexionCalidad, $arrayItem);
			}

			break;
	}

	
	echo $mensaje;
	mysqli_close($conexion);
	sqlsrv_close($conexionServer);
?>
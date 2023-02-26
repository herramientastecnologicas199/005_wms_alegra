<?php
	include_once("../gestion/navBar.php");
?>
<!DOCTYPE html>
<html>
<head>
	<title>Leer Código de Barras Item</title>
	<link rel="stylesheet" type="text/css" href="../resources/estilos.css">
      <meta name="viewport" content="width=device-width,height=device-height,initial-scale=1,minimum-scale=1.0 maximum-scale=1.0" /> 

      <script type="text/javascript">
                  
            function inhabilitarBoton(){
                  document.getElementById("btnEnvio").style.display="none";
            }

      </script>
</head>
<body>
      <?php
            //print_r($_GET);
            include_once("../gestion/conexion.php");
            
            $consultarCodigoBarras="SELECT t120_mc_items.f120_id, 
            t120_mc_items.f120_descripcion, 
            t131_mc_items_barras.f131_id
            FROM t131_mc_items_barras
            INNER JOIN t121_mc_items_extensiones ON t131_mc_items_barras.f131_rowid_item_ext = t121_mc_items_extensiones.f121_rowid 
            AND t131_mc_items_barras.f131_id_cia = t121_mc_items_extensiones.f121_id_cia 
            AND t131_mc_items_barras.f131_id = t121_mc_items_extensiones.f121_id_barras_principal
            INNER JOIN t120_mc_items ON t121_mc_items_extensiones.f121_rowid_item = t120_mc_items.f120_rowid
            where t120_mc_items.f120_id='".$_GET["item"]."'";
            $resultCodigoBarras=sqlsrv_query($conexionServer, $consultarCodigoBarras, array(), array("Scrollable"=>SQLSRV_CURSOR_KEYSET));

            if (sqlsrv_num_rows($resultCodigoBarras)>0) {
                  $codigo="";
                  while ($objC = sqlsrv_fetch_array($resultCodigoBarras, SQLSRV_FETCH_ASSOC)) {
                        $codigo=$objC["f131_id"];
                  }
            }
      ?>
      <h3>Leer Código de Barras Item</h3>
      <br>

	<form>
		<input type="hidden" name="item" value="<?php echo($_GET["item"])?>">
		<input type="hidden" name="descripcion" value="<?php echo($_GET["descripcion"])?>">
		<input type="hidden" name="referencia" value="<?php echo($_GET["referencia"])?>">
		<input type="hidden" name="unidadMedida" value="<?php echo($_GET["unidadMedida"])?>">
            <input type="hidden" name="lote" value="<?php echo($_GET["lote"])?>">
		<input type="hidden" name="cantidadIngresada" value="<?php echo($_GET["cantidadIngresada"])?>">
            <input type="hidden" name="cantidadDisponible" value="<?php echo($_GET["cantidadDisponible"])?>">
            <table>
                  <tr>
                        <td>Codigo de Barras del Item: <?php echo $codigo; ?></td>
                  </tr>
                  <tr>
                        <td>
                              <input type="text" name="txtCodigoBarras" required="true" class="campo" autofocus>
                        </td>
                  </tr>
            </table>
            <br>
		<input type="submit" value="Aceptar" class='btn btn-secondary' id="btnEnvio" onclick="inhabilitarBoton()">
	</form>
      <br>
	<?php
		if (!empty($_GET["txtCodigoBarras"])) {

			if (!empty($codigo)) {
				if ($codigo == $_GET["txtCodigoBarras"]) {
                              echo '<META HTTP-EQUIV="REFRESH" CONTENT="0;URL=accion.php?accion=registrarRecoge&txtItem='.$_GET["item"].'&txtDescripcion='.$_GET["descripcion"].'&txtReferencia='.trim($_GET["referencia"]).'&txtUnidadMedida='.$_GET["unidadMedida"].'&txtLote='.trim($_GET["lote"]).'&txtCantidad='.$_GET["cantidadIngresada"].'&txtCodigoBarrasUbicacion=ENTRADAS&modoEntrada=0&cantidadDisponible='.$_GET["cantidadDisponible"].'">';
                              exit();	
				}else{
					echo "<p class='error'>El código de barras leido no corresponde al Item seleccionado</p>";
				}
			}
      	
      		sqlsrv_close($conexionServer);
		}
	?>
</body>
</html>
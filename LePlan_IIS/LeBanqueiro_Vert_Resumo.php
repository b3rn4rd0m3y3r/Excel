<style>
	/* Comportamentos para Gráfico de barras verticais */
	DIV#Graf1 {float: left; display: inline-block;height: 500px;}
	DIV#Graf2 {float: left; display: inline-block;height: 500px;}
	H2 {
		background: brown;
		color: white;
		font-family: Arial;
		padding: 3px;
		border-radius: 5px;
		}
	TABLE TR  TD > DIV#Graf1 > DIV {
		float: left;
		font-size: 10px;
		background: red; 
		/* height trocado por width */
		width: 35px;
		/* margin-top: 8px; */
		margin-left: 8px;
		font-family: Arial; 
		/* padding-top:5px; */
		}
	DIV#Graf2 > DIV {
		float: left;
		font-size: 10px;
		background: teal; 
		/* height trocado por width */
		width: 35px;
		/* margin-top: 8px; */
		margin-left: 8px;
		font-family: Arial; 
		/* padding-top:5px; */
		position: relative;
		}
	TABLE TR TD {font-family: Arial; font-size: 10px;}
	TABLE TR TH {background: gray; color: white;font-family: Arial; font-size: 10px;}
</style>
<?php
// NOVO
if( $_GET["Mun"] ) {
	$MUN_get = $_GET["Mun"];
	} else {
	$MUN_get = "";
	}
  if( $_GET["Uf"] ) {
	$UF_get = $_GET["Uf"];
	} else {
	$UF_get = "";
	}
  // Header
  header('Content-Type: text/html; charset=iso-8859-1');
  // novo
  echo "<h2>PERFORMANCE DAS CONTAS CORRENTES POR ESTADO E MUNICÍPIO</h2>";
  // Conexão
  $conn = odbc_connect("Driver={Microsoft Excel Driver (*.xls)};DriverId=790;Dbq=Banqueiro.xls;DefaultDir=C:\\Inetpub\\wwwroot\\dbase" , '', '');
  echo "<br>";
  // Variáveis para quebra
  $q = "@#$%";
  $UF_ant = "@#$%";
  // Somas UF
  $UF_soma_valor = 0;
  $UF_soma_valor_total = 0;
  $CID_ant = "@#$%";
  // Somas Municípios
  $CID_soma_valor = 0;
  $CID_soma_valor_total = 0;
  // Sql
  $sql = "SELECT [Id],[UF],[Cidade],[CPF],[Conta],[Valor] FROM [tab1$] ";
  if( $UF_get != "" ){
	$sql = $sql . " WHERE [UF] = '" . $UF_get . "'";
	}
  // NOVO
  if( $MUN_get != "" ){
	$sql = $sql . " AND [Cidade] = '" . $MUN_get . "'";
	}
  $sql = $sql . " ORDER BY [UF], [Cidade], [CPF], [Conta]";
  $stmt = odbc_exec($conn, $sql);
  //var_dump($stmt);
  // Tabela
  if( $UF_get != "" ){
	echo "<table border=1 cellspacing=0 cellpadding=4>";
	echo "<tr><th>Id<th>UF<th>Cidade<th>CPF<th>Conta<th>Valor</tr>";
	}
  // Loop row register
  // Pode tentar odbc_fetch_row também
  $t = "</td><td>";
  $v = "&nbsp;";
  // Maior valor de UF e CIDade
  $UF_max = 0;
  $CID_max = 0;
  // Arrays UF e Cidade
  $arrUF = array();
  $arrCID = array();
  while($row = odbc_fetch_array($stmt)){
	$UF = $row['UF'];
	$CID = $row['Cidade'];
	$valor = (float) $row['Valor'];
	// Quebra CID
	if( $CID != $CID_ant ){
		if( $CID_ant != $q ){
			// NOVO
			if( $UF_get != "" ){
				echo "<tr><td>" . $v . $t . $v . $t . "<b>" . $CID_ant . "</b>" . $t . $v. $t . "Soma:" . $t . $CID_soma_valor . "</td></tr>";
				}
			// Alimenta arrCID
			$arrCID[$UF_ant . $CID_ant] = $CID_soma_valor;
			}
		$CID_ant = $CID;
		// Acúmulo na quebra
		$CID_soma_valor_total += $CID_soma_valor;
		$CID_soma_valor = 0;
		} 
	// Quebra UF
	if( $UF != $UF_ant ){
		if( $UF_ant != $q ){
			// NOVO
			if( $UF_get != "" ){
				echo "<tr><td>" . $v . $t . "<b>" . $UF_ant . "</b>" . $t . $v . $t . $v. $t . "Soma:" . $t . $UF_soma_valor . "</td></tr>";
				}
			// Alimenta arrUF
			$arrUF[$UF_ant] = $UF_soma_valor;
			}
		$UF_ant = $UF;
		// Acúmulo na quebra
		$UF_soma_valor_total += $UF_soma_valor;
		$UF_soma_valor = 0;
		} 
	// Linha de registro - NOVO
	if( $UF_get != "" && $MUN_get != "" ){
		echo "<tr><td>" . $row['Id'] . $t . $UF . $t . $row['Cidade'] . $t . $row['CPF']. $t . $row['Conta'] . $t . $valor . "</td></tr>";
		}
	// Acúmulo
	$UF_soma_valor += (float) $valor;
	$CID_soma_valor += (float) $valor;
	}
	// Alimenta arrCID
	$arrCID[$UF_ant . $CID_ant] = $CID_soma_valor;
	// Alimenta arrUF
	$arrUF[$UF_ant] = $UF_soma_valor;
	// Acúmulo da última quebra
	$CID_soma_valor_total += $CID_soma_valor;
	// NOVO
	if( $UF_get != "" ){
		echo "<tr><td>" . $v . $t . $v . $t . "<b>" . $CID . "</b>" . $t . $v. $t . "Soma:" . $t . $CID_soma_valor . "</td></tr>";
		}
	//echo "<tr><td>" . $v . $t . $v . $t . $CID . $t . $v. $t . "Total:" . $t . $CID_soma_valor_total . "</td></tr>";
	$UF_soma_valor_total += $UF_soma_valor;
	// NOVO
	if( $UF_get != "" ){
		echo "<tr><td>" . $v . $t . "<b>" . $UF . "</b>" . $t . $v . $t . $v. $t . "Soma:" . $t . $UF_soma_valor . "</td></tr>";
		echo "<tr><td>" . $v . $t . $v . $t . $v . $t . $v. $t . "Total:" . $t . $UF_soma_valor_total . "</td></tr>";
		echo "</table>";
		}
	// Exibe arranjos
	echo "<br>";
	echo "<table border=1 cellspacing=0 cellpadding=4>";
	// 2 - com loop foreach
	foreach( $arrUF as $key=>$value ){
		if( (float) $value > $UF_max ) $UF_max = (float) $value;
		echo "<tr><td>" . $key . "</td><td>" . $value . "</td></tr>";
		}
	echo "</table>";
	echo "<br>";
	// NOVO
	if( $UF_get != "" ){
		echo "<table border=1 cellspacing=0 cellpadding=4>";
		foreach( $arrCID as $key=>$value ){
			if( (float) $value > $CID_max ) $CID_max = (float) $value;
			$UF = substr($key, 0,2);
			$CID = substr($key, 2);
			echo "<tr><td>" . $UF . "</td><td>" . $CID . "</td><td>" . $value . "</td></tr>";
			}
		echo "</table>";
		}
	//print_r($UF_max);
	echo "<br>";
	// GRÁFICOS
	$UF_escala = $UF_max/400;
	$UF_altura = 100;
	echo "<br>";
	//print_r($UF_escala);
	echo "<br>";
	echo "<table border=0>";
	echo "<tr>";
	echo "<td>";
	echo "<div id=\"Graf1\">";
	foreach( $arrUF as $key=>$value ){
		echo "<div style=\"cursor: pointer;padding-top:" . ($UF_max/$UF_escala - $value/$UF_escala + $UF_altura) . ";height: " . $value/$UF_escala . "px\" onclick=\"window.location.href = '?Uf=" . $key . "';\">&nbsp;" . $key . "</div>";
		//echo "<div style=\"cursor: pointer;width: " . $value/$UF_escala . "px\" onclick=\"window.location.href = '?Uf=" . $key . "';\">&nbsp;" . $key . "</div>";
		}	
	echo "</div>";
	
	$CID_escala = $CID_max/400;
	$CID_altura = 100;
	echo "</td><td>";
	if( $UF_get != "" ){
		echo "<div id=\"Graf2\">";
		foreach( $arrCID as $key=>$value ){
			$UF = substr($key, 0,2);
			$CID = substr($key, 2);
			echo "<div style=\"cursor: pointer;font-size:8px;margin-top:" . ( $CID_max/$CID_escala - $value/$CID_escala+$CID_altura) . ";height: " . $value/$CID_escala . "px\" onclick=\"window.location.href = '?Uf=" . $UF . "&Mun=" . $CID . "';\">&nbsp;"  . $UF . " " . $CID . "</div>";
			//echo "<div style=\"width: " . $value/$CID_escala . "px\">&nbsp;"  . $UF . " " . $CID . "</div>";
			}	
		echo "</div>";
		}
	
	echo "</td>";
	echo "</tr>";
	echo "</table>";
	// NOVO
	echo "<a href=\"?\">VOLTAR AO INÍCIO</a>";
?>
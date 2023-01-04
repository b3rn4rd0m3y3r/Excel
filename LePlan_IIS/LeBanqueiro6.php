<style>
	DIV#Graf1 DIV {float: clear; background: red; height: 20px;margin-top: 8px;font-family: Arial; font-size: 12px;padding-top:5px;}
	DIV#Graf2 DIV {float: clear; background: teal; height: 20px;margin-top: 8px;font-family: Arial; font-size: 12px;padding-top:5px;}
	TABLE TR TD {font-family: Arial; font-size: 10px;}
	TABLE TR TH {background: gray; color: white;font-family: Arial; font-size: 10px;}
</style>
<?php
  // Header
  header('Content-Type: text/html; charset=iso-8859-1');
  // Conexão
  $conn = odbc_connect("Driver={Microsoft Excel Driver (*.xls)};DriverId=790;Dbq=Banqueiro.xls;DefaultDir=C:\\Inetpub\\wwwroot\\dbase" , '', '');
  var_dump($conn);
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
  $sql = $sql . " ORDER BY [UF], [Cidade], [CPF], [Conta]";
  $stmt = odbc_exec($conn, $sql);
  var_dump($stmt);
  // Tabela
  echo "<table border=1 cellspacing=0 cellpadding=4>";
  echo "<tr><th>Id<th>UF<th>Cidade<th>CPF<th>Conta<th>Valor</tr>";
  // Loop row register
  // Pode tentar odbc_fetch_row também
  $t = "</td><td>";
  $v = "&nbsp;";
  // NOVO - Maior valor de UF e CIDade
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
			echo "<tr><td>" . $v . $t . $v . $t . "<b>" . $CID_ant . "</b>" . $t . $v. $t . "Soma:" . $t . $CID_soma_valor . "</td></tr>";
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
			echo "<tr><td>" . $v . $t . "<b>" . $UF_ant . "</b>" . $t . $v . $t . $v. $t . "Soma:" . $t . $UF_soma_valor . "</td></tr>";
			// Alimenta arrUF
			$arrUF[$UF_ant] = $UF_soma_valor;
			}
		$UF_ant = $UF;
		// Acúmulo na quebra
		$UF_soma_valor_total += $UF_soma_valor;
		$UF_soma_valor = 0;
		} 
	// Linha de registro
	echo "<tr><td>" . $row['Id'] . $t . $UF . $t . $row['Cidade'] . $t . $row['CPF']. $t . $row['Conta'] . $t . $valor . "</td></tr>";
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
	echo "<tr><td>" . $v . $t . $v . $t . "<b>" . $CID . "</b>" . $t . $v. $t . "Soma:" . $t . $CID_soma_valor . "</td></tr>";
	//echo "<tr><td>" . $v . $t . $v . $t . $CID . $t . $v. $t . "Total:" . $t . $CID_soma_valor_total . "</td></tr>";
	$UF_soma_valor_total += $UF_soma_valor;
	echo "<tr><td>" . $v . $t . "<b>" . $UF . "</b>" . $t . $v . $t . $v. $t . "Soma:" . $t . $UF_soma_valor . "</td></tr>";
	echo "<tr><td>" . $v . $t . $v . $t . $v . $t . $v. $t . "Total:" . $t . $UF_soma_valor_total . "</td></tr>";
	echo "</table>";
	// Exibe arranjos
	// 1 - pelo debug
	//print_r($arrUF);
	//echo "<br>";
	//print_r($arrCID);
	echo "<br>";
	echo "<table border=1 cellspacing=0 cellpadding=4>";
	// 2 - com loop foreach
	foreach( $arrUF as $key=>$value ){
		if( (float) $value > $UF_max ) $UF_max = (float) $value;
		echo "<tr><td>" . $key . "</td><td>" . $value . "</td></tr>";
		}
	echo "</table>";
	echo "<br>";
	echo "<table border=1 cellspacing=0 cellpadding=4>";
	foreach( $arrCID as $key=>$value ){
		// NOVO
		if( (float) $value > $CID_max ) $CID_max = (float) $value;
		$UF = substr($key, 0,2);
		$CID = substr($key, 2);
		echo "<tr><td>" . $UF . "</td><td>" . $CID . "</td><td>" . $value . "</td></tr>";
		}
	echo "</table>";
	//print_r($UF_max);
	echo "<br>";
	//print_r($CID_max);
	// GRÁFICOS - NOVO
	$UF_escala = $UF_max/400;
	echo "<br>";
	//print_r($UF_escala);
	echo "<br>";
	echo "<div id=\"Graf1\">";
	foreach( $arrUF as $key=>$value ){
		echo "<div style=\"width: " . $value/$UF_escala . "px\">&nbsp;" . $key . "</div>";
		}	
	echo "</div>";
	$CID_escala = $CID_max/400;
	echo "<br>";
	echo "<div id=\"Graf2\">";
	foreach( $arrCID as $key=>$value ){
		// NOVO
		$UF = substr($key, 0,2);
		$CID = substr($key, 2);
		echo "<div style=\"width: " . $value/$CID_escala . "px\">&nbsp;"  . $UF . " " . $CID . "</div>";
		}	
	echo "</div>";
?>
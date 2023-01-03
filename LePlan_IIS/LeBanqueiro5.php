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
  // NOVO
  // Arrays UF e Cidade
  $arrUF = array();
  $arrCID = array();
  while($row = odbc_fetch_array($stmt)){
	$UF = $row['UF'];
	$CID = $row['Cidade'];
	$valor = $row['Valor'];
	// Quebra CID
	if( $CID != $CID_ant ){
		if( $CID_ant != $q ){
			echo "<tr><td>" . $v . $t . $v . $t . "<b>" . $CID_ant . "</b>" . $t . $v. $t . "Soma:" . $t . $CID_soma_valor . "</td></tr>";
			// NOVO - alimenta arrCID
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
			// NOVO - alimenta arrUF
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
	// NOVO - alimenta arrCID
	$arrCID[$UF_ant . $CID_ant] = $CID_soma_valor;
	// NOVO - alimenta arrUF
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
	print_r($arrUF);
	echo "<br>";
	print_r($arrCID);
	echo "<br>";
	// 2 - com loop foreach
	foreach( $arrUF as $key=>$value ){
		echo $key . "-" . $value . "<br>";
		}
	echo "<br>";
	foreach( $arrCID as $key=>$value ){
		echo $key . "-" . $value . "<br>";
		}
?>
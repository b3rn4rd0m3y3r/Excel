<?php
  // Header
  header('Content-Type: text/html; charset=iso-8859-1');
  // Pega o nome da tabela na URL
  if( $_GET["Planilha"] ) {
	$PLAN_get = $_GET["Planilha"];
	} else {
	$PLAN_get = "";
	}
  if( $PLAN_get == "" ) {
	exit("Nome da planilha a ser listada ausente.");
	}
  if( $_GET["Ordem"] ) {
	$ORD_get = $_GET["Ordem"]; 
	} else {
	$ORD_get = "";
	}
  // Conexão
  $conn = odbc_connect("Driver={Microsoft Excel Driver (*.xls)};DriverId=790;Dbq=" . $PLAN_get . ";DefaultDir=C:\\Inetpub\\wwwroot\\dbase" , '', '');
  //var_dump($conn);
  echo "<br>";
  // Sql
  $sql = "SELECT TOP 1 * FROM [tab1$] ";
  $stmt = odbc_exec($conn, $sql);
  //var_dump($stmt);
  //echo "<br>";
  $row = odbc_fetch_array($stmt);
  //print_r($row);
  //echo "<br>";
  // Array dos campos
  $arrCmps = array();
  // Loop to get fields
  $ind = 0;
  foreach( $row as $key=>$valor ){
	//echo $key . "<br>";
	$arrCmps[$ind] = $key;
	$ind++;
	}
  //print_r($arrCmps);
  // Re-seleciona todos os registros
  $sql = "SELECT * FROM [tab1$] ";
  if( $ORD_get != "" ){
	$sql = $sql . " ORDER BY " . $ORD_get;
	}
  $stmt = odbc_exec($conn, $sql);
  echo "<table border=1 cellpadding=3 cellspacing=0>";
  echo "<tr>";
  foreach( $arrCmps as $key=>$valor ){
	echo "<th>" . $valor . "</th>";
	}  
  echo "</tr>";
  
  // Loop row register
  // Pode tentar odbc_fetch_row também
  
  while($row = odbc_fetch_array($stmt)){
	echo "<tr>";
	//echo $row['Id'] . " - " . $row['UF'] . " - " . $row['Cidade'] . " - " . $row['CPF']. " - " . $row['Conta'] . " - " . $row['Valor'] . "<br>";
	foreach( $arrCmps as $key=>$valor ){
		echo "<td>" . $row[$valor] . "</td>";
		}  	
	echo "</tr>";
	}
	
  echo "</table>";

?>
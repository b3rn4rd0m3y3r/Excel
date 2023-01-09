<?php
  // Header
  header('Content-Type: text/html; charset=iso-8859-1');
  // Conexão
  $conn = odbc_connect("Driver={Microsoft Excel Driver (*.xls)};DriverId=790;Dbq=Banqueiro.xls;DefaultDir=C:\\Inetpub\\wwwroot\\dbase" , '', '');
  //var_dump($conn);
  echo "<br>";
  // Sql
  $sql = "SELECT * FROM [tab1$] ";
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
  echo "<table border=1 cellpadding=3 cellspacing=0>";
  echo "<tr>";
  foreach( $arrCmps as $key=>$valor ){
	echo "<th>" . $valor . "</th>";
	}  
  echo "</tr>";
  echo "</table>";
  // Loop row register
  // Pode tentar odbc_fetch_row também
  /*
  while($row = odbc_fetch_array($stmt)){
	echo "<br>";
	echo $row['Id'] . " - " . $row['UF'] . " - " . $row['Cidade'] . " - " . $row['CPF']. " - " . $row['Conta'] . " - " . $row['Valor'] . "<br>";
	}
*/
?>
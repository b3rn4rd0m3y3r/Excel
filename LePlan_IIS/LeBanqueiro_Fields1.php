<?php
  // Header
  header('Content-Type: text/html; charset=iso-8859-1');
  // Conexão
  $conn = odbc_connect("Driver={Microsoft Excel Driver (*.xls)};DriverId=790;Dbq=Banqueiro.xls;DefaultDir=C:\\Inetpub\\wwwroot\\dbase" , '', '');
  var_dump($conn);
  echo "<br>";
  // Sql
  $sql = "SELECT TOP 1 * FROM [tab1$] ";
  $stmt = odbc_exec($conn, $sql);
  var_dump($stmt);
  echo "<br>";
  $row = odbc_fetch_array($stmt);
  print_r($row);
  // Loop row register
  // Pode tentar odbc_fetch_row também
  /*
  while($row = odbc_fetch_array($stmt)){
	echo "<br>";
	echo $row['Id'] . " - " . $row['UF'] . " - " . $row['Cidade'] . " - " . $row['CPF']. " - " . $row['Conta'] . " - " . $row['Valor'] . "<br>";
	}
*/
?>
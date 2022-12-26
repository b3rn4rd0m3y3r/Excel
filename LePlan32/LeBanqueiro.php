<?php
// Conexão
$conn = odbc_connect("Driver={Microsoft Excel Driver (*.xls)};DriverId=790;Dbq=Banqueiro.xls;DefaultDir=C:\\inetpub\\wwwroot\\dbase" , '', '');
var_dump($conn);
echo "<br>";
// Sql
$sql = "SELECT [Id],[UF] FROM [tab1$] ";
$stmt = odbc_exec($conn, $sql);
var_dump($stmt);
echo "<br>";
// Loop row register
while($row = odbc_fetch_array($stmt))  // Pode tentar odbc_fetch_row também
   {
     print_r($row); 
     echo $row['Id'] . " - " . $row['UF'] . "<br>";
   }

?>
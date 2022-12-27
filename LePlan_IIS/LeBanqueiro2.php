<?php
  // Conexão
  $conn = odbc_connect("Driver={Microsoft Excel Driver (*.xls)};DriverId=790;Dbq=Banqueiro.xls;DefaultDir=C:\\Inetpub\\wwwroot\\dbase" , '', '');
  var_dump($conn);
  echo "<br>";
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
  while($row = odbc_fetch_array($stmt)){
      echo "<tr><td>" . $row['Id'] . $t . $row['UF'] . $t . $row['Cidade'] . $t . $row['CPF']. $t . $row['Conta'] . $t . $row['Valor'] . "</td></tr>";
      }
  echo "</table>";
?>
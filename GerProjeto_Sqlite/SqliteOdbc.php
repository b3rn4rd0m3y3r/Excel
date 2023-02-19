<?php
  // Conexão
  //$conn = odbc_connect("Driver={SQLite ODBC Driver};Dbq=Projetos.db;DefaultDir=C:\\Inetpub\\wwwroot\\dbase" , '', '');
  $conn = odbc_connect("Driver={SQLite3 ODBC Driver};Database=C:\\Inetpub\\wwwroot\\dbase\\Projetos.db;" , '', '');
  var_dump($conn);
  echo "<br>";
?>

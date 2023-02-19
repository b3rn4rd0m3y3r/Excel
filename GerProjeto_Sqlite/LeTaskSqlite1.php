<?php
  /*
	CHAMADA:
	
	http://localhost/dbapp/LeTaskSqlite1.php?
	Planilha=Projetos.db

  */
  // Header
  header('Content-Type: text/html; charset=iso-8859-1');
  // Pega o nome da tabela na URL
  if( $_GET["Planilha"] ) {
	$PLAN_get = $_GET["Planilha"];
	} else {
	$PLAN_get = "";
	}
	  // Conexão
	try {
		$odbt = 'sqlite:'. __DIR__ .'\Projetos.db';
		$conn = new PDO($odbt);
		} catch(PDOException $e) {
		echo $e->getMessage();
		}
  print_r($conn);
  echo "<br>";
  // Sql
  $sql = "SELECT * FROM Tasks ORDER BY Id";
  $stmt = $conn->prepare($sql);
  $res = $stmt->execute();
  print_r($stmt);
  // View
  echo "<table cellspacing=0 cellpadding=4 border=1>";
  while($row = $stmt->fetch()){
	echo "<tr><td>" . $row["Id"] . "</td><td>" . $row["Tarefa"]  . "</td><td>" . $row["DtInicial"] . "</td><td>" . $row["DtFinal"]  . "</td></tr>";
	}
   echo "</table>";
?>
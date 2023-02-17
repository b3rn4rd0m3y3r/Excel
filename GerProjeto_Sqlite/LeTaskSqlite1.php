<?php
  /*
	CHAMADA:
	
	http://localhost/dbapp/LeContasCorrentes8.php?
	Planilha=ContasCorrentes.xls
	&Ordem=UF_t,Cidade_t,CPF_t
	&Quebra=UF_t,Cidade_t
	&Somas=Valor_f_2,Desconto_f_2
	
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
		//$odbt =  "sqlite:./" . $PLAN_get;
		$odbt = 'sqlite:'. __DIR__ .'\Projetos.db';
		//echo $odbt . "<br>";
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
  echo "<br>";
  while($row = $stmt->fetch()){
	echo $row["Tarefa"]  . "<br>";
	}
?>
<style>
	DIV#main {position:absolute;left: 400px;top: 40px;}
	DIV.bar {
		position:absolute;
		display: block;
		margin-top: 40px;
		border: solid 1px maroon;
		background-color:red;
		float:clear;
		}
</style>
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
  // Máximos e mínimos
  $MAIOR_data = strtotime('2000-12-31 00:00:00');
  $MENOR_data = strtotime('2037-12-31 00:00:00');
  echo "MIN<br>";
  print_r($MENOR_data);
  echo "<br>";
  echo "MAX<br>";
  print_r($MAIOR_data);
  echo "<br>";
  // Primeiro loop
  while($row = $stmt->fetch()){
	$DtIni = $row["DtInicial"];
	$DtFim = $row["DtFinal"];
	echo $DtIni . ">" . $DtFim . ":" . $row["Tarefa"] . "<br>";
	if( strtotime($DtIni) < $MENOR_data ){
		$MENOR_data = strtotime($DtIni);
		}
	if( strtotime($DtFim) > $MAIOR_data ){
		$MAIOR_data = strtotime($DtFim);
		}
	}
echo "MIN<br>";
print_r($MENOR_data);
echo "<br>";
echo "MAX<br>";
print_r($MAIOR_data);
  echo "<br>______________________________________________<br>";
  $UmDia = 86400; // 24h*60min*60seg
  // Segundo loop
  $stmt1 = $conn->prepare($sql);
  $res = $stmt1->execute();
  $lin = 0;
  echo "<div id=main>";
  while($row = $stmt1->fetch()){
	$DtIni = $row["DtInicial"];
	$DtFim = $row["DtFinal"];
	$Xini = (strtotime($DtIni) - $MENOR_data)/$UmDia*40; // ERAM 20
	//echo $DtIni . ">" . $DtFim . ":" . $row["Tarefa"] . " | " . $row["Dias"] . "<br>";
	//echo $Xini . ">" . $row["Dias"] . ":" . $row["Tarefa"] . " | " . $row["Dias"] . "<br>";
	$Dias = (strtotime($DtFim)-strtotime($DtIni))/$UmDia+1;
	echo "<div class=bar style=\"top:" . $lin*35 . "px;height:30px;left:" . $Xini . "px;width:" . $Dias*40 . "px\">" . $row["Tarefa"] . "</div>";
	$lin++;
	}
  echo "</div>";
?>
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
	DIV.traco {position:absolute;top:40px;border-left: solid 1px gray;background-color:white;font-size: 5px;}
</style>
<?php
  /*
	CHAMADA:
	
	http://localhost/dbapp/LeTaskSqlite3.php?
	Planilha=Projetos.db
	
  */
  // 1 - DEFINIÇÕES INICIAIS
  // Header
  header('Content-Type: text/html; charset=iso-8859-1');
  // Pega o nome da tabela na URL
  if( $_GET["Planilha"] ) {
	$PLAN_get = $_GET["Planilha"];
	} else {
	$PLAN_get = "";
	}
   // 2 - CONEXÃO AO BANCO DE DADOS
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
  // 3 - CONSTRUÇÃO/EXECUÇÃO DO SQL
  $sql = "SELECT * FROM Tasks ORDER BY Id";
  $stmt = $conn->prepare($sql);
  $res = $stmt->execute();
  // 4 - VALOR INICIAL DA MENOR E DA MAIOR DATA
  $MAIOR_data = strtotime('2000-12-31 00:00:00');
  $MENOR_data = strtotime('2037-12-31 00:00:00');
  echo "MIN<br>";
  print_r($MENOR_data);
  echo "<br>";
  echo "MAX<br>";
  print_r($MAIOR_data);
  echo "<br>";
  // 5 - LEITURA DE TODOS OS REGISTROS E
  //       DESCOBERTA DA MENOR E DA MAIOR DATA
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
  echo "<br>No Linhas: " . $NoLin;
  $lin = 0;
  // 6 - LEITURA DE TODOS OS REGISTROS E
  //       POSICIONAMENTO DAS BARRAS
  echo "<div id=main>";
  while($row = $stmt1->fetch()){
  	$DtIni = $row["DtInicial"];
	$DtFim = $row["DtFinal"];
	$Xini = (strtotime($DtIni) - $MENOR_data)/$UmDia*40;
	$Dias = (strtotime($DtFim)-strtotime($DtIni))/$UmDia+1;
	echo "<div class=bar style=\"top:" . $lin*35 . "px;height:30px;left:" . $Xini . "px;width:" . $Dias*40 . "px;\">" . $row["Tarefa"] . "</div>";
	$lin++;
	}
  // 7 - COLOCAÇÃO DAS BARRAS VERTICAIS
  //       DO GRÁFICO DE GANTT
  $NoLin = $lin;
  for($lin=0;$lin<(int)($MAIOR_data-$MENOR_data)/$UmDia+1;$lin++){	
	echo "<div class=traco style=\"left: " . $lin*40 .  "px;height: " . $NoLin*35 .  "px\">&nbsp;</div>";
	}
  echo "</div>";
?>
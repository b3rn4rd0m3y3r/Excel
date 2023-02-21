<style>
	DIV#main {position:absolute;left: 200px;top: 40px;width:1000px;}
	DIV#trfs    {position:absolute;left: 30px;top: 40px;}
	DIV.task {
		position:absolute;
		display: block;
		margin-top: 40px;
		float:clear;
		}
	DIV.bar {
		position:absolute;
		display: block;
		font-size: 12px;
		margin-top: 40px;
		border: solid 1px maroon;
		
		float:clear;
		}
	DIV.traco {position:absolute;top:40px;border-left: solid 1px gray;background-color:white;font-size: 5px;}
	DIV.dia {
		top: -10px;
		font-size: 6px;height: 6px;left: 1px;position: relative;width: 6px}
</style>
<?php
  /*
	CHAMADA:
	
	http://localhost/dbapp/LeTaskSqlite4.php?
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
  $LARG_DIA = 40;
  $ALT_BARRA = 30;
  // 5 - LEITURA DE TODOS OS REGISTROS E
  //       DESCOBERTA DA MENOR E DA MAIOR DATA
  echo "<div id=trfs>";
  $lin = 0;
  while($row = $stmt->fetch()){
	$DtIni = $row["DtInicial"];
	$DtFim = $row["DtFinal"];
	echo "<div class=task style=\"top:" . $lin*($ALT_BARRA+5) . "px;height:" . $ALT_BARRA . "px;\">" . $row["Tarefa"] . "</div>";
	if( strtotime($DtIni) < $MENOR_data ){
		$MENOR_data = strtotime($DtIni);
		}
	if( strtotime($DtFim) > $MAIOR_data ){
		$MAIOR_data = strtotime($DtFim);
		}
	$lin++;
	}
echo "</div>";
  $UmDia = 86400; // 24h*60min*60seg
  // Segundo loop
  $stmt1 = $conn->prepare($sql);
  $res = $stmt1->execute();
  $lin = 0;
  // 6 - LEITURA DE TODOS OS REGISTROS E
  //       POSICIONAMENTO DAS BARRAS
  echo "<div id=main>";
  while($row = $stmt1->fetch()){
	$DtIni = $row["DtInicial"];
	$DtFim = $row["DtFinal"];
	$Dias = (strtotime($DtFim)-strtotime($DtIni))/$UmDia+1;
	$Xini = ((strtotime($DtIni) - $MENOR_data)/$UmDia)*$LARG_DIA;
	echo "<div class=bar style=\"top:" . $lin*($ALT_BARRA+5) . "px;height:" . $ALT_BARRA . "px;left:" . $Xini . "px;width:" . $Dias*$LARG_DIA . "px;background-color:red;\">&nbsp;&nbsp;" . $Xini/$LARG_DIA . ":" . $Xini . "</div>";
	$lin++;
	}
  // 7 - COLOCAÇÃO DAS BARRAS VERTICAIS
  //       DO GRÁFICO DE GANTT  
  $NoLin = $lin;
  for($lin=0;$lin<(int)($MAIOR_data-$MENOR_data)/$UmDia+2;$lin++){	
	echo "<div class=traco style=\"left: " . $lin*$LARG_DIA .  "px;height: " . $NoLin*($ALT_BARRA+5) .  "px\"><div class=dia>" . $lin . "</div></div>";
	}
  echo "</div>";
?>
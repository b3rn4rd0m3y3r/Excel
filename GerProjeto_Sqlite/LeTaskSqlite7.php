<style>
	BODY {font-family: Arial;}
	DIV#main {position:absolute;left: 200px;top: 50px;width:1000px;}
	DIV#trfs    {position:absolute;left: 30px;top: 50px;}
	DIV.task {
		position:absolute;
		display: block;
		margin-top: 40px;
		float:clear;
		}
	DIV.bar {
		position:absolute;
		display: block;
		margin-top: 40px;
		border: solid 1px maroon;
		font-size: 11px;
		float:clear;
		}
	DIV.traco {position:absolute;top:40px;border-left: solid 1px gray;background-color:white;font-size: 5px;}
	DIV.rotulo { position: absolute; top: 10px; height: 12px; font-size: 10px;text-align:center;}
	H1 {color: maroon;}
</style>
<?php
  /*
	CHAMADA:
	
	http://localhost/dbapp/LeTaskSqlite7.php?
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
	$conn = new PDO($odbt);
	} catch(PDOException $e) {
	echo $e->getMessage();
	}
  
  // 3 - CONSTRUÇÃO/EXECUÇÃO DO SQL
  $sql = "SELECT * FROM Tasks ORDER BY Id";
  $stmt = $conn->prepare($sql);
  $res = $stmt->execute();
  // 4 - VALOR INICIAL DA MENOR E DA MAIOR DATA
  //       E OUTROS PARÂMETROS
  $MAIOR_data = strtotime('2000-12-31 00:00:00');
  $MENOR_data = strtotime('2037-12-31 00:00:00');
  $LARG_DIA = 40;
  $ALT_BARRA = 30;
  echo "<h1>GERENCIADOR DO PROJETO: " . $PLAN_get .  "</h1>";
  
  // 5 - LEITURA DE TODOS OS REGISTROS E
  //       DESCOBERTA DA MENOR E DA MAIOR DATA
  echo "<div id=trfs>";
  $lin = 0;
  while($row = $stmt->fetch()){
	$DtIni = $row["DtInicial"];
	$DtIni = strtotime($DtIni);
	$DtFim = $row["DtFinal"];
	$DtFim = strtotime($DtFim);
	echo "<div class=task style=\"top:" . $lin*($ALT_BARRA+5) . "px;height:" . $ALT_BARRA . "px;\">" . $row["Tarefa"] . "</div>";
	if( $DtIni < $MENOR_data ){
		$MENOR_data = $DtIni;
		}
	if( $DtFim > $MAIOR_data ){
		$MAIOR_data = $DtFim;
		}
	$lin++;
	}
  echo "</div>";
  
  //  echo "<br>______________________________________________<br>";
  $UmDia = 86400; // 24h*60min*60seg
  
  // 6 - LEITURA DE TODOS OS REGISTROS E
  //       POSICIONAMENTO DAS BARRAS
  
  $stmt1 = $conn->prepare($sql);
  $res = $stmt1->execute();
  
  //echo "<br>No Linhas: " . $NoLin;
  $lin = 0;
  echo "<div id=main>";
  while($row = $stmt1->fetch()){
	$Id = $row["Id"];
	$DtIni = $row["DtInicial"];
	$DtIni = strtotime($DtIni);
	$DtFim = $row["DtFinal"];
	$DtFim = strtotime($DtFim);
	//echo "F: " . $DtFim . " ";
	//$Dias = $row["Dias"];
	$Dias = ($DtFim - $DtIni)/$UmDia + 1;
	//echo "s: " . $Dias . " ";
	$COR = "black";
	$LETRA = "white";
	if( $Dias < 14 ) { $COR = "teal"; }
	if( $Dias < 10 ) { $COR = "maroon"; }
	if( $Dias < 6 ) { $COR = "red"; $LETRA = "yellow";}
	if( $Dias < 4 ) { $COR = "gold"; $LETRA = "red"; }
	if( $Dias < 2 ) { $COR = "yellow"; $LETRA = "red";}
	$Xini = (($DtIni - $MENOR_data)/$UmDia)*$LARG_DIA;
	// Barra de exibição de uma tarefa
	echo "<div class=bar style=\"cursor: pointer;top:" . $lin*($ALT_BARRA+5) . "px;height:" . $ALT_BARRA . "px;left:" . $Xini . "px;width:" . $Dias*$LARG_DIA . "px;background-color:" . $COR . "; color:" .  $LETRA . "\" ";
	echo " OnClick=\"window.open('LeUmaTaskSqlite3.php?Id=" . $Id . "','','width=500,height=500');\">&nbsp;&nbsp;";
	echo  ($Xini/$LARG_DIA+1) . "&deg;" . " dia</div>";
	
	$lin++;
	echo "<br>";
	}

  $NoLin = $lin;
  for($lin=0;$lin<(int)($MAIOR_data-$MENOR_data)/$UmDia+1;$lin++){	
	echo "<div class=rotulo style=\"left: " . $lin*$LARG_DIA .  "px;width: " . $LARG_DIA .  "px; height: 10px;\">" . date('d/m',$lin*$UmDia+$MENOR_data) . "</div>";
	echo "<div class=traco style=\"left: " . $lin*$LARG_DIA .  "px;height: " . $NoLin*($ALT_BARRA+5) .  "px\">&nbsp;</div>";
	}
  echo "<div class=traco style=\"left: " . $lin*$LARG_DIA .  "px;height: " . $NoLin*($ALT_BARRA+5) .  "px\">&nbsp;</div>";
  echo "</div>";
?>
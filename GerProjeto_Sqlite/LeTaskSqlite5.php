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
		margin-top: 40px;
		border: solid 1px maroon;
		
		float:clear;
		}
	DIV.traco {position:absolute;top:40px;border-left: solid 1px gray;background-color:white;font-size: 5px;}
	DIV.rotulo { position: absolute; top: 10px; height: 12px; font-size: 10px;text-align:center;}
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
  $LARG_DIA = 40;
  $ALT_BARRA = 30;
  // Primeiro loop
  echo "<div id=trfs>";
  $lin = 0;
  while($row = $stmt->fetch()){
	$DtIni = $row["DtInicial"];
	$DtFim = $row["DtFinal"];
	//echo $DtIni . ">" . $DtFim . ":" . $row["Tarefa"] . " | " . $row["Dias"] . "<br>";
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
  
  //echo "<br>No Linhas: " . $NoLin;
  $lin = 0;
  echo "<div id=main>";
  while($row = $stmt1->fetch()){
	$DtIni = $row["DtInicial"];
	$DtFim = $row["DtFinal"];
	$Dias = (strtotime($DtFim)-strtotime($DtIni))/$UmDia+1;
	$Xini = ((strtotime($DtIni) - $MENOR_data)/$UmDia)*$LARG_DIA;
	echo "<div class=bar style=\"top:" . $lin*($ALT_BARRA+5) . "px;height:" . $ALT_BARRA . "px;left:" . $Xini . "px;width:" . $Dias*$LARG_DIA . "px;background-color:red;\">" . $Xini . "</div>";
	$lin++;
	}
  $NoLin = $lin;
  for($lin=0;$lin<(int)($MAIOR_data-$MENOR_data)/$UmDia+1;$lin++){	
	echo "<div class=rotulo style=\"left: " . $lin*$LARG_DIA .  "px;width: " . $LARG_DIA .  "px; height: 10px;\">" . date('d/m',$lin*$UmDia+$MENOR_data) . "</div>";
	echo "<div class=traco style=\"left: " . $lin*$LARG_DIA .  "px;height: " . $NoLin*($ALT_BARRA+5) .  "px\">&nbsp;</div>";
	}
  echo "<div class=traco style=\"left: " . $lin*$LARG_DIA .  "px;height: " . $NoLin*($ALT_BARRA+5) .  "px\">&nbsp;</div>";
  echo "</div>";
?>
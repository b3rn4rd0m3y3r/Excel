<title>Proj.</title>
<style>
	BODY {font-family: Arial;}
	BUTTON {background: crimson;color: white;}
	/* Larguras das DIVs aumentadas para 300px */
	DIV#main {position:absolute;left: 300px;top: 50px;width:1000px;float:clear;}
	DIV#trfs    {position:absolute;left: 30px;top: 50px;width: 300px;}
	DIV.task {
		cursor: pointer;
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
	DIV.traco {background-color:white;border-left: solid 1px gray;font-size: 5px;position:absolute;top:40px;}
	DIV.rotulo { position: absolute; top: 10px; height: 12px; font-size: 10px;text-align:center;}
	H1 {color: maroon;}
	DIV.prel {position: relative;color: crimson;display: block;width:1000px;}
	SPAN.del {font-size: 16px; font-family: Arial;color:red;}
</style>
<?php
  // Pega o nome da tabela na URL
  if( $_GET["Planilha"] ) {
	$PLAN_get = $_GET["Planilha"];
	} else {
	$PLAN_get = "";
	}
?>
<script>
	// Deleta uma tarefa
	function delTask(ev, vId){
		ev.stopPropagation();
		var resp = confirm("Confirma deleção ?");
		if( resp != 0 ){
			var url = 'DelTaskSqlite1_v2.php?Id='+vId+'&Planilha=<?php echo $PLAN_get; ?>';
			window.open(url,'','width=500,height=500');
			}
		}
	// Extrai caracteres pela direita
	function Right(str, n){
	    if (n <= 0)
		return "";
	    else if (n > String(str).length)
		return str;
	    else {
		var iLen = String(str).length;
		return String(str).substring(iLen, iLen - n);
		}
	}
	// Insere uma task próxima à clicada
	function chgTask(vCod,vPlan){
		//alert(vCod);
		iCod = parseInt(vCod)+10;
		sCod = Right("000"+iCod.toString(),4);
		window.open("FrmInsertTaskSqlite1_v2.php?Planilha=" + vPlan + "&Codigo="+sCod,'NOVA','width=500,height=500');
		}
</script>
<?php
  /*
	CHAMADA:
	
	http://intranet-se01/dvpi/dbapp/LeTaskSqlite10_v2.php?
	Planilha=Projetos.db
	
  */
  // Header
  header('Content-Type: text/html; charset=iso-8859-1');


  try {
	//$odbt =  "sqlite:./" . $PLAN_get;
	//$odbt = 'sqlite:'. __DIR__ .'\Projetos.db';
	$odbt = 'sqlite:'. __DIR__ . "\\" . $PLAN_get;
	$conn = new PDO($odbt);
	} catch(PDOException $e) {
	echo $e->getMessage();
	}
  
  // Sql
  $sql = "SELECT * FROM Tasks ORDER BY CodHierarquia";
  $stmt = $conn->prepare($sql);
  $res = $stmt->execute();
  
  // Máximos e mínimos
  $MAIOR_data = strtotime('2000-12-31 00:00:00');
  $MENOR_data = strtotime('2037-12-31 00:00:00');
  $LARG_DIA = 40;
  $ALT_BARRA = 30;
  echo "<h1>GERENCIADOR DO PROJETO: " . $PLAN_get .  "</h1>";
  echo "<button ";
  echo " OnClick=\"window.open('FrmTaskSqlite2_v2.php?Planilha=" . $PLAN_get . "','','width=500,height=500');\">&nbsp;&nbsp;";
  echo "NOVA TAREFA</button>";
  // Primeiro loop
  echo "<div id=trfs>";
  $lin = 0;
  while($row = $stmt->fetch()){
	$DtIni = $row["DtInicial"];
	$DtIni = strtotime($DtIni);
	$DtFim = $row["DtFinal"];
	$DtFim = strtotime($DtFim);
	$CodHierarquia = $row["CodHierarquia"];
	$POS = substr($CodHierarquia,2,1);
	$Recuo = "";
	if( $POS != "0" ){
		$Recuo = "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
		}
	$TOPO = (string) $lin*($ALT_BARRA+5)+10;
	echo "<div class=task style=\"top:" . $TOPO . "px;height:" . $ALT_BARRA . "px;\" onclick=\"chgTask('" . $CodHierarquia . "','" . $PLAN_get . "')\">" . $Recuo . "<span class=\"del\" onclick=\"delTask(event,". $row["Id"] .");\">&#128465;&nbsp;</span>" . $CodHierarquia . " - " . $row["Tarefa"] . "</div>";
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
  
  // Segundo loop
  
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
	echo " OnClick=\"window.open('LeUmaTaskSqlite4_v2.php?Id=" . $Id . "&Planilha=" . $PLAN_get . "&Dias=" . $Dias . "','','width=400,height=400');\">&nbsp;&nbsp;";
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
  $TopAviso = ($NoLin)*($ALT_BARRA+5)+15;
  echo "<div class=\"prel\" style=\"top: " . $TopAviso . "\">Clique sobre uma tarefa para<br>incluir novas tarefas ou subtarefas.</div>";

?>
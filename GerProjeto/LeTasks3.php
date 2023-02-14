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
  $conn = odbc_connect("Driver={Microsoft Excel Driver (*.xls)};DriverId=790;Dbq=" . $PLAN_get . ";DefaultDir=D:\\Websis\\usu\\dvpi\\Inetpub\\wwwroot\\dbapp" , '', '');
  print_r($conn);
  echo "<br>";
  // Sql
  $sql = "SELECT TOP 100 * FROM [tab1$] ORDER BY [Id]";
  $stmt = odbc_exec($conn, $sql);
  print_r($stmt);
  echo "<br>";
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
  while($row = odbc_fetch_array($stmt)){
	$DtIni = $row["DtInicial"];
	$DtFim = $row["DtFinal"];
	echo $DtIni . ">" . $DtFim . ":" . $row["Tarefa"] . " | " . $row["Dias"] . "<br>";
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
  $stmt1 = odbc_exec($conn, $sql);
  
  echo "<br>No Linhas: " . $NoLin;
  $lin = 0;
  echo "<div id=main>";
  while($row = odbc_fetch_array($stmt1)){
	$DtIni = $row["DtInicial"];
	$DtFim = $row["DtFinal"];
	$Xini = (strtotime($DtIni) - $MENOR_data)/$UmDia*20;
	//echo $DtIni . ">" . $DtFim . ":" . $row["Tarefa"] . " | " . $row["Dias"] . "<br>";
	//echo $Xini . ">" . $row["Dias"] . ":" . $row["Tarefa"] . " | " . $row["Dias"] . "<br>";
	echo "<div class=bar style=\"top:" . $lin*35 . "px;height:30px;left:" . $Xini . "px;width:" . $row["Dias"]*40 . "px;\">" . $row["Tarefa"] . "</div>";
	
	$lin++;
	}
  $NoLin = $lin;
  for($lin=0;$lin<(int)($MAIOR_data-$MENOR_data)/$UmDia+1;$lin++){	
	echo "<div style=\"left: " . $lin*40 .  "px;height: " . $NoLin*35 .  "px\" class=traco>&nbsp;</div>";
	}
  echo "</div>";
?>
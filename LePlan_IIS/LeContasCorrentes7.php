<?php
  /*
	CHAMADA:
	
	http://localhost/dbapp/LeContasCorrentes1.php?Planilha=ContasCorrentes&Ordem=UF_t,Cidade_t,CPF_t
	
  */
  // Header
  header('Content-Type: text/html; charset=iso-8859-1');
  // Pega o nome da tabela na URL
  if( $_GET["Planilha"] ) {
	$PLAN_get = $_GET["Planilha"];
	} else {
	$PLAN_get = "";
	}
  if( $PLAN_get == "" ) {
	exit("Nome da planilha a ser listada ausente.");
	}
  if( $_GET["Ordem"] ) {
	$ORD_get = $_GET["Ordem"]; 
	} else {
	$ORD_get = "";
	}
  if( $_GET["Quebra"] ) {
	$BRK_get = $_GET["Quebra"]; 
	} else {
	$BRK_get = "";
	}
  // Conexão
  $conn = odbc_connect("Driver={Microsoft Excel Driver (*.xls)};DriverId=790;Dbq=" . $PLAN_get . ";DefaultDir=C:\\Inetpub\\wwwroot\\dbase" , '', '');
  //var_dump($conn);
  echo "<br>";
  // Sql
  $sql = "SELECT TOP 1 * FROM [tab1$] ";
  $stmt = odbc_exec($conn, $sql);
  $row = odbc_fetch_array($stmt);
  // Array dos campos
  $arrCmps = array(); // Campos lidos
  $arrCols = array(); // Títulos das colunas para exibição
  $arrTps = array(); // Tipos dos Campos lidos 
  $arrDecs = array(); // Casas decimais dos Campos lidos
  $arrBrks = array(); // Lista de "quebras"/grupos fornecidos na URL
  // Loop to get fields
  $ind = 0;
  // nome_tipo_casas
  foreach( $row as $key=>$valor ){
	$chave = explode("_", $key);
	$arrCols[$ind] = $chave[0];
	$arrTps[$chave[0]] = $chave[1];
	$arrDecs[$chave[0]] = $chave[2];
	$arrCmps[$ind] = $key; 
	$ind++;
	}
  // Procedimento para obter os campos de quebra
  $arrBrks = explode(",", $BRK_get);
  $oarrBrks = [];
  $cnt = 0;
  // Variável inicial de quebra
  $q = "@#$%";
  // Mostra na tela
 foreach( $arrBrks as $key=>$valor ){
	$oarrBrks[$valor]->name = $key;
	$oarrBrks[$valor]->value = $valor;
	$oarrBrks[$valor]->anterior = $q;
	$oarrBrks[$valor]->subtotal = 0;
	$oarrBrks[$valor]->total = 0;
	//echo $key . "-" . $oarrBrks[$cnt]->name . "<br>";
	$cnt++;
	}
  echo "<br>Brks<br>";
  print_r($arrBrks);
  echo "<br>";
  // Re-seleciona todos os registros
  $sql = "SELECT TOP 120 * FROM [tab1$] ";
  if( $ORD_get != "" ){
	$sql = $sql . " ORDER BY " . $ORD_get;
	}
  $stmt = odbc_exec($conn, $sql);
  echo "<table border=1 cellpadding=3 cellspacing=0>";
  echo "<tr>";
  foreach( $arrCols as $key=>$valor ){ // Alterado array para $arrCols
	echo "<th>" . $valor . "</th>";
	}  
  echo "</tr>";
  
  // Loop row register
  // Pode tentar odbc_fetch_row também
  while($row = odbc_fetch_array($stmt)){
	// NOVO - Testes de quebra em ordem INVERTIDA
	// Por isso usaremos loop decremental
	for($i=count($arrBrks)-1;$i>=0;$i--){
		$brkField = $arrBrks[$i];
		//print_r($brkField);
		if( $row[$brkField] != $oarrBrks[$brkField]->anterior ){
			if( $oarrBrks[$brkField]->anterior != $q ){
				// NOVO - Acrescenta o subtotal
				echo "<tr><td colspan=" . count($arrCols) . ">Fechou " . $oarrBrks[$brkField]->anterior . " => " . $oarrBrks[$brkField]->soma . "</td></tr>";
				}
			$oarrBrks[$brkField]->anterior = $row[$brkField];
			$oarrBrks[$brkField]->soma = 0;
			}
		// NOVO - Faz as somas das quebras
		$oarrBrks[$brkField]->soma = $oarrBrks[$brkField]->soma + $row["Valor_f_2"];
		}
	
	// Impressão de uma linha normal de registro
	echo "<tr>";
	foreach( $arrCols as $key=>$valor ){
		// Testa tipo de variável
		if( $arrTps[$valor] == "t" ){
			echo "<td data-vr=\"" . $arrTps[$valor] . "\" align=left >" . preg_replace("[\.0]","",$row[$arrCmps[$key]]) . "</td>";
			}
		if( $arrTps[$valor] == "n" ){
			echo "<td data-vr=\"" . $arrCmps[$key] . "\" align=right>" . $row[$arrCmps[$key]] . "</td>";
			}
		if( $arrTps[$valor] == "f" ){
			echo "<td data-vr=\"" . $arrDecs[$arrCols[$key]] . "\" align=right>" . sprintf("%9." . $arrDecs[$arrCols[$key]] . "f",$row[$arrCmps[$key]]) . "</td>";
			}		
		}  	
	echo "</tr>";
	}
// NOVO - Testes de quebra em ordem INVERTIDA
// Por isso usaremos loop decremental
	for($i=count($arrBrks)-1;$i>=0;$i--){
		$brkField = $arrBrks[$i];
		//print_r($brkField);
		if( $row[$brkField] != $oarrBrks[$brkField]->anterior ){
			if( $oarrBrks[$brkField]->anterior != $q ){
				// NOVO - Acrescenta o subtotal
				echo "<tr><td colspan=" . count($arrCols) . ">Fechou " . $oarrBrks[$brkField]->anterior . " => " . $oarrBrks[$brkField]->soma . "</td></tr>";
				}
			$oarrBrks[$brkField]->anterior = $row[$brkField];
			$oarrBrks[$brkField]->soma = 0;
			}
		// NOVO - Faz as somas das quebras
		$oarrBrks[$brkField]->soma = $oarrBrks[$brkField]->soma + $row["Valor_f_2"];
		}	
  echo "</table>";

?>
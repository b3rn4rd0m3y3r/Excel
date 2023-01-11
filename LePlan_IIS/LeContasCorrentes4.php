<?php
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
  // Loop to get fields
  $ind = 0;
  // nome_tipo_casas
  foreach( $row as $key=>$valor ){
	$chave = explode("_", $key);
	$arrCols[$ind] = $chave[0];
	$arrTps[$chave[0]] = $chave[1]; // NOVO
	$arrDecs[$chave[0]] = $chave[2]; // NOVO
	$arrCmps[$ind] = $key; 
	$ind++;
	}
  echo "Cmps: "; 
  print_r($arrCmps);
  echo "<br>";
  echo "Tps: "; 
  print_r($arrTps);
  echo "<br>";
  echo "Decs: "; 
  print_r($arrDecs);
  echo "<br>";
  echo "Cols: ";
  print_r($arrCols);
  // Re-seleciona todos os registros
  $sql = "SELECT * FROM [tab1$] ";
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
	echo "<tr>";
	//echo $row['Id'] . " - " . $row['UF'] . " - " . $row['Cidade'] . " - " . $row['CPF']. " - " . $row['Conta'] . " - " . $row['Valor'] . "<br>";
	foreach( $arrCols as $key=>$valor ){
		// NOVO - Testa tipo de variável
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
	
  echo "</table>";

?>
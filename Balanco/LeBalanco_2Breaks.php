<?php
  /*
	CHAMADA:
	
	http://localhost/dbapp/LeBalanco_2breaks.php?
	Planilha=Balanco02_01.xls
	&Ordem=APLICACAO_t,CONTA_t
	&Quebra=APLICACAO_t,CONTA_t
	&Somas=VALOR_f_2
	
	CUIDADOS: 
		1. Conferir nome da script
		2. Conferir nome da planilha e lembrar que tem que ser xls (32 bits)
		3. Conferir nomes dos parametros na URL: Planilha, Ordem, Quebra, Somas
		4. Conferir o nome da "orelha" no xls de onde virão os dados
		5. Conferir se cada nome de coluna tem o tipo após um underline e, no caso do campo float,
		     o número de casas decimais.
	
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
  if( $_GET["Somas"] ) {
	$SUM_get = $_GET["Somas"]; 
	} else {
	$SUM_get = "";
	}
  // Conexão
  // DefaultDir=C:\\Inetpub\\wwwroot\\dbase
  // DefaultDir=D:\\Websis\\usu\\dvpi\\Inetpub\\wwwroot\\dbapp
  $conn = odbc_connect("Driver={Microsoft Excel Driver (*.xls)};DriverId=790;Dbq=" . $PLAN_get . ";DefaultDir=D:\\Websis\\usu\\dvpi\\Inetpub\\wwwroot\\dbapp" , '', '');
  //var_dump($conn);
  echo "<br>";
  // Sql
  $sql = "SELECT TOP 1 * FROM [Lancamentos$] ";
  $stmt = odbc_exec($conn, $sql);
  $row = odbc_fetch_array($stmt);
  // Array dos campos
  $arrCmps = array(); // Campos lidos
  $arrCols = array(); // Títulos das colunas para exibição
  $arrTps = array(); // Tipos dos Campos lidos 
  $arrDecs = array(); // Casas decimais dos Campos lidos
  $arrBrks = array(); // Lista de "quebras"/grupos fornecidos na URL
  $arrSums = array(); // NOVO - Lista de campos a serem somados
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
  //print_r($arrCmps);
  // Procedimento para obter os campos de quebra
  // Combinado com os campos de soma
  $arrBrks = explode(",", $BRK_get);
  $oarrBrks = [];
  // NOVO - Somas
  $arrSums = explode(",", $SUM_get);
  $oarrSums = [];
 foreach( $arrSums as $key=>$valor ){
	$oarrSums[$valor]->name = $key;
	$oarrSums[$valor]->value = $valor;	
	}
  $cnt = 0;
  // Variável inicial de quebra
  $q = "@#$%";
  // Mostra na tela
 foreach( $arrBrks as $key=>$valor ){
	$oarrBrks[$valor]->name = $key;
	$oarrBrks[$valor]->value = $valor;
	$oarrBrks[$valor]->anterior = $q;
	// NOVO - Somas para cada campo de valor
	 foreach( $arrSums as $keyc=>$valorc ){
		$oarrSums[$valor][$valorc] = 0;
		}
	$oarrBrks[$valor]->total = 0;
	$cnt++;
	}
  
  // Re-seleciona todos os registros
  $sql = "SELECT TOP 120 * FROM [Lancamentos$] ";
  if( $ORD_get != "" ){
	$sql = $sql . " ORDER BY " . $ORD_get;
	}
  echo $sql . "<br>";
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
	// Testes de quebra em ordem INVERTIDA
	// Por isso usaremos loop decremental
	for($i=count($arrBrks)-1;$i>=0;$i--){
		$brkField = $arrBrks[$i];
		//print_r($brkField);
		if( $row[$brkField] != $oarrBrks[$brkField]->anterior ){
			if( $oarrBrks[$brkField]->anterior != $q ){
				// NOVO - Acrescenta o subtotal
				echo "<tr><td colspan=" . count($arrCols) . ">Fechou ";
				echo $oarrBrks[$brkField]->anterior . " Somas ";
				// $oarrBrks[$brkField]->soma;
				// Mostra as somas das quebras, uma para cada campo do array de valores
				foreach( $arrSums as $keyc=>$valorc ){
					echo " " . $arrSums[$valorc] . " => " . $oarrSums[$oarrBrks[$brkField]->anterior][$valorc];
					}				
				echo "</td></tr>";
				}
			$oarrBrks[$brkField]->anterior = $row[$brkField];
			$oarrSums[$row[$brkField]][$valorc] = 0;
			//$oarrBrks[$brkField]->soma = 0;
			}
		// Faz as somas das quebras, uma para cada campo do array de valores
		}
	// Faz as somas das quebras, uma para cada campo do array de valores
	// NOVO - SÓ PARA ESTE CASO DO BALANÇO
	echo "SOMAS DAS Q U E B R A S<br>";
	$Operacao = $row["Operacao_t"];
	for($i=count($arrBrks)-1;$i>=0;$i--){
		$brkField = $row[$arrBrks[$i]];
		echo $brkField . ",";
		foreach( $arrSums as $keyc=>$valorc ){
			if( $Operacao == "Aumenta" ){
				$oarrSums[$brkField][$valorc] = $oarrSums[$brkField][$valorc] + (float)$row[$valorc];
				} else {
				$oarrSums[$brkField][$valorc] = $oarrSums[$brkField][$valorc] - (float)$row[$valorc];
				}
			}
		}
	echo "<br>";
	
	// Impressão de uma linha normal de registro
	echo "<tr>";
	foreach( $arrCols as $key=>$valor ){
		// Testa tipo de variável
		if( $arrTps[$valor] == "t" ){
			echo "<td data-vr=\"" . $arrTps[$valor] . "\" align=left >" . preg_replace("[\.0]","",$row[$arrCmps[$key]]) . "</td>";
			}
		if( $arrTps[$valor] == "n" ){
			
			echo "<td data-vr=\"" . $arrCmps[$key] . "\" align=right>";
			echo $row[$arrCmps[$key]] . "</td>";
			}
		if( $arrTps[$valor] == "f" ){
			echo "<td data-vr=\"" . $arrDecs[$arrCols[$key]] . "\" align=right>" ;
			// NOVO - Apenas sabemos que existe apenas um campo float no BALANÇO
			if( $Operacao == "Aumenta" ){
				echo "+ ";
				} else {
				echo "- ";
				}			
			echo sprintf("%9." . $arrDecs[$arrCols[$key]] . "f",$row[$arrCmps[$key]]) . "</td>";
			}		
		}  	
	echo "</tr>";
	}
// Testes de quebra em ordem INVERTIDA
// Por isso usaremos loop decremental
	for($i=count($arrBrks)-1;$i>=0;$i--){
		$brkField = $arrBrks[$i];
		// NOVO - Acrescenta os subtotais finais
		echo "<tr><td colspan=" . count($arrCols) . ">Fechou ";
		echo $oarrBrks[$brkField]->anterior . " Somas ";
		// $oarrBrks[$brkField]->soma;
		// NOVO - Mostra as somas das quebras, uma para cada campo do array de valores
		foreach( $arrSums as $keyc=>$valorc ){
			echo " " . $arrSums[$valorc] . " => " . $oarrSums[$oarrBrks[$brkField]->anterior][$valorc];
			}				
		echo "</td></tr>";
		
		
		}	
  echo "</table>";
  echo "<br>Sums<br>";
  print_r($oarrSums);
  echo "<br>";
  echo "______________________________________________<br>";

	foreach( $oarrSums as $keyc=>$valorc ){
		foreach( $valorc as $key=>$valor ){
			echo $keyc . " - " . $key . " -> " . $valor . "<br>";
			}
		}

?>
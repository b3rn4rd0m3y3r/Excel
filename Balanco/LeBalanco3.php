<?php
  /*
	CHAMADA:
	
	LeBalanco2.php?Planilha=Balanco02_01.xls
	&Ordem=APLICACAO_t,CONTA_t
	&Quebra=APLICACAO_t,CONTA_t
	&Somas=VALOR_f_2
	
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
  $conn = odbc_connect("Driver={Microsoft Excel Driver (*.xls)};DriverId=790;Dbq=" . $PLAN_get . ";DefaultDir=C:\\Inetpub\\wwwroot\\dbase" , '', '');
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
  // NOVO - Array das somas apenas das contas
  $oarrSumsCtas = [];
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
  
  // Loop dos registros: Uma row é um REGISTRO ou linha de dados da planilha
  // Pode tentar odbc_fetch_row também
  while($row = odbc_fetch_array($stmt)){
	// Testes de quebra em ordem INVERTIDA
	// Por isso usaremos loop decremental
	for($i=count($arrBrks)-1;$i>=0;$i--){
		$brkField = $arrBrks[$i];
		if( $row[$brkField] != $oarrBrks[$brkField]->anterior ){
			if( $oarrBrks[$brkField]->anterior != $q ){
				// NOVO - Acrescenta o subtotal
				echo "<tr><td colspan=" . count($arrCols) . ">Fechou ";
				echo $oarrBrks[$brkField]->anterior . " Somas ";
				// $oarrBrks[$brkField]->soma;
				// NOVO - Mostra as somas das quebras, uma para cada campo do array de valores
				foreach( $arrSums as $keyc=>$valorc ){
					echo " " . $arrSums[$valorc] . " => " ;
					echo $oarrSums[$oarrBrks[$brkField]->anterior][$valorc];
					}				
				echo "</td></tr>";
				}
			$oarrBrks[$brkField]->anterior = $row[$brkField];
			$oarrSums[$row[$brkField]][$valorc] = 0;
			//$oarrBrks[$brkField]->soma = 0;
			}

		// $oarrBrks[$brkField]->soma = $oarrBrks[$brkField]->soma + $row["Valor_f_2"];
		}
	// Faz as somas das quebras, uma para cada campo do array de valores
	// Primeiro loop: QUEBRAS
	// Segundo loop: Campos de SOMA
	$Operacao = $row["Operacao_t"];
	// NOVO - Pega o grupo de contas A, P ou PL
	$Aplicacao = $row["APLICACAO_t"];
	for($i=count($arrBrks)-1;$i>=0;$i--){
		$brkField = $arrBrks[$i];
		foreach( $arrSums as $keyc=>$valorc ){
			//echo $row[$valorc] . ",";
			if( $Operacao == "Aumenta" ){
				// Alterado para Ctas
				$oarrSumsCtas[$Aplicacao][$row[$brkField]][$valorc] = $oarrSumsCtas[$Aplicacao][$row[$brkField]][$valorc] + (float)$row[$valorc];
				$oarrSums[$row[$brkField]][$valorc] = $oarrSums[$row[$brkField]][$valorc] + (float)$row[$valorc];
				} else {
				$oarrSumsCtas[$Aplicacao][$row[$brkField]][$valorc] = $oarrSumsCtas[$Aplicacao][$row[$brkField]][$valorc] - (float)$row[$valorc];
				$oarrSums[$row[$brkField]][$valorc] = $oarrSums[$row[$brkField]][$valorc] - (float)$row[$valorc];
				}
			//echo $valorc . " > " . $oarrSums[$row[$brkField]][$valorc] . "<br>";
			}
		}
	//echo "<br>";
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
		// Acrescenta os subtotais finais
		echo "<tr><td colspan=" . count($arrCols) . ">Fechou ";
		echo $oarrBrks[$brkField]->anterior . " Somas ";
		// $oarrBrks[$brkField]->soma;
		// Mostra as somas das quebras, uma para cada campo do array de valores
		foreach( $arrSums as $keyc=>$valorc ){
			echo " " . $arrSums[$valorc] . " => " . $oarrSums[$oarrBrks[$brkField]->anterior][$valorc];
			}				
		echo "</td></tr>";
		
		
		}	
  echo "</table>";
  echo "<br>Sums<br>";
  // Alterado array
  print_r($oarrSumsCtas);
  echo "<br>";
  echo "  	<b>TODAS AS CONTAS, INCLUSIVE REDUNDÂNCIAS</b> <br>";
// NOVO - Alterado array Ctas
$Apps = array("Ativo","Passivo","PL");
print_r($Apps);
echo "<br>";
$iApp = 0;
foreach( $oarrSumsCtas as $keyc=>$valorc ){
	foreach( $valorc as $keyd=>$valord ){
		foreach( $valord as $key=>$valor ){
			echo $keyc . " : " . $keyd .  " -> ". $key .  " -> " . $valor . "<br>";
			echo $oarrSumsCtas[$keyc][$keyd][$key] . "<br>";
			}
		}
	}
// Listando apenas Ativo

echo "	<b>APENAS CONTAS DO ATIVO, SEM REDUNDÂNCIA</b> <br>";
print_r($oarrSumsCtas["Ativo"]);
echo "<br>";
foreach( $oarrSumsCtas["Ativo"] as $keyc=>$valorc ){
	foreach( $valorc as $keyd=>$valord ){
		//foreach( $valord as $key=>$valor ){
			if( $keyc != "Ativo" ){
				echo $keyc . " : " . $keyd .  " -> " . $valord . "<br>";
				echo $oarrSumsCtas["Ativo"][$keyc][$keyd] . "<br>";
				}
		//	}
		}
	}
?>
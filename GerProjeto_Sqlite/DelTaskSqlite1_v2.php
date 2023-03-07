<script>
function grafupd(plan){
	var o = window.opener;
	var p = o.parent;
	p.window.location.href = 'LeTaskSqlite10_v2.php?Planilha=' + plan;
	}
</script>
<?php
  // 1 - COLETA DE PARÂMETROS 
  if( $_GET["Planilha"] ) {
	$PLAN_get = $_GET["Planilha"];
	} else {
	$PLAN_get = "";
	}
  if( $_GET["Id"] ) {
	$ID_get = $_GET["Id"];
	} else {
	$ID_get = "";
	}
  // 2 - CONEXÃO AO BANCO
	try {
		//$odbt =  "sqlite:./" . $PLAN_get;
		//$odbt = 'sqlite:'. __DIR__ .'\Projetos.db';
		$odbt = 'sqlite:'. __DIR__ . "\\" . $PLAN_get;
		//echo $odbt . "<br>";
		$conn = new PDO($odbt);
		} catch(PDOException $e) {
		echo $e->getMessage();
		}
   // 3 - CONSTRUÇÃO DO SQL
   $sql = "DELETE FROM Tasks WHERE Id = " . strval($ID_get);
   $conn->exec($sql);
echo $sql . "<br>";
?>
<button onclick="grafupd('<?php echo $PLAN_get; ?>');">ATUALIZA GRÁFICO</button>
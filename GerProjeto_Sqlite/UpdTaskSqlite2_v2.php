<script>
function grafupd(plan){
	var o = window.opener;
	var p = o.parent;
	p.window.location.href = 'LeTaskSqlite10_v2.php?Planilha=' + plan;
	}
</script>
<?php
	// Header
	header('Content-Type: text/html; charset=iso-8859-1');
	// Pega o nome da tabela na URL
	if( $_GET["Planilha"] ) {
		$PLAN_get = $_GET["Planilha"];
		} else {
		$PLAN_get = "";
		}
	$Id = $_POST["Id"];
	$CodHierarquia = $_POST["CodHierarquia"];
	$Tarefa = $_POST["Tarefa"];
	$Membro = $_POST["Membro"];
	$Dtinicial = $_POST["Dtinicial"];
	$Dtfinal = $_POST["Dtfinal"];

	// Conex�o
	try {
		//$odbt =  "sqlite:./" . $PLAN_get;
		//$odbt = 'sqlite:'. __DIR__ .'\Projetos.db';
		$odbt = 'sqlite:'. __DIR__ . "\\" . $PLAN_get;
		//echo $odbt . "<br>";
		$conn = new PDO($odbt);
		} catch(PDOException $e) {
		echo $e->getMessage();
		}
	// Sql
	$sql = "UPDATE Tasks SET CodHierarquia = '" . $CodHierarquia . "', Tarefa = '" . $Tarefa . "', Membro = '" . $Membro . "', DtInicial = '" . $Dtinicial . "', DtFinal = '" . $Dtfinal . "' WHERE Id = " . strval($Id);
	echo $sql . "<br>";
	try { 
		$res = $conn->exec($sql);
		} catch(Exception $e) {
		echo $e->getMessage();
		}
	$row = array();
?>
<button onclick="grafupd('<?php echo $PLAN_get; ?>');">ATUALIZA GR�FICO</button>
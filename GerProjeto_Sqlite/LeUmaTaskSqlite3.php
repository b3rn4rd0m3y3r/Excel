<meta charset="iso-8859-1"/>
<style>
	BODY {font-family: Arial; color: gray;}
	H1 {color: #2196F3;}
	LABEL {color: #607d8b;font-weight: bold;}
</style>
<?php
function dtinv2normal($txt){
	$normal = substr($txt,8,2) . "/" .substr($txt,5,2) . "/" . substr($txt,0,4);
	return $normal;
	}
function dtinvtraco($txt){
	$normal = substr($txt,0,4). "-" .substr($txt,5,2) . "-" . substr($txt,8,2)  ;
	return $normal;
	}
  /*
	CHAMADA:
	
	http://localhost/dbapp/LeUmaTask1.php?
	Planilha=Tasks.xls
	&Id=999
	
  */
  // Header
  header('Content-Type: text/html; charset=iso-8859-1');
  // Pega o nome da tabela na URL
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
  //print_r($ID_get);
  // Conexão
  try {
	//$odbt =  "sqlite:./" . $PLAN_get;
	$odbt = 'sqlite:'. __DIR__ .'\Projetos.db';
	//echo $odbt . "<br>";
	$conn = new PDO($odbt);
	} catch(PDOException $e) {
	echo $e->getMessage();
	}
  
  // Sql
  $sql = "SELECT * FROM Tasks WHERE Id = " . $ID_get;
  //$sql = "SELECT * FROM Tasks WHERE Id = 4";
  //echo $sql . "<br>";
  $stmt = $conn->prepare($sql);
  $res = $stmt->execute();
  $row = $stmt->fetch();
?>
<h1>REGISTRO N<sup>o</sup> <?php echo (int) $row["Id"]; ?></h1>
<form method="post" action="UpdTaskSqlite1.php">
	<table>
		<tr><td><label>Id:</label></td><td><input name=Id value="<?php echo (int) $row["Id"]; ?>"></td></tr>
		<tr><td><label>Tarefa:</label></td><td><input name=Tarefa value="<?php echo $row["Tarefa"]; ?>"></td></tr>
		<tr><td><label>Membro:</label></td><td><input name=Membro value="<?php echo mb_convert_encoding($row["Membro"], 'ISO-8859-1', 'UTF-8'); ?>"></td></tr>
		<tr><td><label>Início:</label></td><td><input type="date" name=Dtinicial value="<?php echo dtinvtraco($row["DtInicial"]); ?>"></td></tr>
		<tr><td><label>Final:</label></td><td><input type="date" name=Dtfinal value="<?php echo dtinvtraco($row["DtFinal"]); ?>"></td></tr>
		<tr><td align=center><input type=submit value="ALTERAR"></td></tr>
	<table>
</form>
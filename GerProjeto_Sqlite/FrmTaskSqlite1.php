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


  
?>
<h1>REGISTRO N<sup>o</sup> <?php echo (int) $row["Id"]; ?></h1>
<form method="post" action="AddTaskSqlite1.php">
	<table>
		<tr><td><label>Tarefa:</label></td><td><input name=Tarefa value=""></td></tr>
		<tr><td><label>Membro:</label></td><td><input name=Membro value=""></td></tr>
		<tr><td><label>Início:</label></td><td><input type="date" name=Dtinicial value=""></td></tr>
		<tr><td><label>Final:</label></td><td><input type="date" name=Dtfinal value=""></td></tr>
		<tr><td align=center><input type=submit value="GRAVAR"></td></tr>
	<table>
</form>
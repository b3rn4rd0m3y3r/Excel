<%
Const adOpenStatic = 3
Const adLockOptimistic = 3
Const adCmdText = &H1
pTitulo = Request("Titulo")
pAssunto = Request("Assunto")
pCategoria = Request("Categoria")
pKeywords = Request("Keywords")
pObservacao = Request("Observacao")
Response.Charset="UTF-8"
Response.write Server.MapPath("Docum.xls")
Provedor = "Provider=Microsoft.Jet.OLEDB.4.0;"
DBQ = "Dbq=" & Server.MapPath("Docum.xls")
Driver = "Driver={Microsoft Excel Driver (*.xls)};"
DataSource = "Data Source=" & Server.MapPath("Docum.xls") & ";"
CONN_STRING = Provedor & DataSource & "Extended Properties=""Excel 8.0;HDR=YES;IMEX=0"""
' Objetos
ADOCON = "ADODB.Connection"
ADOREC = "ADODB.Recordset"
' Conex�o
Set conn = Server.CreateObject(ADOCON)
Set rs = Server.CreateObject(ADOREC)
conn.Open CONN_STRING
rs.Open "SELECT [ID], [TITULO], [ASSUNTO] FROM [tab1$]", conn, 3, 3
Response.write "<br>"
Do While Not rs.EOF
	Response.write rs.Fields("ID").Value & " - " 
	Response.write rs.Fields("TITULO").Value & " - "
	Response.write rs.Fields("ASSUNTO").Value & "<br>"
	rs.MoveNext
Loop
NoReg = rs.RecordCount
Response.write "No. Registros:" & NoReg
NoReg = NoReg + 1
rs.Close
conn.close

conn.Open CONN_STRING_UPD

if pTitulo <> "" then
	rs.Open "[tab1$]", conn, , 3
	rs.addnew
	rs.Fields("ID").Value = NoReg
	rs.Fields("TITULO").Value = pTitulo
	rs.Fields("ASSUNTO").Value = pAssunto
	rs.update
	rs.close
end if
conn.Close
%>
<%
Const adOpenStatic = 3
Const adLockOptimistic = 3
Const adCmdText = &H1

'* Pega o parâmetro do título na URL
pTitulo = Request("Titulo")

Response.write Server.MapPath("Docum.dbf") & "<br>"
Provedor = "Provider=Microsoft.ACE.OLEDB.12.0;"
'Provedor = "Provider=Microsoft.Jet.OLEDB.4.0;"
Driver = "Driver={Microsoft dBase Driver (*.dbf)};"
DataSource = "Data Source=C:\Inetpub\wwwroot\dbase;"
DBQ = "Dbq=C:\Inetpub\wwwroot\dbase;"
CONN_STRING = Provedor & DataSource & "Extended Properties=""dBASE IV;User=Admin;Password="";"
Response.write CONN_STRING & "<br>"
' Objetos
ADOCON = "ADODB.Connection"
ADOREC = "ADODB.Recordset"
' Conexão
Set conn = Server.CreateObject(ADOCON)
Set rs = Server.CreateObject(ADOREC)
conn.Open CONN_STRING
'Response.end WHERE TITULO = """ & pTitulo & ""
rs.Open "SELECT TOP 1 [ID], [TITULO], [ASSUNTO] FROM [Docum] WHERE ( ( [TITULO] Like '%" & pTitulo & "%' ) = -1) ORDER BY [ID] DESC", conn, 3, 3
Response.write "<table>"
NoReg = rs.RecordCount
Response.write "<tr><td>Id:</td><td><input name =Id value=""" & rs.Fields("ID").Value & """></td></tr>" 
Response.write "<tr><td>Título:</td><td><input name =titulo value=""" & rs.Fields("TITULO").Value & """></td></tr>"
Response.write "<tr><td>Assunto:</td><td><input name =assunto value=""" & rs.Fields("ASSUNTO").Value & """></td></tr>"
rs.MoveNext
rs.Close
conn.Close
Response.write "</table>"

%>
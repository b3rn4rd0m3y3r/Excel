<meta charset="UTF-8">
<%
Const adOpenStatic = 3
Const adLockOptimistic = 3
Const adCmdText = &H1

pDescri = Request("Descri")

Response.write Server.MapPath("tabela1.xls")
' Drives de 32 bits instalados
'Provedor = "Provider=Microsoft.Jet.OLEDB.4.0;"
' Drives de 64 bits instalados
Provedor = "Provider=Microsoft.ACE.OLEDB.12.0;"
DBQ = "Dbq=" & Server.MapPath("tabela1.xls")
Driver = "Driver={Microsoft Excel Driver (*.xls)};"
DataSource = "Data Source=" & Server.MapPath("tabela1.xls") & ";"
CONN_STRING = Provedor & DataSource & "Extended Properties=""Excel 8.0;HDR=YES;IMEX=1"""
CONN_STRING_UPD = Provedor & DataSource & "Extended Properties=""Excel 8.0;HDR=YES;IMEX=0"""
' Objetos
ADOCON = "ADODB.Connection"
ADOREC = "ADODB.Recordset"
' Conex�o
Set conn = Server.CreateObject(ADOCON)
Set rs = Server.CreateObject(ADOREC)
conn.Open CONN_STRING
'Response.end
rs.Open "SELECT [ID], [DESCRI] FROM [tab1$]", conn, 3, 3
Response.write "<br>"
' Rela��o de registros
Do While Not rs.EOF
	Response.write rs.Fields("ID").Value & " - " 
	Response.write rs.Fields("DESCRI").Value & "<br>"
	rs.MoveNext
Loop
NoReg = rs.RecordCount
Response.write "No. Registros:" & NoReg
NoReg = NoReg + 1
rs.Close
conn.close
' Inclus�o de registro
conn.Open CONN_STRING_UPD

if pDescri <> "" then
	rs.Open "[tab1$]", conn, , 3
	rs.addnew
	rs.Fields("ID").Value = NoReg
	rs.Fields("DESCRI").Value = pDescri
	rs.update
	rs.close
end if
conn.Close
%>
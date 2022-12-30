<style>
	DIV#conn {font-family: Helvetica;font-size: 7px;color:lightgrey;}
	TD, TH {font-family: Helvetica;font-size: 9px;}
	TH {background: gray;color: white;}
</style>
<%
'Provedor = "Provider=Microsoft.ACE.OLEDB.12.0;"
'Driver = "Driver={Microsoft Excel Driver (*.xls, *.xlsx, *.xlsm, *.xlsb)};"
'DataSource = "Data Source=C:\Ap\dbase\Localidades.xls;"
'* Parâmetros da URL
if Request.QueryString("Bairro") <> "" then
	BRR_get = Request.QueryString("Bairro")
else
	BRR_get = ""
end if
'* Conexão
Provedor = "Provider=Microsoft.Jet.OLEDB.4.0;"
DBQ = "Dbq=" & Server.MapPath("Bairros.xls") &  ";"
Driver = "Driver={Microsoft Excel Driver (*.xls)};"
CONN_STRING = Driver & DBQ
'Response.write "<div id=""Conn""><br><span>" & CONN_STRING & "</span><br>"
'* Objetos
ADOCON = "ADODB.Connection"
ADOREC = "ADODB.Recordset"
'* Conexão
Set conn = Server.CreateObject(ADOCON)
Set rs = Server.CreateObject(ADOREC)
conn.Open CONN_STRING
strSQL = "SELECT [Id], [Regional], [Bairro], [Tipo], [Gerencia] "
strSQL = strSQL & " FROM [tab1$]"
if CID_get <> "" then
	strSQL = strSQL & " WHERE [Tipo] = 'Bairro' AND [Bairro] Like '%" & BRR_get & "%'"
end if
strSQL = strSQL & " ORDER BY [Bairro]"
'Response.write "<br><span>" & strSQL & "</span><br>"
'Response.write "</div>"
'* Executa SQL
rs.Open strSQL, conn, 3, 3
'* Dicionarios
SCRIPT_DICT = "Scripting.Dictionary"
Set Unidades = Server.CreateObject(SCRIPT_DICT)
Set Gerencias = Server.CreateObject(SCRIPT_DICT)
Set Municipios = Server.CreateObject(SCRIPT_DICT)
'* Variáveis
brk = "@#$%"
UN_ant = brk
GR_ant = brk
'* Tabela de registros
Response.write "<table>"
Response.write "<tr><th>UN</th><th>GR</th>"
Response.write "<th>Nome da Localidade</th></tr>"
Do While Not rs.EOF
	ID = rs.Fields("Id").Value
	RG = rs.Fields("Regional").Value
	Bairro = rs.Fields("Bairro").Value
	Tipo = rs.Fields("Tipo").Value
	GR = rs.Fields("Gerencia").Value
	'if GR <> GR_ant then
	'	if GR_ant <> brk then
	'		Gerencias.Add UN_ant & GR_ant, 0
	'	end if
	'	GR_ant = GR
	'end if
	'if UN <> UN_ant then
	'	if UN_ant <> brk then
	'		Unidades.Add UN_ant, 0
	'	end if
	'	UN_ant = UN
	'end if
	Response.write "<tr>"
	Response.write "<td>" & RG & "</td>"
	Response.write "<td>" & Bairro & "</td>"
	Response.write "<td>" & GR & "</td>"
	Response.write "</tr>"
	Municipios.Add GR & RG & ID & Bairro , 0
	rs.MoveNext
Loop
Response.write "</table>"
'* Últimas quebras
'* Gerencias.Add UN_ant & GR_ant, 0
'* Unidades.Add  UN_ant, 0
rs.Close
conn.Close
'* Finais
'*
'*
For each chave2 in Municipios.Keys
    'Response.Write( chave2 & " => " & Municipios.Item(chave2) & "<br>")
Next
'Response.write "<br>------------------------------------------------ (3)<br>"
%>
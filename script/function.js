function Loaddiv(id_load,halaman,value){
	$("#"+id_load).html("<img src='../images/ajax.gif' align='center' >");
	$("#"+id_load).load(halaman+"?"+value);
}
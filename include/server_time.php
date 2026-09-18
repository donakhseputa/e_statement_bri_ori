<script type="text/javascript">
<!--
function clock() {
    document.write('<span id="clock"></span>');
    // here's the PHP code that spawn out the server local time
    <?php list($sy,$sm,$sd,$sa,$sh,$si,$ss) = explode(",", date("y,n,d,a,G,i,s")); ?>
    // now we represent the server time as javascript date
    var server = new Date(<?=$sy?>, <?=$sm?>, <?=$sd?>, <?=$sh?>, <?=intval($si)?>, <?=intval($ss)?>).getTime();
    // calculate the client time
    var client = new Date().getTime();
    // run our ticker
    tick(client-server);
}
function tick(diff) {
	
	/*var d=new Date()*/
	var month=new Array(12);
	month[1]="January";
	month[2]="February";
	month[3]="March";
	month[4]="April";
	month[5]="May";
	month[6]="June";
	month[7]="July";
	month[8]="August";
	month[9]="September";
	month[10]="October";
	month[11]="November";
	month[12]="December";
    
	var d = new Date(new Date().getTime()-diff);
	var y = '20'+ d.getYear();
	var mo = d.getMonth();
	var m = month[mo];
	var tgl = d.getDate();
    var i = d.getMinutes(); if(i < 10) i='0'+i;
    var s = d.getSeconds(); if(s < 10) s='0'+s;
    var clock = document.getElementById("clock");
	
    if(clock) {
        clock.innerHTML=
		tgl+' '+m+' '+y+' '+
            d.getHours()+':'+i+':'+s+'';
        setTimeout('tick(' + diff + ');', 1000);
    }
}
//-->
</script>
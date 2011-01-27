function createLogs(){
 var popup = window.showModalDialog(modulechatbaselink+"admin.php?force=createLogs","","dialogWidth: 400px; dialogHeight: 400px; center: yes;");
 //popup.focus();
}


function clearU2ULogs(){

 var x=window.confirm("This action is irreversible! Are you sure?")
 if (x)
  $J.get(modulechatbaselink+"admin.php?force=clearU2ULogs", function(data){
      alert(data);
   });
 return false;
}

function setChatheartBeat(){

 /*var val = prompt('After how many seconds will new messages be searched?', '');

	if (!IsNumeric(val)){

		alert('You did not enter a valid number.');

		return;

	}

	val = val*1000;

	$J.get(modulechatbaselink+"admin.php?force=setChatheartBeat&t="+val);*/
 /*var rate;

	$J.get(modulechatbaselink+'admin.php?force=getChatHeartbeat', '', function(data){

   			//alert(data);

			//$J.(".action").css("cursor","help");

			//$J.("#heartbeatrate").html("Current Chat Engine Rate: <br>");

			$J('#heartbeatrateDiv').html("Current Chat Engine rate: "+data);

			$J('#heartbeatrate').toggle();

			return false;

 		});*/
}
function setRefresh_rate(){
 var val = prompt('Every how many seconds will the user list be populated?', '');
 if (!IsNumeric(val)){
  alert('You did not enter a valid number.');
  return;
 }
 val = val*1000;
 $J.get(modulechatbaselink+"admin.php?force=setRefresh_rate&t="+val);
}
function IsNumeric(input)
{
   return (input - 0) == input && input.length > 0;
}
var win=null;
function NewWindow(mypage,myname,w,h,scroll,pos){
if(pos=="random"){LeftPosition=(screen.width)?Math.floor(Math.random()*(screen.width-w)):100;TopPosition=(screen.height)?Math.floor(Math.random()*((screen.height-h)-75)):100;}
if(pos=="center"){LeftPosition=(screen.width)?(screen.width-w)/2:100;TopPosition=(screen.height)?(screen.height-h)/2:100;}
else if((pos!="center" && pos!="random") || pos==null){LeftPosition=0;TopPosition=20}
settings='width='+w+',height='+h+',top='+TopPosition+',left='+LeftPosition+',scrollbars='+scroll+',location=no,directories=no,status=no,menubar=no,toolbar=no,resizable=no';
win=window.open(mypage,myname,settings);}

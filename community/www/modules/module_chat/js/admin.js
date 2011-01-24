


function createLogs(){
 var popup = window.showModalDialog(modulechatbaselink+"admin.php?force=createLogs","","dialogWidth: 400px;");
 popup.focus();
}

function clearU2ULogs(){

 var x=window.confirm("Are you sure? This action cannot be changed afterwards!")
 if (x)
  $J.get(modulechatbaselink+"admin.php?force=clearU2ULogs", function(data){
      alert(data);
   });
}

function setChatheartBeat(){

 var val = prompt('After how many seconds will new messages be searched?', '');
 if (!IsNumeric(val)){
  alert('You did not enter a valid number.');
  return;
 }
 val = val*1000;
 $J.get(modulechatbaselink+"admin.php?force=setChatheartBeat&t="+val);
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

if ($('autocomplete_lessons')) {
 new Ajax.Autocompleter("autocomplete",
         "autocomplete_lessons",
         "admin.php?fors=getlessons", {paramName: "preffix",
            afterUpdateElement : function (t, li) {var x = "to do next...";},
            indicator : "busy"});
}



function clearU2ULogs(){

 var x=window.confirm("This action is irreversible! Are you sure?")
 if (x)
  $J.get(modulechatbaselink+"admin.php?force=clearU2ULogs", function(data){
      alert(data);
   });
 return false;
}

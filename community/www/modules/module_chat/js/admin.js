if ($('autocomplete_lessons')) {
 new Ajax.Autocompleter("autocomplete",
         "autocomplete_lessons",
         "ask.php?ask_type=lesson", {paramName: "preffix",
            afterUpdateElement : function (t, li) {
                  $J.get(modulechatbaselink+"admin.php?force=getLessonFromId&loglessonid="+li.id, function(data){
                      $J('#autocomplete').val(data.substring(4));
                  });
                },
            indicator : "busy"});
}



function clearU2ULogs(){

 var x=window.confirm("This action is irreversible! Are you sure?")
 if (x){
  if ($J('#clearLessonLogs').is(':checked')){
   $J.get(modulechatbaselink+"admin.php?force=clearAllLogs", function(data){
       alert(data);
    });
  }
  else{
   $J.get(modulechatbaselink+"admin.php?force=clearU2ULogs", function(data){
       alert(data);
    });
  }
 }
}

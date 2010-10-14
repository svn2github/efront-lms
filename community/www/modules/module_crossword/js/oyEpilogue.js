if (!oygInit){
 oygError =
  "There was an error requesting puzzle data from the server.\n" +
  "Please try again shortly or send us a note about the problem."
} else {
 oygBind(oygCrosswordPuzzle);
}
if (oygError){
 alert(oygError);
}

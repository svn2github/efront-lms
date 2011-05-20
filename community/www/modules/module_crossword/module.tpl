{*Smarty template*}
{if $smarty.session.s_type == "professor"}
{if $smarty.get.view_list != ""}
{capture name = "t_module_crossword_crosslists_list_code"}
        <table class = "QuestionsListTable sortedTable" id = "crosswordCardTable">
            <tr><td class = "topTitle" name = "text">{$smarty.const._CROSSWORD_CLUE}</td>
                <td class = "topTitle" name = "parent_name">{$smarty.const._CROSSWORD_ANSWER}</td>
                <td class = "topTitle centerAlign" name = "partof">{$smarty.const._CROSSWORD_USELIST}</td></tr>
   {foreach name = "crosslists_list" key = "key" item = "item" from = $T_CROSSWORD_CROSSLISTS}
            <tr class = "{cycle name = "main_cycle" values="oddRowColor, evenRowColor"}">
                <td><a href = "{$smarty.server.PHP_SELF}?ctg=tests&edit_question={$item->question.id}&question_type=empty_spaces">{$item->question.text|eF_truncate:50}</a></td>
                <td>
    {foreach name = "card_answer" key = "key2" item = "answerCard" from = $item->answer}
     {if !$smarty.foreach.card_answer.last}
      {$answerCard}&rarr;
     {else}
      {$answerCard}
     {/if}

    {/foreach}
    </td>

                <td class = "centerAlign"><input class = "inputCheckbox" type = "checkbox" name = "checked_{$item->question.id}" id = "checked_{$item->question.id}" onclick = "ajaxPost('{$item->question.id}', this);" {if in_array($item->question.id, $T_CROSSWORD_LIST_WORDS)}checked = "checked"{/if} /></td> {*span is used for sorting*}
            </tr>
            {foreachelse}
            <tr class = "oddRowColor defaultRowHeight"><td class = "emptyCategory" colspan = "100%">{$smarty.const._NODATAFOUND}</td></tr>
            {/foreach}
        </table>
{literal}
        <script>
        function ajaxPost(id, el, table_id) {
            var baseUrl = '{/literal}{$T_MODULE_CROSSWORD_BASEURL}{literal}&view_list={/literal}{$smarty.get.view_list}{literal}&postAjaxRequest=1';
            if (id) {
                var checked = $('checked_'+id).checked;
                var url = baseUrl + '&id='+id;
                var img_id = 'img_'+id;
            } else if (table_id && table_id == 'crosswordCardTable') {
                el.checked ? url = baseUrl + '&addAll=1' : url = baseUrl + '&removeAll=1';
                if ($(table_id+'_currentFilter')) {
                 url = url+'&filter='+$(table_id+'_currentFilter').innerHTML;
                }
                var img_id = 'img_selectAll';
            }

            var position = eF_js_findPos(el);
            var img = document.createElement("img");

            img.style.position = 'absolute';
            img.style.top = Element.positionedOffset(Element.extend(el)).top + 'px';
            img.style.left = Element.positionedOffset(Element.extend(el)).left + 6 + Element.getDimensions(Element.extend(el)).width + 'px';

            img.setAttribute("id", img_id);
            img.setAttribute('src', '{/literal}{$T_MODULE_CROSSWORD_BASELINK}{literal}images/progress1.gif');

            el.parentNode.appendChild(img);

                new Ajax.Request(url, {
                        method:'get',
                        asynchronous:true,
                        onSuccess: function (transport) {
                            img.style.display = 'none';
                            img.setAttribute('src', 'images/16x16/success.png');
                            new Effect.Appear(img_id);
                            window.setTimeout('Effect.Fade("'+img_id+'")', 2500);
                            }
                    });
        }
        </script>
        {/literal}

{/capture}
{capture name = 't_module_crossword_list_options_code'}
    {$T_CROSSWORD_OPTIONS.javascript}
    <form {$T_CROSSWORD_OPTIONS.attributes}>
    {$T_CROSSWORD_OPTIONS.hidden}
        <table class = "formElements" width="100%">
            <tr><td class = "labelCell">{$T_CROSSWORD_OPTIONS.active.label}:&nbsp;</td>
                <td class = "elementCell">{$T_CROSSWORD_OPTIONS.active.html}</td></tr>
                {if $T_CROSSWORD_OPTIONS.active.error}<tr><td></td><td class = "formError">{$T_CROSSWORD_OPTIONS.active.error}</td></tr>{/if}

   <tr><td class = "labelCell">{$T_CROSSWORD_OPTIONS.reveal_answer.label}:&nbsp;</td>
                <td class = "elementCell">{$T_CROSSWORD_OPTIONS.reveal_answer.html}</td></tr>
                {if $T_CROSSWORD_OPTIONS.reveal_answer.error}<tr><td></td><td class = "formError">{$T_CROSSWORD_OPTIONS.reveal_answer.error}</td></tr>{/if}
            <tr><td class = "labelCell">{$T_CROSSWORD_OPTIONS.save_pdf.label}:&nbsp;</td>
                <td class = "elementCell">{$T_CROSSWORD_OPTIONS.save_pdf.html}</td></tr>
                {if $T_CROSSWORD_OPTIONS.save_pdf.error}<tr><td></td><td class = "formError">{$T_CROSSWORD_OPTIONS.save_pdf.error}</td></tr>{/if}
            <tr><td class = "labelCell">{$smarty.const._CROSSWORD_NUMBEROFWORDS}:&nbsp;</td>
   <td class = "elementCell">{$T_CROSSWORD_OPTIONS.max_word.html}
   </td></tr>
   {if $T_CROSSWORD_OPTIONS.max_word.error}<tr><td></td><td class = "formError">{$T_CROSSWORD_OPTIONS.max_word.error}</td></tr>{/if}

   <tr><td></td><td class = "submitCell">{$T_CROSSWORD_OPTIONS.submit_options.html}</td></tr>
        </table>
    </form>
    {literal}
        <script>
        function optionSubmit(){
        itemVal = document.list_options.max_word.value;
        if(itemVal=='' || itemVal==0){
        alert("Please enter max no of words");
        return false;
        }else{
  var objRegEx = /^[0-9]*$/i;
  res = objRegEx.test(itemVal);
  if(!res) {
   alert("Please enter max no of words only integer");
   document.list_options.max_word.focus();
      return false;
  }else{
   if(itemVal>30){
   alert("Please enter max no of words <=30");
   document.list_options.max_word.focus();
      return false;
   }
  }
  }
        }
        </script>
    {/literal}
{/capture}
{capture name = "t_module_crossword_words_code"}
  <div class = "tabber">
         <div class = "tabbertab" title = "{$smarty.const._CROSSWORD_CLUES}">
    {eF_template_printBlock title=$smarty.const._CROSSWORD_CLUES_ANSWER data=$smarty.capture.t_module_crossword_crosslists_list_code image=$T_MODULE_CROSSWORD_BASELINK|cat:'images/crossword32.png' absoluteImagePath=1}
            </div>
   <div class = "tabbertab {if $smarty.get.tab == 'options'}tabbertabdefault{/if}"" title = "{$smarty.const._CROSSWORD_OPTIONS}">
    {eF_template_printBlock title=$smarty.const._CROSSWORD_OPTIONS data=$smarty.capture.t_module_crossword_list_options_code image=$T_MODULE_CROSSWORD_BASELINK|cat:'images/crossword32.png' absoluteImagePath=1}
   </div>
  </div>
{/capture}
{eF_template_printBlock title=$smarty.const._CROSSWORD_WORDS data=$smarty.capture.t_module_crossword_words_code image=$T_MODULE_CROSSWORD_BASELINK|cat:'images/crossword32.png' absoluteImagePath=1}
{else}

{capture name = "t_module_crossword_words_list_code"}
<table class = "sortedTable" width="100%">
    <tr>
    <td class = "topTitle smallHeader" style = "width:20%;text-align:left;vertical-align:middle">{$smarty.const._CROSSWORD_NAME}</td>
    <td style = "width:40%" class = "topTitle centerAlign">{$smarty.const._CROSSWORD_LISTS}</td>
    <td style = "width:10%" class = "topTitle centerAlign">{$smarty.const._CROSSWORD_PUBLISHED}</td>
 </tr>
 {foreach name = "flashlists_list" item = "list" key = "key" from = $T_CROSSWORD_WORDS}
 <tr class = "{cycle name = $key values = "oddRowColor,evenRowColor"}">
  <td><span style="display:none">{$list.name}</span><a href="{$T_MODULE_CROSSWORD_BASEURL}&view_list={$list.id}">{$list.name}</a></td>
  <td class="centerAlign">{$list.num_crosslists}/{$list.questions}</td>
  <td class = "centerAlign">
  {if $list.options.active == 1}
   <img src = "{$T_MODULE_CROSSWORD_BASELINK}images/success.png" alt="{$smarty.const._ACTIVE}" title="{$smarty.const._ACTIVE}" style = "vertical-align:middle" />
  {/if}
  </td>
 </tr>
 {foreachelse}
  <tr class = "oddRowColor defaultRowHeight"><td colspan = "100%" class = "emptyCategory">{$smarty.const._NODATAFOUND}</td></tr>
 {/foreach}
</table>
{/capture}
{eF_template_printBlock title=$smarty.const._CROSSWORD_WORDS data=$smarty.capture.t_module_crossword_words_list_code image=$T_MODULE_CROSSWORD_BASELINK|cat:'images/crossword32.png' absoluteImagePath=1}
{/if}
{elseif $smarty.session.s_type == "student"}
<script>
var translationgamestarted = '{$smarty.const._CROSSWORD_GAMESTARTED}';
var translationscore = '{$smarty.const._SCORE}';
var translationstatus = '{$smarty.const._STATUS}';
var translationacross = '{$smarty.const._CROSSWORD_ACROSS}';
var translationdown = '{$smarty.const._CROSSWORD_DOWN}';
var translationtime = '{$smarty.const._CROSSWORD_TIME}';
var translationcheck = '{$smarty.const._CROSSWORD_CHECK}';
var translationcheckdisabled = '{$smarty.const._CROSSWORD_CHECKDISABLED}';
var translationcheckall = '{$smarty.const._CROSSWORD_CHECKALL}';
var translationchecked = '{$smarty.const._CROSSWORD_CHECKED}';
var translationmatched = '{$smarty.const._CROSSWORD_MATCHED}';
var translationreveal = '{$smarty.const._CROSSWORD_REVEAL}';
var translationrevealed = '{$smarty.const._CROSSWORD_REVEALED}';
var translationacrossfirstletter = '{$smarty.const._CROSSWORD_ACROSSFIRSTLETTER}';
var translationdownfirstletter = '{$smarty.const._CROSSWORD_DOWNFIRSTLETTER}';
var translationnocompletewordsfound = '{$smarty.const._CROSSWORD_NOCOMPLETEWORDSFOUND}';
var translationsubmit = '{$smarty.const._SUBMIT}';
var translationhavenotpoints = '{$smarty.const._CROSSWORD_HAVENOTSUFFICIENTPOINTS}';
var translationword = '{$smarty.const._CROSSWORD_THEWORD}';
var translationincomplete = '{$smarty.const._CROSSWORD_ISINCOMPLETE}';
var translationdidnotmatch = '{$smarty.const._CROSSWORD_DIDNOTMATCH}';
</script>
 {if $smarty.get.finish == 1}

 {elseif isset($smarty.get.view_list)}
 {capture name = "t_module_crossword_words_puzzle"}
    <form {$T_CROSSWORD_SUBMIT.attributes} >
    <div id="oygContext" align="center" style="width:100%; //margin-top:-20px;" >
    <table class="oyOuterFrame" border="0" cellpadding="0" cellspacing="0" width="100%" >
    <tr><td align="center">
    <table class="oyFrame" border="0" cellpadding="0" cellspacing="0" width="100%" >
    <tr >
      <td colspan="4" class="title_bg" align="left" >
      {$T_CROSSWORD_UNITNAME}
      </td>
      <td class="title_bg" align="right" ><a href="{$T_MODULE_CROSSWORD_BASEURL}" ><img src = "{$T_MODULE_CROSSWORD_BASELINK}images/delete.png" border="0"/></a>
      </td>
    </tr>
    <tr>
    <td colspan="5">
    <div id="oygFooter" style="font-size:18px;font-weight:bold;"></div>
    <div id="oygHeader" style="display:none;"></div>
    </td>
    </tr>
     <tr>
      <td rowspan="3" class="oyPuzzleCell" align="left" valign="top" >
       <div id="oygState" style="font-color:#fff;display:none;" ></div>
       <div style="width:100%;background:#003366;" >
       <div class="oyPuzzle" id="oygPuzzle" ></div>
       </div>
       <div class="oyPuzzleFooter" id="oygPuzzleFooter" ></div>
      </td>
      <td class="oyListCell" colspan="4" valign="top" id="oygListH" style="font-size:16px;text-align:left;"></td>
     </tr>
     <tr>
      <td class="oyListCell" colspan="5" valign="top" id="oygListV" style="font-size:16px;text-align:left;"></td>
     </tr>
     <tr>
      <td colspan="5" class="oyFooter" style="font-size:14px;">
      <input type='hidden' name='crosstime' id='crosstime'>
      <input type='hidden' name='points' id='points'>
      <input type='hidden' name='completewordlength' id='completewordlength' value={$T_CROSSWORD_LENGTH}>
      <input type='hidden' name='crossreveal' id='crossreveal' value={$T_CROSSWORD_REVEALANSWER}>
      </td>
     </tr>
    </table>
   </td></tr>
  </table>
  <div id="oygStatic" align="center" style="font-size: 10px; color: #4282B5; font-family: Arial;"></div>
 </div>
</form>
 <!--
  here we include all oy-cword-1.0 CSS files; all our style class name are prefixed with "oy"
 -->
 <link rel="stylesheet" href="{$T_MODULE_CROSSWORD_BASELINK}css/base.css" type="text/css">

 <!--
  here we include all oy-cword-1.0 JavaScript files; order is important;
  all the files can be combined into one file to reduce number of separate requests
 -->
 <script type="text/javascript" src="{$T_MODULE_CROSSWORD_BASELINK}js/oyPrologue.js"></script>
 <script type="text/javascript" src="{$T_MODULE_CROSSWORD_BASELINK}js/oyJsrAjax.js"></script>
 <script type="text/javascript" src="{$T_MODULE_CROSSWORD_BASELINK}js/oyClue.js"></script>
 <script type="text/javascript" src="{$T_MODULE_CROSSWORD_BASELINK}js/oyMenu.js"></script>
 <script type="text/javascript" src="{$T_MODULE_CROSSWORD_BASELINK}js/oyPuzzle.js"></script>
 <script type="text/javascript" src="{$T_MODULE_CROSSWORD_BASELINK}js/oyServer.js"></script>
 <script type="text/javascript" src="{$T_MODULE_CROSSWORD_BASELINK}js/oySign.js"></script>
 <script type="text/javascript" src="{$T_MODULE_CROSSWORD_BASELINK}js/oyMisc.js"></script>

 <script type="text/javascript"><!--

  // 
  //	here we include our own puzzle; it has to be fully prepared with all words properly arranged on the grid;
  //	currently only one instance of the puzzle can be embedded into the page, we may fix this in the future
  // 

  var oygCrosswordPuzzle = new oyCrosswordPuzzle (
    "5748185539682739085",
    "{$T_MODULE_CROSSWORD_BASELINK}",
    "/a/a",
    "",
    "",
    [
      {foreach name = "crossword_list" item = "crosswordlist" key = "key" from = $T_CROSSWORD_ANSWERS}
     new oyCrosswordClue({$crosswordlist.wordlength}, "{$crosswordlist.question|replace:'"':'\"'}", '{$crosswordlist.word}', "26f265b96e01081a5ef26a432eda9cff", '{$crosswordlist.axis-1}', {$crosswordlist.x}, {$crosswordlist.y}),
    {/foreach}
   ],
     {$T_CROSSWORD_MAXWORD},
     {$T_CROSSWORD_MAXWORD}
  );

  //
  //	here we configure puzzle options, callbacks and publisher information
  // 

  // publisher information
  oygCrosswordPuzzle.publisherName = "";
  oygCrosswordPuzzle.publisherURL = "";
  // game exit URL
  oygCrosswordPuzzle.leaveGameURL = "{$T_MODULE_CROSSWORD_BASEURL}";
       // this is how to turn off server support; score submission and action tracking will be disabled
  oygCrosswordPuzzle.canTalkToServer = false;

 --></script>

 <!--
  here we instantiate the puzzle and bind it to the HTML template above and show it to the user
 -->
 <script type="text/javascript" src="{$T_MODULE_CROSSWORD_BASELINK}js/oyEpilogue.js"></script>

      {/capture}
  {eF_template_printBlock title=$smarty.const._CROSSWORD_WORDS data=$smarty.capture.t_module_crossword_words_puzzle image=$T_MODULE_CROSSWORD_BASELINK|cat:'images/crossword32.png' absoluteImagePath=1}


 {else}
  {capture name = "t_module_crossword_words_list_code"}
  <table class = "sortedTable" width="100%">
   <tr>
   <td class = "topTitle leftAlign">{$smarty.const._CROSSWORD_NAME}</td>
   <td class = "topTitle centerAlign">{$smarty.const._CROSSWORD_SAVEPDF}</td>
   <td class = "topTitle centerAlign">{$smarty.const._CROSSWORD_SCORE}</td>
   <td class = "topTitle noSort centerAlign"><span style = "vertical-align:middle">{$smarty.const._CROSSWORD_TIME}</span></td>
   </tr>
   {foreach name = "flashlists_list" item = "list" key = "key" from = $T_CROSSWORD_WORDS}
   <tr class = "{cycle name = $key values = "oddRowColor,evenRowColor"}">
    <td><span style="display:none">{$T_CROSSWORD_WORDSNAMES.$key.name}</span><a href="{$T_MODULE_CROSSWORD_BASEURL}&view_list={$key}">{$T_CROSSWORD_WORDSNAMES.$key.name}</a></td>
    <td class = "centerAlign">{if $list.options.save_pdf eq 1}
    <a href = "{$T_MODULE_CROSSWORD_BASEURL}&view_list={$key}&pdf=cross">
    <img src = "{$T_MODULE_CROSSWORD_BASELINK}images/pdf-icon.png" alt="{$smarty.const._CROSSWORD_SAVEPDF}" title="{$smarty.const._CROSSWORD_SAVEPDF}" style = "vertical-align:middle" />
    </a>
    {else}{/if}</td>
    <td class = "progressCell">
     {if $list.points gt 0}
     <span class = "progressNumber">{$list.points}%</span>
      <span class = "progressBar" style = "width:{$list.points}px;">&nbsp;</span>
     {/if}
    </td>
    <td class = "centerAlign">
    {if $list.points gt 0}
    {$list.crosstime}
    {/if}
    </td>
   </tr>
   {foreachelse}
    <tr class = "oddRowColor defaultRowHeight"><td colspan = "100%" class = "emptyCategory">{$smarty.const._NODATAFOUND}</td></tr>
   {/foreach}
  </table>
  {/capture}
  {eF_template_printBlock title=$smarty.const._CROSSWORD_WORDS data=$smarty.capture.t_module_crossword_words_list_code image=$T_MODULE_CROSSWORD_BASELINK|cat:'images/crossword32.png' absoluteImagePath=1}
 {/if}
{/if}

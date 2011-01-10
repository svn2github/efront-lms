{*Smarty template*}
{if $smarty.session.s_type == "professor"}
{if $smarty.get.view_deck != ""}
{capture name = "t_module_flashcard_cards_list_code"}
        <table class = "QuestionsListTable sortedTable" id = "flashcardsCardTable">
            <tr><td class = "topTitle" name = "text">{$smarty.const._QUESTIONTEXT}</td>
                <td class = "topTitle" name = "parent_name">{$smarty.const._FLASHCARDS_ANSWER}</td>
                <td class = "topTitle centerAlign" name = "difficulty">{$smarty.const._DIFFICULTY}</td>
                <td class = "topTitle centerAlign noSort">{$smarty.const._OPERATIONS}</td>
                <td class = "topTitle centerAlign" name = "partof">{$smarty.const._FLASHCARDS_USECARD}</td></tr>
   {foreach name = "cards_list" key = "key" item = "item" from = $T_FLASHCARDS_CARDS}
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
                <td class = "centerAlign">
                    {if $item->question.difficulty == 'low'} <img src = "{$T_MODULE_FLASHCARDS_BASELINK}images/flag_green.png" title = "{$smarty.const._LOW}" alt = "{$smarty.const._LOW}" />
                    {elseif $item->question.difficulty == 'medium'} <img src = "{$T_MODULE_FLASHCARDS_BASELINK}images/flag_blue.png" title = "{$smarty.const._MEDIUM}" alt = "{$smarty.const._MEDIUM}" />
                    {elseif $item->question.difficulty == 'high'} <img src = "{$T_MODULE_FLASHCARDS_BASELINK}images/flag_yellow.png" title = "{$smarty.const._HIGH}" alt = "{$smarty.const._HIGH}" />
                    {elseif $item->question.difficulty == 'very_high'}<img src = "{$T_MODULE_FLASHCARDS_BASELINK}images/flag_red.png" title = "{$smarty.const._VERYHIGH}" alt = "{$smarty.const._VERYHIGH}" />
                    {/if}
                    <span style = "display:none">{$item->question.difficulty}</span>{*We put this here in order to be able to sort by type*}
                </td>
             <td class = "centerAlign">
     <a href = "{$smarty.server.PHP_SELF}?ctg=tests&show_question={$item->question.id}&popup=1" target = "POPUP_FRAME" onclick="eF_js_showDivPopup('{$smarty.const._PREVIEW}',2)"><img src = "{$T_MODULE_FLASHCARDS_BASELINK}images/search.png" alt="{$smarty.const._PREVIEW}" title="{$smarty.const._PREVIEW}" border = "0" style = "vertical-align:middle" /></a>
     <a href = "{$smarty.server.PHP_SELF}?ctg=tests&edit_question={$item->question.id}&question_type=empty_spaces"><img src = "{$T_MODULE_FLASHCARDS_BASELINK}images/edit.png" alt="{$smarty.const._EDIT}" title="{$smarty.const._EDIT}" border = "0" style = "vertical-align:middle" /></a>
    </td>
                <td class = "centerAlign"><input class = "inputCheckbox" type = "checkbox" name = "checked_{$item->question.id}" id = "checked_{$item->question.id}" onclick = "ajaxPost('{$item->question.id}', this);" {if in_array($item->question.id, $T_FLASHCARDS_DECK_CARDS)}checked = "checked"{/if} /></td> {*span is used for sorting*}
            </tr>
            {foreachelse}
            <tr class = "oddRowColor defaultRowHeight"><td class = "emptyCategory" colspan = "100%">{$smarty.const._NODATAFOUND}</td></tr>
            {/foreach}
        </table>
{literal}
        <script>
        function ajaxPost(id, el, table_id) {
            var baseUrl = '{/literal}{$T_MODULE_FLASHCARDS_BASEURL}{literal}&view_deck={/literal}{$smarty.get.view_deck}{literal}&postAjaxRequest=1';
            if (id) {
                var checked = $('checked_'+id).checked;
                var url = baseUrl + '&id='+id;
                var img_id = 'img_'+id;
            } else if (table_id && table_id == 'flashcardsCardTable') {
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
            img.setAttribute('src', '{/literal}{$T_MODULE_FLASHCARDS_BASELINK}{literal}images/progress1.gif');

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
{capture name = 't_module_flashcard_deck_options_code'}
    {$T_FLASHCARDS_OPTIONS.javascript}
    <form {$T_FLASHCARDS_OPTIONS.attributes}>
    {$T_FLASHCARDS_OPTIONS.hidden}
        <table class = "formElements" width="100%">
            <tr><td class = "labelCell">{$T_FLASHCARDS_OPTIONS.active.label}:&nbsp;</td>
                <td class = "elementCell">{$T_FLASHCARDS_OPTIONS.active.html}</td></tr>
                {if $T_FLASHCARDS_OPTIONS.active.error}<tr><td></td><td class = "formError">{$T_FLASHCARDS_OPTIONS.active.error}</td></tr>{/if}

   <tr><td class = "labelCell">{$smarty.const._FLASHCARD_NUMBEROFSUCCESSFORMASTERY}:&nbsp;</td>
   <td class = "elementCell">{$T_FLASHCARDS_OPTIONS.low.label} {$T_FLASHCARDS_OPTIONS.low.html}&nbsp;
    {$T_FLASHCARDS_OPTIONS.medium.label} {$T_FLASHCARDS_OPTIONS.medium.html}&nbsp;
    {$T_FLASHCARDS_OPTIONS.hard.label} {$T_FLASHCARDS_OPTIONS.hard.html}&nbsp;
    {$T_FLASHCARDS_OPTIONS.very_hard.label} {$T_FLASHCARDS_OPTIONS.very_hard.html}&nbsp;
   </td></tr>
   {if $T_FLASHCARDS_OPTIONS.low.error}<tr><td></td><td class = "formError">{$T_FLASHCARDS_OPTIONS.low.error}</td></tr>{/if}
   {if $T_FLASHCARDS_OPTIONS.medium.error}<tr><td></td><td class = "formError">{$T_FLASHCARDS_OPTIONS.medium.error}</td></tr>{/if}
   {if $T_FLASHCARDS_OPTIONS.hard.error}<tr><td></td><td class = "formError">{$T_FLASHCARDS_OPTIONS.hard.error}</td></tr>{/if}
   {if $T_FLASHCARDS_OPTIONS.very_hard.error}<tr><td></td><td class = "formError">{$T_FLASHCARDS_OPTIONS.very_hard.error}</td></tr>{/if}

   <tr><td class = "labelCell">{$T_FLASHCARDS_OPTIONS.answer_first.label}:&nbsp;</td>
                <td class = "elementCell">{$T_FLASHCARDS_OPTIONS.answer_first.html}</td></tr>
                {if $T_FLASHCARDS_OPTIONS.answer_first.error}<tr><td></td><td class = "formError">{$T_FLASHCARDS_OPTIONS.answer_first.error}</td></tr>{/if}
            <tr><td class = "labelCell">{$T_FLASHCARDS_OPTIONS.shuffle.label}:&nbsp;</td>
                <td class = "elementCell">{$T_FLASHCARDS_OPTIONS.shuffle.html}</td></tr>
                {if $T_FLASHCARDS_OPTIONS.shuffle.error}<tr><td></td><td class = "formError">{$T_FLASHCARDS_OPTIONS.shuffle.error}</td></tr>{/if}
            <tr><td class = "labelCell">{$T_FLASHCARDS_OPTIONS.display_mastery.label}:&nbsp;</td>
                <td class = "elementCell">{$T_FLASHCARDS_OPTIONS.display_mastery.html}</td></tr>
                {if $T_FLASHCARDS_OPTIONS.display_mastery.error}<tr><td></td><td class = "formError">{$T_FLASHCARDS_OPTIONS.display_mastery.error}</td></tr>{/if}
            <tr><td class = "labelCell">{$T_FLASHCARDS_OPTIONS.wrong.label}:&nbsp;</td>
                <td class = "elementCell">{$T_FLASHCARDS_OPTIONS.wrong.html}</td></tr>
                {if $T_FLASHCARDS_OPTIONS.wrong.error}<tr><td></td><td class = "formError">{$T_FLASHCARDS_OPTIONS.wrong.error}</td></tr>{/if}
            <tr><td class = "labelCell">{$T_FLASHCARDS_OPTIONS.show_count.label}:&nbsp;</td>
                <td class = "elementCell">{$T_FLASHCARDS_OPTIONS.show_count.html}</td></tr>
                {if $T_FLASHCARDS_OPTIONS.show_count.error}<tr><td></td><td class = "formError">{$T_FLASHCARDS_OPTIONS.show_count.error}</td></tr>{/if}
            <tr><td class = "labelCell">{$T_FLASHCARDS_OPTIONS.show_explanation.label}:&nbsp;</td>
                <td class = "elementCell">{$T_FLASHCARDS_OPTIONS.show_explanation.html}</td></tr>
                {if $T_FLASHCARDS_OPTIONS.show_explanation.error}<tr><td></td><td class = "formError">{$T_FLASHCARDS_OPTIONS.show_explanation.error}</td></tr>{/if}

   <tr><td></td><td class = "submitCell">{$T_FLASHCARDS_OPTIONS.submit_options.html}</td></tr>
        </table>
    </form>
{/capture}
{capture name = "t_module_flashcard_decks_code"}
  <div class = "tabber">
         <div class = "tabbertab" title = "{$smarty.const._FLASHCARDS_CARDS}">
    {eF_template_printBlock title=$smarty.const._FLASHCARDS_CARDS data=$smarty.capture.t_module_flashcard_cards_list_code image=$T_MODULE_FLASHCARDS_BASELINK|cat:'images/flashcard32.png' absoluteImagePath=1 help = "Flashcards"}
            </div>
   <div class = "tabbertab {if $smarty.get.tab == 'options'}tabbertabdefault{/if}"" title = "{$smarty.const._FLASHCARDS_OPTIONS}">
    {eF_template_printBlock title=$smarty.const._FLASHCARDS_OPTIONS data=$smarty.capture.t_module_flashcard_deck_options_code image=$T_MODULE_FLASHCARDS_BASELINK|cat:'images/flashcard32.png' absoluteImagePath=1 help = "Flashcards"}
   </div>
  </div>
{/capture}
{eF_template_printBlock title=$smarty.const._FLASHCARDS_DECKS data=$smarty.capture.t_module_flashcard_decks_code image=$T_MODULE_FLASHCARDS_BASELINK|cat:'images/flashcard32.png' absoluteImagePath=1 help = "Flashcards"}
{else}

{capture name = "t_module_flashcard_decks_list_code"}
<table class = "sortedTable" width="100%">
    <tr>
    <td class = "topTitle smallHeader" style = "width:20%;text-align:left;vertical-align:middle">{$smarty.const._FLASHCARDS_NAME}</td>
    <td style = "width:40%" class = "topTitle centerAlign">{$smarty.const._FLASHCARDS_CARDS}</td>
    <td style = "width:10%" class = "topTitle centerAlign">{$smarty.const._FLASHCARDS_PUBLISHED}</td>
 </tr>
 {foreach name = "flashdecks_list" item = "deck" key = "key" from = $T_FLASHCARDS_DECKS}
 <tr class = "{cycle name = $key values = "oddRowColor,evenRowColor"}">
  <td><span style="display:none">{$deck.name}</span><a href="{$T_MODULE_FLASHCARDS_BASEURL}&view_deck={$deck.id}">{$deck.name}</a></td>
  <td class="centerAlign">{$deck.num_cards}/{$deck.questions}</td>
  <td class = "centerAlign">
  {if $deck.options.active == 1}
   <img src = "{$T_MODULE_FLASHCARDS_BASELINK}images/success.png" alt="{$smarty.const._ACTIVE}" title="{$smarty.const._ACTIVE}" style = "vertical-align:middle" />
  {/if}
  </td>
 </tr>
 {foreachelse}
  <tr class = "oddRowColor defaultRowHeight"><td colspan = "100%" class = "emptyCategory">{$smarty.const._NODATAFOUND}</td></tr>
 {/foreach}
</table>
{/capture}
{eF_template_printBlock title=$smarty.const._FLASHCARDS_DECKS data=$smarty.capture.t_module_flashcard_decks_list_code image=$T_MODULE_FLASHCARDS_BASELINK|cat:'images/flashcard32.png' absoluteImagePath=1 help = "Flashcards"}
{/if}
{elseif $smarty.session.s_type == "student"}
 {if $smarty.get.finish == 1}
     <script>
         //parent.location = '{$T_MODULE_FLASHCARDS_BASEURL}&message={$smarty.get.message}&message_type={$smarty.get.message_type}';//!re.test(parent.location) ? parent.location = parent.location+'?message={$T_MESSAGE}&message_type={$T_MESSAGE_TYPE}' : parent.location = parent.location+'&message={$T_MESSAGE}&message_type={$T_MESSAGE_TYPE}';
         //eF_js_showDivPopup('', '');
     </script>

 {elseif isset($smarty.get.view_card)}
  {capture name = "t_module_flashcard_view_card_code"}
   {if $T_FLASHCARDS_CURRENTDECK.options.show_count == 1}
    {if $T_FLASHCARDS_SUCCESSARRAY[$smarty.get.view_card] == ""}
     {assign var = "successCount" value = "0 `$smarty.const._FLASHCARDS_TIMES`"}
    {elseif $T_FLASHCARDS_SUCCESSARRAY[$smarty.get.view_card] == 1}
     {assign var = "successCount" value = "`$T_FLASHCARDS_SUCCESSARRAY[$smarty.get.view_card]` `$smarty.const._FLASHCARDS_TIME`"}
    {else}
     {assign var = "successCount" value = "`$T_FLASHCARDS_SUCCESSARRAY[$smarty.get.view_card]` `$smarty.const._FLASHCARDS_TIMES`"}
    {/if}
   {/if}
   <div class = "flashcard">
    <div id = "card_lesson_name">{$T_FLASHCARDS_LESSONNAME}</div>
    <div id = "card">
     <div id = "flip_card" onclick = "flipCard()">
      <img id = "card_flip_icon" src = "{$T_MODULE_FLASHCARDS_BASELINK}images/arrow_right.png" title = "{$smarty.const._FLASHCARDS_FLIP}" alt = "{$smarty.const._FLASHCARDS_FLIP}" />
      <div>{$smarty.const._FLASHCARDS_FLIP}</div>
     </div>
     <div id = "unit_name">{$T_FLASHCARDS_DECKSNAMES[$T_FLASHCARDS_CURRENTDECK.content_ID].name}</div>
     <div id = "card_info">
      <span id = "card_count">{if $T_FLASHCARDS_CURRENTDECK.options.show_count == 1}{$smarty.const._FLASHCARDS_RIGHT}: {$successCount}{/if}</span>
      <span id = "card_total">{$T_FLASHCARDS_CURRENTDECK.non_finished} {$smarty.const._FLASHCARDS_CARDSINDECK}</span>
     </div>
     <div id = "question" {if $T_FLASHCARDS_CURRENTDECK.options.answer_first}style = "display:none"{/if}>
      {if $T_FLASHCARDS_CURRENTDECK.options.show_explanation && $T_FLASHCARDS_CURRENTCARD->question.explanation}
       <a href = "javascript:void(0)" onclick = "flipExplanation()" class = "card_explanation">{$smarty.const._FLASHCARDS_EXPLAIN}</a>
      {/if}
      {$T_FLASHCARDS_CURRENTCARD_PREVIEW}
     </div>
     <div id = "answer" {if !$T_FLASHCARDS_CURRENTDECK.options.answer_first}style = "display:none"{/if}>
      {if $T_FLASHCARDS_CURRENTDECK.options.show_explanation && $T_FLASHCARDS_CURRENTCARD->question.explanation}
       <a href = "javascript:void(0)" onclick = "flipExplanation()" class = "card_explanation">{$smarty.const._FLASHCARDS_EXPLAIN}</a>
      {/if}
      <table class = "solvedQuestion"><tr><td>{$T_FLASHCARDS_CURRENTCARD_PREVIEW_ANSWERED}</td></tr></table>
     </div>
     <div id = "explanation" style = "display:none">
      <a href = "javascript:void(0)" onclick = "flipExplanation()" class = "card_explanation">{$smarty.const._FLASHCARDS_RETURNCARD}</a>
      {$T_FLASHCARDS_CURRENTCARD->question.explanation}
     </div>
    </div>
    <div id = "card_tools">
     <input id = "got_it_right" type = "button" style = "display:none" value = "{$smarty.const._FLASHCARDS_RIGHT}" onclick = "getCard('{$smarty.get.view_card}', 'true')" />
     <input id = "got_it_wrong" type = "button" style = "display:none" value = "{$smarty.const._FLASHCARDS_FALSE}" onclick = "getCard('{$smarty.get.view_card}', 'false')" />
     <div class = "progressCell" id = "card_mastery">
     {if $T_FLASHCARDS_CURRENTDECK.options.display_mastery == 1}
      <span>{$smarty.const._FLASHCARDS_MASTERYOFDECK}:&nbsp;</span>
      <span class = "progressNumber">#filter:score-{$T_FLASHCARDS_CURRENTDECK.mastery}#%</span>
      <span class = "progressBar" style = "width:{$T_FLASHCARDS_CURRENTDECK.mastery}px;">&nbsp;</span>
     {/if}
     </div>
    </div>
   </div>
   <script>
  {if $T_FLASHCARDS_CURRENTDECK.options.answer_first}
   var visibleCard = 'answer';
  {else}
   var visibleCard = 'question';
  {/if}

   {literal}
   function getCard(card, answer) {
    url = '{/literal}{$T_MODULE_FLASHCARDS_BASEURL}{literal}&view_deck={/literal}{$smarty.get.view_deck}{literal}&view_card='+card+'&answer='+answer+'&popup=1';
    if ($('explanation').visible()) {
     visibleCard = 'explanation';
    }
    new Effect.SwitchOff($(visibleCard), {duration:0.33, fps:100, afterFinish:function(){location=url;}});
   }
   function flipCard() {
    $('card_flip_icon').src.match('arrow_left') ? imgSrc = 'arrow_right.png' : imgSrc = 'arrow_left.png';
//				$('flip_card').toggle();
    if (visibleCard == 'question') {
     if ($('explanation').visible()) {
      new Effect.BlindUp($('explanation'), {duration:0.33, fps:100});
     } else {
      new Effect.BlindUp($('question'), {duration:0.33, fps:100});
     }
     new Effect.BlindDown($('answer'), {duration:0.33, fps:100, queue:'end', afterFinish:function() {visibleCard = 'answer';$('got_it_right').toggle();$('got_it_wrong').toggle();$('card_flip_icon').src='{/literal}{$T_MODULE_FLASHCARDS_BASELINK}{literal}images/'+imgSrc}});
    } else if (visibleCard == 'answer'){
     $('got_it_right').toggle();$('got_it_wrong').toggle();
     if ($('explanation').visible()) {
      new Effect.BlindUp($('explanation'), {duration:0.33, fps:100});
     } else {
      new Effect.BlindUp($('answer'), {duration:0.33, fps:100});
     }
     new Effect.BlindDown($('question'), {duration:0.33, fps:100, queue:'end', afterFinish:function() {visibleCard = 'question';$('card_flip_icon').src='{/literal}{$T_MODULE_FLASHCARDS_BASELINK}{literal}images/'+imgSrc}})
    }
   }
   function flipExplanation() {
    if ($('explanation').visible()) {
     new Effect.BlindUp($('explanation'), {duration:0.33, fps:100});
     if (visibleCard == 'question') {
      new Effect.BlindDown($('question'), {duration:0.33, fps:100, queue:'end'});
     } else if (visibleCard == 'answer') {
      new Effect.BlindDown($('answer'), {duration:0.33, fps:100, queue:'end'});
     }
    } else {
     if (visibleCard == 'question') {
      new Effect.BlindUp($('question'), {duration:0.33, fps:100});
     } else if (visibleCard == 'answer') {
      new Effect.BlindUp($('answer'), {duration:0.33, fps:100});
     }
     new Effect.BlindDown($('explanation'), {duration:0.33, fps:100, queue:'end'});
    }
   }

   if (parent.$('card_progress_bar_{/literal}{$T_FLASHCARDS_CURRENTDECK.content_ID}{literal}')) {
    parent.$('card_progress_bar_{/literal}{$T_FLASHCARDS_CURRENTDECK.content_ID}{literal}').style.width = '{/literal}{$T_FLASHCARDS_CURRENTDECK.mastery}{literal}px';
    parent.$('card_progress_num_{/literal}{$T_FLASHCARDS_CURRENTDECK.content_ID}{literal}').update('{/literal}#filter:score-{$T_FLASHCARDS_CURRENTDECK.mastery}#{literal}%');
   }
   {/literal}
   </script>
  {/capture}
  {$smarty.capture.t_module_flashcard_view_card_code}

 {else}
  {capture name = "t_module_flashcard_decks_list_code"}
  <table class = "sortedTable" width="100%">
   <tr>
   <td class = "topTitle leftAlign">{$smarty.const._FLASHCARDS_NAME}</td>
   <td class = "topTitle centerAlign">{$smarty.const._FLASHCARDS_CARDS}</td>
   <td class = "topTitle centerAlign">{$smarty.const._FLASHCARDS_MASTERY}</td>
   <td class = "topTitle noSort centerAlign"><span style = "vertical-align:middle">{$smarty.const._FLASHCARDS_RESTART}</span> <a href = "{$T_MODULE_FLASHCARDS_BASEURL}&restart_decks&reset_popup=1" onclick = "return confirm ('{$smarty.const._FLASHCARDS_PROGRESSRESETAREYOUSURE}')"><img src = "{$T_MODULE_FLASHCARDS_BASELINK}images/restart.png" alt="{$smarty.const._FLASHCARDS_RESTARTALL}" title="{$smarty.const._FLASHCARDS_RESTARTALL}" border = "0" style = "vertical-align:middle" /></a></td>
   </tr>
   {foreach name = "flashdecks_list" item = "deck" key = "key" from = $T_FLASHCARDS_DECKS}
   <tr class = "{cycle name = $key values = "oddRowColor,evenRowColor"}">
    <td><span style="display:none">{$T_FLASHCARDS_DECKSNAMES.$key.name}</span><a href="{$T_MODULE_FLASHCARDS_BASEURL}&view_deck={$key}&popup=1" onclick = "eF_js_showDivPopup('{$smarty.const._FLASHCARDS_FLASHCARDS}', 3)" target="POPUP_FRAME">{$T_FLASHCARDS_DECKSNAMES.$key.name}</a></td>
    <td class = "centerAlign">{$deck.number_cards}</td>
    <td class = "progressCell">
     {if $deck.mastery == '100'}
      <span style = "display:none">{$deck.mastery+1000}</span>
      <span class = "progressNumber">&nbsp;</span>
      <span class = "progressBar" style = "width:100px;text-align:center"><img src = "{$T_MODULE_FLASHCARDS_BASELINK}images/success.png" alt="{$smarty.const._FLASHCARDS_COMPLTETEDDECK}" title="{$smarty.const._FLASHCARDS_COMPLTETEDDECK}" style = "vertical-align:middle"/></span>&nbsp;&nbsp;
     {else}
      <span style = "display:none">{$deck.mastery+1000}</span>
      <span id = "card_progress_num_{$key}" class = "progressNumber">#filter:score-{$deck.mastery}#%</span>
      <span id = "card_progress_bar_{$key}" class = "progressBar" style = "width:{$deck.mastery}px;">&nbsp;</span>&nbsp;&nbsp;
     {/if}
    </td>
    <td class = "centerAlign">
     {if $deck.mastery != 0}
      <a href = "{$T_MODULE_FLASHCARDS_BASEURL}&restart_deck={$key}&reset_popup=1" onclick = "return confirm ('{$smarty.const._FLASHCARDS_PROGRESSRESETAREYOUSURE}')"><img src = "{$T_MODULE_FLASHCARDS_BASELINK}images/restart.png" alt="{$smarty.const._FLASHCARDS_RESTARTDECK}" title="{$smarty.const._FLASHCARDS_RESTARTDECK}" border = "0" style = "vertical-align:middle" /></a>
     {/if}
    </td>
   </tr>
   {foreachelse}
    <tr class = "oddRowColor defaultRowHeight"><td colspan = "100%" class = "emptyCategory">{$smarty.const._NODATAFOUND}</td></tr>
   {/foreach}
  </table>
  {/capture}
  {eF_template_printBlock title=$smarty.const._FLASHCARDS_DECKS data=$smarty.capture.t_module_flashcard_decks_list_code image=$T_MODULE_FLASHCARDS_BASELINK|cat:'images/flashcard32.png' absoluteImagePath=1 help = "Flashcards"}
 {/if}
{/if}

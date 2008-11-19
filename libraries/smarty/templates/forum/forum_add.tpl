{* Smarty template for forum_add.php *}

{include file = "includes/header.tpl"}

{if $T_MESSAGE}
    {if ($T_MESSAGE_TYPE == 'success' || $T_RELOAD_PARENT == true)}
        <script>
            re = /\?/;
            !re.test(parent.location) ? parent.location = parent.location+'?message={$T_MESSAGE}&message_type={$T_MESSAGE_TYPE}' : parent.location = parent.location+'&message={$T_MESSAGE}&message_type={$T_MESSAGE_TYPE}';
        </script>
    {else}
        {eF_template_printMessage message = $T_MESSAGE type = $T_MESSAGE_TYPE}    
    {/if}
{/if}

{if $smarty.get.add_forum || $smarty.get.edit_forum}
    {$T_FORUM_ADD_FORM.javascript}
    <form {$T_FORUM_ADD_FORM.attributes}>
    {$T_FORUM_ADD_FORM.hidden}
        <table class = "formElements">
            <tr><td class = "labelCell">{$smarty.const._TITLE}:&nbsp;</td>
                <td class = "elementCell">{$T_FORUM_ADD_FORM.title.html}</td></tr>
                {if $T_FORUM_ADD_FORM.title.error}<tr><td></td><td class = "formError">{$T_FORUM_ADD_FORM.title.error}</td></tr>{/if}
    {if !$smarty.get.forum_id}
            <tr><td class = "labelCell">{$smarty.const._ACCESSIBLEBYUSERSOFLESSON}:&nbsp;</td>
                <td class = "elementCell">{$T_FORUM_ADD_FORM.lessons_ID.html}</td></tr>
                {if $T_FORUM_ADD_FORM.lessons_ID.error}<tr><td></td><td class = "formError">{$T_FORUM_ADD_FORM.lessons_ID.error}</td></tr>{/if}
    {/if}
	{if $smarty.session.s_type != 'student'}
            <tr><td class = "labelCell">{$T_FORUM_ADD_FORM.status.label}:&nbsp;</td>
                <td class = "elementCell">{$T_FORUM_ADD_FORM.status.html}</td></tr>
                {if $T_FORUM_ADD_FORM.status.error}<tr><td></td><td class = "formError">{$T_FORUM_ADD_FORM.status.error}</td></tr>{/if}
    {/if}
	
            <tr><td class = "labelCell">{$smarty.const._COMMENTS}:&nbsp;</td>
                <td class = "elementCell">{$T_FORUM_ADD_FORM.comments.html}</td></tr>
                {if $T_FORUM_ADD_FORM.comments.error}<tr><td></td><td class = "formError">{$T_FORUM_ADD_FORM.comments.error}</td></tr>{/if}
            <tr><td colspan = "2">&nbsp;</td></tr>
            <tr><td></td><td class = "submitCell">{$T_FORUM_ADD_FORM.submit_add_forum.html}</td></tr>
        </table>
    </form>
{elseif $smarty.get.add_topic || $smarty.get.edit_topic}
    {$T_TOPIC_ADD_FORM.javascript}
    <form {$T_TOPIC_ADD_FORM.attributes}>
    {$T_TOPIC_ADD_FORM.hidden}
        <table class = "formElements">
            <tr><td class = "labelCell">{$smarty.const._TITLE}:&nbsp;</td>
                <td class = "elementCell">{$T_TOPIC_ADD_FORM.title.html}</td></tr>
                {if $T_TOPIC_ADD_FORM.title.error}<tr><td></td><td class = "formError">{$T_TOPIC_ADD_FORM.title.error}</td></tr>{/if}
        {if $smarty.session.s_type != 'student'}
            <tr><td class = "labelCell">{$smarty.const._STATUS}:&nbsp;</td>
                <td class = "elementCell">{$T_TOPIC_ADD_FORM.status.html}</td></tr>
                {if $T_TOPIC_ADD_FORM.status.error}<tr><td></td><td class = "formError">{$T_TOPIC_ADD_FORM.status.error}</td></tr>{/if}
        {/if}
{*            <tr><td class = "labelCell">{$smarty.const._COMMENTS}:&nbsp;</td>
                <td class = "elementCell">{$T_TOPIC_ADD_FORM.comments.html}</td></tr>
                {if $T_TOPIC_ADD_FORM.comments.error}<tr><td></td><td class = "formError">{$T_TOPIC_ADD_FORM.comments.error}</td></tr>{/if}
*}
{if !$smarty.get.edit_topic}
            <tr><td class = "labelCell">{$smarty.const._MESSAGE}:&nbsp;</td>
                <td class = "elementCell">{$T_TOPIC_ADD_FORM.message.html}</td></tr>
                {if $T_TOPIC_ADD_FORM.message.error}<tr><td></td><td class = "formError">{$T_TOPIC_ADD_FORM.message.error}</td></tr>{/if}
{/if}
            <tr><td colspan = "2">&nbsp;</td></tr>
            <tr><td></td><td class = "submitCell">{$T_TOPIC_ADD_FORM.submit_add_topic.html}</td></tr>
        </table>
    </form>
{elseif $smarty.get.add_poll || $smarty.get.edit_poll}
    <script>
        {literal}
        function eF_js_addAdditionalChoice() {
            var els = document.getElementsByTagName('input');           //Find all 'input' elements in th document.

            var counter = els.length;
            if (counter > 1) {                                                  //If the counter is less than 2 (where 2 is the default input fields), it means that the selected question type is not one that may have multiple inputs (i.e. it may be raw_text)
                var last_node = document.getElementById('last_node');           //This is the node that the new elements will be inserted before
                var tr = document.createElement('tr');                          //Create a table row to hold the new element
                var td = document.createElement('td');                          //Create a new table cell to hold the new element
                tr.appendChild(td);                                             //Append this table cell to the table row we created above

                var input = document.createElement('input');                    //Create the new input element
                input.setAttribute('type', 'text');                             //Set the element to be text box
                input.className = 'inputText inputText_QuestionChoice';         //Set its class to 'inputText'
                input.setAttribute('name', 'options['+counter+']');             //We give the new textboxes names if the form multiple_one[0], multiple_one[1] so we can handle them alltogether
                td.appendChild(input);                                          //Append the text box to the table cell we created above

                var img = document.createElement('img');						//Create an image element, that will hold the "delete" icon
                img.setAttribute('alt', '_REMOVECHOICE');                       //Set alt and title for this image
                img.setAttribute('style','white-space:nowrap');
                img.setAttribute('title', '_REMOVECHOICE');
                img.setAttribute('src', 'images/16x16/delete.png');            //Set the icon source
                img.onclick = function() {eF_js_removeImgNode(this, "options")};
                //img.setAttribute('onclick', 'eF_js_removeImgNode(this, "options")');  //Set the event that will trigger the deletion        

                //var img_td = document.createElement('td');                      //Create a new table cell to hold the image element
                td.appendChild(img);                                            //Append the <td> to the row

                var parent_node = last_node.parentNode;                         //Find the parent element, that will hold the new element
                parent_node.insertBefore(tr, last_node);                        //Append the table row, that holds the input element, to its parent.
            }

        }

        //This function removes the <tr> element that contains the inserted node.
        function eF_js_removeImgNode(el, question_type) {

            var counter = 0;        
            var els = document.getElementsByTagName('input');           //Find all 'input' elements in th document.
            for (var i = 0; i < els.length; i++) {                      //Count all the 'input' elements whose names match the selected question type (e.g. multiple_one[0], multiple_one[1] etc)
                if (els[i].name.match(question_type) && els[i].type.match('text')) {                                            
                    counter++;
                }
            }

            if (counter  <= 2  ){
                {/literal}
                alert("{$smarty.const._TWOOPTIONSATMINIMUMREQUIRED}");
                {literal}
            }
            else {
                el.parentNode.parentNode.parentNode.removeChild(el.parentNode.parentNode);      //It is <tr><td><img></td></tr>, so we need to remove the <tr> element, which is the el.parentNode.parentNode

                for (var i = 0; i < els.length; i++) {                      //Count all the 'input' elements whose names match the selected question type (e.g. multiple_one[0], multiple_one[1] etc)
                    if (els[i].name.match(question_type) && els[i].type.match('text')) {
                        els[i].name = question_type+'['+counter+']';        //If the user deletes a middle choice, the remaining choices will not be continuous. I.e, if we have choices multiple_one[0-5] and the user removes multiple_one[3], then the array will have indexes 0,1,2,4,5. So, here, we re-calculate the number of choices and apply new array indexes
                        counter++;
                    }
                }
            }
        }
        {/literal}
    </script>

    {$T_POLL_ADD_FORM.javascript}
    <form {$T_POLL_ADD_FORM.attributes}>
    {$T_POLL_ADD_FORM.hidden}
        <table class = "formElements">
            <tr><td class = "labelCell">{$smarty.const._TITLE}:&nbsp;</td>
                <td class = "elementCell">{$T_POLL_ADD_FORM.poll_subject.html}</td></tr>
                {if $T_POLL_ADD_FORM.poll_subject.error}<tr><td></td><td class = "formError">{$T_POLL_ADD_FORM.poll_subject.error}</td></tr>{/if}
            <tr><td class = "labelCell">{$smarty.const._BODY}:&nbsp;</td>
                <td class = "elementCell">{$T_POLL_ADD_FORM.poll_text.html}</td></tr>
                {if $T_POLL_ADD_FORM.poll_text.error}<tr><td></td><td class = "formError">{$T_POLL_ADD_FORM.poll_text.error}</td></tr>{/if}
            <tr><td class = "labelCell">{$smarty.const._AVAILABLEFROM}:&nbsp;</td>
                <td class = "elementCell">{$T_POLL_ADD_FORM.from.html}</td></tr>
                {if $T_POLL_ADD_FORM.from.error}<tr><td></td><td class = "formError">{$T_POLL_ADD_FORM.from.error}</td></tr>{/if}
            <tr><td class = "labelCell">{$smarty.const._TO}:&nbsp;</td>
                <td class = "elementCell">{$T_POLL_ADD_FORM.to.html}</td></tr>
                {if $T_POLL_ADD_FORM.to.error}<tr><td></td><td class = "formError">{$T_POLL_ADD_FORM.to.error}</td></tr>{/if}

            <tr><td class = "labelCell" style = "vertical-align:top;white-space:normal">{$smarty.const._INSERTMULTIPLEQUESTIONS}:</td>
                <td><table>
    {foreach name = 'multiple_one_list' key = key item = item from = $T_POLL_ADD_FORM.options}
                        <tr><td>{$item.html}</td>
        {if $smarty.foreach.multiple_one_list.iteration > 2}                  {*The if smarty.iteration is put here, so that the user cannot remove the first 2 rows *}
                            <td><a href = "javascript:void(0)" onclick = "eF_js_removeImgNode(this, 'multiple_one')">
                                    <img src = "images/16x16/delete.png" border = "no" alt = "{$smarty.const._REMOVECHOICE}" title = "{$smarty.const._REMOVECHOICE}" />
                                </a></td>
        {/if}
                        </tr>
        {if $item.error}
                        <tr><td colspan = "100%" class = "formError">{$item.error}</td></tr>
        {/if}
    {/foreach}
                        <tr id = "last_node"></tr>
                    </table>
                </td></tr>
            <tr><td class = "labelCell">
                    <a href = "javascript:void(0)" onclick = "eF_js_addAdditionalChoice()"><img src = "images/16x16/add2.png" alt = "{$smarty.const._ADDQUESTION}" title = "{$smarty.const._ADDQUESTION}" border = "0"/></a>
                </td><td>
                    <a href = "javascript:void(0)" onclick = "eF_js_addAdditionalChoice()">{$smarty.const._ADDOPTION}</a>
                </td></tr>
            <tr><td colspan = "2">&nbsp;</td></tr>
            <tr><td></td><td class = "submitCell">{$T_POLL_ADD_FORM.submit_add_poll.html}</td></tr>
        </table>
    </form>
{elseif $smarty.get.add_message || $smarty.get.edit_message}
    {$T_MESSAGE_ADD_FORM.javascript}
    <form {$T_MESSAGE_ADD_FORM.attributes}>
    {$T_MESSAGE_ADD_FORM.hidden}
        <table class = "formElements" style = "width:99%">
            <tr><td class = "labelCell">{$smarty.const._TITLE}:&nbsp;</td>
                <td class = "elementCell">{$T_MESSAGE_ADD_FORM.title.html}</td></tr>
                {if $T_MESSAGE_ADD_FORM.title.error}<tr><td></td><td class = "formError">{$T_MESSAGE_ADD_FORM.title.error}</td></tr>{/if}
            <tr><td class = "labelCell">{$smarty.const._BODY}:&nbsp;</td>
                <td class = "elementCell">{$T_MESSAGE_ADD_FORM.body.html}</td></tr>
                {if $T_MESSAGE_ADD_FORM.body.error}<tr><td></td><td class = "formError">{$T_MESSAGE_ADD_FORM.body.error}</td></tr>{/if}
            <tr><td colspan = "2">&nbsp;</td></tr>
            <tr><td></td><td class = "submitCell">{$T_MESSAGE_ADD_FORM.submit_add_message.html}</td></tr>
        </table>
    </form>

{/if}
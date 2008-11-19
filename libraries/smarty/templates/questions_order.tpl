{* Smarty template for add_test.php *}
{if $T_MESSAGE_TYPE == 'success'}
    <script>
        re = /\?/;
        !re.test(parent.location) ? parent.location = parent.location+'?message={$T_MESSAGE}&message_type={$T_MESSAGE_TYPE}' : parent.location = parent.location+'&message={$T_MESSAGE}&message_type={$T_MESSAGE_TYPE}';            
    </script>
{/if}

{include file = "includes/header.tpl"}          {*The inclusion is put here instead of the beginning in order to speed up reloading, in case of success*}

{if $T_MESSAGE}
        {eF_template_printMessage message = $T_MESSAGE type = $T_MESSAGE_TYPE}    
{/if}


<div style="font-size:12px; text-align:center;" class="popUpInfoDiv">{$smarty.const._DRAGITEMSTOCHANGEQUESTIONSORDER}</div><br/>
{$T_QUESTION_TREE}
            <form name = "order_questions" action = "" method = "post">
             <input type = "hidden" name = "test_ID" value="{$T_TEST_ID}" />
             <br/>
             <div style="text-align:center;">
             <input class = "flatButton" type="button" onclick="saveQuestionTree()" value="{$smarty.const._SAVECHANGES}">
            </div>
             </form>
             
             
             
<script type='text/javascript'>
{literal}

var ajaxObjects = new Array();

function saveQuestionTree()
{
    testID = document.forms['order_questions'].test_ID.value;
    saveString = treeObj.getNodeOrders();
    var ajaxIndex = ajaxObjects.length;
    ajaxObjects[ajaxIndex] = new sack();
    var url = '/saveQuestionOrder.php?saveString=' + saveString + '&test_ID=' + testID;
    ajaxObjects[ajaxIndex].requestFile = url;   // Specifying which file to get
    ajaxObjects[ajaxIndex].onCompletion = function() { saveQuestionComplete(ajaxIndex); } ; // Specify function that will be executed after file has been found
    ajaxObjects[ajaxIndex].runAJAX();       // Execute AJAX function
}

function saveQuestionComplete(index)
{
    alert(ajaxObjects[index].response);         
}

if(document.getElementById('dhtmlgoodies_question_tree'))
{
    treeObj = new JSDragDropTree();
    treeObj.setTreeId('dhtmlgoodies_question_tree');
    treeObj.setMaximumDepth(7);
    treeObj.setMessageMaximumDepthReached('Maximum depth reached'); // If you want to show a message when maximum depth is reached, i.e. on drop.
    treeObj.initTree();
    treeObj.expandAll();
}
{/literal}
</script>
</body>
</html>
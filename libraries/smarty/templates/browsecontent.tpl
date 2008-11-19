{include file = "includes/header.tpl"}

    <table>
    	<tr><td>
    		{$T_CONTENT_TREE}
    	</td></tr>
    </table>
    <script>
    {literal}
        function setLink(el) {
        	Element.extend(el);
        	top.document.getElementById('href').value = '##EFRONTINNERLINK##.php?ctg=content&view_unit=' + el.id.replace(/nodeATag(\d*)/, "$1");
        }
    {/literal}
    </script>
{include file = "includes/closing.tpl"}
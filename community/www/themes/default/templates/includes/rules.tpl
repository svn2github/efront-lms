{*moduleRules: Print content Rules list*}
{capture name = "moduleRules"}
    <tr><td class = "moduleCell">
    {if $smarty.get.add_rule || $smarty.get.edit_rule}
        {capture name = 't_add_rule_code'}
            {$T_ADD_RULE_FORM.javascript}
            <form {$T_ADD_RULE_FORM.attributes}>
                {$T_ADD_RULE_FORM.hidden}
                <fieldset class = "fieldsetSeparator">
                <legend>{$smarty.const._ADDCUSTOMRULE}</legend>
                <table class = "formElements" style = "margin-left:0px">
                    <tr><td class = "labelCell">{$smarty.const._VALIDFOR}:&nbsp;</td>
                        <td>{$T_ADD_RULE_FORM.scope.html}</td></tr>
                    {if $T_ADD_RULE_FORM.scope.error}<tr><td></td><td class = "formError">{$T_ADD_RULE_FORM.scope.error}</td></tr>{/if}
                    <tr><td class = "labelCell">{$smarty.const._TOBEEXCLUDEDFROMUNIT}:&nbsp;</td>
                        <td>{$T_ADD_RULE_FORM.exclusion_unit.html}</td></tr>
                    {if $T_ADD_RULE_FORM.exclusion_unit.error}<tr><td></td><td class = "formError">{$T_ADD_RULE_FORM.exclusion_unit.error}</td></tr>{/if}
                    <tr><td class = "labelCell">{$smarty.const._BASEDONTERM}:&nbsp;</td>
                        <td>{$T_ADD_RULE_FORM.rule_type.html}</td></tr>
                    {if $T_ADD_RULE_FORM.rule_type.error}<tr><td></td><td class = "formError">{$T_ADD_RULE_FORM.rule_type.error}</td></tr>{/if}
                    <tr id = "rule_unit" style = "{if $T_CURRENT_RULE != 'hasnot_seen' && $smarty.post.rule_type != 'hasnot_seen'}display:none{/if}"><td class = "labelCell">{$smarty.const._WITHNAME}:&nbsp;</td>
                        <td>{$T_ADD_RULE_FORM.rule_unit.html}</td></tr>
                    {if $T_ADD_RULE_FORM.rule_unit.error}<tr><td></td><td class = "formError">{$T_ADD_RULE_FORM.rule_unit.error}</td></tr>{/if}
                    <tr id = "test_unit" style = "{if $T_CURRENT_RULE != 'hasnot_passed' && $smarty.post.rule_type != 'hasnot_passed'}display:none{/if}"><td class = "labelCell">{$smarty.const._WITHNAME}:&nbsp;</td>
                        <td>{$T_ADD_RULE_FORM.test_unit.html}</td></tr>
                    {if $T_ADD_RULE_FORM.test_unit.error}<tr><td></td><td class = "formError">{$T_ADD_RULE_FORM.test_unit.error}</td></tr>{/if}
                    <tr id = "test_score" style = "{if $T_CURRENT_RULE != 'hasnot_passed' && $smarty.post.rule_type != 'hasnot_passed'}display:none{/if}"><td class = "labelCell">{$smarty.const._ANDSCOREGREATEROREQUAL}:&nbsp;</td>
                        <td>{$T_ADD_RULE_FORM.score.html}</td></tr>
                    {if $T_ADD_RULE_FORM.score.error}<tr><td></td><td class = "formError">{$T_ADD_RULE_FORM.score.error}</td></tr>{/if}
                    <tr><td colspan = "100%">&nbsp;</td></tr>
                    <tr><td></td><td>{$T_ADD_RULE_FORM.submit_rule.html}</td></tr>
                </table>
                </fieldset>
            </form>
	        {if !$smarty.get.edit_rule}
	            {$T_ADD_READY_RULE_FORM.javascript}
	            <form {$T_ADD_READY_RULE_FORM.attributes}>
	                {$T_ADD_READY_RULE_FORM.hidden}
	                <fieldset class = "fieldsetSeparator">
	                <legend>{$smarty.const._ADDREADYRULE}</legend>
	                <table>
	                    <tr><td class = "labelCell">{$smarty.const._SERIALRULE}:&nbsp;</td>
	                        <td class = "elementCell">{$T_ADD_READY_RULE_FORM.ready_rule.serial.html}</td></tr>
	{*                    <tr><td class = "labelCell">{$smarty.const._TREERULE}:&nbsp;</td>
	                        <td class = "elementCell">{$T_ADD_READY_RULE_FORM.ready_rule.tree.html}</td></tr>
	*}
	                    <tr><td colspan = "100%">&nbsp;</td></tr>
	                    <tr><td class = "labelCell"></td>
	                        <td class = "elementCell">{$T_ADD_READY_RULE_FORM.submit_ready_rule.html}</td></tr>
	                </table>
	                </fieldset>
	            </form>
	        {/if}
        {/capture}

        {eF_template_printBlock title=$smarty.const._RULEPROPERTIES data=$smarty.capture.t_add_rule_code image='32x32/rules.png'}
    {elseif $smarty.get.add_condition || $smarty.get.edit_condition}

        {capture name = 't_add_condition_code'}        	
            {$T_COMPLETE_LESSON_FORM.javascript}
            <form {$T_COMPLETE_LESSON_FORM.attributes}>
                {$T_COMPLETE_LESSON_FORM.hidden}
                <table class = "formElements" style = "margin-left:0px">
                    <tr><td class = "labelCell">{$smarty.const._THEUSERMUSTHAVE}:&nbsp;</td>
                        <td>{$T_COMPLETE_LESSON_FORM.condition_types.html}</td></tr>
                    {if $T_COMPLETE_LESSON_FORM.condition_types.error}<tr><td class = "formError">{$T_COMPLETE_LESSON_FORM.condition_types.error}</td></tr>{/if}

                    <tr id = "percentage_units" {if $T_CURRENT_CONDITION.type != "percentage_units" && $smarty.post.condition_types != "percentage_units"}style = "display:none"{/if}><td class = "labelCell">{$smarty.const._UNITSPERCENTAGE}:&nbsp;</td>
                        <td>{$T_COMPLETE_LESSON_FORM.percentage_units.html}%</td></tr>
                    {if $T_COMPLETE_LESSON_FORM.percentage_units.error}<tr><td class = "formError">{$T_COMPLETE_LESSON_FORM.percentage_units.error}</td></tr>{/if}

                    <tr id = "specific_unit" {if $T_CURRENT_CONDITION.type != "specific_unit" && $smarty.post.condition_types != "specific_unit"}style = "display:none"{/if}><td class = "labelCell">{$smarty.const._WITHNAME}:&nbsp;</td>
                        <td>{$T_COMPLETE_LESSON_FORM.specific_unit.html}</td></tr>
                    {if $T_COMPLETE_LESSON_FORM.specific_unit.error}<tr><td class = "formError">{$T_COMPLETE_LESSON_FORM.specific_unit.error}</td></tr>{/if}

                    <tr id = "specific_test" {if $T_CURRENT_CONDITION.type != "specific_test" && $smarty.post.condition_types != "specific_test"}style = "display:none"{/if}><td class = "labelCell">{$smarty.const._WITHNAME}:&nbsp;</td>
                        <td>{$T_COMPLETE_LESSON_FORM.specific_test.html}</td></tr>
                    {if $T_COMPLETE_LESSON_FORM.specific_test.error}<tr><td class = "formError">{$T_COMPLETE_LESSON_FORM.specific_test.error}</td></tr>{/if}

                    <tr><td class = "labelCell">{$smarty.const._RELATIONTOOTHERS}:&nbsp;</td>
                        <td>{$T_COMPLETE_LESSON_FORM.relation.html}</td></tr>
                    {if $T_COMPLETE_LESSON_FORM.relation.error}<tr><td class = "formError">{$T_COMPLETE_LESSON_FORM.relation.error}</td></tr>{/if}

                    <tr><td colspan = "100%">&nbsp;</td></tr>
                    <tr><td></td><td>{$T_COMPLETE_LESSON_FORM.submit_complete_lesson_condition.html}</td></tr>
                </table>
            </form>
        {/capture}

        {eF_template_printBlock title=$smarty.const._CONDITIONPROPERTIES data=$smarty.capture.t_add_condition_code image='32x32/rules.png'}
    {else}
        {capture name = 't_conditions_code'}
            {if !isset($T_CURRENT_USER->coreAccess.content) || $T_CURRENT_USER->coreAccess.content == 'change'}
                <div class = "headerTools">
                	<span>
                		<img src = "images/16x16/add.png" title="{$smarty.const._ADDCONDITION}" alt="{$smarty.const._ADDCONDITION}"/>
                		<a href = "professor.php?ctg=rules&tab=conditions&add_condition=1">{$smarty.const._ADDCONDITION}</a>
                	</span>
                	<span>
                		<img src = "images/16x16/autocomplete.png" title="{$smarty.const._AUTOCOMPLETE}" alt="{$smarty.const._AUTOCOMPLETE}"/>
                		<a href = "javascript:void(0)" onclick = "setAutoComplete(this)">{$smarty.const._AUTOCOMPLETE}:&nbsp;{if $T_CURRENT_LESSON->options.auto_complete}{$smarty.const._YES}{else}{$smarty.const._NO}{/if}</a>
                	</span>
                </div>
            {/if}
                <table width = "100%" class = "sortedTable" rowsPerPage = "15">
                    <tr class = "topTitle">
                        <td class = "topTitle">{$smarty.const._CONDITIONTYPE}</td>
                        <td class = "topTitle">{$smarty.const._CONDITION}</td>
                        <td class = "topTitle">{$smarty.const._RELATIONTOOTHERS}</td>
                        <td class = "topTitle centerAlign noSort">{$smarty.const._FUNCTIONS}</td>

            {foreach name = 'conditions_list' key = "key" item = "item" from = $T_LESSON_CONDITIONS}
                    <tr class = "{cycle values = "oddRowColor, evenRowColor"} defaultRowHeight">
                        <td>{$T_CONDITION_TYPES[$item.type]}</td>
                        <td>
                            {if $item.type == 'all_units'}
                            {elseif $item.type == 'percentage_units'}
						        {$item.options.0}%
                            {elseif $item.type == 'specific_unit'}
						        {$T_TREE_NAMES[$item.options.0]}
                            {elseif $item.type == 'all_tests'}
						        {$item.options.0}
                            {elseif $item.type == 'specific_test'}
        						{$T_TREE_NAMES[$item.options.0]}
                            {/if}
                        </td>
                        <td>{if $item.relation == 'or'}{$smarty.const._OR}{else}{$smarty.const._AND}{/if}</td>
                        <td class = "centerAlign">
            {if !isset($T_CURRENT_USER->coreAccess.content) || $T_CURRENT_USER->coreAccess.content == 'change'}
                            <a href = "{$smarty.server.PHP_SELF}?ctg=rules&tab=conditions&edit_condition={$item.id}"><img src = "images/16x16/edit.png" alt = "{$smarty.const._CORRECTION}" title = "{$smarty.const._CORRECTION}" /></a>
                            <img class = "ajaxHandle" src = "images/16x16/error_delete.png" alt = "{$smarty.const._DELETE}" title = "{$smarty.const._DELETE}" onclick = "if (confirm('{$smarty.const._IRREVERSIBLEACTIONAREYOUSURE}')) deleteCondition(this, '{$item.id}');"/>
            {/if}
                        </td>
                    </tr>
            {foreachelse}
                    <tr class = "oddRowColor defaultRowHeight"><td colspan = "5" class = "emptyCategory">{$smarty.const._NODATAFOUND}</td></tr>
            {/foreach}
                </table>
        {/capture}

        {capture name = "t_lesson_rules"}
            {if !isset($T_CURRENT_USER->coreAccess.content) || $T_CURRENT_USER->coreAccess.content == 'change'}
            	<script>var autocompleteyes = '{$smarty.const._AUTOCOMPLETE}: {$smarty.const._YES}';var autocompleteno = '{$smarty.const._AUTOCOMPLETE}: {$smarty.const._NO}';</script>
                <div class = "headerTools">
                	<span>
                		<img src = "images/16x16/add.png" title = "{$smarty.const._ADDRULE}" alt = "{$smarty.const._ADDRULE}"  />
                		<a href = "professor.php?ctg=rules&add_rule=1" >{$smarty.const._ADDRULE}</a>
                	</span>
                </div>
            {/if}
                    <table width = "100%" class = "sortedTable">
                        <tr class = "topTitle defaultRowHeight">
                            <td class = "topTitle">{$smarty.const._VALIDFOR}</td>
                            <td class = "topTitle">{$smarty.const._EXCLUDECONSTRAINT}</td>
                            <td class = "topTitle">{$smarty.const._EXCLUSIONUNIT}</td>
                            <td class = "topTitle noSort centerAlign">{$smarty.const._FUNCTIONS}</td>
                {foreach name = rules_list key = "key" item = "rule" from = $T_RULES}
                    {assign var = "rule_content_id" value = $rule.rule_content_ID}
                    {assign var = "content_id" value = $rule.content_ID}
                    <tr class = "{cycle values = "oddRowColor, evenRowColor"} defaultRowHeight {if (!$T_TREE_ACTIVE.$content_id || !$T_TREE_ACTIVE.$rule_content_id) && {$rule.rule_type != 'serial' && $rule.rule_type != 'tree'}}deactivatedTableElement{/if}">
                    {if ($rule.users_LOGIN != '*')}
                        <td>#filter:login-{$rule.users_LOGIN}#</td>
                    {else}
                        <td>{$smarty.const._ALLOFTHEM}</td>
                    {/if}

                    {if ($rule.rule_type == 'always')}
                            <td>{$smarty.const._STUDENTALLWAYS} </td>
                    {elseif ($rule.rule_type == 'hasnot_seen')}
                            <td>{$smarty.const._IFSTUDENTHASNOTSEEN} {$smarty.const._THEUNIT} "{$T_TREE_NAMES.$rule_content_id}"</td>
                    {elseif ($rule.rule_type == 'hasnot_passed')}
                            <td>{$smarty.const._IFSTUDENTHASNOTPASSED} {$smarty.const._THETEST} "{$T_TREE_NAMES.$rule_content_id}" {$smarty.const._WITHSCOREATLEAST} {$rule.rule_option*100}%</td>
                    {elseif ($rule.rule_type == 'serial')}
                            <td>{$smarty.const._SERIALRULE}</td>
                    {elseif ($rule.rule_type == 'tree')}
                            <td>{$smarty.const._TREERULE}</td>
                    {/if}
                            <td>{$T_TREE_NAMES.$content_id}</td>
                            <td class = "centerAlign">
                    {if !isset($T_CURRENT_USER->coreAccess.content) || $T_CURRENT_USER->coreAccess.content == 'change'}
                        {if $rule.rule_type != 'serial' && $rule.rule_type != 'tree'}
						        <a href = "{$smarty.server.PHP_SELF}?ctg=rules&edit_rule={$rule.id}"><img src = "images/16x16/edit.png" alt = "{$smarty.const._CORRECTION}" title = "{$smarty.const._CORRECTION}" /></a>
                        {/if}
        						<img class = "ajaxHandle" src = "images/16x16/error_delete.png" alt = "{$smarty.const._DELETE}" title = "{$smarty.const._DELETE}" onclick = "if (confirm('{$smarty.const._IRREVERSIBLEACTIONAREYOUSURE}')) deleteRule(this, '{$rule.id}')"/>
                    {/if}
                            </td></tr>
                {foreachelse}
                        <tr class = "oddRowColor defaultRowHeight"><td colspan = "5" class = "emptyCategory">{$smarty.const._NODATAFOUND}</td></tr>
                {/foreach}
                    </table>

        {/capture}

		{capture name = 't_rules_code'}
        <div class = "tabber">
			{eF_template_printBlock tabber = "rules" title=$smarty.const._CONTENTTRAVERSINGRULES data=$smarty.capture.t_lesson_rules image='32x32/content.png'}
			{eF_template_printBlock tabber = "conditions" title=$smarty.const._LESSONCONDITIONS data=$smarty.capture.t_conditions_code image='32x32/graduation.png'}

        </div>
        {/capture}
		{eF_template_printBlock title = $smarty.const._RULES data = $smarty.capture.t_rules_code image = '32x32/rules.png'}
        
    {/if}
    </td></tr>
    {/capture}
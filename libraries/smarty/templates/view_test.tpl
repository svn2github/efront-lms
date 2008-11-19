{include file = "includes/header.tpl"}
{if $T_MESSAGE}
        {eF_template_printMessage message = $T_MESSAGE type = $T_MESSAGE_TYPE}    
{/if}

{$T_SOLVED_TEST}

{include file = "includes/closing.tpl"}
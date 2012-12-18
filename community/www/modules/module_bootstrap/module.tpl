{capture name = "t_form_block_code"}
 {eF_template_printForm form = $T_FORM}
{/capture}
{eF_template_printBlock title = $smarty.const._MODULE_BOOTSTRAP_SETUPMODULE data = $smarty.capture.t_form_block_code}

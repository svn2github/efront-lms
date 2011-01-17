{capture name = "t_demo_data_code"}
This appears because you are calling getCatalog() and getCatalogSmartyTpl() from inside the demo module

{/capture}
{eF_template_printBlock title = $smarty.const._MODULE_DEMO_DEMODATACATALOG data = $smarty.capture.t_demo_data_code}

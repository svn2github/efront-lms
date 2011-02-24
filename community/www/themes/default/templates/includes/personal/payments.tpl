 {capture name = "t_my_payments_code"}
 {/capture}
 {eF_template_printBlock title = $smarty.const._PAYMENTS data = $smarty.capture.t_my_payments_code image = '32x32/shopping_basket.png' options = $T_PAYMENTS_OPTIONS}

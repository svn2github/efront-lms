<?php
/**
* prints a block
*
*/
function smarty_function_eF_template_printBlock($params, &$smarty) {
    //<img src = "'.$params['image'].'" alt = "'.$params['title'].'" title = "'.$params['title'].'" class = "title">
    $str = '	
    <div class = "block" style = "'.$params['style'].'">
        <div class = "top-left"></div>
        <div class = "top-right"></div>
        <div class = "blockContents">
        		<span class = "title">'.$params['title'].'</span>
        		<span class = "toggle open" onclick = "toggleBlock(this)"></span>		
        		<span class = "subtitle">'.$params['sub_title'].'</span>
        		<div class = "content">'.$params['content'].'</div>
        		<span style = "display:none">&nbsp;</span>	
        </div>
        <div class = "bottom-left"></div>
        <div class = "bottom-right"></div>
    </div>';
         
    return $str;
}

/*
    <div class = "block content">
		<span class = "title">'.$params['title'].'</span>
		<span class = "toggle open" onclick = "toggleBlock(this)"></span>		
		<span class = "subtitle">'.$params['sub_title'].'</span>
		<div class = "content">'.$params['content'].'</div>
		<span style = "display:none">&nbsp;</span>	
	</div>
 */
?>
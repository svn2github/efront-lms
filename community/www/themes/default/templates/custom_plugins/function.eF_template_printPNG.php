<?php
/**
* Smarty plugin: eF_template_printPNG function
*/
function smarty_function_eF_template_printPNG($params, &$smarty) {
    
    $x = $params['data'];
    $img_path = '';//$params['img_path'];// ? $params['img_path'] : '';
    $sizeMeth = '';//$params['sizeMeth'];// ? $params['sizeMeth'] : 'scale';
    $inScript = FALSE;//$params['inScript'];// ? $params['inScript'] : FALSE;

    $arr2=array();
    // make sure that we are only replacing for the Windows versions of Internet
    // Explorer 5.5+
    $msie='/msie\s(5\.[5-9]|[6]\.[0-9]*).*(win)/i';
    if( !isset($_SERVER['HTTP_USER_AGENT']) ||
        !preg_match($msie,$_SERVER['HTTP_USER_AGENT']) ||
        preg_match('/opera/i',$_SERVER['HTTP_USER_AGENT']))
        return $x;

    if($inScript){
        // first, I want to remove all scripts from the page...
        $saved_scripts=array();
        $placeholders=array();
        preg_match_all('`<script[^>]*>(.*)</script>`isU',$x,$scripts);
        for($i=0;$i<count($scripts[0]);$i++){
            $x=str_replace($scripts[0][$i],'replacePngTags_ScriptTag-'.$i,$x);
            $saved_scripts[]=$scripts[0][$i];
            $placeholders[]='replacePngTags_ScriptTag-'.$i;
        }
    }

    // find all the png images in backgrounds
    preg_match_all('/background-image:\s*url\(([\\"\\\']?)([^\)]+\.png)\1\);/Uis',$x,$background);
    for($i=0;$i<count($background[0]);$i++){
        // simply replace:
        //  "background-image: url('image.png');"
        // with:
        //  "filter:progid:DXImageTransform.Microsoft.AlphaImageLoader(
        //      enabled=true, sizingMethod=scale, src='image.png');"
        // I don't think that the background-repeat styles will work with this...
        $x=str_replace($background[0][$i],'filter:progid:DXImageTransform.'.
                'Microsoft.AlphaImageLoader(enabled=true, sizingMethod='.$sizeMeth.
                ', src=\''.$background[2][$i].'\');',$x);
    }

    // find all the IMG tags with ".png" in them
    $pattern='/<(input|img)[^>]*src=([\\"\\\']?)([^>]*\.png)\2[^>]*>/i';
    preg_match_all($pattern,$x,$images);
    for($num_images=0;$num_images<count($images[0]);$num_images++){
        // for each found image pattern
        $original=$images[0][$num_images];
        $quote=$images[2][$num_images];
        $atts=''; $width=0; $height=0; $modified=$original;

        // We do this so that we can put our spacer.png image in the same
        // directory as the image - if a path wasn't passed to the function
        if(empty($img_path)){
            $tmp=split('[\\/]',$images[3][$num_images]);
            $this_img=array_pop($tmp);
            $img_path=join('/',$tmp);
            if(empty($img_path)){
                // this was a relative URI, image should be in this directory
                $tmp=split('[\\/]',$_SERVER['SCRIPT_NAME']);
                array_pop($tmp);    // trash the script name, we only want the directory name
                $img_path=join('/',$tmp).'/';
            }else{
                $img_path.='/';
            }
        }else if(mb_substr($img_path,-1)!='/'){
            // in case the supplied path didn't end with a /
            $img_path.='/';
        }

        // If the size is defined by styles, find them
        preg_match_all(
            '/style=([\\"\\\']).*(\s?width:\s?([0-9]+(px|%));).*'.
            '(\s?height:\s?([0-9]+(px|%));).*\\1/Ui',
               $images[0][$num_images],$arr2); 
        if(is_array($arr2) && count($arr2[0])){
            // size was defined by styles, get values
            $width=$arr2[3][0];
            $height=$arr2[6][0];

            // remove the width and height from the style
            $stripper=str_replace(' ','\s','/('.$arr2[2][0].'|'.$arr2[5][0].')/');
            // Also remove any empty style tags
            $modified=preg_replace(
                '`style='.$arr2[1][0].$arr2[1][0].'`i',
                '',
                preg_replace($stripper,'',$modified));
        }else{
            // size was not defined by styles, get values from attributes
            preg_match_all('/width=([\\"\\\']?)([0-9%]+)\\1/i',$images[0][$num_images],$arr2);
            if(is_array($arr2) && count($arr2[0])){
                $width=$arr2[2][0];
                if(is_numeric($width))
                    $width.='px';
    
                // remove width from the tag
                $modified=str_replace($arr2[0][0],'',$modified);
            }
            preg_match_all('/height=([\\"\\\']?)([0-9%]+)\\1/i',$images[0][$num_images],$arr2);
            if(is_array($arr2) && count($arr2[0])){
                $height=$arr2[2][0];
                if(is_numeric($height))
                    $height.='px';
    
                // remove height from the tag
                $modified=str_replace($arr2[0][0],'',$modified);
            }
        }

        if($width==0 || $height==0){
            // width and height not defined in HTML attributes or css style, try to get
            // them from the image itself
            // this does not work in all conditions... It is best to define width and
            // height in your img tag or with inline styles..
            if(file_exists($_SERVER['DOCUMENT_ROOT'].$img_path.$images[3][$num_images])){
                // image is on this filesystem, get width & height
                $size=getimagesize($_SERVER['DOCUMENT_ROOT'].$img_path.$images[3][$num_images]);
                $width=$size[0].'px';
                $height=$size[1].'px';
            }else if(file_exists($_SERVER['DOCUMENT_ROOT'].$images[3][$num_images])){
                // image is on this filesystem, get width & height
                $size=getimagesize($_SERVER['DOCUMENT_ROOT'].$images[3][$num_images]);
                $width=$size[0].'px';
                $height=$size[1].'px';
            }
        }
        
        // end quote is already supplied by originial src attribute
        $replace_src_with=$quote.$img_path.'spacer.png'.$quote.' style="width: '.$width.
            '; height: '.$height.'; filter: progid:DXImageTransform.'.
            'Microsoft.AlphaImageLoader(src=\''.$images[3][$num_images].'\', sizingMethod='.
            $sizeMeth.');"';

        // now create the new tag from the old
        $new_tag=str_replace($quote.$images[3][$num_images].$quote,$replace_src_with,
            str_replace('  ',' ',$modified));
        // now place the new tag into the content
        $x=str_replace($original,$new_tag,$x);
    }
    
    if($inScript){
        // before the return, put the script tags back in. (I was having problems when there was
        // javascript that had image tags for PNGs in it when using this function...
        $x=str_replace($placeholders,$saved_scripts,$x);
    }
    
    return $x;

}

?>
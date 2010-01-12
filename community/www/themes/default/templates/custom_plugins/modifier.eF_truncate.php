<?php
/**
 * Smarty plugin
 * @package Smarty
 * @subpackage plugins
 */


/**
 * Smarty truncate modifier plugin
 *
 * Type:     modifier<br>
 * Name:     truncate<br>
 * Purpose:  Truncate a string to a certain length if necessary,
 *           optionally splitting in the middle of a word, and
 *           appending the $etc string or inserting $etc into the middle.
 * @link http://smarty.php.net/manual/en/language.modifier.truncate.php
 *          truncate (Smarty online manual)
 * @param string
 * @param integer
 * @param string
 * @param boolean
 * @param boolean
 * @return string
 */
function smarty_modifier_eF_truncate($string, $length = 80, $etc = '...',
                                  $break_words = false, $middle = false)
{
    if ($length == 0)
        return '';

    if (mb_strlen($string) > $length) {
        $length -= mb_strlen($etc);
        if (!$middle) {
            return mb_substr($string, 0, $length).$etc;
        } else {
            return mb_substr($string, 0, round($length/2)) . $etc . mb_substr($string, -round($length/2));
        }
    } else {
        return $string;
    }
}

/* vim: set expandtab: */

?>

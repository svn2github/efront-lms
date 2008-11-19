{*Template file for analyze_code.php*}

                <script>
                {literal}
                function show_hide(img_el, id) {
                    var el = document.getElementById(id);
                    if (el.style.display == 'none') {
                        el.style.display = ''; 
                        img_el.src = 'images/others/minus.png';
                    } else {
                        el.style.display = 'none';
                        img_el.src = 'images/others/plus.png';
                    }
                }
                {/literal}
                </script>

                <form name = "analyze_form" method = "post" target = "">
                <table>
                    <tr><td>Αναζήτηση για </td><td>
                            <select name = "category">
                                <option value = "function" {if $smarty.post.category == "function"}selected{/if}>Συναρτήσεις</option>
                                <!--<option value = "css"      {if $smarty.post.category == "css"}selected{/if}>CSS Classes</option>-->
                                <option value = "language" {if $smarty.post.category == "language"}selected{/if}>Γλωσσικά tags</option>
                                <option value = "file"     {if $smarty.post.category == "file"}selected{/if}>Αρχεία</option>
                                <option value = "image"    {if $smarty.post.category == "image"}selected{/if}>Εικόνες</option>
                                <option value = "any"      {if $smarty.post.category == "any"}selected{/if}>Οτιδήποτε</option>
                            </select>
                        </td></tr>
                    <tr><td>Με όνομα </td><td>
                            <input class = "textBox" type = "text" name = "search_term" value = "{$smarty.post.search_term}"/> (Αφήστε κενό για να γίνει αναζήτηση αχρησιμοποίητων)
                        </td></tr>
                    <tr><td>Σε τύπους αρχείων</td><td>
                            <select name = "file_types">
                                <option value = "php" {if $smarty.post.file_types == "php"}selected{/if}>.php</option>
                                <option value = "js"  {if $smarty.post.file_types == "js"}selected{/if}>.js</option>
                                <option value = "html"{if $smarty.post.file_types == "html"}selected{/if}>.html, .htm</option>
                                <option value = "tpl" {if $smarty.post.file_types == "tpl"}selected{/if}>.tpl</option>
                                <option value = "all" {if $smarty.post.file_types == "all"}selected{/if}>Όλα</option>
                            </select>
                        </td></tr>
                    <tr><td>Στους φακέλους</td><td>
                            <input class = "textBox" type = "text" name = "folders" value = "{if $smarty.post.folders}{$smarty.post.folders}{/if}"/> (Αφήστε κενό για αναζήτηση σε όλους)
                        </td></tr>
                    <tr><td colspan = "2">
                            <input class = "flatButton" type = "submit" name = "submit" value = "Αναζήτηση" />
                        </td></tr>
                </table>
                </form>


{if ($smarty.post.submit)}
    {if $smarty.post.category == 'function'}
                    <table align = "center">
                        <tr class = "defaultRowHeight">
                            <td colspan = "100%" class = "boldFont">Αποτελέσματα αναζήτησης για Συναρτήσεις</td></tr>
        {foreach name=functions_list key=key item=item from=$T_OCCURENCES_IN_FILES}
                        <tr class = "defaultRowHeight">
                            <th colspan = "100%" class = "topTitle">{$key}</th>
            {section name = current_file_functions_loop loop = $item}
                {assign var = "current_function" value = $item[current_file_functions_loop]}
                        <tr class = "{cycle values = "oddRowColor, evenRowColor"} defaultRowHeight">
                            <td style = "width:10px;vertical-align:top"><img id = "image_{$item[current_file_functions_loop]}" src = "images/others/plus.png" onclick = "show_hide(this, '{$item[current_file_functions_loop]}')" ></td>
                            <td id = "td_{$item[current_file_functions_loop]}" class = "centerAlign">
                                    {$item[current_file_functions_loop]} {*- {$T_TOTAL_TIME[$current_function]}*}
                {section name = "into_file_functions_list" loop = $T_INTO_FILES[$current_function]}
                    {if $smarty.section.into_file_functions_list.first}
                                <div id = "{$item[current_file_functions_loop]}" style = "background-color:khaki;position:relative;display:none">
                    {/if}
                                    {$T_INTO_FILES[$current_function][into_file_functions_list]}<br>
                    {if $smarty.section.into_file_functions_list.last}
                                </div>
                    {/if}
                {sectionelse}
                    {if $smarty.post.category == 'function'}
                            <script>
                            <!--
                                document.getElementById('td_{$item[current_file_functions_loop]}').style.color='red';
                                document.getElementById('image_{$item[current_file_functions_loop]}').style.display='none';
                            -->
                            </script>
                    {/if}
                {/section}
                            </td></tr>
            {sectionelse}
                        <tr class = "oddRowColor defaultRowHeight"><td colspan = "100%" class = "emptyCategory centerAlign">Δεν βρέθηκαν συναρτήσεις σε αυτό το αρχείο</td></tr>
            {/section}    
        {/foreach}
                    </table>


                    
    {elseif $smarty.post.category == 'any'}
                    <table align = "center">
                        <tr class = "defaultRowHeight">
                            <td colspan = "100%" class = "boldFont centerAlign">Αποτελέσματα αναζήτησης για Οτιδήποτε</td></tr>
                        <tr class = "defaultRowHeight">
                            <th colspan = "100%" class = "topTitle">Αναφέρεται στα αρχεία</th></tr>
        {foreach name=in_files_list key=key item=item from=$T_IN_FILES}
                        <tr class = "{cycle values = "oddRowColor, evenRowColor"} defaultRowHeight"><td>
                            {$item}
                        </td></tr>
        {/foreach}    
                    </table>

    {elseif $smarty.post.category == 'file'}
                    <table align = "center">
                        <tr class = "defaultRowHeight">
                            <td colspan = "100%" class = "boldFont centerAlign">Αποτελέσματα αναζήτησης για Αρχεία</td></tr>
                        <tr><td style = "vertical-align:top">
                                <table>
                                    <tr class = "defaultRowHeight">
                                        <th colspan = "100%" class = "topTitle">Αναφέρονται σε αρχεία</th></tr>
        {foreach name=in_files_list key=key item=item from=$T_IN_FILES}
                                    <tr class = "{cycle values = "oddRowColor, evenRowColor"} defaultRowHeight">
                                        <td style = "width:10px;vertical-align:top"><img id = "image_{$key}" src = "images/others/plus.png" onclick = "show_hide(this, '{$key}')" ></td>
                                        <td id = "td_{$key}" class = "centerAlign">{$key}
            {section name = "files_loop" loop = $item}
                    {if $smarty.section.files_loop.first}
                                            <div id = "{$key}" style = "background-color:khaki;position:relative;display:none">
                    {/if}
                                    {$item[files_loop]}<br>
                    {if $smarty.section.files_loop.last}
                                            </div>
                    {/if}
            {/section}
                                        </td></tr>
        {/foreach}    
                                </table>
                            </td><td style = "vertical-align:top">
                                    <table>
                        <tr class = "defaultRowHeight">
                                        <th colspan = "100%" class = "topTitle">Υπάρχουν, αλλά δεν αναφέρονται πουθενά</th></tr>
        {foreach name=unused_files_list key=key item=item from=$T_UNUSED_FILES}
                                    <tr class = "{cycle name = "unused_files" values = "oddRowColor, evenRowColor"} defaultRowHeight">
                                        <td class = "centerAlign">{$item}</td></tr>
        {/foreach}    
                                </table>                            
                            </td></tr>
                        
                    </table>


    {elseif $smarty.post.category == 'language'}
                    <table align = "center">
                        <tr class = "defaultRowHeight">
                            <td colspan = "100%" class = "boldFont centerAlign">Αποτελέσματα αναζήτησης για Γλωσσικά tags</td></tr>
                        <tr><td style = "vertical-align:top">
                                <table>
                                    <tr class = "defaultRowHeight">
                                        <th colspan = "100%" class = "topTitle">Αναφέρονται σε αρχεία</th></tr>
        {foreach name=in_files_tags_list key=key item=item from=$T_IN_FILES}
                                    <tr class = "{cycle values = "oddRowColor, evenRowColor"} defaultRowHeight">
                                        <td style = "width:10px;vertical-align:top"><img id = "image_{$key}" src = "images/others/plus.png" onclick = "show_hide(this, '{$key}')" ></td>
                                        <td id = "td_{$key}" class = "centerAlign">{$key}
            {section name = "files_loop" loop = $item}
                    {if $smarty.section.files_loop.first}
                                            <div id = "{$key}" style = "background-color:khaki;position:relative;display:none">
                    {/if}
                                    {$item[files_loop]}<br>
                    {if $smarty.section.files_loop.last}
                                            </div>
                    {/if}
            {/section}
                                        </td></tr>
        {/foreach}    
                                </table>
                            </td><td style = "vertical-align:top">
                                    <table>
                        <tr class = "defaultRowHeight">
                                        <th colspan = "100%" class = "topTitle">Υπάρχουν, αλλά δεν αναφέρονται πουθενά</th></tr>
        {foreach name=unused_tags_list key=key item=item from=$T_UNUSED_TAGS}
                                    <tr class = "{cycle name = "unused_tags" values = "oddRowColor, evenRowColor"} defaultRowHeight">
                                        <td class = "centerAlign">{$item}</td></tr>
        {/foreach}    
                                </table>                            
                            </td></tr>
                        
                    </table>
                    
    {elseif $smarty.post.category == 'image'}
                    <table align = "center">
                        <tr class = "defaultRowHeight">
                            <td colspan = "100%" class = "centerAlign boldFont">Αποτελέσματα αναζήτησης για Εικόνες</td></tr>
                        <tr><td style = "vertical-align:top">
                            <table>
                                <tr class = "defaultRowHeight">
                                    <th colspan = "100%" class = "topTitle">Εικόνες που αναφέρονται σε αρχεία</th></tr>
        {foreach name=files_list key=key item=item from=$T_IN_FILES}
                                <tr class = "{cycle values = "oddRowColor, evenRowColor"} defaultRowHeight">
                                    <td style = "width:10px;vertical-align:top"><img id = "image_{$key}" src = "images/others/plus.png" onclick = "show_hide(this, '{$key}')" ></td>
                                    <td id = "td_{$key}" class = "centerAlign">{$key}
            {section name = "image_files_list" loop = $item}
                {if $smarty.section.image_files_list.first}
                                        <div id = "{$key}" style = "background-color:khaki;position:relative;display:none">
                {/if}
                                            {$item[image_files_list]}<br>
                {if $smarty.section.into_file_functions_list.last}
                                        </div>
                {/if}
            {/section}
                                </td></tr>
        {/foreach}                            
        {foreach name=files_list key=key item=item from=$T_NONEXISTENT_FILES}
                                <tr class = "{cycle values = "oddRowColor, evenRowColor"} defaultRowHeight">
                                    <td style = "width:10px;vertical-align:top"><img id = "image_{$key}" src = "images/others/plus.png" onclick = "show_hide(this, '{$key}')" ></td>
                                    <td id = "td_{$key}" class = "centerAlign" style = "color:red;">{$key}
            {section name = "image_files_list" loop = $item}
                {if $smarty.section.image_files_list.first}
                                        <div id = "{$key}" style = "background-color:khaki;position:relative;display:none">
                {/if}
                                            {$item[image_files_list]}<br>
                {if $smarty.section.into_file_functions_list.last}
                                        </div>
                {/if}
            {/section}
                                </td></tr>
        {/foreach}                            
                            </table>
                        </td><td style = "vertical-align:top">
                            <table>
                                <tr class = "defaultRowHeight"><th class = "topTitle"> Εικόνες που δεν αναφέρονται πουθενά</td></tr>
        {foreach name=unlisted_images_list key=key item=item from=$T_UNLISTED_FILES}
                                <tr class = "{cycle name = "unlisted" values = "oddRowColor, evenRowColor"}"><td class = "centerAlign">{$item}</td></tr>
        {/foreach}                            
                            </table>
                        </td></tr>
                    </table>
    {/if}


{/if}




<br/><br/>
&nbsp;
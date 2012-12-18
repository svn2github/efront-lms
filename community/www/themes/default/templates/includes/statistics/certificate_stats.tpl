 {if $smarty.get.query == "course_certificated"}
  {capture name = 'course_certificated'}
  <table class = "sortedTable" sortBy = "0" width="100%">
                        <tr>
                            <td class = "topTitle" style = "width:300px">{$smarty.const._USER}</td>
       <td class = "topTitle" align="center" style = "width:300px">{$smarty.const._DATE}</td>
       <td class = "topTitle" align="center" style = "width:300px">{$smarty.const._GRADE}</td>
       <td class = "topTitle" align="center" style = "width:300px">{$smarty.const._CERTIFICATEEXPIRESON}</td>
       <td class = "topTitle" align="center" style = "width:300px">{$smarty.const._CERTIFICATEKEY}</td>
       <td class = "topTitle" align="center" style = "width:300px">{$smarty.const._RESET}</td>
       <td class = "topTitle" align="center" style = "width:100px">{$smarty.const._PREVIEW}</td>
                        </tr>
                    {foreach name = 'users_list' key = 'key' item = "item" from = $T_COURSE_CERTIFICATED}
        <tr class = "{cycle name = 'users_list' values = 'oddRowColor, evenRowColor'}">
                            <td><a href = "{$T_BASIC_TYPE}.php?ctg=statistics&option=user&sel_user={$item.login}">#filter:login-{$item.login}#</a></td>
       <td align="center"><span style = "display:none">{$item.date}</span>#filter:timestamp_time-{$item.date}#</td>
       <td align="center">{$item.grade}</td>
       <td align="center"><span style = "display:none">{$item.expire_certificate}</span>#filter:timestamp_time_nosec-{$item.expire_certificate}#</td>
       <td align="center">{$item.serial_number}</td>
       <td align="center">{if $item.reset == 1} {$smarty.const._YES}{else}{$smarty.const._NO} {/if}</td>
       <td align="center">
       {if $smarty.session.s_type == 'administrator'}
        <a href = "{$smarty.server.PHP_SELF}?ctg=courses&op=course_certificates&export={$item.certificate_export_method}&user={$item.login}&course={$smarty.post.course_id_certificated}" target="_blank" title = "{$smarty.const._VIEWCERTIFICATE}">
       {else}
        <a href = "{$smarty.server.PHP_SELF}?ctg=lessons&op=course_certificates&export={$item.certificate_export_method}&user={$item.login}&course={$smarty.post.course_id_certificated}" target="_blank" title = "{$smarty.const._VIEWCERTIFICATE}">
       {/if}
                                  <img class = "handle" src = "images/16x16/certificate.png" title = "{$smarty.const._VIEWCERTIFICATE}" alt = "{$smarty.const._VIEWCERTIFICATE}"/>
                                 </a>
       </td>
      </tr>
                    {foreachelse}
                     <tr class = "oddRowColor defaultRowHeight"><td colspan = "100%" class = "emptyCategory">{$smarty.const._NODATAFOUND}</td></tr>
                    {/foreach}
        </table>
<br/><br/>
  <table class = "sortedTable" sortBy = "0" width="100%">
                        <tr>
       <td class = "topTitle">{$smarty.const._DATE}</td>
                            <td class = "topTitle">{$smarty.const._PREVIOUSCERTIFICATES}</td>
                        </tr>
                    {foreach name = 'users_list' key = 'key' item = "item" from = $T_COURSE_EVENTCERTIFICATES}
        <tr class = "{cycle name = 'event_list' values = 'oddRowColor, evenRowColor'}">
                            <td>#filter:timestamp_time-{$item.timestamp}#</td>
                            <td>{$item.message}</td>
      </tr>
                    {foreachelse}
                     <tr class = "oddRowColor defaultRowHeight"><td colspan = "100%" class = "emptyCategory">{$smarty.const._NODATAFOUND}</td></tr>
                    {/foreach}
        </table>

  {/capture}
  {eF_template_printBlock title = "`$smarty.const._WANTUSERSCERTIFICATEDCOURSE` <span class=\"innerTableName\"> &quot;`$T_COURSE_NAME`&quot;</span> [#filter:timestamp-`$T_FROM_DATE`# - #filter:timestamp-`$T_TO_DATE`#]" data = $smarty.capture.course_certificated image = '32x32/certificate.png' options=$T_COURSE_CERTIFICATED_OPTIONS}
 {elseif $smarty.get.query == "course_certificated_all"}
  {capture name = 'course_certificated_all'}
  <table class = "sortedTable" sortBy = "0" width="100%">
                        <tr>
       <td class = "topTitle" style = "width:300px">{$smarty.const._COURSE}</td>
                            <td class = "topTitle" style = "width:300px">{$smarty.const._USER}</td>
       <td class = "topTitle" align="center" style = "width:300px">{$smarty.const._DATE}</td>
       <td class = "topTitle" align="center" style = "width:300px">{$smarty.const._GRADE}</td>
       <td class = "topTitle" align="center" style = "width:300px">{$smarty.const._CERTIFICATEEXPIRESON}</td>
       <td class = "topTitle" align="center" style = "width:300px">{$smarty.const._CERTIFICATEKEY}</td>
       <td class = "topTitle" align="center" style = "width:300px">{$smarty.const._RESET}</td>
       <td class = "topTitle" align="center" style = "width:100px">{$smarty.const._PREVIEW}</td>
                        </tr>
                    {foreach name = 'users_list' key = 'key' item = "item" from = $T_COURSE_CERTIFICATED}
        <tr class = "{cycle name = 'users_list' values = 'oddRowColor, evenRowColor'}">
       <td>{$item.name}</td>
                            <td><a href = "{$T_BASIC_TYPE}.php?ctg=statistics&option=user&sel_user={$item.login}">#filter:login-{$item.login}#</a></td>
       <td align="center"><span style = "display:none">{$item.date}</span>#filter:timestamp_time-{$item.date}#</td>
       <td align="center">{$item.grade}</td>
       <td align="center"><span style = "display:none">{$item.expire_certificate}</span>#filter:timestamp_time_nosec-{$item.expire_certificate}#</td>
       <td align="center">{$item.serial_number}</td>
       <td align="center">{if $item.reset == 1} {$smarty.const._YES}{else}{$smarty.const._NO} {/if}</td>
       <td align="center">
       {if $smarty.session.s_type == 'administrator'}
        <a href = "{$smarty.server.PHP_SELF}?ctg=courses&op=course_certificates&export={$item.certificate_export_method}&user={$item.login}&course={$item.id}" target="_blank" title = "{$smarty.const._VIEWCERTIFICATE}">
                            {else}
        <a href = "{$smarty.server.PHP_SELF}?ctg=lessons&op=course_certificates&export={$item.certificate_export_method}&user={$item.login}&course={$item.id}" target="_blank" title = "{$smarty.const._VIEWCERTIFICATE}">
                            {/if}
                                 <img class = "handle" src = "images/16x16/certificate.png" title = "{$smarty.const._VIEWCERTIFICATE}" alt = "{$smarty.const._VIEWCERTIFICATE}" />
                                </a>
       </td>
      </tr>
                    {foreachelse}
                     <tr class = "oddRowColor defaultRowHeight"><td colspan = "100%" class = "emptyCategory">{$smarty.const._NODATAFOUND}</td></tr>
                    {/foreach}
        </table>
  <br/><br/>
  <table class = "sortedTable" sortBy = "0" width="100%">
                        <tr>
       <td class = "topTitle">{$smarty.const._DATE}</td>
                            <td class = "topTitle">{$smarty.const._PREVIOUSCERTIFICATES}</td>
                        </tr>
                    {foreach name = 'users_list' key = 'key' item = "item" from = $T_COURSE_EVENTCERTIFICATES}
        <tr class = "{cycle name = 'event_list' values = 'oddRowColor, evenRowColor'}">
       <td>#filter:timestamp_time-{$item.timestamp}#</td>
                            <td>{$item.message}</td>
      </tr>
                    {foreachelse}
                     <tr class = "oddRowColor defaultRowHeight"><td colspan = "100%" class = "emptyCategory">{$smarty.const._NODATAFOUND}</td></tr>
                    {/foreach}
        </table>
  {/capture}
  {eF_template_printBlock title = "`$smarty.const._WANTUSERSCERTIFICATEDALLCOURSES` [#filter:timestamp-`$T_FROM_DATE`# - #filter:timestamp-`$T_TO_DATE`#]" data = $smarty.capture.course_certificated_all image = '32x32/certificate.png' options=$T_COURSE_CERTIFICATED_OPTIONS}
 {elseif $smarty.get.query == "course_certificated_expire"}
  {capture name = 'course_certificated_expire'}
  <table class = "sortedTable" sortBy = "0" width="100%">
                        <tr>
       <td class = "topTitle" style = "width:300px">{$smarty.const._COURSE}</td>
                            <td class = "topTitle" style = "width:300px">{$smarty.const._USER}</td>
       <td class = "topTitle" align="center" style = "width:300px">{$smarty.const._DATE}</td>
       <td class = "topTitle" align="center" style = "width:300px">{$smarty.const._GRADE}</td>
       <td class = "topTitle" align="center" style = "width:300px">{$smarty.const._CERTIFICATEEXPIRESON}</td>
       <td class = "topTitle" align="center" style = "width:300px">{$smarty.const._CERTIFICATEKEY}</td>
       <td class = "topTitle" align="center" style = "width:300px">{$smarty.const._RESET}</td>
       <td class = "topTitle" align="center" style = "width:100px">{$smarty.const._PREVIEW}</td>
                        </tr>
                    {foreach name = 'users_list' key = 'key' item = "item" from = $T_COURSE_CERTIFICATED_EXPIRE}
        <tr class = "{cycle name = 'users_list' values = 'oddRowColor, evenRowColor'}">
       <td>{$item.name}</td>
                            <td><a href = "{$T_BASIC_TYPE}.php?ctg=statistics&option=user&sel_user={$item.login}">#filter:login-{$item.login}#</a></td>
       <td align="center"><span style = "display:none">{$item.date}</span>#filter:timestamp_time-{$item.date}#</td>
       <td align="center">{$item.grade}</td>
       <td align="center"><span style = "display:none">{$item.expire_certificate}</span>#filter:timestamp_time_nosec-{$item.expire_certificate}#</td>
       <td align="center">{$item.serial_number}</td>
       <td align="center">{if $item.reset == 1} {$smarty.const._YES}{else}{$smarty.const._NO} {/if}</td>
       <td align="center">
       {if $smarty.session.s_type == 'administrator'}
        <a href = "{$smarty.server.PHP_SELF}?ctg=courses&op=course_certificates&export={$item.certificate_export_method}&user={$item.login}&course={$item.id}" target="_blank" title = "{$smarty.const._VIEWCERTIFICATE}">
       {else}
        <a href = "{$smarty.server.PHP_SELF}?ctg=lessons&op=course_certificates&export={$item.certificate_export_method}&user={$item.login}&course={$item.id}" target="_blank" title = "{$smarty.const._VIEWCERTIFICATE}">
       {/if}
                                 <img src = "images/16x16/certificate.png" title = "{$smarty.const._VIEWCERTIFICATE}" alt = "{$smarty.const._VIEWCERTIFICATE}" border = "0"/>
                                </a>
       </td>
      </tr>
                    {foreachelse}
                     <tr class = "oddRowColor defaultRowHeight"><td colspan = "100%" class = "emptyCategory">{$smarty.const._NODATAFOUND}</td></tr>
                    {/foreach}
        </table>
  {/capture}
  {eF_template_printBlock title = "`$smarty.const._WANTCERTIFICATESEXPIRED` [#filter:timestamp-`$T_FROM_DATE`# - #filter:timestamp-`$T_TO_DATE`#]" data = $smarty.capture.course_certificated_expire image = '32x32/certificate.png' options=$T_COURSE_CERTIFICATED_OPTIONS}

 {elseif $smarty.get.query == "search_certificate_key"}
  {capture name = 'search_certificate_key'}
  <table class = "sortedTable" sortBy = "0" width="100%">
                        <tr>
       <td class = "topTitle" style = "width:300px">{$smarty.const._COURSE}</td>
                            <td class = "topTitle" style = "width:300px">{$smarty.const._USER}</td>
       <td class = "topTitle" align="center" style = "width:300px">{$smarty.const._DATE}</td>
       <td class = "topTitle" align="center" style = "width:300px">{$smarty.const._GRADE}</td>
       <td class = "topTitle" align="center" style = "width:300px">{$smarty.const._CERTIFICATEEXPIRESON}</td>
       <td class = "topTitle" align="center" style = "width:300px">{$smarty.const._CERTIFICATEKEY}</td>
       <td class = "topTitle" align="center" style = "width:300px">{$smarty.const._RESET}</td>
       <td class = "topTitle" align="center" style = "width:100px">{$smarty.const._PREVIEW}</td>
                        </tr>
                    {foreach name = 'users_list' key = 'key' item = "item" from = $T_CERTIFICATES}
        <tr class = "{cycle name = 'users_list' values = 'oddRowColor, evenRowColor'}">
       <td>{$item.issued_certificate.course_name}</td>
                            <td><a href = "{$T_BASIC_TYPE}.php?ctg=statistics&option=user&sel_user={$item.users_LOGIN}">#filter:login-{$item.users_LOGIN}#</a></td>
       <td align="center"><span style = "display:none">{$item.issued_certificate.date}</span>#filter:timestamp_time-{$item.issued_certificate.date}#</td>
       <td align="center">{$item.issued_certificate.grade}</td>
       <td align="center"><span style = "display:none">{$item.expire_certificate}</span>#filter:timestamp_time_nosec-{$item.expire_certificate}#</td>
       <td align="center">{$item.issued_certificate.serial_number}</td>
       <td align="center">{if $item.reset == 1} {$smarty.const._YES}{else}{$smarty.const._NO} {/if}</td>
       <td align="center">
       {if $smarty.session.s_type == 'administrator'}
        <a href = "{$smarty.server.PHP_SELF}?ctg=courses&op=course_certificates&export={$item.certificate_export_method}&user={$item.users_LOGIN}&course={$item.id}" target="_blank" title = "{$smarty.const._VIEWCERTIFICATE}">
       {else}
        <a href = "{$smarty.server.PHP_SELF}?ctg=lessons&op=course_certificates&export={$item.certificate_export_method}&user={$item.users_LOGIN}&course={$item.id}" target="_blank" title = "{$smarty.const._VIEWCERTIFICATE}">
       {/if}
                                 <img src = "images/16x16/certificate.png" title = "{$smarty.const._VIEWCERTIFICATE}" alt = "{$smarty.const._VIEWCERTIFICATE}" border = "0"/>
                                </a>
       </td>
      </tr>
                    {foreachelse}
                     <tr class = "oddRowColor defaultRowHeight"><td colspan = "100%" class = "emptyCategory">{$smarty.const._NODATAFOUND}</td></tr>
                    {/foreach}
        </table>
  {/capture}
  {eF_template_printBlock title = $smarty.const._SEARCHCERTIFICATESBYKEY data = $smarty.capture.search_certificate_key image = '32x32/certificate.png'}



 {else}
  {capture name = 'certificate_statistics'}

        <table class = "statisticsSelectList">
   <tr><td>
   <a href = "javascript:void(0)" onclick = "showCertificateStats('day')">{$smarty.const._LAST24HOURS}</a> - <a href = "javascript:void(0)" onclick = "showCertificateStats('week')">{$smarty.const._LASTWEEK}</a> - <a href = "javascript:void(0)" onclick = "showCertificateStats('month')">{$smarty.const._LASTMONTH}</a><br /><br />
   </td></tr>
   <tr><td>
   <fieldset class = "fieldsetSeparator">
    <legend>{$smarty.const._WANTUSERSCERTIFICATEDCOURSE}</legend>
    <form name="course_certificated" method="post" action="{$smarty.server.PHP_SELF}?ctg=statistics&option=certificate&query=course_certificated">
    <table width="100%"><tr>
    <td align="left" width="50px">
     {$smarty.const._COURSE}
    </td>
    <td>
    <input type = "text" id = "autocomplete_course_certificated" class = "autoCompleteTextBox" value="{$smarty.const._STARTTYPINGFORRELEVENTMATCHES}" onClick="this.value='';"/>
                        <img id = "busy_course_certificated" src = "images/16x16/hourglass.png" style="display:none;" alt = "{$smarty.const._LOADING}" title = "{$smarty.const._LOADING}"/>
                        <div id = "course_choices_certificated" class = "autocomplete"></div>&nbsp;&nbsp;&nbsp;
    <input type = "hidden" name="course_id_certificated" id = "course_id_certificated" />
    </td>
    </tr><tr>
    <td>
    {$smarty.const._FROM}</td><td> {eF_template_html_select_date prefix="from_" time=$T_FROM_TIMESTAMP start_year="-5" end_year="+0" field_order = $T_DATE_FORMATGENERAL}
    {$smarty.const._TO} {eF_template_html_select_date prefix="to_" time=$T_TO_TIMESTAMP start_year="-5" end_year="+0" field_order = $T_DATE_FORMATGENERAL}
    </td>
    </tr><tr>
    <td></td><td>
    <input type = "submit" value = "{$smarty.const._SUBMIT}" class = "flatButton"/>
    </td></tr>
    </table>
    </form>
   </fieldset>
   </td></tr>

   <tr><td>
   <fieldset class = "fieldsetSeparator">
    <legend>{$smarty.const._WANTUSERSCERTIFICATEDALLCOURSES}</legend>
    <form name="course_certificated_all" method="post" action="{$smarty.server.PHP_SELF}?ctg=statistics&option=certificate&query=course_certificated_all">
    <table width="100%"><tr>
    <td align="left" width="50px">
    </td><td></td>
    </tr><tr>
    <td>
    {$smarty.const._FROM}</td><td> {eF_template_html_select_date prefix="from_" time=$T_FROM_TIMESTAMP start_year="-5" end_year="+0" field_order = $T_DATE_FORMATGENERAL}
    {$smarty.const._TO} {eF_template_html_select_date prefix="to_" time=$T_TO_TIMESTAMP start_year="-5" end_year="+0" field_order = $T_DATE_FORMATGENERAL}
    </td>
    </tr><tr>
    <td>
    </td><td>
    <input type = "submit" value = "{$smarty.const._SUBMIT}" class = "flatButton"/>
    </td></tr>
    </table>
    </form>
   </fieldset>
   </td></tr>

   <tr><td>
   <fieldset class = "fieldsetSeparator">
    <legend>{$smarty.const._WANTCERTIFICATESEXPIRED}</legend>
    <form name="course_certificated_expire" method="post" action="{$smarty.server.PHP_SELF}?ctg=statistics&option=certificate&query=course_certificated_expire">
    <table width="100%"><tr>
    <td align="left" width="50px">
    </td><td></td>
    </tr><tr>
    <td>
    {$smarty.const._FROM}</td><td> {eF_template_html_select_date prefix="from_" time=$T_FROM_TIMESTAMP_EXPIRE start_year="+0" end_year="+5" field_order = $T_DATE_FORMATGENERAL}
    {$smarty.const._TO} {eF_template_html_select_date prefix="to_" time=$T_TO_TIMESTAMP_EXPIRE start_year="+0" end_year="+5" field_order = $T_DATE_FORMATGENERAL}
    </td>
    </tr><tr>
    <td>
    </td><td>
    <input type = "submit" value = "{$smarty.const._SUBMIT}" class = "flatButton"/>
    </td></tr>
    </table>
    </form>
   </fieldset>
   </td></tr>


   <tr><td>
   <fieldset class = "fieldsetSeparator">
    <legend>{$smarty.const._SEARCHCERTIFICATESBYKEY}</legend>
    <form name="search_certificate_key" method="post" action="{$smarty.server.PHP_SELF}?ctg=statistics&option=certificate&query=search_certificate_key">
    <table width="100%"><tr>
    <td align="left" width="50px">
     {$smarty.const._KEY}
    </td>
    <td>
    <input type = "text" name="certificate_key" class="inputText" value="{$smarty.const._ENTERPARTOFCERTIFICATEKEY}" onClick="this.value='';"/>
    </td>
    </tr><tr>
    <td></td>
    </tr><tr>
    <td></td><td>
    <input type = "submit" value = "{$smarty.const._SUBMIT}" class = "flatButton"/>
    </td></tr>
    </table>
    </form>
   </fieldset>
   </td></tr>


   </table>
   {/capture}
   {eF_template_printBlock title = $smarty.const._CERTIFICATESTATISTICS data = $smarty.capture.certificate_statistics image = '32x32/certificate.png' help = 'Reports'}

 {/if}

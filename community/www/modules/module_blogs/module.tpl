{*Smarty template*}
{if isset($smarty.get.add_blog) || isset($smarty.get.edit_blog)}
{capture name = 't_module_blogs_addBlog}
    {$T_BLOG_ADD_FORM.javascript}
    <form {$T_BLOG_ADD_FORM.attributes}>
    {$T_BLOG_ADD_FORM.hidden}
        <table class = "formElements">
            <tr><td class = "labelCell">{$smarty.const._TITLE}:&nbsp;</td>
                <td class = "elementCell">{$T_BLOG_ADD_FORM.title.html}</td></tr>
                {if $T_BLOG_ADD_FORM.title.error}<tr><td></td><td class = "formError">{$T_BLOG_ADD_FORM.title.error}</td></tr>{/if}
            <tr><td class = "labelCell">{$T_BLOG_ADD_FORM.description.label}:&nbsp;</td>
                <td class = "elementCell">{$T_BLOG_ADD_FORM.description.html}</td></tr>
                {if $T_BLOG_ADD_FORM.description.error}<tr><td></td><td class = "formError">{$T_BLOG_ADD_FORM.description.error}</td></tr>{/if}

     <!-- <tr><td class = "labelCell">{$T_BLOG_ADD_FORM.registered.label}:&nbsp;</td>
                <td class = "elementCell">{$T_BLOG_ADD_FORM.registered.html}</td></tr>
                {if $T_BLOG_ADD_FORM.registered.error}<tr><td></td><td class = "formError">{$T_BLOG_ADD_FORM.registered.error}</td></tr>{/if}
            <tr><td colspan = "2">&nbsp;</td></tr> -->
            <tr><td></td><td class = "submitCell">{$T_BLOG_ADD_FORM.submit_add_blog.html}</td></tr>
        </table>
    </form>
{/capture}


{capture name = 't_users_to_blogs_code'}
<!--ajax:usersTable-->
                                                    <table style = "width:100%" class = "sortedTable" size = "{$T_USERS_SIZE}" sortBy = "0" id = "usersTable" useAjax = "1" rowsPerPage = "{$smarty.const.G_DEFAULT_TABLE_SIZE}" url = "{$T_MODULE_BASEURL}&edit_blog={$smarty.get.edit_blog}&">
                                                        <tr class = "topTitle">
                                                            <td class = "topTitle" name = "login">{$smarty.const._LOGIN}</td>
                                                            <td class = "topTitle" name = "name">{$smarty.const._FIRSTNAME}</td>
                                                            <td class = "topTitle" name = "surname">{$smarty.const._LASTNAME}</td>
                                                            <td class = "topTitle" name = "user_type">{$smarty.const._USERTYPE}</td>
                                                            <td class = "topTitle centerAlign" name = "blog_creator">{$smarty.const._BLOGS_CREATORS}</td>
                                                        </tr>
                                {foreach name = 'users_to_blogs_list' key = 'key' item = 'user' from = $T_ALL_USERS}
                                                        <tr class = "defaultRowHeight {cycle values = "oddRowColor, evenRowColor"} {if !$user.active}deactivatedTableElement{/if}">
                                                            <td>{$user.login}</td>
                                                            <td>{$user.name}</td>
                                                            <td>{$user.surname}</td>
                                                            <td>{$user.basic_user_type}</td>
                                                            <td align="center">
                                                                <input class = "inputCheckbox" type = "checkbox" name = "checked_{$user.login}" id = "checked_{$user.login}" onclick = "ajaxPost('{$user.login}', this);" {if in_array($user.login, $T_BLOGS_USERS)}checked = "checked"{/if} />
                                                            </td>
                                                    </tr>
                                {/foreach}
                                </table>
<!--/ajax:usersTable-->

        {literal}
        <script>
        function ajaxPost(login, el, table_id) {
            var baseUrl = '{/literal}{$T_MODULE_BASEURL}{literal}&edit_blog={/literal}{$smarty.get.edit_blog}{literal}&postAjaxRequest=1';
            if (login) {
                var checked = $('checked_'+login).checked;
                var url = baseUrl + '&login='+login;
                var img_id = 'img_'+login;
            } else if (table_id && table_id == 'usersTable') {
                el.checked ? url = baseUrl + '&addAll=1' : url = baseUrl + '&removeAll=1';
                if ($(table_id+'_currentFilter')) {
                 url = url+'&filter='+$(table_id+'_currentFilter').innerHTML;
                }
                var img_id = 'img_selectAll';
            }

            var position = eF_js_findPos(el);
            var img = document.createElement("img");

            img.style.position = 'absolute';
            img.style.top = Element.positionedOffset(Element.extend(el)).top + 'px';
            img.style.left = Element.positionedOffset(Element.extend(el)).left + 6 + Element.getDimensions(Element.extend(el)).width + 'px';

            img.setAttribute("id", img_id);
            img.setAttribute('src', '{/literal}{$T_MODULE_BASELINK}{literal}images/progress1.gif');

            el.parentNode.appendChild(img);

                new Ajax.Request(url, {
                        method:'get',
                        asynchronous:true,
                        onSuccess: function (transport) {
                            img.style.display = 'none';
                            img.setAttribute('src', 'images/16x16/success.png');
                            new Effect.Appear(img_id);
                            window.setTimeout('Effect.Fade("'+img_id+'")', 2500);
                            }
                    });
        }
        </script>
        {/literal}

{/capture}


{capture name = 't_module_blogs_code'}
<div class = "tabber">
 <div class = "tabbertab">
        <h3>{$smarty.const._BLOGS_EDITBLOG}</h3>
            {eF_template_printBlock title=$smarty.const._BLOGS_BLOGS_FORM data=$smarty.capture.t_module_blogs_addBlog image=$T_MODULE_BASELINK|cat:'images/eFrontBlog32.png' absoluteImagePath=1}
 </div>
 {if isset($smarty.get.edit_blog)}
    <div class = "tabbertab{if $smarty.get.tab=='blog_creators'} tabbertabdefault{/if}">
        <h3>{$smarty.const._BLOGS_BLOGSCREATORS}</h3>
        {eF_template_printBlock title = $smarty.const._BLOGS_SELECTBLOGCREATORS data = $smarty.capture.t_users_to_blogs_code image = $T_MODULE_BASELINK|cat:'images/book_blue_preferences.png' absoluteImagePath=1}
    </div>
 {/if}
</div>
{/capture}
{eF_template_printBlock title = $smarty.const._BLOGS_BLOG data = $smarty.capture.t_module_blogs_code image=$T_MODULE_BASELINK|cat:'images/eFrontBlog32.png' absoluteImagePath=1 help = 'Blog'}

{elseif isset($smarty.get.add_article) || isset($smarty.get.edit_article)}
{capture name = 't_module_blogs_addArticle}
    {$T_ARTICLE_ADD_FORM.javascript}
    <form {$T_ARTICLE_ADD_FORM.attributes}>
    {$T_ARTICLE_ADD_FORM.hidden}
        <table class = "formElements" width="100%">
            <tr><td class = "labelCell">{$smarty.const._TITLE}:&nbsp;</td>
                <td class = "elementCell">{$T_ARTICLE_ADD_FORM.title.html}</td></tr>
                {if $T_ARTICLE_ADD_FORM.title.error}<tr><td></td><td class = "formError">{$T_ARTICLE_ADD_FORM.title.error}</td></tr>{/if}
            <tr><td class = "labelCell">{$T_ARTICLE_ADD_FORM.data.label}:&nbsp;</td>
                <td class = "elementCell">{$T_ARTICLE_ADD_FORM.data.html}</td></tr>
     <tr><td></td><td class = "elementCell"><a href="javascript:toggleEditor('blog_article_data','simpleEditor');" id="toggleeditor_link">
                                               <img src = {$T_MODULE_BASELINK|cat:"images/order.png"} title = "{$smarty.const._TOGGLEHTMLEDITORMODE}" alt = "{$smarty.const._TOGGLEHTMLEDITORMODE}" style = "vertical-align:middle" border = "0"/>
                                               &nbsp;<span style = "vertical-align:middle">{$smarty.const._TOGGLEHTMLEDITORMODE}</span>
                                              </a></td></tr>
                {if $T_ARTICLE_ADD_FORM.data.error}<tr><td></td><td class = "formError">{$T_ARTICLE_ADD_FORM.data.error}</td></tr>{/if}
       <tr><td colspan = "2">&nbsp;</td></tr>
            <tr><td></td><td class = "submitCell">{$T_ARTICLE_ADD_FORM.submit_add_article.html}</td></tr>
        </table>
    </form>

{/capture}
{eF_template_printBlock title=$smarty.const._BLOGS_ARTICLESFORM data=$smarty.capture.t_module_blogs_addArticle image=$T_MODULE_BASELINK|cat:'images/eFrontBlog32.png' absoluteImagePath=1 help = 'Blog'}

{elseif isset($smarty.get.add_comment) || isset($smarty.get.edit_comment)}
{capture name = 't_module_blogs_addComment}
    {$T_COMMENT_ADD_FORM.javascript}
    <form {$T_COMMENT_ADD_FORM.attributes}>
    {$T_COMMENT_ADD_FORM.hidden}
        <table width="100%" class = "formElements">
            <tr><td style = "text-align:left;white-space:nowrap;">{$smarty.const._BLOGS_YOURCOMMENT}:&nbsp;</td></tr>
            <tr><td class = "elementCell">{$T_COMMENT_ADD_FORM.data.html}</td></tr>
                {if $T_COMMENT_ADD_FORM.data.error}<tr><td></td><td class = "formError">{$T_COMMENT_ADD_FORM.data.error}</td></tr>{/if}
            <tr><td>&nbsp;</td></tr>
            <tr><td>{$T_COMMENT_ADD_FORM.submit_add_comment.html}</td></tr>
        </table>
    </form>
<br />
 <table width="100%" border="0" cellpadding="3px">
  <tr>
   <td>
      <span class="moduleblogsDate">#filter:timestamp_time-{$T_BLOGS_ARTICLE.timestamp}#</span><br />

      <span class="moduleblogsArticleTitle"><a href="{$T_MODULE_BASEURL}&view_article={$T_BLOGS_ARTICLE.id}">{$T_BLOGS_ARTICLE.title}</a></span> {$smarty.const._BLOGS_BY} {$T_BLOGS_ARTICLE.users_LOGIN}
      {if $smarty.session.s_login == $T_BLOGS_BLOG.users_LOGIN || $smarty.session.s_login == $T_BLOGS_ARTICLE.users_LOGIN}
       <a href="{$T_MODULE_BASEURL}&blog_id={$T_BLOGS_BLOG.id}&edit_article={$T_BLOGS_ARTICLE.id}"><img border="0" style="vertical-align:middle" src = "{$T_MODULE_BASELINK}images/edit.png" alt = "{$smarty.const._BLOGS_EDITARTICLE}" title = "{$smarty.const._BLOGS_EDITARTICLE}"/></a>
       <a href="{$T_MODULE_BASEURL}&delete_article={$T_BLOGS_ARTICLE.id}" onclick = "return confirm ('{$smarty.const._IRREVERSIBLEACTIONAREYOUSURE}')"><img style="vertical-align:middle" src = {$T_MODULE_BASELINK|cat:"images/error_delete.png"} alt = "{$smarty.const._DELETE}" title = "{$smarty.const._DELETE}" border = "0"/></a>
      {/if}
      <br /><br />
      <div>{$T_BLOGS_ARTICLE.data}</div><br />
      <span class="moduleblogsItalics">{$T_BLOGS_ARTICLE.comments} {if $T_BLOGS_ARTICLE.comments != 1}{$smarty.const._BLOGS_COMMENTS} {else}{$smarty.const._BLOGS_COMMENT}{/if}</span> <a href = "{$T_MODULE_BASEURL}&article_id={$T_BLOGS_ARTICLE.id}&add_comment"><img border="0" style="vertical-align:middle" src = "{$T_MODULE_BASELINK}images/write.png" alt = "{$smarty.const._BLOGS_ADDCOMMENT}" title = "{$smarty.const._BLOGS_ADDCOMMENT}"/></a>




   </td>
  </tr>
     {foreach name = "blogComments_list" item = "comment" key = "key" from = $T_BLOGS_COMMENTS}
      <tr class = "{cycle values = "blogsoddRowColor, blogsevenRowColor"}">
      <td>

      <span class="moduleblogsGray">{$comment.users_LOGIN} {$smarty.const._BLOGS_COMMENTED}...</span><br />
      <div>{$comment.data}</div><br />

      <span class="moduleblogsDate">#filter:timestamp_time-{$comment.timestamp}#</span>
      {if $smarty.session.s_login == $T_BLOGS_BLOG.users_LOGIN || $smarty.session.s_login == $comment.users_LOGIN}
       <a href = "{$T_MODULE_BASEURL}&article_id={$T_BLOGS_ARTICLE.id}&edit_comment={$comment.id}"><img border="0" style="vertical-align:middle" src = "{$T_MODULE_BASELINK}images/edit.png" alt = "{$smarty.const._BLOGS_EDITCOMMENT}" title = "{$smarty.const._BLOGS_EDITCOMMENT}"/></a>
       <a href = "{$T_MODULE_BASEURL}&article_id={$T_BLOGS_ARTICLE.id}&delete_comment={$comment.id}" onclick = "return confirm ('{$smarty.const._IRREVERSIBLEACTIONAREYOUSURE}')"><img border="0" style="vertical-align:middle" src = {$T_MODULE_BASELINK|cat:"images/error_delete.png"} alt = "{$smarty.const._BLOGS_DELETECOMMENT}" title = "{$smarty.const._BLOGS_DELETECOMMENT}"/></a>
      {/if}
      {if !$smarty.foreach.blogComments_list.last}<hr class="moduleblogsThin">{/if}</td></tr>
     {/foreach}
 </table>
{/capture}
{eF_template_printBlock title=$smarty.const._BLOGS_COMMENTS_FORM data=$smarty.capture.t_module_blogs_addComment image=$T_MODULE_BASELINK|cat:'images/eFrontBlog32.png' absoluteImagePath=1 help = 'Blog'}
{elseif (isset($smarty.get.view_blog))}
{capture name = 't_module_blogs_viewBlog}
 <table width="100%" border="0" cellpadding="3px">
  <tr><td width="70%" valign="top">
   <table width="100%" valign="top">

   {if $smarty.session.s_login == $T_BLOGS_BLOG.users_LOGIN || $T_BLOGS_ISBLOGCREATOR == 1}
   <tr><td><img style="vertial-align" src = {$T_MODULE_BASELINK|cat:"images/add.png"} alt = "{$smarty.const._BLOGS_NEWARTICLE}" title = "{$smarty.const._BLOGS_NEWARTICLE}"/>
   <a href = "{$T_MODULE_BASEURL}&blog_id={$T_BLOGS_BLOG.id}&add_article">{$smarty.const._BLOGS_NEWARTICLE}</a></td></tr>
   {/if}



     {foreach name = "blogPosts_list" item = "article" key = "key" from = $T_BLOGS_POSTS}
       {if $smarty.foreach.blogPosts_list.iteration <= 10}
      <tr>
      <td>
      <span class="moduleblogsDate">#filter:timestamp_time-{$article.timestamp}#</span><br />

      <span class="moduleblogsArticleTitle"><a href="{$T_MODULE_BASEURL}&view_article={$article.id}">{$article.title}</a></span> {$smarty.const._BLOGS_BY} {$article.users_LOGIN}

      {if $smarty.session.s_login == $article.users_LOGIN}
       <a href="{$T_MODULE_BASEURL}&blog_id={$T_BLOGS_BLOG.id}&edit_article={$article.id}"><img border="0" style="vertical-align:middle" src = "{$T_MODULE_BASELINK}images/edit.png" alt = "{$smarty.const._BLOGS_EDITARTICLE}" title = "{$smarty.const._BLOGS_EDITARTICLE}"/></a>
      {/if}
       {if $smarty.session.s_login == $article.users_LOGIN || $smarty.session.s_login == $T_BLOGS_BLOG.users_LOGIN}
       <a href="{$T_MODULE_BASEURL}&delete_article={$article.id}" onclick = "return confirm ('{$smarty.const._IRREVERSIBLEACTIONAREYOUSURE}')"><img style="vertical-align:middle" src = {$T_MODULE_BASELINK|cat:"images/error_delete.png"} alt = "{$smarty.const._DELETE}" title = "{$smarty.const._DELETE}" border = "0"/></a>
      {/if}
      <br /><br />
      <div>{$article.data}</div><br />
      <span class="moduleblogsItalics"><a href="{$T_MODULE_BASEURL}&view_article={$article.id}#comments_start"> {$article.comments} {if $article.comments != 1}{$smarty.const._BLOGS_COMMENTS} {else}{$smarty.const._BLOGS_COMMENT}{/if}</a></span> {if $article.comments != 0}<span class="moduleblogsGray">({$smarty.const._BLOGS_LASTCOMMENTAT} #filter:timestamp_time-{$article.last_comment.timestamp}#) </span>{/if}<a href = "{$T_MODULE_BASEURL}&article_id={$article.id}&add_comment"> <img border="0" style="vertical-align:middle" src = "{$T_MODULE_BASELINK}images/write.png" alt = "{$smarty.const._BLOGS_ADDCOMMENT}" title = "{$smarty.const._BLOGS_ADDCOMMENT}"/></a>
      <hr class="moduleblogsThin">
      </td></tr>
       {/if}
     {foreachelse}
      <tr class = "oddRowColor defaultRowHeight"><td colspan = "100%" class = "emptyCategory">{$smarty.const._BLOGS_NOARTICLESFOUND}</td></tr>
     {/foreach}
     {if $smarty.foreach.blogPosts_list.total >0 && !isset($smarty.get.show_all)}
       <tr><td align ="right"><a href="{$T_MODULE_BASEURL}&view_blog={$T_BLOGS_BLOG.id}&show_all">{$smarty.const._BLOGS_SHOWALLARTICLES}</a></td></tr>
     {/if}
   </table>
  </td><td height="100%" width="30%" valign="top" class="moduleblogsRight">
   <table width="100%">
    <tr><td valign="top"><span class="moduleblogsSubTitle">{$smarty.const._BLOGS_DESCRIPTION}</span><br/>{$T_BLOGS_BLOG.description}</td></tr>
    <tr><td valign="top">&nbsp;</td></tr>
    {if $smarty.foreach.blogPosts_list.total > 0}
    <tr><td valign="top"><span class="moduleblogsSubTitle">{$smarty.const._BLOGS_LASTCOMMENTS}</span></td></tr>
    {foreach name = "blogLastComments_list" item = "comment" key = "key" from = $T_BLOGS_LASTCOMMENTS}
     {if $smarty.foreach.blogLastComments_list.iteration <= 10}
     {assign var="commentData" value=$comment.data|eF_truncate:40:"...":true}
      <tr class = "oddRowColor"><td valign="top"><span class="moduleblogsGray">{$comment.users_LOGIN} {$smarty.const._BLOGS_COMMENTEDON} <a href="{$T_MODULE_BASEURL}&view_article={$comment.article_id}">{$comment.title|eF_truncate:27:"...":true}</a></span><br />
      <div><span style="float: left;"><a href="{$T_MODULE_BASEURL}&view_article={$comment.article_id}#{$comment.comment_id}">{$commentData|replace:'<p>':' '}</a></span><span class="moduleblogsGray" style="float: right;text-align: right;">#filter:timestamp_time-{$comment.timestamp}#</span></div></td></tr>
     {/if}
    {foreachelse}
     <tr class = "oddRowColor defaultRowHeight"><td colspan = "100%" class = "emptyCategory">{$smarty.const._BLOGS_NOCOMMENTSFOUND}</td></tr>
    {/foreach}
    <tr><td valign="top">&nbsp;</td></tr>
    <tr><td valign="top"><span class="moduleblogsSubTitle">{$smarty.const._BLOGS_LASTARTICLES}</span></td></tr>
    {foreach name = "blogLastArticles_list" item = "article" key = "key" from = $T_BLOGS_POSTS}
     {if $smarty.foreach.blogLastArticles_list.iteration <= 20}
      <tr class = "oddRowColor"><td valign="top"><div><span style="float: left;"><a href="{$T_MODULE_BASEURL}&view_article={$article.id}">{$article.title|eF_truncate:40:"...":true}</a></span><span class="moduleblogsGray" style="float: right;">#filter:timestamp_time-{$article.timestamp}#</span></div></td></tr>
     {/if}
    {foreachelse}
     <tr class = "oddRowColor defaultRowHeight"><td colspan = "100%" class = "emptyCategory">{$smarty.const._BLOGS_NOARTICLESSFOUND}</td></tr>
    {/foreach}

    <tr><td valign="top">&nbsp;</td></tr>
    <tr><td valign="top"><span class="moduleblogsSubTitle">{$smarty.const._BLOGS_ARCHIVE}</span></td></tr>
    <tr><td valign="top">
      <table>
       {foreach name = "blogArchive" item = "year" key = "key" from = $T_BLOGS_INDEXING}
       <tr><td><img id="{$key}_img" src= "{$T_MODULE_BASELINK}images/navigate_down12.png" onClick="$('{$key}').toggle();this.src.match(/down/)?this.src = '{$T_MODULE_BASELINK}images/navigate_up12.png' :this.src = '{$T_MODULE_BASELINK}images/navigate_down12.png'" /><strong>{$key}</strong></td><td></td></tr>
       <tr><td></td><td>
        <table id="{$key}" style="display:none;">
        {foreach name = "blogArchive2" item = "month" key = "key2" from = $year}
        <tr><td><img id="{$key}_{$key2}_img" src="{$T_MODULE_BASELINK}images/navigate_down12.png" onClick="$('{$key}_{$key2}').toggle();this.src.match(/down/)?this.src = '{$T_MODULE_BASELINK}images/navigate_up12.png':this.src = '{$T_MODULE_BASELINK}images/navigate_down12.png' " /><strong>{$key2}</strong></td><td></td></tr>
        <tr><td>
         <table id="{$key}_{$key2}" style="display:none;">
         {foreach name = "blogArchive3" item = "article" key = "key3" from = $month}
          <tr><td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td><td><a href="{$T_MODULE_BASEURL}&view_article={$key3}">{$article}</a></td></tr>
         {/foreach}
         </table>
        </td></tr>
        {/foreach}
        </table>
       </td></tr>
       {/foreach}
      </table>
    </td></tr>
   {/if}
   </table>
  </td></tr>
 </table>
{/capture}
{eF_template_printBlock title=$T_BLOGS_BLOG.name data=$smarty.capture.t_module_blogs_viewBlog image=$T_MODULE_BASELINK|cat:'images/eFrontBlog32.png' absoluteImagePath=1 help = 'Blog'}
{elseif (isset($smarty.get.view_article))}
{capture name = 't_module_blogs_viewArticle}
 <table width="100%" border="0" cellpadding="3px">
 <tr>
   <td>
     {if $smarty.session.s_login == $T_BLOGS_BLOG.users_LOGIN || $T_BLOGS_ISBLOGCREATOR == 1}
      <img style="vertial-align" src = {$T_MODULE_BASELINK|cat:"images/add.png"} alt = "{$smarty.const._BLOGS_NEWARTICLE}" title = "{$smarty.const._BLOGS_NEWARTICLE}"/><a href = "{$T_MODULE_BASEURL}&blog_id={$T_BLOGS_BLOG.id}&add_article">{$smarty.const._BLOGS_NEWARTICLE}</a><br/>
     {/if}
      <span class="moduleblogsDate">#filter:timestamp_time-{$T_BLOGS_ARTICLE.timestamp}#</span><br />

      <span class="moduleblogsArticleTitle"><a href="{$T_MODULE_BASEURL}&view_article={$T_BLOGS_ARTICLE.id}">{$T_BLOGS_ARTICLE.title}</a></span> {$smarty.const._BLOGS_BY} {$T_BLOGS_ARTICLE.users_LOGIN}

      {if $T_BLOGS_BLOG.users_LOGIN == $smarty.session.s_login || $smarty.session.s_login == $T_BLOGS_ARTICLE.users_LOGIN}
       <a href="{$T_MODULE_BASEURL}&blog_id={$T_BLOGS_BLOG.id}&edit_article={$T_BLOGS_ARTICLE.id}"><img border="0" style="vertical-align:middle" src = "{$T_MODULE_BASELINK}images/edit.png" alt = "{$smarty.const._BLOGS_EDITARTICLE}" title = "{$smarty.const._BLOGS_EDITARTICLE}"/></a>
       <a href="{$T_MODULE_BASEURL}&delete_article={$T_BLOGS_ARTICLE.id}" onclick = "return confirm ('{$smarty.const._IRREVERSIBLEACTIONAREYOUSURE}')"><img style="vertical-align:middle" src = "{$T_MODULE_BASELINK}images/error_delete.png" alt = "{$smarty.const._DELETE}" title = "{$smarty.const._DELETE}" border = "0"/></a>
      {/if}
      <br /><br />
      <div>{$T_BLOGS_ARTICLE.data}</div><br />
      <span class="moduleblogsItalics">{$T_BLOGS_ARTICLE.comments} {if $T_BLOGS_ARTICLE.comments != 1}{$smarty.const._BLOGS_COMMENTS} {else}{$smarty.const._BLOGS_COMMENT}{/if}</span> <a href = "{$T_MODULE_BASEURL}&article_id={$T_BLOGS_ARTICLE.id}&add_comment"><img border="0" style="vertical-align:middle" src = "{$T_MODULE_BASELINK}images/write.png" alt = "{$smarty.const._BLOGS_ADDCOMMENT}" title = "{$smarty.const._BLOGS_ADDCOMMENT}"/></a>

   </td>
  </tr>
     {foreach name = "blogComments_list" item = "comment" key = "key" from = $T_BLOGS_COMMENTS}
      <tr class = "{cycle values = "blogsoddRowColor, blogsevenRowColor"}">
      <td>
      {if $smarty.foreach.blogComments_list.first}<a name="comments_start"></a>{/if}
      <a name="{$comment.id}"></a>
      <span class="moduleblogsGray">{$comment.users_LOGIN} {$smarty.const._BLOGS_COMMENTED}...</span><br />
      <div>{$comment.data}</div><br />
      <span class="moduleblogsDate">#filter:timestamp_time-{$comment.timestamp}#</span>

      {if $T_BLOGS_BLOG.users_LOGIN == $smarty.session.s_login || $smarty.session.s_login == $comment.users_LOGIN}
       <a href = "{$T_MODULE_BASEURL}&article_id={$T_BLOGS_ARTICLE.id}&edit_comment={$comment.id}"><img border="0" style="vertical-align:middle" src = "{$T_MODULE_BASELINK}images/edit.png" alt = "{$smarty.const._BLOGS_EDITCOMMENT}" title = "{$smarty.const._BLOGS_EDITCOMMENT}"/></a>
       <a href = "{$T_MODULE_BASEURL}&article_id={$T_BLOGS_ARTICLE.id}&delete_comment={$comment.id}" onclick = "return confirm ('{$smarty.const._IRREVERSIBLEACTIONAREYOUSURE}')"><img border="0" style="vertical-align:middle" src = "{$T_MODULE_BASELINK}images/error_delete.png" alt = "{$smarty.const._BLOGS_DELETECOMMENT}" title = "{$smarty.const._BLOGS_DELETECOMMENT}"/></a>
      {/if}
      </td></tr>
     {/foreach}
 </table>

{/capture}
{eF_template_printBlock title=$T_BLOGS_BLOG.name|cat:" ("|cat:$T_BLOGS_ARTICLE.title|cat:")" data=$smarty.capture.t_module_blogs_viewArticle image=$T_MODULE_BASELINK|cat:'images/eFrontBlog32.png' absoluteImagePath=1 help = 'Blog'}
{else}
{capture name = 't_module_blogs_lessonBlogs}
{if $smarty.session.s_type == "professor"}
<img src = "{$T_MODULE_BASELINK}images/add.png" alt = "{$smarty.const._BLOGS_NEWBLOG}" title = "{$smarty.const._BLOGS_NEWBLOG}"/>
<a href = "{$T_MODULE_BASEURL}&add_blog">{$smarty.const._BLOGS_NEWBLOG}</a>&nbsp;
{/if}

<table class = "sortedTable" width="100%">
    <tr>
    <td class = "topTitle smallHeader" style = "width:20%;text-align:left;vertical-align:middle">{$smarty.const._BLOGS_TITLE}</td>
    <td style = "width:40%" class = "topTitle">{$smarty.const._BLOGS_DESCRIPTION}</td>
    <td style = "width:10%" class = "topTitle">{$smarty.const._BLOGS_CREATOR}</td>
 <td style = "width:25%" class = "topTitle">{$smarty.const._BLOGS_LASTPOST}</td>
 <td style = "width:5%" class = "topTitle noSort">{$smarty.const._BLOGS_OPERATIONS}</td>
 </tr>
 {foreach name = "lessonBlogs_list" item = "blog" key = "key" from = $T_BLOGS_LESSONBLOGS}
 {if $blog.active == 1 || $blog.users_LOGIN == $smarty.session.s_login}
 <tr class = "{cycle name = $key values = "oddRowColor,evenRowColor"}">
  <td><span style="display:none">{$blog.name}</span><a href="{$T_MODULE_BASEURL}&view_blog={$blog.id}">{$blog.name}</a></td>
  <td>{$blog.description}</td>
  <td>{$blog.users_LOGIN}</td>
  <td>
   {if ($blog.last_article.title != "")}
   <span style="display:none">{$blog.last_article.title}</span><a href="{$T_MODULE_BASEURL}&view_article={$blog.last_article.id}">{$blog.last_article.title}</a><br/>#filter:timestamp_time-{$blog.last_article.timestamp}#, {$smarty.const._BLOGS_BY} {$blog.last_article.users_LOGIN}
   {/if}
  </td>

  <td>
  {if $smarty.session.s_login == $blog.users_LOGIN}
   <a href="{$T_MODULE_BASEURL}&edit_blog={$blog.id}"><img src = "{$T_MODULE_BASELINK}images/edit.png" alt = "{$smarty.const._BLOGS_EDITBLOG}" title = "{$smarty.const._BLOGS_EDITBLOG}"/></a>
   <a href="{$T_MODULE_BASEURL}&delete_blog={$blog.id}" onclick = "return confirm ('{$smarty.const._IRREVERSIBLEACTIONAREYOUSURE}')"><img src = "{$T_MODULE_BASELINK}images/error_delete.png" alt = "{$smarty.const._DELETE}" title = "{$smarty.const._DELETE}" border = "0"/></a>
   {if $blog.active == 1}
                <a href="{$T_MODULE_BASEURL}&deactivate_blog={$blog.id}"><img src = "{$T_MODULE_BASELINK}images/trafficlight_green.png" alt = "{$smarty.const._DEACTIVATE}" title = "{$smarty.const._DEACTIVATE}" border = "0"></a>
            {else}
                <a href="{$T_MODULE_BASEURL}&activate_blog={$blog.id}"><img src = "{$T_MODULE_BASELINK}images/trafficlight_red.png" alt = "{$smarty.const._ACTIVATE}" title = "{$smarty.const._ACTIVATE}" border = "0"></a>
   {/if}
  {/if}
  </td>
 </tr>
 {/if}
 {foreachelse}
  <tr class = "oddRowColor defaultRowHeight"><td colspan = "100%" class = "emptyCategory">{$smarty.const._BLOGS_NOBLOGSFOUND}</td></tr>
 {/foreach}
</table>
{/capture}

{eF_template_printBlock title=$smarty.const._BLOGS_BLOG data=$smarty.capture.t_module_blogs_lessonBlogs image=$T_MODULE_BASELINK|cat:'images/eFrontBlog32.png' absoluteImagePath=1 help = 'Blog'}
{/if}

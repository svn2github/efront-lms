
 {if $T_LESSON_INFO}
         {foreach name = 'info_list' item = "item" key = "key" from = $T_LESSON_INFO->metadataArray}
          <div class = "lessonInfo"><span class = "infoTitle">{$T_LESSON_INFO->metadataAttributes.$key}:</span> {$item}</div>
         {/foreach}
          <div class = "lessonInfo"><span class = "infoTitle">{$smarty.const._LANGUAGE}:</span> {$T_LANGUAGES[$T_ADDITIONAL_LESSON_INFO.language]}</div>
          {if $T_ADDITIONAL_LESSON_INFO.professors_string}<div class = "lessonInfo"><span class = "infoTitle">{$smarty.const._PROFESSORS}:</span> {$T_ADDITIONAL_LESSON_INFO.professors_string}</div>{/if}
          <div class = "lessonInfo"><span class = "infoTitle">{$smarty.const._TOTALUNITS}:</span> {$T_ADDITIONAL_LESSON_INFO.content}</div>
          {if $T_ADDITIONAL_LESSON_INFO.tests}<div class = "lessonInfo"><span class = "infoTitle">{$smarty.const._TOTALTESTS}:</span> {$T_ADDITIONAL_LESSON_INFO.tests}</div>{/if}
          {if $T_ADDITIONAL_LESSON_INFO.projects}<div class = "lessonInfo"><span class = "infoTitle">{$smarty.const._TOTALPROJECTS}:</span> {$T_ADDITIONAL_LESSON_INFO.projects}</div>{/if}
         {if $T_CONTENT_TREE}
          <fieldset class = "fieldsetSeparator">
              <legend>{$smarty.const._LESSONCONTENT}</legend>
              {$T_CONTENT_TREE}
          </fieldset>
         {/if}
  {if !$T_LESSON->lesson.course_only && (!$T_CURRENT_USER || $T_CURRENT_USER->user.user_type == 'student')}
          <div id = "buy">
      {if $T_LESSON->lesson.price}
          {if $T_HAS_LESSON}
              <span>{$T_LESSON->lesson.price_string}</span>
              <img class = "ajaxHandle inactiveImage" src = "images/32x32/shopping_basket_add.png" title = "{$smarty.const._YOUALREADYHAVETHISLESSON}" alt = "{$smarty.const._YOUALREADYHAVETHISLESSON}" onclick = "alert('{$smarty.const._YOUALREADYHAVETHISLESSON}')">
             {else}
              <span>{$T_LESSON->lesson.price_string}</span>
              <img class = "ajaxHandle" src = "images/32x32/shopping_basket_add.png" title = "{$smarty.const._ADDTOCART}" alt = "{$smarty.const._ADDTOCART}" onclick = "addToCart(this, '{$T_LESSON->lesson.id}', 'lesson')">
             {/if}
         {else}
          {if $T_HAS_LESSON}
              <span>{$T_LESSON->lesson.price_string}</span>
              <img class = "ajaxHandle inactiveImage" src = "images/32x32/shopping_basket_add.png" title = "{$smarty.const._YOUALREADYHAVETHISLESSON}" alt = "{$smarty.const._YOUALREADYHAVETHISLESSON}" onclick = "alert('{$smarty.const._YOUALREADYHAVETHISLESSON}')">
             {else}
              <span>{$smarty.const._FREEOFCHARGE}</span>
              <img class = "ajaxHandle" src = "images/32x32/shopping_basket_add.png" title = "{$smarty.const._ENROLL}" alt = "{$smarty.const._ENROLL}" onclick = "addToCart(this, '{$T_LESSON->lesson.id}', 'lesson')">
             {/if}
         {/if}
          </div>
        {elseif $T_LESSON->lesson.course_only}
          <div id = "buy">
          {if $T_COURSE->course.price}
              {if $T_HAS_COURSE}
                  <span>{$smarty.const._YOULAREDYHAVETHECOURSE} &quot;{$T_COURSE->course.name}&quot;</span>
                  <img class = "ajaxHandle inactiveImage" src = "images/32x32/shopping_basket_add.png" title = "{$smarty.const._YOUALREADYHAVETHISCOURSE}" alt = "{$smarty.const._YOUALREADYHAVETHISCOURSE}" onclick = "alert('{$smarty.const._YOUALREADYHAVETHISCOURSE}')">
                 {else}
                  <span>{$smarty.const._GETTHECOURSE} &quot;{$T_COURSE->course.name}&quot;, {$T_COURSE->course.price_string}</span>
                  <img class = "ajaxHandle" src = "images/32x32/shopping_basket_add.png" title = "{$smarty.const._ADDTOCART}" alt = "{$smarty.const._ADDTOCART}" onclick = "addToCart(this, '{$T_COURSE->course.id}', 'course')">
                 {/if}
          {else}
              {if $T_HAS_COURSE}
                  <span>{$smarty.const._YOULAREDYHAVETHECOURSE} &quot;{$T_COURSE->course.name}&quot; {$T_COURSE->course.price_string}</span>
                  <img class = "ajaxHandle inactiveImage" src = "images/32x32/shopping_basket_add.png" title = "{$smarty.const._YOUALREADYHAVETHISCOURSE}" alt = "{$smarty.const._YOUALREADYHAVETHISCOURSE}" onclick = "alert('{$smarty.const._YOUALREADYHAVETHISCOURSE}')">
                 {else}
                  <span>{$smarty.const._GETTHECOURSE} &quot;{$T_COURSE->course.name}&quot;, {$smarty.const._FREEOFCHARGE}</span>
                  <img class = "ajaxHandle" src = "images/32x32/shopping_basket_add.png" title = "{$smarty.const._ENROLL}" alt = "{$smarty.const._ENROLL}" onclick = "addToCart(this, '{$T_COURSE->course.id}', 'course')">
                 {/if}
       {/if}
    </div>
          <div>
           <div class = "lessonInfo"><span class = "infoTitle">{$smarty.const._LESSONPARTOFCOURSES}:</span>
         {foreach name = 'lesson_courses' item = "item" key = "key" from = $T_LESSON_COURSES}
          <a class = "editLink" href = "{$smarty.server.PHP_SELF}?{if $smarty.get.ctg}ctg={$smarty.get.ctg}&{/if}courses_ID={$item.id}">{$item.name}{if !$smarty.foreach.lesson_courses.last},{/if}</a>
         {/foreach}
           </div>
          </div>
        {/if}
    {elseif $T_COURSE_INFO}
        {capture name = 't_lesson_info_code'}
         {foreach name = 'info_list' item = "item" key = "key" from = $T_COURSE_INFO->metadataArray}
          <div class = "lessonInfo"><span class = "infoTitle">{$T_COURSE_INFO->metadataAttributes.$key}:</span> {$item}</div>
         {/foreach}
      <div class = "lessonInfo"><span class = "infoTitle">{$smarty.const._LANGUAGE}:</span> {$T_LANGUAGES[$T_ADDITIONAL_COURSE_INFO.language]}</div>

         {if $T_COURSE_LESSONS}
          <div class = "lessonInfo"><span class = "infoTitle">{$smarty.const._COURSELESSONS}:</span>
          {foreach name = 'course_lessons_list' item = "item" key = "key" from = $T_COURSE_LESSONS}

    {if $smarty.get.catalog}
     <a href = "{$smarty.server.PHP_SELF}?{if $smarty.get.ctg}ctg={$smarty.get.ctg}&{/if}catalog=1&info_lesson={$item.id}&from_course={if $smarty.get.courses_ID}{$smarty.get.courses_ID}{else}{$smarty.get.info_course}{/if}" class = "editLink">{$item.name}{if !$smarty.foreach.course_lessons_list.last},{/if}</a>
    {else}
     <a href = "{$smarty.server.PHP_SELF}?{if $smarty.get.ctg}ctg={$smarty.get.ctg}&{/if}lessons_ID={$item.id}&course={if $smarty.get.courses_ID}{$smarty.get.courses_ID}{else}{$smarty.get.info_course}{/if}" class = "editLink">{$item.name}{if !$smarty.foreach.course_lessons_list.last},{/if}</a>
    {/if}

    {/foreach}
          </div>
         {/if}
         {if !$T_CURRENT_USER || $T_CURRENT_USER->user.user_type == 'student'}
              <div id = "buy">
          {if $T_COURSE->course.price}
              {if $T_HAS_COURSE}
                  <span>{$T_COURSE->course.price_string}</span>
                  <img class = "ajaxHandle inactiveImage" src = "images/32x32/shopping_basket_add.png" title = "{$smarty.const._YOUALREADYHAVETHISCOURSE}" alt = "{$smarty.const._YOUALREADYHAVETHISCOURSE}" onclick = "alert('{$smarty.const._YOUALREADYHAVETHISCOURSE}')">
                 {else}
                  <span>{$T_COURSE->course.price_string}</span>
                  <img class = "ajaxHandle" src = "images/32x32/shopping_basket_add.png" title = "{$smarty.const._ADDTOCART}" alt = "{$smarty.const._ADDTOCART}" onclick = "addToCart(this, '{$T_COURSE->course.id}', 'course')">
                 {/if}
          {else}
              {if $T_HAS_COURSE}
                  <span>{$T_COURSE->course.price_string}</span>
                  <img class = "ajaxHandle inactiveImage" src = "images/32x32/shopping_basket_add.png" title = "{$smarty.const._YOUALREADYHAVETHISCOURSE}" alt = "{$smarty.const._YOUALREADYHAVETHISCOURSE}" onclick = "alert('{$smarty.const._YOUALREADYHAVETHISCOURSE}')">
                 {else}
                  <span>{$smarty.const._FREEOFCHARGE}</span>
                  <img class = "ajaxHandle" src = "images/32x32/shopping_basket_add.png" title = "{$smarty.const._ENROLL}" alt = "{$smarty.const._ENROLL}" onclick = "addToCart(this, '{$T_COURSE->course.id}', 'course')">
                 {/if}
       {/if}
                 </div>
             {/if}
 {/if}

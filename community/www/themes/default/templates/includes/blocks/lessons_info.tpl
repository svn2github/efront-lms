 {if $T_CONFIGURATION.enable_cart}{assign var = "cart_image" value = "shopping_basket_add.png"}{else}{assign var = "cart_image" value = "add.png"}{/if}

 {if $T_LESSON_INFO}
         {foreach name = 'info_list' item = "item" key = "key" from = $T_LESSON_INFO->metadataArray}
          {if $item}<div class = "lessonInfo"><span class = "infoTitle">{$T_LESSON_INFO->metadataAttributes.$key}:</span> {$item}</div>{/if}
         {/foreach}
          {if $T_LANGUAGES[$T_ADDITIONAL_LESSON_INFO.language]}<div class = "lessonInfo"><span class = "infoTitle">{$smarty.const._LANGUAGE}:</span> {$T_LANGUAGES[$T_ADDITIONAL_LESSON_INFO.language]}</div>{/if}
          {if $T_ADDITIONAL_LESSON_INFO.professors_string}<div class = "lessonInfo"><span class = "infoTitle">{$smarty.const._PROFESSORS}:</span> {$T_ADDITIONAL_LESSON_INFO.professors_string}</div>{/if}
          {if $T_ADDITIONAL_LESSON_INFO.content || $T_ADDITIONAL_LESSON_INFO.tests || $T_ADDITIONAL_LESSON_INFO.projects}
           <div class = "lessonInfo">
     {if $T_ADDITIONAL_LESSON_INFO.content}<span class = "infoTitle">{$smarty.const._UNITS}:</span> {$T_ADDITIONAL_LESSON_INFO.content}{/if}
     {if $T_ADDITIONAL_LESSON_INFO.tests}{if $T_ADDITIONAL_LESSON_INFO.content},{/if} <span class = "infoTitle">{$smarty.const._TESTS}:</span> {$T_ADDITIONAL_LESSON_INFO.tests}{/if}
     {if $T_ADDITIONAL_LESSON_INFO.projects}{if $T_ADDITIONAL_LESSON_INFO.content || $T_ADDITIONAL_LESSON_INFO.tests},{/if} <span class = "infoTitle">{$smarty.const._PROJECTS}:</span> {$T_ADDITIONAL_LESSON_INFO.projects}{/if}
     </div>
    {/if}
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
              <img class = "ajaxHandle inactiveImage" src = "images/32x32/{$cart_image}" title = "{$smarty.const._YOUALREADYHAVETHISLESSON}" alt = "{$smarty.const._YOUALREADYHAVETHISLESSON}" onclick = "alert('{$smarty.const._YOUALREADYHAVETHISLESSON}')">
             {else}
              <span>{$T_LESSON->lesson.price_string}</span>
              <img class = "ajaxHandle" src = "images/32x32/{$cart_image}" title = "{$smarty.const._ADDTOCART}" alt = "{$smarty.const._ADDTOCART}" onclick = "addToCart(this, '{$T_LESSON->lesson.id}', 'lesson');{if !$T_CONFIGURATION.enable_cart}location=redirectLocation{/if}">
             {/if}
         {else}
          {if $T_HAS_LESSON}
              <span>{$T_LESSON->lesson.price_string}</span>
              <img class = "ajaxHandle inactiveImage" src = "images/32x32/{$cart_image}" title = "{$smarty.const._YOUALREADYHAVETHISLESSON}" alt = "{$smarty.const._YOUALREADYHAVETHISLESSON}" onclick = "alert('{$smarty.const._YOUALREADYHAVETHISLESSON}')">
             {else}
              <span>{$smarty.const._FREEOFCHARGE}</span>
              <img class = "ajaxHandle" src = "images/32x32/{$cart_image}" title = "{$smarty.const._ENROLL}" alt = "{$smarty.const._ENROLL}" onclick = "addToCart(this, '{$T_LESSON->lesson.id}', 'lesson');{if !$T_CONFIGURATION.enable_cart}location=redirectLocation{/if}">
             {/if}
         {/if}
          </div>
        {elseif $T_LESSON->lesson.course_only}
          <div id = "buy">
          {if $T_COURSE->course.price}
              {if $T_HAS_COURSE}
                  <span>{$smarty.const._YOULAREDYHAVETHECOURSE} &quot;{$T_COURSE->course.name}&quot;</span>
                  <img class = "ajaxHandle inactiveImage" src = "images/32x32/{$cart_image}" title = "{$smarty.const._YOUALREADYHAVETHISCOURSE}" alt = "{$smarty.const._YOUALREADYHAVETHISCOURSE}" onclick = "alert('{$smarty.const._YOUALREADYHAVETHISCOURSE}')">
                 {else}
                  <span>{$smarty.const._GETTHECOURSE} &quot;{$T_COURSE->course.name}&quot;, {$T_COURSE->course.price_string}</span>
                  <img class = "ajaxHandle" src = "images/32x32/{$cart_image}" title = "{$smarty.const._ADDTOCART}" alt = "{$smarty.const._ADDTOCART}" onclick = "addToCart(this, '{$T_COURSE->course.id}', 'course');{if !$T_CONFIGURATION.enable_cart}location=redirectLocation{/if}">
                 {/if}
          {else}
              {if $T_HAS_COURSE}
                  <span>{$smarty.const._YOULAREDYHAVETHECOURSE} &quot;{$T_COURSE->course.name}&quot; {$T_COURSE->course.price_string}</span>
                  <img class = "ajaxHandle inactiveImage" src = "images/32x32/{$cart_image}" title = "{$smarty.const._YOUALREADYHAVETHISCOURSE}" alt = "{$smarty.const._YOUALREADYHAVETHISCOURSE}" onclick = "alert('{$smarty.const._YOUALREADYHAVETHISCOURSE}')">
                 {else}
                  <span>{$smarty.const._GETTHECOURSE} &quot;{$T_COURSE->course.name}&quot;, {$smarty.const._FREEOFCHARGE}</span>
                  <img class = "ajaxHandle" src = "images/32x32/{$cart_image}" title = "{$smarty.const._ENROLL}" alt = "{$smarty.const._ENROLL}" onclick = "addToCart(this, '{$T_COURSE->course.id}', 'course');{if !$T_CONFIGURATION.enable_cart}location=redirectLocation{/if}">
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

  {capture name = "t_buy_course_code"}
         {if !$T_HAS_COURSE}
              <div id = "buy">
          {if $T_COURSE->course.price}
              {if $T_HAS_COURSE}
                  <span>{$T_COURSE->course.price_string}</span>
                  <img class = "ajaxHandle inactiveImage" src = "images/32x32/{$cart_image}" title = "{$smarty.const._YOUALREADYHAVETHISCOURSE}" alt = "{$smarty.const._YOUALREADYHAVETHISCOURSE}" onclick = "alert('{$smarty.const._YOUALREADYHAVETHISCOURSE}')">
                 {else}
                  <span>{$T_COURSE->course.price_string}</span>
                  <img class = "ajaxHandle" src = "images/32x32/{$cart_image}" title = "{$smarty.const._ADDTOCART}" alt = "{$smarty.const._ADDTOCART}" onclick = "addToCart(this, '{$T_COURSE->course.id}', 'course');{if !$T_CONFIGURATION.enable_cart}location=redirectLocation{/if}">
                 {/if}
          {else}
              {if $T_HAS_COURSE}
                  <span>{$T_COURSE->course.price_string}</span>
                  <img class = "ajaxHandle inactiveImage" src = "images/32x32/{$cart_image}" title = "{$smarty.const._YOUALREADYHAVETHISCOURSE}" alt = "{$smarty.const._YOUALREADYHAVETHISCOURSE}" onclick = "alert('{$smarty.const._YOUALREADYHAVETHISCOURSE}')">
                 {else}
                  <span>{$smarty.const._FREEOFCHARGE}</span>
                  <img class = "ajaxHandle" src = "images/32x32/{$cart_image}" title = "{$smarty.const._ENROLL}" alt = "{$smarty.const._ENROLL}" onclick = "addToCart(this, '{$T_COURSE->course.id}', 'course');{if !$T_CONFIGURATION.enable_cart}location=redirectLocation{/if}">
                 {/if}
       {/if}
                 </div>
             {/if}
         {/capture}

         {if $T_COURSE_INSTANCES && sizeof($T_COURSE_INSTANCES) > 1}
         <div class = "lessonInfo"><span class = "infoTitle">Available Instances:</span>
          <select onchange = "var val = this.options[this.options.selectedIndex].value;location = '{$smarty.server.PHP_SELF}?'+'{$smarty.server.QUERY_STRING}'.replace(/&courses_ID=\d*/, '&courses_ID='+val).replace(/&info_course=\d*/, '&info_course='+val);">
           {foreach name = 'instances_list' item = "item" key = 'key' from = $T_COURSE_INSTANCES}
           <option value = "{$key}" {if $smarty.get.courses_ID == $key || $smarty.get.info_course == $key}selected{/if}>{$item->course.name}</option>
           {/foreach}
          </select>
         </div>
         {/if}
         {foreach name = 'info_list' item = "item" key = "key" from = $T_COURSE_INFO->metadataArray}
          <div class = "lessonInfo"><span class = "infoTitle">{$T_COURSE_INFO->metadataAttributes.$key}:</span> {$item}</div>
         {/foreach}
      <div class = "lessonInfo"><span class = "infoTitle">{$smarty.const._LANGUAGE}:</span> {$T_LANGUAGES[$T_ADDITIONAL_COURSE_INFO.language]}</div>

   {if $T_COURSE->course.max_users}
    <div class = "lessonInfo"><span class = "infoTitle">{$smarty.const._MAXIMUMUSERS}:</span>
    {$T_COURSE->course.max_users} {if $T_COURSE->course.seats_remaining}({$T_COURSE->course.seats_remaining} {$smarty.const._SEATSREMAINING}){/if}
    </div>
   {/if}

   {if $T_COURSE->options.training_hours}
    <div class = "lessonInfo"><span class = "infoTitle">{$smarty.const._TRAININGHOURS}:</span> {$T_COURSE->options.training_hours}</div>
   {/if}
   {if $T_COURSE->course.start_date}
    <div class = "lessonInfo"><span class = "infoTitle">{$smarty.const._COURSESTARTSAT}:</span> #filter:timestamp_time_nosec-{$T_COURSE->course.start_date}#</div>
    <div class = "lessonInfo"><span class = "infoTitle">{$smarty.const._COURSEENDSAT}:</span> #filter:timestamp_time_nosec-{$T_COURSE->course.end_date}#</div>
   {/if}
   {if $T_COURSE->course.location}
    <div class = "lessonInfo"><span class = "infoTitle">{$smarty.const._LOCATION}:</span> {$T_COURSE->course.location}</div>
   {/if}
         {if $T_COURSE_LESSONS}
          <div class = "lessonInfo"><span class = "infoTitle">{$smarty.const._COURSELESSONS}:</span>
          {foreach name = 'course_lessons_list' item = "lesson" key = "id" from = $T_COURSE_LESSONS}
           <a href = "{$smarty.server.PHP_SELF}?{$smarty.server.QUERY_STRING}#{$lesson->lesson.id}" class = "editLink">{$lesson->lesson.name}{if !$smarty.foreach.course_lessons_list.last},{/if}</a>
          {/foreach}
          </div>
          {$smarty.capture.t_buy_course_code}
          {foreach name = 'course_lessons_list' item = "lesson" key = "id" from = $T_COURSE_LESSONS}
          <div class = "courseLessonInfo">
           <div class = "topTitle" >
            <a href = "javascript:scroll(0,0)" name = "{$lesson->lesson.id}" >{$lesson->lesson.name}</a>
           </div>
           {foreach name = 'info_list' item = "item" key = "key" from = $T_COURSE_LESSON_INFO[$id]->metadataArray}
            <div class = "lessonInfo"><span class = "infoTitle">{$T_COURSE_LESSON_INFO[$id]->metadataAttributes.$key}:</span> {$item}</div>
           {/foreach}
            {assign var = "language" value = $T_ADDITIONAL_LESSON_INFO[$id].language}
            <div class = "lessonInfo"><span class = "infoTitle">{$smarty.const._LANGUAGE}:</span> {$T_LANGUAGES[$language]}</div>
            {if $T_ADDITIONAL_LESSON_INFO[$id].professors_string}<div class = "lessonInfo"><span class = "infoTitle">{$smarty.const._PROFESSORS}:</span> {$T_ADDITIONAL_LESSON_INFO[$id].professors_string}</div>{/if}
            <div class = "lessonInfo"><span class = "infoTitle">{$smarty.const._TOTALUNITS}:</span> {$T_ADDITIONAL_LESSON_INFO[$id].content}</div>
            {if $T_ADDITIONAL_LESSON_INFO[$id].tests}<div class = "lessonInfo"><span class = "infoTitle">{$smarty.const._TOTALTESTS}:</span> {$T_ADDITIONAL_LESSON_INFO[$id].tests}</div>{/if}
            {if $T_ADDITIONAL_LESSON_INFO[$id].projects}<div class = "lessonInfo"><span class = "infoTitle">{$smarty.const._TOTALPROJECTS}:</span> {$T_ADDITIONAL_LESSON_INFO[$id].projects}</div>{/if}
           {if $T_CONTENT_TREE[$id]}
            <fieldset class = "fieldsetSeparator">
                <legend>{$smarty.const._LESSONCONTENT}</legend>
                {$T_CONTENT_TREE[$id]}
            </fieldset>
           {/if}
          </div>
          {/foreach}
         {/if}

   {$smarty.capture.t_buy_course_code}
 {/if}

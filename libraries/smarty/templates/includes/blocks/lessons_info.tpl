
	{if $T_LESSON_INFO}
        	{foreach name = 'info_list'  item = "item" key = "key" from = $T_LESSON_INFO->metadataArray}
        		<div class = "lessonInfo"><span class = "infoTitle">{$T_LESSON_INFO->metadataAttributes.$key}:</span> {$item}</div>	
        	{/foreach}
        		{if $T_ADDITIONAL_LESSON_INFO.professors_string}<div class = "lessonInfo"><span class = "infoTitle">{$smarty.const._PROFESSORS}:</span> {$T_ADDITIONAL_LESSON_INFO.professors_string}</div>{/if}
        		<div class = "lessonInfo"><span class = "infoTitle">{$smarty.const._TOTALUNITS}:</span> {$T_ADDITIONAL_LESSON_INFO.content}</div>
        		{if $T_ADDITIONAL_LESSON_INFO.tests}<div class = "lessonInfo"><span class = "infoTitle">{$smarty.const._TOTALTESTS}:</span> {$T_ADDITIONAL_LESSON_INFO.tests}</div>{/if}
        		{if $T_ADDITIONAL_LESSON_INFO.projects}<div class = "lessonInfo"><span class = "infoTitle">{$smarty.const._TOTALPROJECTS}:</span> {$T_ADDITIONAL_LESSON_INFO.projects}</div>{/if}
        	{if $T_CONTENT_TREE}
        		<fieldset>
            		<legend>{$smarty.const._LESSONCONTENT}</legend>
            		{$T_CONTENT_TREE}
        		</fieldset>
        	{/if}
		{if !$T_LESSON->lesson.course_only}	
        		<div id = "buy">
    		{if $T_LESSON->lesson.price}
            		<span>{$T_LESSON->lesson.price_string}</span>
            		{if !$T_LESSON->options.recurring}
            		<a href = "javascript:void(0)" onclick = "ajaxPost('{$T_LESSON->lesson.id}', this)" >
            			<img src = "images/32x32/cart.png" title = "{$smarty.const._ADDTOCART}" alt = "{$smarty.const._ADDTOCART}">
            		</a>
            		{/if}
            		<a href = "javascript:void(0)" >
            			<img src = "images/others/money.png" title = "{$smarty.const._SUBSCRIBE}" alt = "{$smarty.const._SUBSCRIBE}">
            		</a>
        	{else}
            		<span>{$smarty.const._FREEOFCHARGE}</span>
            		<a href = "javascript:void(0)" onclick = "ajaxPost('{$T_LESSON->lesson.id}', this)" >
            			<img src = "images/32x32/redo.png" title = "{$smarty.const._ENROLL}" alt = "{$smarty.const._ENORLL}">
            		</a>
        	{/if}
        		</div>
        {/if}
    {elseif $T_COURSE_INFO}
        {capture name = 't_lesson_info_code'}
        	{foreach name = 'info_list'  item = "item" key = "key" from = $T_COURSE_INFO->metadataArray}
        		<div class = "courseInfo"><span class = "infoTitle">{$T_COURSE_INFO->metadataAttributes.$key}:</span> {$item}</div>	
        	{/foreach}
        	{if $T_COURSE_LESSONS}
        		<p>{$smarty.const._COURSELESSONS}</p>
        		{foreach name = 'course_lessons_list' item = "item" key = "key" from = $T_COURSE_LESSONS}
        		<div>{$item.name}</div>
        		{/foreach}
        	{/if}
        		<hr>
        		<div id = "buy">
    		{if $T_COURSE->course.price}
            		<span>{$T_COURSE->course.price} {$T_CURRENCYSYMBOLS[$T_CONFIGURATION.currency]}</span>
            		<a href = "javascript:void(0)" onclick = "ajaxPost({$T_COURSE->course.id}+100000, this)">
            			<img src = "images/32x32/cart.png" title = "{$smarty.const._ADDTOCART}" alt = "{$smarty.const._ADDTOCART}">
            		</a>
    		{else}
            		<span>{$smarty.const._FREEOFCHARGE}</span>
            		<a href = "javascript:void(0)" onclick = "ajaxPost({$T_COURSE->course.id}+100000, this)">
            			<img src = "images/32x32/redo.png" title = "{$smarty.const._ENROLL}" alt = "{$smarty.const._ENROLL}">
            		</a>
			{/if}
            	</div>
	{/if}

	
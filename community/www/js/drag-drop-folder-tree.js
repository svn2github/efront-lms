    /************************************************************************************************************

    (C) www.dhtmlgoodies.com, July 2006



    Update log:

        August, 8th, 2006: Replaced getLeftPos and getTopPos methods.



    This is a script from www.dhtmlgoodies.com. You will find this and a lot of other scripts at our website.



    Terms of use:

    You are free to use this script as long as the copyright message is kept intact. However, you may not

    redistribute, sell or repost it without our permission.



    Thank you!



    www.dhtmlgoodies.com

    Alf Magne Kalleland



    ************************************************************************************************************/
    var JSTreeObj;
    var treeUlCounter = 0;
    var nodeId = 1;
    /* Constructor */
    function JSDragDropTree()
    {
        var idOfTree;
        var imageFolder;
        var folderImage;
        //var transparentImg = 'themes/default/images/others/transparent.png';
        //var theoryImage;
        //var scormImage;
        //var checkImage;
        //var deleteImage;
        var plusImage;
        var minusImage;
        var maximumDepth;
        var dragNode_source;
        var dragNode_parent;
        var dragNode_sourceNextSib;
        var dragNode_noSiblings;
        var ajaxObjects;
        var dragNode_destination;
        var floatingContainer;
        var dragDropTimer;
        var dropTargetIndicator;
        var insertAsSub;
        var indicator_offsetX;
        var indicator_offsetX_sub;
        var indicator_offsetY;
        this.imageFolder = 'themes/default/images/16x16/';
        this.folderImage = 'folder.png';
        this.transparentImg = 'themes/default/images/others/transparent.png';
//        this.theoryImage = 'theory.png';
//        this.scormImage  = 'theory.png';
//        this.checkImage  = 'success.png';
//        this.deleteImage = 'error_delete.png';
        this.plusImage = 'plus';
        this.minusImage = 'minus';
        this.maximumDepth = 6;
        this.deletedUnits = new Array(); //Efront custom variable
        this.activatedUnits = new Array(); //Efront custom variable
        this.deactivatedUnits = new Array(); //Efront custom variable
        var messageMaximumDepthReached;
        var filePathRenameItem;
        var filePathDeleteItem;
        var renameAllowed;
        var deleteAllowed;
        var currentlyActiveItem;
        var contextMenu;
        var currentItemToEdit; // Reference to item currently being edited(example: renamed)
        var helpObj;
        this.contextMenu = false;
        this.floatingContainer = document.createElement('UL');
        this.floatingContainer.style.position = 'absolute';
        this.floatingContainer.style.display='none';
        this.floatingContainer.id = 'floatingContainer';
        this.insertAsSub = false;
        document.body.appendChild(this.floatingContainer);
        this.dragDropTimer = -1;
        this.dragNode_noSiblings = false;
        this.currentItemToEdit = false;
        if(document.all){
            this.indicator_offsetX = 2; // Offset position of small black lines indicating where nodes would be dropped.
            this.indicator_offsetX_sub = 4;
            this.indicator_offsetY = -4; //changed by makriria 26/5/2010. It was -2
        }else{
            this.indicator_offsetX = 1; // Offset position of small black lines indicating where nodes would be dropped.
            this.indicator_offsetX_sub = 3;
            this.indicator_offsetY = 4; //changed by makriria 26/5/2010. It was 2
        }
        if(navigator.userAgent.indexOf('Opera')>=0){
            this.indicator_offsetX = 2; // Offset position of small black lines indicating where nodes would be dropped.
            this.indicator_offsetX_sub = 3;
            this.indicator_offsetY = -7;
        }
        this.messageMaximumDepthReached = ''; // Use '' if you don't want to display a message
        this.renameAllowed = true;
        this.deleteAllowed = true;
        this.currentlyActiveItem = false;
        this.filePathRenameItem = 'folderTree_updateItem.php';
        this.filePathDeleteItem = 'folderTree_updateItem.php';
        this.ajaxObjects = new Array();
        this.helpObj = false;
    }
    /* JSDragDropTree class */
    JSDragDropTree.prototype = {
        // {{{ addEvent()
        /**

         *

         *  This function adds an event listener to an element on the page.

         *

         *  @param Object whichObject = Reference to HTML element(Which object to assigne the event)

         *  @param String eventType = Which type of event, example "mousemove" or "mouseup"

         *  @param functionName = Name of function to execute.

         *

         * @public

         */
        addEvent : function(whichObject,eventType,functionName)
        {
          if(whichObject.attachEvent){
            whichObject['e'+eventType+functionName] = functionName;
            whichObject[eventType+functionName] = function(){whichObject['e'+eventType+functionName]( window.event );};
            whichObject.attachEvent( 'on'+eventType, whichObject[eventType+functionName] );
          } else
            whichObject.addEventListener(eventType,functionName,false);
        }
        // }}}
        ,
        // {{{ removeEvent()
        /**

         *

         *  This function removes an event listener from an element on the page.

         *

         *  @param Object whichObject = Reference to HTML element(Which object to assigne the event)

         *  @param String eventType = Which type of event, example "mousemove" or "mouseup"

         *  @param functionName = Name of function to execute.

         *

         * @public

         */
        removeEvent : function(whichObject,eventType,functionName)
        {
          if(whichObject.detachEvent){
            whichObject.detachEvent('on'+eventType, whichObject[eventType+functionName]);
            whichObject[eventType+functionName] = null;
          } else
            whichObject.removeEventListener(eventType,functionName,false);
        }
        ,
        Get_Cookie : function(name) {
           var start = document.cookie.indexOf(name+"=");
           var len = start+name.length+1;
           if ((!start) && (name != document.cookie.substring(0,name.length))) return null;
           if (start == -1) return null;
           var end = document.cookie.indexOf(";",len);
           if (end == -1) end = document.cookie.length;
           return unescape(document.cookie.substring(len,end));
        }
        ,
        // This function has been slightly modified
        Set_Cookie : function(name,value,expires,path,domain,secure) {
            expires = expires * 60*60*24*1000;
            var today = new Date();
            var expires_date = new Date( today.getTime() + (expires) );
            var cookieString = name + "=" +escape(value) +
               ( (expires) ? ";expires=" + expires_date.toGMTString() : "") +
               ( (path) ? ";path=" + path : "") +
               ( (domain) ? ";domain=" + domain : "") +
               ( (secure) ? ";secure" : "");
            document.cookie = cookieString;
        }
        ,
        setFileNameRename : function(newFileName)
        {
            this.filePathRenameItem = newFileName;
        }
        ,
        setFileNameDelete : function(newFileName)
        {
            this.filePathDeleteItem = newFileName;
        }
        ,setRenameAllowed : function(renameAllowed)
        {
            this.renameAllowed = renameAllowed;
        }
        ,
        setDeleteAllowed : function(deleteAllowed)
        {
            this.deleteAllowed = deleteAllowed;
        }
        ,setMaximumDepth : function(maxDepth)
        {
            this.maximumDepth = maxDepth;
        }
        ,setMessageMaximumDepthReached : function(newMessage)
        {
            this.messageMaximumDepthReached = newMessage;
        }
        ,
        setImageFolder : function(path)
        {
            this.imageFolder = path;
        }
        ,
        setFolderImage : function(imagePath)
        {
            this.folderImage = imagePath;
        }
        ,
        setPlusImage : function(imagePath)
        {
            this.plusImage = imagePath;
        }
        ,
        setMinusImage : function(imagePath)
        {
            this.minusImage = imagePath;
        }
        ,
        setTreeId : function(idOfTree)
        {
            this.idOfTree = idOfTree;
        }
        ,
        expandAll : function()
        {
            var menuItems = document.getElementById(this.idOfTree).getElementsByTagName('LI');
            for (var no=0;no<menuItems.length;no++){
                var subItems = menuItems[no].getElementsByTagName('UL');
                if (subItems.length>0 && subItems[0].style.display!='block'){
                    JSTreeObj.showHideNode(false, menuItems[no].id);
                }
            }
   this.status = 1;
        }
        ,
        collapseAll : function()
        {
            var menuItems = document.getElementById(this.idOfTree).getElementsByTagName('LI');
            for(var no=0;no<menuItems.length;no++){
                var subItems = menuItems[no].getElementsByTagName('UL');
                if(subItems.length>0 && subItems[0].style.display=='block'){
                    JSTreeObj.showHideNode(false,menuItems[no].id);
                }
            }
            this.status = 0;
        }
        ,
        /*

        Find top pos of a tree node

        */
        getTopPos : function(obj){
            var top = obj.offsetTop/1;
            while((obj = obj.offsetParent) != null){
                if(obj.tagName!='HTML')top += obj.offsetTop;
            }
            if(document.all)top = top/1 + 13; else top = top/1 + 4;
            return top;
        }
        ,
        /*

        Find left pos of a tree node

        */
        getLeftPos : function(obj){
            var left = obj.offsetLeft/1 + 1;
            while((obj = obj.offsetParent) != null){
                if(obj.tagName!='HTML')left += obj.offsetLeft;
            }
            if(document.all)left = left/1 - 2;
            return left;
        }
        ,
        showHideNode : function(e, inputId)
        {
            if(inputId){
                if (!document.getElementById(inputId)) return;
    var found = false;
    var obj = this;
    $(inputId).ancestors().each(function(s) {
     if (s.id == obj.idOfTree) {
      found = true;
     }
    });
    //Exit if this node is not part of the current tree
    if (!found) return;
                thisNode = document.getElementById(inputId).getElementsByTagName('IMG')[0];
                if (!thisNode) return; //Added this line to avoid js error in copy content
            }else {
                thisNode = this;
                if(this.tagName=='A')thisNode = this.parentNode.getElementsByTagName('IMG')[0];
            }
            if(thisNode.style.visibility=='hidden')return;
            var parentNode = thisNode.parentNode;
            inputId = parentNode.id.replace(/[^0-9]/g,'');
            if (thisNode.className.indexOf(JSTreeObj.plusImage)>=0) {
                thisNode.className = thisNode.className.replace(JSTreeObj.plusImage, JSTreeObj.minusImage);
                var ul = parentNode.getElementsByTagName('UL')[0];
                ul.style.display='block';
                if(!initExpandedNodes)initExpandedNodes = ',';
                if(initExpandedNodes.indexOf(',' + inputId + ',')<0) initExpandedNodes = initExpandedNodes + inputId + ',';
            } else {
                thisNode.className = thisNode.className.replace(JSTreeObj.minusImage,JSTreeObj.plusImage);
                parentNode.getElementsByTagName('UL')[0].style.display='none';
                initExpandedNodes = initExpandedNodes.replace(',' + inputId,'');
            }
            return false;
        }
        ,
        /* Initialize drag */
        initDrag : function(e)
        {
            if(document.all)e = event;
            var subs = JSTreeObj.floatingContainer.getElementsByTagName('LI');
            if(subs.length>0){
                if(JSTreeObj.dragNode_sourceNextSib){
                    JSTreeObj.dragNode_parent.insertBefore(JSTreeObj.dragNode_source,JSTreeObj.dragNode_sourceNextSib);
                }else{
                    JSTreeObj.dragNode_parent.appendChild(JSTreeObj.dragNode_source);
                }
            }
            JSTreeObj.dragNode_source = this.parentNode;
            JSTreeObj.dragNode_parent = this.parentNode.parentNode;
            JSTreeObj.dragNode_sourceNextSib = false;
            if(JSTreeObj.dragNode_source.nextSibling)JSTreeObj.dragNode_sourceNextSib = JSTreeObj.dragNode_source.nextSibling;
            JSTreeObj.dragNode_destination = false;
            JSTreeObj.dragDropTimer = 0;
            JSTreeObj.timerDrag();
            return false;
        }
        ,
        timerDrag : function()
        {
            if(this.dragDropTimer>=0 && this.dragDropTimer<10){
                this.dragDropTimer = this.dragDropTimer + 1;
                setTimeout('JSTreeObj.timerDrag()',20);
                return;
            }
            if(this.dragDropTimer==10)
            {
                JSTreeObj.floatingContainer.style.display='block';
                JSTreeObj.floatingContainer.appendChild(JSTreeObj.dragNode_source);
            }
        }
        ,
        moveDragableNodes : function(e)
        {
            if(JSTreeObj.dragDropTimer<10)return;
            if(document.all)e = event;
            dragDrop_x = e.clientX/1 + 5 + document.body.scrollLeft;
            dragDrop_y = e.clientY/1 + 5 + document.documentElement.scrollTop;
            JSTreeObj.floatingContainer.style.left = dragDrop_x + 'px';
            JSTreeObj.floatingContainer.style.top = dragDrop_y + 'px';
            var thisObj = this;
            if(thisObj.tagName=='A' || thisObj.tagName=='IMG')thisObj = thisObj.parentNode;
            JSTreeObj.dragNode_noSiblings = false;
            var tmpVar = thisObj.getAttribute('noSiblings');
            if(!tmpVar)tmpVar = thisObj.noSiblings;
            if(tmpVar=='true')JSTreeObj.dragNode_noSiblings=true;
            if(thisObj && thisObj.id)
            {
                JSTreeObj.dragNode_destination = thisObj;
                var img = thisObj.getElementsByTagName('IMG')[1];
                var tmpObj= JSTreeObj.dropTargetIndicator;
                tmpObj.style.display='block';
                var eventSourceObj = this;
                if(JSTreeObj.dragNode_noSiblings && eventSourceObj.tagName=='IMG')eventSourceObj = eventSourceObj.nextSibling;
                var tmpImg = tmpObj.getElementsByTagName('IMG')[0];
                if(this.tagName=='A' || JSTreeObj.dragNode_noSiblings){
                    tmpImg.src = tmpImg.src.replace('ind1','ind2');
                    JSTreeObj.insertAsSub = true;
                    tmpObj.style.left = (JSTreeObj.getLeftPos(eventSourceObj) + JSTreeObj.indicator_offsetX_sub) + 'px';
                }else{
                    tmpImg.src = tmpImg.src.replace('ind2','ind1');
                    JSTreeObj.insertAsSub = false;
                    tmpObj.style.left = (JSTreeObj.getLeftPos(eventSourceObj) + JSTreeObj.indicator_offsetX) + 'px';
                }
                tmpObj.style.top = (JSTreeObj.getTopPos(thisObj) + JSTreeObj.indicator_offsetY) + 'px';
            }
            return false;
        }
        ,
        dropDragableNodes:function()
        {
            if(JSTreeObj.dragDropTimer<10){
                JSTreeObj.dragDropTimer = -1;
                return;
            }
            var showMessage = false;
            if(JSTreeObj.dragNode_destination){ // Check depth
                var countUp = JSTreeObj.dragDropCountLevels(JSTreeObj.dragNode_destination,'up');
                var countDown = JSTreeObj.dragDropCountLevels(JSTreeObj.dragNode_source,'down');
                var countLevels = countUp/1 + countDown/1 + (JSTreeObj.insertAsSub?1:0);
                if(countLevels>JSTreeObj.maximumDepth){
                    JSTreeObj.dragNode_destination = false;
                    showMessage = true; // Used later down in this function
                }
            }
            if(JSTreeObj.dragNode_destination){
                if(JSTreeObj.insertAsSub){
                    var uls = JSTreeObj.dragNode_destination.getElementsByTagName('UL');
                    if(uls.length>0){
                        ul = uls[0];
                        ul.style.display='block';
                        var lis = ul.getElementsByTagName('LI');
                        if(lis.length>0){ // Sub elements exists - drop dragable node before the first one
                            ul.insertBefore(JSTreeObj.dragNode_source,lis[0]);
                        }else { // No sub exists - use the appendChild method - This line should not be executed unless there's something wrong in the HTML, i.e empty <ul>
                            ul.appendChild(JSTreeObj.dragNode_source);
                        }
                    }else{
                        var ul = document.createElement('UL');
                        ul.style.display='block';
                        JSTreeObj.dragNode_destination.appendChild(ul);
                        ul.appendChild(JSTreeObj.dragNode_source);
                    }
                    var img = JSTreeObj.dragNode_destination.getElementsByTagName('IMG')[0];
                    img.style.visibility='visible';
                    img.className = img.className.replace(JSTreeObj.plusImage,JSTreeObj.minusImage);
                }else{
                    if(JSTreeObj.dragNode_destination.nextSibling){
                        var nextSib = JSTreeObj.dragNode_destination.nextSibling;
                        nextSib.parentNode.insertBefore(JSTreeObj.dragNode_source,nextSib);
                    }else{
                        JSTreeObj.dragNode_destination.parentNode.appendChild(JSTreeObj.dragNode_source);
                    }
                }
            }else{
                // Putting the item back to it's original location

                if(JSTreeObj.dragNode_sourceNextSib){
                    JSTreeObj.dragNode_parent.insertBefore(JSTreeObj.dragNode_source,JSTreeObj.dragNode_sourceNextSib);
                }else{
                    JSTreeObj.dragNode_parent.appendChild(JSTreeObj.dragNode_source);
                }

            }
            JSTreeObj.dropTargetIndicator.style.display='none';
            JSTreeObj.dragDropTimer = -1;
            if(showMessage && JSTreeObj.messageMaximumDepthReached)alert(JSTreeObj.messageMaximumDepthReached);
        }
        ,
        createDropIndicator : function()
        {
            this.dropTargetIndicator = document.createElement('DIV');
            this.dropTargetIndicator.style.position = 'absolute';
            this.dropTargetIndicator.style.display='none';
            var img = document.createElement('IMG');
            //img.src = this.imageFolder + 'dragDrop_ind1.gif';
            //img.src = this.imageFolder + 'testme.png';
            img.src = this.transparentImg;

            Element.extend(img).addClassName('sprite16 sprite16-tree_indicator');
            img.id = 'dragDropIndicatorImage';
            this.dropTargetIndicator.appendChild(img);
            document.body.appendChild(this.dropTargetIndicator);
        }
        ,
        dragDropCountLevels : function(obj,direction,stopAtObject){
            var countLevels = 0;
            if(direction=='up'){
                while(obj.parentNode && obj.parentNode!=stopAtObject){
                    obj = obj.parentNode;
                    if(obj.tagName=='UL')countLevels = countLevels/1 +1;
                }
                return countLevels;
            }

            if(direction=='down'){
                var subObjects = obj.getElementsByTagName('LI');
                for(var no=0;no<subObjects.length;no++){
                    countLevels = Math.max(countLevels,JSTreeObj.dragDropCountLevels(subObjects[no],"up",obj));
                }
                return countLevels;
            }
        }
        ,
        cancelEvent : function()
        {
            return false;
        }
        ,
        cancelSelectionEvent : function()
        {

            if(JSTreeObj.dragDropTimer<10)return true;
            return false;
        }
        ,toggleNodes: function(nodesStatus)
        {
         alert(nodesStatus);
        }
        ,getNodeOrders : function(initObj,saveString)
        {

            if(!saveString)var saveString = '';
            if(!initObj){
                initObj = document.getElementById(this.idOfTree);

            }
            var lis = initObj.getElementsByTagName('LI');

            if(lis.length>0){
                var li = lis[0];
                while(li){
                    if(li.id){
                        if(saveString.length>0)saveString = saveString + ',';
                        var numericID = li.id.replace(/[^0-9]/gi,'');
                        if(numericID.length==0)numericID='A';
                        var numericParentID = li.parentNode.parentNode.id.replace(/[^0-9]/gi,'');
                        if(numericID!='0'){
                            saveString = saveString + numericID;
                            saveString = saveString + '-';


                            if(li.parentNode.id!=this.idOfTree)saveString = saveString + numericParentID; else saveString = saveString + '0';
                        }
                        var ul = li.getElementsByTagName('UL');
                        if(ul.length>0){
                            saveString = this.getNodeOrders(ul[0],saveString);
                        }
                    }
                    li = li.nextSibling;
                }
            }

            if(initObj.id == this.idOfTree){
                return saveString;

            }
            return saveString;
        }
        ,highlightItem : function(inputObj,e)
        {
            if(JSTreeObj.currentlyActiveItem)JSTreeObj.currentlyActiveItem.className = '';
            this.className = 'highlightedNodeItem';
            JSTreeObj.currentlyActiveItem = this;
        }
        ,
        removeHighlight : function()
        {
            if(JSTreeObj.currentlyActiveItem)JSTreeObj.currentlyActiveItem.className = '';
            JSTreeObj.currentlyActiveItem = false;
        }
        ,
        hasSubNodes : function(obj)
        {
            var subs = obj.getElementsByTagName('LI');
            if(subs.length>0)return true;
            return false;
        }
        ,
        deleteItem : function(obj1,obj2)
        {
            var message = 'Click OK to delete item ' + obj2.innerHTML;
            if(this.hasSubNodes(obj2.parentNode)) message = message + ' and it\'s sub nodes';
            if(confirm(message)){
                this.__deleteItem_step2(obj2.parentNode); // Sending <LI> tag to the __deleteItem_step2 method
            }

        }
        ,
        //Efront Custom function 
        deleteUnit : function(obj1,obj2)
        {

    obj = obj2.parentNode;
             var saveString = obj.id.replace(/[^0-9]/gi,'');

             var lis = obj.getElementsByTagName('LI');
             for(var no=0;no<lis.length;no++){
                 saveString = saveString + ',' + lis[no].id.replace(/[^0-9]/gi,'');
             }

    this.deletedUnits ? this.deletedUnits = this.deletedUnits.concat(saveString.split(",")) : this.deletedUnits = saveString.split(",");
                var parentRef = obj.parentNode.parentNode;
                obj.parentNode.removeChild(obj);
                this.__refreshDisplay(parentRef);

        },
        //Efront Custom function 
        activateUnit : function(obj1, obj2)
        {
    obj = obj2.parentNode;
             var saveString = obj.id.replace(/[^0-9]/gi,'');

             var lis = obj.getElementsByTagName('LI');
             for (var no = 0; no < lis.length; no++) {
                 saveString = saveString + ',' + lis[no].id.replace(/[^0-9]/gi,'');
             }
    saveString = saveString.split(",");
    var matched = '';
    var img = null;
    obj2.next().select('img').each (function (s) {
     if (s.className.match('trafficlight_green')) {
      matched = 'green';
      img = s;
     } else if (s.className.match('trafficlight_red')) {
      matched = 'red';
      img = s;
     }
    });

                if ((this.activatedUnits.indexOf(saveString.first()) == -1) && matched == 'green') {
                 setImageSrc(img, 16, 'trafficlight_red.png');

                 var outsideThis = this;
     saveString.each(function (s) {outsideThis.deactivatedUnits.push(s);});
     obj.select('a').each(function (s) {s.removeClassName('treeactive');s.addClassName('treeinactive');});
     obj.select('img').each(function (s) {if (s.className.match('green')) setImageSrc(s, 16, 'trafficlight_red.png');});

     this.deactivatedUnits = this.deactivatedUnits.uniq();
                } else if ((this.deactivatedUnits.indexOf(saveString.first()) == -1) && matched == 'red') {
                 setImageSrc(img, 16, 'trafficlight_green.png');

                 var outsideThis = this;
     saveString.each(function (s) {outsideThis.activatedUnits.push(s);});
     obj.select('a').each(function (s) {s.removeClassName('treeinactive');s.addClassName('treeactive');});
     obj.select('img').each(function (s) {if (s.className.match('trafficlight_red')) setImageSrc(s, 16, 'trafficlight_green.png');});

     this.activatedUnits = this.activatedUnits.uniq();
                } else if (this.deactivatedUnits.indexOf(saveString.first()) > -1) {
                 img = obj2.next().select('img.sprite16-trafficlight_red')[0];
                 //obj2.next().select('img').each(function (s) {alert(s.className)});
                 setImageSrc(img, 16, 'trafficlight_green.png');

                 obj.select('a').each(function (s) {s.removeClassName('treeinactive');s.addClassName('treeactive');});
                 obj.select('img').each(function (s) {if (s.className.match('trafficlight_red')) setImageSrc(s, 16, 'trafficlight_green.png');});

                 var outsideThis = this;
                 saveString.each(function (s) {outsideThis.deactivatedUnits = outsideThis.deactivatedUnits.without(s);});
                } else if (this.activatedUnits.indexOf(saveString.first()) > -1) {
                 img = obj2.next().select('img.sprite16-trafficlight_green')[0];
                 setImageSrc(img, 16, 'trafficlight_red.png');

                 obj.select('a').each(function (s) {s.removeClassName('treeactive');s.addClassName('treeinactive');});
                 obj.select('img').each(function (s) {if (s.className.match('green')) setImageSrc(s, 16, 'trafficlight_red.png');});

                 var outsideThis = this;
                 saveString.each(function (s) {outsideThis.activatedUnits = outsideThis.activatedUnits.without(s);});
                }

                var parentRef = obj.parentNode.parentNode;

                this.__refreshDisplay(parentRef);
        },

        //Efront custom function
        getDeletedUnits : function()
        {
         return this.deletedUnits;
        },
        //Efront custom function
        getActivatedUnits : function()
        {
         return this.activatedUnits;
        },
        //Efront custom function
        getDeactivatedUnits : function()
        {
         return this.deactivatedUnits;
        },

        __refreshDisplay : function(obj)
        {
            if(this.hasSubNodes(obj))return;

            var img = obj.getElementsByTagName('IMG')[0];
            img.style.visibility = 'hidden';
        }
        ,
        __deleteItem_step2 : function(obj)
        {

            var saveString = obj.id.replace(/[^0-9]/gi,'');

            var lis = obj.getElementsByTagName('LI');
            for(var no=0;no<lis.length;no++){
                saveString = saveString + ',' + lis[no].id.replace(/[^0-9]/gi,'');
            }

            // Creating ajax object and send items
            var ajaxIndex = JSTreeObj.ajaxObjects.length;
            JSTreeObj.ajaxObjects[ajaxIndex] = new sack();
            var url = JSTreeObj.filePathDeleteItem + '?deleteIds=' + saveString;
            JSTreeObj.ajaxObjects[ajaxIndex].requestFile = url; // Specifying which file to get
            JSTreeObj.ajaxObjects[ajaxIndex].onCompletion = function() { JSTreeObj.__deleteComplete(ajaxIndex,obj); } ; // Specify function that will be executed after file has been found
            JSTreeObj.ajaxObjects[ajaxIndex].runAJAX(); // Execute AJAX function


        }
        ,
        __deleteComplete : function(ajaxIndex,obj)
        {
            if(this.ajaxObjects[ajaxIndex].response!='OK'){
                alert('ERROR WHEN TRYING TO DELETE NODE: ' + this.ajaxObjects[ajaxIndex].response); // Rename failed
            }else{
                var parentRef = obj.parentNode.parentNode;
                obj.parentNode.removeChild(obj);
                this.__refreshDisplay(parentRef);

            }

        }
        ,
        __renameComplete : function(ajaxIndex)
        {
            if(this.ajaxObjects[ajaxIndex].response!='OK'){
                alert('ERROR WHEN TRYING TO RENAME NODE: ' + this.ajaxObjects[ajaxIndex].response); // Rename failed
            }
        }
        ,
        __saveTextBoxChanges : function(e,inputObj)
        {
            if(!inputObj && this)inputObj = this;
            if(document.all)e = event;
            if(e.keyCode && e.keyCode==27){
                JSTreeObj.__cancelRename(e,inputObj);
                return;
            }
            inputObj.style.display='none';
            inputObj.nextSibling.style.visibility='visible';
            if(inputObj.value.length>0){
                inputObj.nextSibling.innerHTML = inputObj.value;
                // Send changes to the server.
                var ajaxIndex = JSTreeObj.ajaxObjects.length;
                JSTreeObj.ajaxObjects[ajaxIndex] = new sack();
                var url = JSTreeObj.filePathRenameItem + '?renameId=' + inputObj.parentNode.id.replace(/[^0-9]/gi,'') + '&newName=' + inputObj.value;
                JSTreeObj.ajaxObjects[ajaxIndex].requestFile = url; // Specifying which file to get
                JSTreeObj.ajaxObjects[ajaxIndex].onCompletion = function() { JSTreeObj.__renameComplete(ajaxIndex); } ; // Specify function that will be executed after file has been found
                JSTreeObj.ajaxObjects[ajaxIndex].runAJAX(); // Execute AJAX function



            }
        }
        ,
        __cancelRename : function(e,inputObj)
        {
            if(!inputObj && this)inputObj = this;
            inputObj.value = JSTreeObj.helpObj.innerHTML;
            inputObj.nextSibling.innerHTML = JSTreeObj.helpObj.innerHTML;
            inputObj.style.display = 'none';
            inputObj.nextSibling.style.visibility = 'visible';
        }
        ,
        __renameCheckKeyCode : function(e)
        {
            if(document.all)e = event;
            if(e.keyCode==13){ // Enter pressed
                JSTreeObj.__saveTextBoxChanges(false,this);
            }
            if(e.keyCode==27){ // ESC pressed
                JSTreeObj.__cancelRename(false,this);
            }
        }
        ,
        __createTextBox : function(obj)
        {
            var textBox = document.createElement('INPUT');
            textBox.className = 'folderTreeTextBox';
            textBox.value = obj.innerHTML;
            obj.parentNode.insertBefore(textBox,obj);
            textBox.id = 'textBox' + obj.parentNode.id.replace(/[^0-9]/gi,'');
            textBox.onblur = this.__saveTextBoxChanges;
            textBox.onkeydown = this.__renameCheckKeyCode;
            this.__renameEnableTextBox(obj);
        }
        ,
        __renameEnableTextBox : function(obj)
        {
            obj.style.visibility = 'hidden';
            obj.previousSibling.value = obj.innerHTML;
            obj.previousSibling.style.display = 'inline';
            obj.previousSibling.select();
        }
        ,
        renameItem : function(obj1,obj2)
        {
            currentItemToEdit = obj2.parentNode; // Reference to the <li> tag.
            if(!obj2.previousSibling || obj2.previousSibling.tagName.toLowerCase()!='input'){
                this.__createTextBox(obj2);
            }else{
                this.__renameEnableTextBox(obj2);
            }
            this.helpObj.innerHTML = obj2.innerHTML;

        }
        ,
        initTree : function()
        {
            JSTreeObj = this;
            JSTreeObj.createDropIndicator();
            document.documentElement.onselectstart = JSTreeObj.cancelSelectionEvent;
            document.documentElement.ondragstart = JSTreeObj.cancelEvent;
            document.documentElement.onmousedown = JSTreeObj.removeHighlight;

            /* Creating help object for storage of values */
            this.helpObj = document.createElement('DIV');
            this.helpObj.style.display = 'none';
            document.body.appendChild(this.helpObj);

            /* Create context menu */
            if(this.deleteAllowed || this.renameAllowed){
                try{
                    /* Creating menu model for the context menu, i.e. the datasource */
                    var menuModel = new DHTMLGoodies_menuModel();
                    //if(this.deleteAllowed)menuModel.addItem(1,'Delete','','',false,'JSTreeObj.deleteItem');
                    if(this.deleteAllowed)menuModel.addItem(1,'Delete','','',false,'JSTreeObj.deleteUnit');
                    if(this.renameAllowed)menuModel.addItem(2,'Rename','','',false,'JSTreeObj.renameItem');
                    menuModel.init();

                    var menuModelRenameOnly = new DHTMLGoodies_menuModel();
                    if(this.renameAllowed)menuModelRenameOnly.addItem(3,'Rename','','',false,'JSTreeObj.renameItem');
                    menuModelRenameOnly.init();

                    var menuModelDeleteOnly = new DHTMLGoodies_menuModel();
                    //if(this.deleteAllowed)menuModelDeleteOnly.addItem(4,'Delete','','',false,'JSTreeObj.deleteItem');
                    if(this.deleteAllowed)menuModelDeleteOnly.addItem(4,'Delete','','',false,'JSTreeObj.deleteUnit');
                    menuModelDeleteOnly.init();

                    window.refToDragDropTree = this;

                    this.contextMenu = new DHTMLGoodies_contextMenu();
                    this.contextMenu.setWidth(120);
                    referenceToDHTMLSuiteContextMenu = this.contextMenu;
                }catch(e){

                }
            }

            var nodeId = 0;
            var dhtmlgoodies_tree = document.getElementById(this.idOfTree);
            var menuItems = dhtmlgoodies_tree.getElementsByTagName('LI'); // Get an array of all menu items

            for(var no=0;no<menuItems.length;no++){
                // No children var set ?
                var noChildren = false;
                var tmpVar = menuItems[no].getAttribute('noChildren');
                if(!tmpVar)tmpVar = menuItems[no].noChildren;
                if(tmpVar=='true')noChildren=true;
                // No drag var set ?
                var noDrag = false;
                var tmpVar = menuItems[no].getAttribute('noDrag');
                if(!tmpVar)tmpVar = menuItems[no].noDrag;
                if(tmpVar=='true')noDrag=true;

                nodeId++;
                var subItems = menuItems[no].getElementsByTagName('UL');
                var img = document.createElement('IMG');
                img.src = this.transparentImg;
                Element.extend(img).addClassName('sprite16 sprite16-plus');
                //img.src = this.imageFolder + this.plusImage; 
                img.onclick = JSTreeObj.showHideNode;

                if(subItems.length==0)img.style.visibility='hidden';else{
                    subItems[0].id = 'tree_ul_' + treeUlCounter;
                    treeUlCounter++;
                }
                var aTag = menuItems[no].getElementsByTagName('A')[0];
                aTag.id = 'nodeATag' + menuItems[no].id.replace(/[^0-9]/gi,'');

                if(!noDrag)aTag.onmousedown = JSTreeObj.initDrag;
                if(!noChildren)aTag.onmousemove = JSTreeObj.moveDragableNodes;
                if(img == null)
                    alert('image is null');
                if(aTag == null)
                    alert('aTag is null');
                menuItems[no].insertBefore(img,aTag);

                var folderImg = document.createElement('IMG');
                if(!noDrag)folderImg.onmousedown = JSTreeObj.initDrag;
                folderImg.onmousemove = JSTreeObj.moveDragableNodes;
                folderImg.id = 'tree_image_' + menuItems[no].id.replace(/[^0-9]/gi,''); //periklis: Added here to be able to dynamically change image from plain to 'seen' etc 

                if(menuItems[no].className){
                    //folderImg.src = this.imageFolder + menuItems[no].className + '.'+globalImageExtension;
                    //alert(menuItems[no].className);
                    folderImg.src = this.transparentImg;
                    Element.extend(folderImg).addClassName('handle sprite16 sprite16-'+menuItems[no].className);
                } else {
                 folderImg.src = this.transparentImg;
                 Element.extend(folderImg).addClassName('handle sprite16 sprite16-folder');
                    //folderImg.src = this.imageFolder + this.folderImage;
                }
                menuItems[no].insertBefore(folderImg,aTag);


                if(this.contextMenu){
                    var noDelete = menuItems[no].getAttribute('noDelete');
                    if(!noDelete)noDelete = menuItems[no].noDelete;
                    var noRename = menuItems[no].getAttribute('noRename');
                    if(!noRename)noRename = menuItems[no].noRename;

                    if(noRename=='true' && noDelete=='true'){}else{
                        if(noDelete == 'true')this.contextMenu.attachToElement(aTag,false,menuModelRenameOnly);
                        else if(noRename == 'true')this.contextMenu.attachToElement(aTag,false,menuModelDeleteOnly);
                        else this.contextMenu.attachToElement(aTag,false,menuModel);

                    }
                }
                this.addEvent(aTag,'contextmenu',this.highlightItem);


            }

            initExpandedNodes = this.Get_Cookie('dhtmlgoodies_expandedNodes');
            if(initExpandedNodes){
                var nodes = initExpandedNodes.split(',');
                for(var no=0;no<nodes.length;no++){
                    if(nodes[no])this.showHideNode(false,nodes[no]);
                }
            }

            document.documentElement.onmousemove = JSTreeObj.moveDragableNodes;
            document.documentElement.onmouseup = JSTreeObj.dropDragableNodes;
        }
    };


    // Used for the HCD organizational chart correct display:
    // Opening nodes by level, while under a limit
    var allChildrenSum = 0;

    // This function is called below -search it- to identify whether the next level's nodes overcome a limit
    function findChildrenOfLevel(myArray) {
        var returnArray = new Array(); // all ul's of the next level will be put here
        var childrenSum = 0; // all li's of this level will be accounted in this variable

        while(newNode = myArray.pop(myArray)) {
            childrenSum += newNode.childElements().length;
            for (i = 0 ; i < newNode.childElements().length; i++) {
                if (newNode.childElements()[i] && newNode.childElements()[i].childElements()[3]) {
                    returnArray.push(newNode.childElements()[i].childElements()[3]); // the hardcoded 3 accounts for <img, <img, <a, ...-> the new ul
                }
            }
        }
        allChildrenSum += childrenSum;
        return returnArray;
    }

    $$('ul', 'dhtmlgoodies_tree').each(function (s) {

        if (s.id.startsWith('dhtml')) {
            treeObj = new JSDragDropTree();
            treeObj.setTreeId(s.id);
            treeObj.setMaximumDepth(20);
            treeObj.initTree();
            if (treeObj.getNodeOrders().length) {

                if ($('expand_collapse_div'+treeObj.idOfTree) && $('expand_collapse_div'+treeObj.idOfTree).getAttribute("expand")) {
                    $('expand_collapse_div'+treeObj.idOfTree).getAttribute("expand") ? treeObj.expandAll() : treeObj.collapseAll();
                } else {

                    if (treeObj.getNodeOrders().match(/-/g).length > 30) { //for enterprise->charts this should be "100", {if $T_ENTERPRISE && $T_CTG == "module_hcd" && $T_OP == "chart"}100{else}30{/if}
                        treeObj.collapseAll();
                        // using the title attibute to maintain the information whether the tree is expanded or not
                        s.collapsed = true;
                        treeObj.collapsed = true;
                    } else {
                        s.collapsed = false;
                        treeObj.expandAll();
                        treeObj.collapsed = false;
                    }
                }
                if ($('expand_collapse_div'+treeObj.idOfTree)) {
                    treeObj.getNodeOrders().match(/-[^0]/g) && //Has subnodes
                    treeObj.getNodeOrders().match(/-[^0]/g).length > 10 && //Has more than 10 root nodes
                    treeObj.getNodeOrders().match(/-/g).length > 30 && //Has more than 30 nodes
                    !currentUnit ? $('expand_collapse_div'+treeObj.idOfTree).show() : $('expand_collapse_div'+treeObj.idOfTree).hide(); //Do not display "expand/collapse all" link if there are few children
                }
                if (currentUnit && treeObj.status == 0) { //If status = 0, then the tree is collapsed. So we need to make appear the seelcted node
                    var depth = treeObj.dragDropCountLevels(currentUnit, 'up');
                    for (var i = depth; i > 0; i--) { //Make appear the parent nodes of the selected node
                        treeObj.showHideNode(false, currentUnit.id);
                        currentUnit = currentUnit.parentNode.parentNode;
                    }
                }
            } else if ($('expand_collapse_div'+treeObj.idOfTree)) { //Hide expand/collapse link for an empty tree
                $('expand_collapse_div'+treeObj.idOfTree).update('<span class = "emptyCategory">-</span>');
            }


            // Level-wise expanding of Organizational branch tree or preparation for printing (full expanding)
            if (s.id.match('dhtmlgoodies_branches_tree')) {
                if (window.location.toString().match('/popup/')) {
                    // Either expand everything for printing
                    s.collapsed = false;
                    treeObj.expandAll();
                    treeObj.collapsed = false;
                } else {
                    // Or create semi-open view if nodes are more than a hundred
                    if (treeObj.getNodeOrders().match(/-/g).length > 100) {
                        var myArray = new Array();
                        var previousArray = new Array();
                        // Initialize the algorithm only with the root
                        myArray.push(s);
                        var levelCount = 0;
                        var maximumAllowedOpenNodes = 40;
                        var flag_first_iteration = 1; // to get the previousArray only after the first iteration
                        while (allChildrenSum < maximumAllowedOpenNodes && myArray.length > 0) {
                            returnArray = findChildrenOfLevel(myArray);

                            if (allChildrenSum < maximumAllowedOpenNodes) {
                                if (!flag_first_iteration) {
                                    // The reasoning is to expand the array of the previous level, not the current one
                                    // so as not to exceed limits, while having opened the level
                                    for(i =0 ;i<previousArray.length; i++) {
                                        treeObj.showHideNode(true, previousArray[i].up().id);
                                    }
                                } else {
                                    flag_first_iteration = 0;
                                }
                                previousArray.clear();
                                for(i =0 ;i<returnArray.length; i++) {
                                    previousArray.push(returnArray[i]);
                                }
                                myArray = returnArray;
                            }
                        }
                    }
                }
            }

            //Event.observe(s.id, 'load', positionCorrectly(s.id));	//Commented out because it caused sources overuse 

        }
    });
    //This is used to correctly position any content tree tools. It is put here so that html loading and rendering is complete (or nearly complete)
    //@deprecated, see comment 3 lines above
    function positionCorrectly(id) {
        obj = $(id);
        //obj2 = obj.up().up().up().up().up();
        width = obj.getWidth() + obj.positionedOffset().left;
        //alert('width: '+width+'up: '+obj2+parseInt(parseInt(obj2.getWidth())+parseInt(obj2.positionedOffset().left)));
        $(obj).select('toolsDiv').each(function (s) {s.setStyle({left: width+'px'});});
    }
